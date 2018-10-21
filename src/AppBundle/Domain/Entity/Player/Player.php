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
    /** @var int player status */
    const STATUS_REGULAR = 1;
    const STATUS_POWERED = 2;
    const STATUS_RELOADING = 4;
    const STATUS_KILLED = 8;

    /** @var int default values */
    const DEFAULT_STATUS_COUNT = 6;

    /** @var int the current status of the player: regular, powered, reloading, killed */
    protected $status;

    /** @var int the number of moves to change back the status to regular */
    protected $statusCount;

    /** @var int the current score of the player */
    protected $score;

    /** @var \DateTime the timestamp of the last movement */
    protected $timestamp;

    /** @var string the uuid of the player */
    protected $uuid;

    /** @var string the name of the player */
    protected $name;

    /** @var string the email of the player */
    protected $email;

    /** @var string the URL of the API to move the player */
    protected $url;

    /**
     * Player constructor.
     *
     * @param string $url
     * @param Position $position
     * @param Position $previous
     * @throws \Exception
     */
    public function __construct(
        string $url,
        Position $position,
        Position $previous = null
    ) {
        parent::__construct($position, $previous);
        $this->url = $url;
        $this->status = static::STATUS_REGULAR;
        $this->statusCount = 0;
        $this->score = 0;
        $this->timestamp = new \DateTime();
        $this->uuid = Uuid::uuid4()->toString();
        $this->name = $this->uuid;
        $this->email = null;
    }

    /**
     * Get current status of the player: regular, powered, reloading, killed
     *
     * @return int
     */
    public function status() : int
    {
        return $this->status;
    }

    /**
     * Get the number of moves to change back the status to regular
     *
     * @return int
     */
    public function statusCount() : int
    {
        return $this->statusCount;
    }

    /**
     * Get the current score of the player
     *
     * @return int
     */
    public function score() : int
    {
        return $this->score;
    }

    /**
     * Get current timestamp
     *
     * @return \DateTime
     */
    public function timestamp() : \DateTime
    {
        return $this->timestamp;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function uuid() : string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function name() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function email() : string
    {
        return $this->email;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function url() : string
    {
        return $this->url;
    }

    /**
     * Sets the name and the email of the player
     *
     * @param string $name
     * @param string $email
     * @return $this
     */
    public function setPlayerIds(string $name, string $email) : Player
    {
        $this->name = $name;
        $this->email = $email;
        return $this;
    }

    /**
     * Get if player is powered
     *
     * @return bool
     */
    public function isPowered() : bool
    {
        return static::STATUS_POWERED == $this->status;
    }

    /**
     * Get if the player is reloading
     *
     * @return bool
     */
    public function isReloading() : bool
    {
        return static::STATUS_RELOADING == $this->status;
    }

    /**
     * Get if the player was killed
     *
     * @return bool
     */
    public function isKilled() : bool
    {
        return static::STATUS_KILLED == $this->status;
    }

    /**
     * Change the player status to powered
     *
     * @param int $countMoves
     * @return $this
     */
    public function powered(int $countMoves = null) : Player
    {
        $this->status = static::STATUS_POWERED;
        $this->statusCount = $countMoves ?? static::DEFAULT_STATUS_COUNT;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * The player fires, change the status to reloading
     *
     * @param int $countMoves
     * @return $this
     */
    public function fire(int $countMoves = null) : Player
    {
        $this->status = static::STATUS_RELOADING;
        $this->statusCount = $countMoves ?? static::DEFAULT_STATUS_COUNT;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * The player has been killed
     *
     * @param int $countMoves
     * @return $this
     */
    public function killed(int $countMoves = null) : Player
    {
        $this->status = static::STATUS_KILLED;
        $this->statusCount = $countMoves ?? static::DEFAULT_STATUS_COUNT;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * Increases the score of the player
     *
     * @param int $score
     * @return $this
     */
    public function addScore(int $score) : Player
    {
        $this->score += $score;
        return $this;
    }

    /**
     * Reset the status of the player
     *
     * @return $this
     */
    public function resetStatus()
    {
        $this->status = static::STATUS_REGULAR;
        $this->statusCount = 0;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * Reset the score of the player
     *
     * @return $this
     */
    public function resetScore()
    {
        $this->score = 0;
        return $this;
    }

    /**
     * Reset all the game data for this player
     *
     * @param Position $pos
     * @return $this
     */
    public function resetAll(Position $pos)
    {
        $this->position = clone $pos;
        $this->previous = clone $pos;
        return $this
            ->resetStatus()
            ->resetScore();
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
            'status_count' => $this->statusCount(),
            'score' => $this->score(),
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
        $url = $data['url'];
        $position = $data['position'];
        $previous = $data['previous'] ?? null;

        $player = new static(
            $url,
            Position::unserialize($position),
            $previous ? Position::unserialize($previous) : null
        );

        $status = $data['status'] ?? null;
        if (null !== $status) {
            $player->status = $status;
        }

        $statusCount = $data['status_count'] ?? null;
        if (null !== $statusCount) {
            $player->statusCount = $statusCount;
        }

        $score = $data['score'] ?? null;
        if (null !== $score) {
            $player->score = $score;
        }

        $timestamp = $data['timestamp'] ?? null;
        if (null !== $timestamp) {
            $player->timestamp = \DateTime::createFromFormat('YmdHisu', $timestamp);
        }

        $uuid = $data['uuid'] ?? null;
        if (null !== $uuid) {
            $player->uuid = $uuid;
        }

        $name = $data['name'] ?? null;
        if (null !== $name) {
            $player->name = $name;
        }

        $email = $data['email'] ?? null;
        if (null !== $email) {
            $player->email = $email;
        }

        return $player;
    }
}
