<?php

namespace AppBundle\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Event\MovementRequest;
use AppBundle\Domain\Service\MovePlayer\MoveAllPlayersServiceInterface;
use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use AppBundle\Domain\Service\MovePlayer\MovePlayerServiceInterface;
use AppBundle\Domain\Service\MovePlayer\PlayerRequestInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Service to move all the players of a game in asynchronous mode using RabbitMQ. Request are published to a named queue
 * (q-player-movement-request) and responses are received in an unnamed created queue like an RPC system.
 *
 * @package AppBundle\Service\MovePlayer
 */
class MoveAllPlayersAsyncService implements MoveAllPlayersServiceInterface
{
    /** @var AMQPStreamConnection */
    private $rabbitmq;

    /** @var PlayerRequestInterface */
    private $requestBuilder;

    /** @var MovePlayerServiceInterface */
    protected $movePlayerService;

    /** @var int */
    private $timeout;

    /** @var string AMQP resource names */
    public const X_PLAYER_MOVEMENT_REQUEST = 'x-player-movement-request';
    public const Q_PLAYER_MOVEMENT_REQUEST = 'q-player-movement-request';

    /** @var int Default timeout */
    private const DEFAULT_TIMEOUT = 3;
    /**
     * MoveAllPlayersAsyncService constructor.
     *
     * @param AMQPStreamConnection $rabbitmq
     * @param PlayerRequestInterface $requestBuilder
     * @param MovePlayerServiceInterface $movePlayerService
     * @param int $timeout
     */
    public function __construct(
        AMQPStreamConnection $rabbitmq,
        PlayerRequestInterface $requestBuilder,
        MovePlayerServiceInterface $movePlayerService,
        int $timeout = self::DEFAULT_TIMEOUT
    ) {
        $this->rabbitmq = $rabbitmq;
        $this->requestBuilder = $requestBuilder;
        $this->movePlayerService = $movePlayerService;
        $this->timeout = $timeout;
        $this->createResources();
    }


    /**
     * Move all the players in a game
     *
     * @param Game $game
     * @return void
     * @throws MovePlayerException
     */
    public function move(Game &$game)
    {
        /** @var Player[] $players */
        $players = $game->players();

        // Open channel to RabbitMQ
        $channel = $this->rabbitmq->channel();

        // Declare unnamed callback queue
        $queueData = $channel->queue_declare('', false, false, false, true);
        $callbackQueue = reset($queueData);

        /** @var $published: player-uuid => request-uuid */
        $published = [];
        foreach ($players as $player) {
            if ($player->status() == Player::STATUS_PLAYING) {
                $published[$player->uuid()] = $this->publishRequest($channel, $callbackQueue, $game, $player);
            }
        }

        /** @var string[] $responses: request-uuid => response */
        $responses = $this->readResponses($channel, $callbackQueue, $published);

        // Move players with responses
        foreach ($players as $player) {
            if (array_key_exists($player->uuid(), $published)) {
                $requestUUid = $published[$player->uuid()];
                if (array_key_exists($requestUUid, $responses)) {
                    $response = $responses[$requestUUid];
                    $this->movePlayerService->move($player, $game, $response);
                }
            }
        }

        // Close connections
        $channel->queue_delete($callbackQueue);
        $channel->close();
    }

    /**
     * Creates the resources in RabbitMQ
     *
     * @return void
     */
    private function createResources() : void
    {
        $channel = $this->rabbitmq->channel();
        $channel->exchange_declare(self::X_PLAYER_MOVEMENT_REQUEST, 'fanout', false, true, false);
        $channel->queue_declare(self::Q_PLAYER_MOVEMENT_REQUEST, false, true, false, false);
        $channel->queue_bind(self::Q_PLAYER_MOVEMENT_REQUEST, self::X_PLAYER_MOVEMENT_REQUEST);
        $channel->close();
    }

    /**
     * Publishes the event to ask for the next player movement to RabbitMQ
     *
     * @param Game        $game
     * @param Player      $player
     * @param AMQPChannel $channel
     * @param string      $callbackQueue
     * @return string     the UUID of the event sent (to be uses as correllation id)
     * @throws MovePlayerException
     */
    private function publishRequest(AMQPChannel $channel, string $callbackQueue, Game $game, Player $player) : string
    {
        $requestData = $this->requestBuilder->create(
            $player,
            $game,
            PlayerRequestInterface::DEFAULT_VIEW_RANGE,
            true
        );

        $eventData = MovementRequest::createEvent($game, $player, $requestData);

        $message = new AMQPMessage($eventData->serialize(), [
            'reply_to'       => $callbackQueue,
            'correlation_id' => $eventData->uuid(),
            'expiration'     => sprintf("%d", 1000 * $this->timeout)
        ]);

        $channel->basic_publish($message, self::X_PLAYER_MOVEMENT_REQUEST);

        return $eventData->uuid();
    }

    /**
     * Reads the responses from RabbitMQ callback queue
     *
     * @param AMQPChannel $channel
     * @param string $callbackQueue
     * @param array $published
     * @return array
     */
    private function readResponses(AMQPChannel $channel, string $callbackQueue, array $published) : array
    {
        $remaining = count($published);
        $responses = array_fill_keys($published, null);

        $channel->basic_consume(
            $callbackQueue,
            '',
            false,
            true,
            false,
            false,
            function (AMQPMessage $message) use (&$responses, &$remaining) {
                $uuid = $message->get('correlation_id');
                if (array_key_exists($uuid, $responses)
                    && null === $responses[$uuid]) {
                    $responses[$uuid] = $message->body;
                    --$remaining;
                }
            }
        );

        while ($remaining > 0) {
            try {
                $channel->wait(null, false, 1 + $this->timeout);
            } catch (AMQPTimeoutException $exc) {
                // End process when timeout
                $remaining = 0;
            }
        }

        return $responses;
    }
}
