<?php

namespace AppBundle\Domain\Entity\Player;

use AppBundle\Domain\Entity\Fire\Fire;
use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Position\Direction;
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
    const DEFAULT_FIRE_RANGE = Fire::DEFAULT_FIRE_RANGE;
    const DEFAULT_POWERED_TURNS = 3;
    const DEFAULT_RELOAD_TURNS = 4;
    const DEFAULT_KILLED_TURNS = 5;

    /** @var int the current status of the player: regular, powered, reloading, killed */
    protected $status;

    /** @var int the number of moves to change back the status to regular */
    protected $statusCount;

    /** @var string the firing direction or null */
    protected $firingDir;

    /** @var int the current fire range (positions) */
    protected $fireRange;

    /** @var int the current score of the player */
    protected $score;

    /** @var \DateTime the timestamp of the last score change */
    protected $timestamp;

    /** @var string the uuid of the player */
    protected $uuid;

    /** @var string the name of the player */
    protected $name;

    /** @var string the email of the player */
    protected $email;

    /** @var string the URL of the API to move the player */
    protected $url;

    /** @var bool If the player respawned this turn */
    protected $respawned;

    /**
     * Player constructor.
     *
     * @param string        $url
     * @param Position      $position
     * @param Position|null $previous
     * @throws \Exception
     */
    public function __construct(
        string $url,
        Position $position,
        Position $previous = null
    ) {
        parent::__construct($position, $previous);

        $this->uuid = Uuid::uuid4()->toString();
        $this->name = $this->uuid;
        $this->email = null;
        $this->url = $url;
        $this->respawned = false;

        $this->resetStatus();
        $this->resetScore();
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
     * Get the firing direction or nul
     *
     * @return null|string
     */
    public function firingDir() : ?string
    {
        return $this->firingDir;
    }

    /**
     * Get the current fire range (positions)
     *
     * @return int
     */
    public function fireRange() : int
    {
        return $this->fireRange;
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
     * Get the timestamp of the last score change
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
     * @return string|null
     */
    public function email() : ?string
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
     * Get if the player respawned this turn
     *
     * @return bool
     */
    public function isRespawned() : bool
    {
        return $this->respawned;
    }

    /**
     * Get current direction - where is the starship facing? (left, right, up, down)
     *
     * If it's firing, the name is facing in the shot direction. If not it depends on the las movement.
     *
     * @return string|null
     */
    public function direction(): ?string
    {
        if ($this->isFiring()) {
            return Fire::direction($this->firingDir);
        }

        return parent::direction();
    }

    /**
     * Sets the name and the email of the player
     *
     * @param string      $name
     * @param string|null $email
     * @return $this
     */
    public function setPlayerIds(string $name, ?string $email) : Player
    {
        $this->name = $name;
        $this->email = $email;
        return $this;
    }

    /**
     * Sets the URL of the player
     *
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url) : Player
    {
        $this->url = $url;
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
     * Get if the player is firing
     *
     * @return bool
     */
    public function isFiring() : bool
    {
        return Fire::firing($this->firingDir);
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
     * @param int|null $countMoves
     * @return $this
     */
    public function powered(int $countMoves = null) : Player
    {
        $this->status = static::STATUS_POWERED;
        $this->statusCount = $countMoves ?? static::DEFAULT_POWERED_TURNS;
        return $this;
    }

    /**
     * The player fires, change the status to reloading
     *
     * @param string   $firingDir
     * @param int|null $reloadMoves
     * @return $this
     */
    public function fire(string $firingDir, int $reloadMoves = null) : Player
    {
        if (Fire::firing($firingDir)) {
            $this->status = static::STATUS_RELOADING;
            $this->firingDir = $firingDir;
            $this->fireRange = self::DEFAULT_FIRE_RANGE;
            $this->statusCount = $reloadMoves ?? static::DEFAULT_RELOAD_TURNS;
        }
        return $this;
    }

    /**
     * The player has been killed
     *
     * @param int|null $countMoves
     * @return $this
     */
    public function killed(int $countMoves = null) : Player
    {
        $this->status = static::STATUS_KILLED;
        $this->statusCount = $countMoves ?? static::DEFAULT_KILLED_TURNS;
        return $this;
    }

    /**
     * @param Position $position
     * @return MazeObject
     */
    public function move(Position $position) : MazeObject
    {
        parent::move($position);
        if ($this->statusCount() > 0) {
            --$this->statusCount;
            if (0 == $this->statusCount()) {
                if ($this->isKilled()) {
                    $this->status = static::STATUS_RELOADING;
                    $this->statusCount = 1;
                    $this->respawned = true;
                } elseif ($this->isPowered()
                    || $this->isReloading()) {
                    $this->status = static::STATUS_REGULAR;
                }
            }
        }
        return $this;
    }

    /**
     * Increases the score of the player
     *
     * @param int $score
     * @return $this
     * @throws \Exception
     */
    public function addScore(int $score) : Player
    {
        $this->score += $score;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * Return the fire direction if the position is compromised
     *
     * @param Position $pos
     * @return null|string
     */
    public function fireDirAtPosition(Position $pos) : ?string
    {
        if (!$this->isFiring()) {
            return null;
        }

        $startPos = clone $this->position();
        $endPos = clone $this->position();

        $dir = Fire::direction($this->firingDir());
        for ($i = 0; $i < $this->fireRange(); $i++) {
            $endPos->moveTo($dir);
        }

        if ($dir == Direction::LEFT || $dir == Direction::UP) {
            $tempPos = $startPos;
            $startPos = $endPos;
            $endPos = $tempPos;
        }

        if ($startPos->y() <= $pos->y() &&
            $startPos->x() <= $pos->x() &&
            $pos->y() <= $endPos->y() &&
            $pos->x() <= $endPos->x()) {
            return $dir;
        }

        return null;
    }

    /**
     * Set the new fire range
     *
     * @param int $range
     * @return Player
     */
    public function setFireRange(int $range) : Player
    {
        $this->fireRange = $range;
        return $this;
    }

    /**
     * Reset firing dir
     *
     * @return $this
     */
    public function resetFiringDir()
    {
        $this->firingDir = Fire::NONE;
        $this->fireRange = 0;
        return $this;
    }

    /**
     * Reset the status of the player
     *
     * @return $this
     */
    public function resetStatus()
    {
        $this->resetFiringDir();
        $this->status = static::STATUS_REGULAR;
        $this->statusCount = 0;
        return $this;
    }

    /**
     * Reset the score of the player
     *
     * @return $this
     * @throws \Exception
     */
    public function resetScore()
    {
        $this->score = 0;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * Reset all the game data for this player
     *
     * @param Position $pos
     * @return $this
     * @throws \Exception
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
            'firing_dir' => $this->firingDir(),
            'fire_range' => $this->fireRange(),
            'score' => $this->score(),
            'timestamp' => $this->timestamp()->format('YmdHisu'),
            'uuid' => $this->uuid(),
            'name' => $this->name(),
            'email' => $this->email(),
            'url' => $this->url()
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

        $firingDir = $data['firing_dir'] ?? null;
        if (null !== $firingDir) {
            $player->firingDir = $firingDir;
        }

        $fireRange = $data['fire_range'] ?? null;
        if (null !== $fireRange) {
            $player->fireRange = $fireRange;
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
