<?php

namespace AppBundle\Domain\Event;

use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use Ramsey\Uuid\Uuid;

/**
 * Event to get the response for a movement from a player
 *
 * @package AppBundle\Domain\Event
 */
class MovementResponse implements EventBase
{
    /** @var string the UUID of the event */
    private $uuid;

    /** @var \DateTime the timestamp of the event */
    private $timestamp;

    /** @var string the UUID of the game */
    private $game;

    /** @var string the UUID of the player */
    private $player;

    /** @var string the UUID of the request */
    private $request;

    /** @var string the response from the API */
    private $response;

    /**
     * Empty constructor
     */
    private function __construct()
    {
    }

    /**
     * Creates a new MovementResponse event from a MovementRequest event
     *
     * @param MovementRequest $request
     * @param string $response
     * @return MovementResponse
     * @throws MovePlayerException
     */
    public static function createEvent(MovementRequest $request, string $response) : MovementResponse
    {
        $event = new MovementResponse();
        $event->uuid = $event->genetateUuid();
        $event->timestamp = new \DateTime();
        $event->game = $request->game();
        $event->player = $request->player();
        $event->request = $request->uuid();
        $event->response = $response;
        return $event;
    }

    /**
     * Creates a new event when an error occurred
     *
     * @param string $uuid
     * @return MovementResponse
     * @throws MovePlayerException
     */
    public static function createErrorEvent(string $uuid) : MovementResponse
    {
        $event = new MovementResponse();
        $event->uuid = $event->genetateUuid();
        $event->timestamp = new \DateTime();
        $event->game = null;
        $event->player = null;
        $event->request = $uuid;
        $event->response = 'error';
        return $event;
    }

    /**
     * Reads an event
     *
     * @param string $data
     * @return MovementResponse
     * @throws MovePlayerException
     */
    public static function readEvent(string $data) : MovementResponse
    {
        $event = new MovementResponse();
        $event->unserialize($data);
        return $event;
    }

    /**
     * Serializes the event into a string
     *
     * @return string
     */
    public function serialize(): string
    {
        return json_encode([
            'type'      => $this->type(),
            'uuid'      => $this->uuid(),
            'timestamp' => $this->timestamp()->format('YmdHisu'),
            'game'      => $this->game(),
            'player'    => $this->player(),
            'request'   => $this->request(),
            'response'  => $this->response(),
        ]);
    }

    /**
     * Unserializes the event from a string
     *
     * @param string $event
     * @throws MovePlayerException
     */
    public function unserialize(string $event): void
    {
        $data = json_decode($event, true);

        $type = $data['type'] ?? null;
        if ($this->type() != $type) {
            throw new MovePlayerException(
                'Invalid event type: ' . $type . ' - Expected: ' . $this->type()
            );
        }

        $this->uuid = $data['uuid'];
        $this->timestamp = \DateTime::createFromFormat('YmdHisu', $data['timestamp']);
        $this->game = $data['game'];
        $this->player = $data['player'];
        $this->request = $data['request'];
        $this->response = $data['response'];
    }

    /**
     * Returns the type of the event
     *
     * @return string
     */
    public function type(): string
    {
        return self::class;
    }

    /**
     * Returns the UUID of the event
     *
     * @return string
     */
    public function uuid(): string
    {
        return $this->uuid;
    }

    /**
     * Returns the timestamp of the event
     *
     * @return \DateTime
     */
    public function timestamp(): \DateTime
    {
        return $this->timestamp;
    }

    /**
     * Returns the UUID of the game
     *
     * @return string
     */
    public function game(): string
    {
        return $this->game;
    }

    /**
     * Returns the UUID of the player
     *
     * @return string
     */
    public function player(): string
    {
        return $this->player;
    }

    /**
     * Returns the UUID of the request
     *
     * @return string
     */
    public function request(): string
    {
        return $this->request;
    }

    /**
     * Returns the response of the API
     *
     * @return string
     */
    public function response(): string
    {
        return $this->response;
    }

    /**
     * Generates a new UUID
     *
     * @return string
     * @throws MovePlayerException
     */
    private function genetateUuid()
    {
        try {
            return Uuid::uuid4()->toString();
        } catch (\Exception $exception) {
            throw new MovePlayerException(
                'An error occured creating the UUID for the event!'
            );
        }
    }
}
