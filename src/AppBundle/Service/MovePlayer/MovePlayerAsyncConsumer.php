<?php

namespace AppBundle\Service\MovePlayer;

use AppBundle\Domain\Event\MovementRequest;
use AppBundle\Domain\Service\MovePlayer\AskNextMovementInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class MovePlayerAsyncConsumer
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MovePlayerAsyncConsumer
{
    /** @var AMQPStreamConnection */
    private $rabbitmq;

    /** @var AskNextMovementInterface */
    private $askNextMovementService;

    /** @var string AMQP resource names */
    private const Q_PLAYER_MOVEMENT_REQUEST = MoveAllPlayersAsyncService::Q_PLAYER_MOVEMENT_REQUEST;

    /**
     * MovePlayerAsyncConsumer constructor
     *
     * @param AMQPStreamConnection $rabbitmq
     * @param AskNextMovementInterface $askNextMovementService
     */
    public function __construct(AMQPStreamConnection $rabbitmq, AskNextMovementInterface $askNextMovementService)
    {
        $this->rabbitmq = $rabbitmq;
        $this->askNextMovementService = $askNextMovementService;
    }

    public function consume()
    {
        // Open channel to RabbitMQ
        $channel = $this->rabbitmq->channel();

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
            function (AMQPMessage $reqMessage) {

                $requestRawData = $reqMessage->getBody();

                $requestEvent = MovementRequest::readEvent($requestRawData);

                $apiRequest = json_encode($requestEvent->request());

                $move = $this->askNextMovementService->askNextMovement(
                    $requestEvent->url(),
                    $requestEvent->player(),
                    $requestEvent->game(),
                    $apiRequest
                );

                $responseMsg = new AMQPMessage($move, [
                    'correlation_id' => $reqMessage->get('correlation_id')
                ]);

                /** @var AMQPChannel $channel */
                $channel = $reqMessage->delivery_info['channel'];

                $channel->basic_publish(
                    $responseMsg,
                    '',
                    $reqMessage->get('reply_to')
                );

                $channel->basic_ack(
                    $reqMessage->delivery_info['delivery_tag']
                );
            }
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        // Close connections
        $channel->close();
    }
}