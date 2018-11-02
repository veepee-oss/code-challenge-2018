<?php

namespace AppBundle\Service\MovePlayer;

use AppBundle\Domain\Event\MovementRequest;
use AppBundle\Domain\Event\MovementResponse;
use AppBundle\Domain\Service\MovePlayer\AskNextMovementInterface;
use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Service to consume messages from the movement queue and make the API calls to the players
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MovePlayerAsyncConsumer
{
    /** @var AMQPStreamConnection */
    private $rabbitmq;

    /** @var AskNextMovementInterface */
    private $askNextMovementService;

    /** @var LoggerInterface */
    private $logger;

    /** @var string AMQP resource names */
    private const Q_PLAYER_MOVEMENT_REQUEST = MoveAllPlayersAsyncService::Q_PLAYER_MOVEMENT_REQUEST;

    /** @var int max iddle time = 15 min * 60 sec = 900 */
    const MAX_IDLE = 900;

    /**
     * MovePlayerAsyncConsumer constructor
     *
     * @param AMQPStreamConnection $rabbitmq
     * @param AskNextMovementInterface $askNextMovementService
     * @param LoggerInterface $logger
     */
    public function __construct(
        AMQPStreamConnection $rabbitmq,
        AskNextMovementInterface $askNextMovementService,
        LoggerInterface $logger
    ) {
        $this->rabbitmq = $rabbitmq;
        $this->askNextMovementService = $askNextMovementService;
        $this->logger = $logger;
    }

    /**
     * Consumes messages from the queue
     *
     * @return void
     */
    public function consume() : void
    {
        $this->logger->debug(
            'MovePlayerAsyncConsumer - Starting consumer for queue: ' . self::Q_PLAYER_MOVEMENT_REQUEST
        );

        // Open channel to RabbitMQ
        $channel = $this->rabbitmq->channel();
        $running = true;

        try {
            // Set channel options
            $channel->basic_qos(null, 1, null);

            // Prepare consuming
            $channel->basic_consume(
                self::Q_PLAYER_MOVEMENT_REQUEST,
                '',
                false,
                false,
                false,
                false,
                [ $this, 'onMessageReceived' ]
            );
        } catch (\Exception $exc) {
            $this->logger->error(
                'MovePlayerAsyncConsumer - AMQP error consuming messages: ' . $exc->getMessage()
            );
            $running = false;
        }

        // Consume messages loop
        while ($running && count($channel->callbacks)) {
            try {
                $channel->wait(null, false, self::MAX_IDLE);
            } catch (AMQPTimeoutException $exc) {
                // End process when timeout
                $running = false;
            } catch (\Exception $exc) {
                // End process when any other error
                $running = false;
            }
        }

        // Close connections
        $channel->close();
    }

    /**
     * Callback function called when a message is consumed from the queue
     *
     * @param AMQPMessage $requestMsg
     * @throws MovePlayerException
     */
    public function onMessageReceived(AMQPMessage $requestMsg) : void
    {
        // Read mesage body with the request data
        $requestRawData = $requestMsg->getBody();
        $correlationId = $requestMsg->get('correlation_id');
        $responseQueue = $requestMsg->get('reply_to');

        /** @var AMQPChannel $responseChannel */
        $responseChannel = $requestMsg->delivery_info['channel'];

        $this->logger->debug(
            'MovePlayerAsyncConsumer - Received request event - ' . $requestRawData
        );

        // Create the event with the request data
        $requestEvent = null;
        try {
            $requestEvent = MovementRequest::readEvent($requestRawData);
        } catch (MovePlayerException $exc) {
            $errorResponseEvent = MovementResponse::createErrorEvent($correlationId);
            $this->publishResponse($responseChannel, $responseQueue, $correlationId, $errorResponseEvent);
        }

        // Ask the player for the next movement (call the API)
        $responseEvent = null;
        if (null !== $requestEvent) {
            $apiRequest = json_encode($requestEvent->request());

            try {
                $move = $this->askNextMovementService->askNextMovement(
                    $requestEvent->url(),
                    $requestEvent->player(),
                    $requestEvent->game(),
                    $apiRequest
                );

                $responseEvent = MovementResponse::createEvent($requestEvent, $move);
            } catch (MovePlayerException $exc) {
                $errorResponseEvent = MovementResponse::createErrorEvent($correlationId);
                $this->publishResponse($responseChannel, $responseQueue, $correlationId, $errorResponseEvent);
            }
        }

        if (null !== $responseEvent) {
            $this->publishResponse($responseChannel, $responseQueue, $correlationId, $responseEvent);
        }

        $responseChannel->basic_ack(
            $requestMsg->delivery_info['delivery_tag']
        );
    }

    /**
     * Publishes a response in the callback queue
     *
     * @param AMQPChannel $channel
     * @param string $queue
     * @param string $correlationId
     * @param MovementResponse $responseEvent
     * @return bool true when success
     */
    private function publishResponse(
        AMQPChannel $channel,
        string $queue,
        string $correlationId,
        MovementResponse $responseEvent
    ) : bool {
        try {
            $responseRawData = $responseEvent->serialize();

            $this->logger->debug(
                'MovePlayerAsyncConsumer - Publishing response event with payload: ' . $responseRawData
            );

            $responseMsg = new AMQPMessage($responseRawData, [
                'correlation_id' => $correlationId
            ]);
            $channel->basic_publish($responseMsg, '', $queue);
        } catch (\Exception $exc) {
            return false;
        }

        return true;
    }
}
