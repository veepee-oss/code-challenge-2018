<?php

namespace AppBundle\Domain\Event;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use Ramsey\Uuid\Uuid;

/**
 * Event to send a movement request for a player
 *
 * @package AppBundle\Domain\Event
 */
class MovementRequest implements EventBase
{
    /** @var string the UUID of the event */
    private $uuid;

    /** @var \DateTime the timestamp of the event */
    private $timestamp;

    /** @var string the UUID of the game */
    private $game;

    /** @var string the UUID of the player */
    private $player;

    /** @var string the URL of the API to call */
    private $url;

    /** @var array the request data to send to the API */
    private $request;

    /**
     * Empty constructor
     */
    private function __construct()
    {
    }

    /**
     * Creates a new event
     *
     * @param Game $game
     * @param Player $player
     * @param array $request
     * @return MovementRequest
     * @throws MovePlayerException
     */
    public static function createEvent(Game $game, Player $player, array $request) : MovementRequest
    {
        $event = new MovementRequest();
        try {
            $event->uuid = Uuid::uuid4()->toString();
        } catch (\Exception $exception) {
            throw new MovePlayerException(
                'An error occured creating the UUID for the event!'
            );
        }

        $event->timestamp = new \DateTime();
        $event->game = $game->uuid();
        $event->player = $player->uuid();
        $event->url = $player->url();
        $event->request = $request;
        return $event;
    }

    /**
     * Reads an event
     *
     * @param string $data
     * @return MovementRequest
     * @throws MovePlayerException
     */
    public static function readEvent(string $data) : MovementRequest
    {
        $event = new MovementRequest();
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
            'url'       => $this->url(),
            'request'   => $this->request(),
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

        $type = $data['type'];
        if ($this->type() != $type) {
            throw new MovePlayerException(
                'Invalid event type: ' . $type . ' - Expected: ' . $this->type()
            );
        }

        $this->uuid = $data['uuid'];
        $this->timestamp = \DateTime::createFromFormat('YmdHisu', $data['timestamp']);
        $this->game = $data['game'];
        $this->player = $data['player'];
        $this->url = $data['url'];
        $this->request = $data['request'];
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
     * Returns the URL to call
     *
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * Returns the request data to send to the API
     *
     * @return array
     */
    public function request(): array
    {
        return $this->request;
    }
}
