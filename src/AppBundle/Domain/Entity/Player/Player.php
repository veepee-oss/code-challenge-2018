<?php

namespace AppBundle\Domain\Entity\Player;

use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Position\Position;
use Ramsey\Uuid\Uuid;

/**
 * Domain Entity: Player
 *
 * @package AppBundle\Domain\Entity\Player
 */
class Player extends MazeObject
{
    /** @var int */
    protected $status;

    /** @var \DateTime */
    protected $timestamp;

    /** @var string */
    protected $uuid;

    /** @var string */
    protected $name;

    /** @var string */
    protected $email;

    /** @var string */
    protected $url;

    /** Player statuses */
    const STATUS_PLAYING = 1;
    const STATUS_DIED = 8;
    const STATUS_WINNER = 12;

    /**
     * Player constructor.
     *
     * @param string $url
     * @param Position $position
     * @param Position $previous
     * @param int $status
     * @param \DateTime $timestamp
     * @param string $uuid
     * @param string $name
     * @param string $email
     * @throws \Exception
     */
    public function __construct(
        $url,
        Position $position,
        Position $previous = null,
        $status = null,
        \DateTime $timestamp = null,
        $uuid = null,
        $name = null,
        $email = null
    ) {
        parent::__construct($position, $previous);
        $this->url = $url;
        $this->status = $status ?: static::STATUS_PLAYING;
        $this->timestamp = $timestamp ?: new \DateTime();
        $this->uuid = $uuid ?: Uuid::uuid4()->toString();
        $this->name = $name ?: $this->uuid;
        $this->email = $email;
    }

    /**
     * Get current status
     *
     * @return int
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Get current timestamp
     *
     * @return \DateTime
     */
    public function timestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function uuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function email()
    {
        return $this->email;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * Sets the name of the player
     *
     * @param string $name
     * @param string $email
     * @return $this
     */
    public function setPlayerIds($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
        return $this;
    }

    /**
     * Get if player is alive (and not winner)
     *
     * @return bool
     */
    public function alive()
    {
        return static::STATUS_PLAYING == $this->status;
    }

    /**
     * The player wins the game
     *
     * @return $this
     */
    public function wins()
    {
        $this->status = static::STATUS_WINNER;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * Get if the player won the game
     *
     * @return bool
     */
    public function winner()
    {
        return static::STATUS_WINNER == $this->status;
    }

    /**
     * The player dies
     *
     * @return $this
     */
    public function dies()
    {
        $this->status = static::STATUS_DIED;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * Get if the player died
     *
     * @return bool
     */
    public function dead()
    {
        return static::STATUS_DIED == $this->status;
    }

    /**
     * Reset the game for this player
     *
     * @param Position $pos
     * @return $this
     */
    public function reset(Position $pos)
    {
        $this->status = static::STATUS_PLAYING;
        $this->timestamp = new \DateTime();
        $this->position = clone $pos;
        $this->previous = clone $pos;
        return $this;
    }

    /**
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return array(
            'position' => $this->position()->serialize(),
            'previous' => $this->previous()->serialize(),
            'status' => $this->status(),
            'timestamp' => $this->timestamp()->format('YmdHisu'),
            'uuid' => $this->uuid(),
            'name' => $this->name(),
            'email' => $this->email(),
            'url' => $this->url(),
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return Player
     * @throws \Exception
     */
    public static function unserialize(array $data)
    {
        return new static(
            $data['url'],
            Position::unserialize($data['position']),
            isset($data['previous']) ? Position::unserialize($data['previous']) : null,
            isset($data['status']) ? $data['status'] : null,
            isset($data['timestamp']) ? \DateTime::createFromFormat('YmdHisu', $data['timestamp']) : null,
            isset($data['uuid']) ? $data['uuid'] : null,
            isset($data['name']) ? $data['name'] : null,
            isset($data['email']) ? $data['email'] : null
        );
    }
}
