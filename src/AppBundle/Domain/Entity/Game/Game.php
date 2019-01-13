<?php

namespace AppBundle\Domain\Entity\Game;

use AppBundle\Domain\Entity\Ghost\Ghost;
use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Position;
use Ramsey\Uuid\Uuid;

/**
 * Domain entity: Game
 *
 * @package AppBundle\Domain\Entity\Game
 */
class Game
{
    /** @var int the constants form the game statuses */
    const STATUS_NOT_STARTED = 0;
    const STATUS_RUNNING = 1;
    const STATUS_PAUSED = 8;
    const STATUS_FINISHED = 16;

    /** @var int the default values for a game */
    const DEFAULT_MAZE_HEIGHT = 15;
    const DEFAULT_MAZE_WIDTH  = 30;
    const DEFAULT_MIN_GHOSTS  = 10;
    const DEFAULT_GHOST_RATE  = 10;
    const DEFAULT_MOVES_LIMIT = 100;

    /** @var int The default view range and fire range */
    const DEFAULT_VIEW_RANGE = 4;

    /** @var Maze the maze */
    protected $maze;

    /** @var Player[] the players array */
    protected $players;

    /** @var Ghost[] the active ghosts */
    protected $ghosts;

    /** @var Ghost[] the recently killed ghosts  */
    protected $killedGhosts;

    /** @var int the frequency of new ghosts */
    protected $ghostRate;

    /** @var int the minimum ghosts any time */
    protected $minGhosts;

    /** @var int the status of the game: playing, paused, ... */
    protected $status;

    /** @var int the number of moves done */
    protected $moves;

    /** @var int the limit of movements to do */
    protected $limit;

    /** @var string the UUID of the game */
    protected $uuid;

    /** @var string the name of the game (optional) */
    protected $name;

    /**
     * Game constructor.
     *
     * @param Maze $maze
     * @param Player[] $players
     * @param Ghost[] $ghosts
     * @param Ghost[] $killedGhosts
     * @param int $ghostRate
     * @param int $minGhosts
     * @param int $status
     * @param int $moves
     * @param int $limit
     * @param string $uuid
     * @param string $name
     * @throws \Exception
     */
    public function __construct(
        Maze $maze,
        array $players,
        array $ghosts,
        array $killedGhosts = [],
        $ghostRate = 0,
        $minGhosts = 0,
        $status = self::STATUS_NOT_STARTED,
        $moves = 0,
        $limit = self::DEFAULT_MOVES_LIMIT,
        $uuid = null,
        $name = null
    ) {
        $this->maze = $maze;
        $this->players = $players;
        $this->ghosts = $ghosts;
        $this->killedGhosts = $killedGhosts;
        $this->ghostRate = $ghostRate;
        $this->minGhosts = $minGhosts;
        $this->status = $status;
        $this->moves = $moves;
        $this->limit = $limit;
        $this->uuid = $uuid ?: Uuid::uuid4()->toString();
        $this->name = $name ?: $this->uuid;
    }

    /**
     * Get the maze
     *
     * @return Maze
     */
    public function maze()
    {
        return $this->maze;
    }

    /**
     * Get the players array
     *
     * @return Player[]
     */
    public function players()
    {
        return $this->players;
    }

    /**
     * Get the active ghosts array
     *
     * @return Ghost[]
     */
    public function ghosts()
    {
        return $this->ghosts;
    }

    /**
     * Get The the recently killed ghosts
     *
     * @return Ghost[]
     */
    public function killedGhosts(): array
    {
        return $this->killedGhosts;
    }

    /**
     * Get the frequency of new ghosts
     *
     * @return int
     */
    public function ghostRate()
    {
        return $this->ghostRate;
    }

    /**
     * Get the minimum ghosts any time
     *
     * @return int
     */
    public function minGhosts()
    {
        return $this->minGhosts;
    }

    /**
     * Get the status of the game: playing, paused, ...
     *
     * @return int
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Get the number of moves done
     *
     * @return int
     */
    public function moves()
    {
        return $this->moves;
    }

    /**
     * Get the limit of movements to do
     *
     * @return int
     */
    public function limit()
    {
        return $this->limit;
    }

    /**
     * Get the UUID of the game
     *
     * @return string
     */
    public function uuid()
    {
        return $this->uuid;
    }

    /**
     * Get the name of the game
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Returns if the game is started
     *
     * @return bool
     */
    public function started()
    {
        return static::STATUS_RUNNING == $this->status
            || static::STATUS_PAUSED == $this->status;
    }

    /**
     * Returns if the game is playing
     *
     * @return bool
     */
    public function playing()
    {
        return static::STATUS_RUNNING == $this->status;
    }

    /**
     * Returns if the game is paused
     *
     * @return bool
     */
    public function paused()
    {
        return static::STATUS_PAUSED == $this->status;
    }

    /**
     * Returns if the game is finished
     *
     * @return bool
     */
    public function finished()
    {
        return static::STATUS_FINISHED == $this->status;
    }

    /**
     * Starts playing the game
     *
     * @return $this
     */
    public function startPlaying()
    {
        $this->status = static::STATUS_RUNNING;
        return $this;
    }

    /**
     * Stops playing the game
     *
     * @return $this
     */
    public function stopPlaying()
    {
        if ($this->status == static::STATUS_RUNNING) {
            $this->status = static::STATUS_PAUSED;
        }
        return $this;
    }

    /**
     * Ends playing the game
     *
     * @return $this
     */
    public function endGame()
    {
        $this->status = static::STATUS_FINISHED;
        return $this;
    }

    /**
     * Resets the game to its initial position
     *
     * @return $this
     */
    public function resetPlaying()
    {
        $this->moves = 0;
        $this->ghosts = array();
        $this->resetKilledGhosts();
        $this->status = static::STATUS_NOT_STARTED;
        foreach ($this->players as $player) {
            $player->resetAll($this->maze->createStartPosition());
        }
        return $this;
    }

    /**
     * Removes all the killed ghosts
     *
     * @return $this
     */
    public function resetKilledGhosts()
    {
        $this->killedGhosts = [];
        return $this;
    }

    /**
     * Get the height of the maze
     *
     * @return int
     */
    public function height()
    {
        return $this->maze()->height();
    }

    /**
     * Get the width of the maze
     *
     * @return int
     */
    public function width()
    {
        return $this->maze()->width();
    }

    /**
     * Get the last update date
     *
     * @return \DateTime
     */
    public function lastUpdatedAt()
    {
        $datetime = null;
        foreach ($this->players as $player) {
            $timestamp = $player->timestamp();
            if (null === $datetime || $timestamp > $datetime) {
                $datetime = $timestamp;
            }
        }
        return $datetime;
    }

    /**
     * Increments the moves counter
     *
     * @return $this
     */
    public function incMoves()
    {
        $this->moves++;
        if ($this->moves >= $this->limit) {
            $this->endGame();
        }
        return $this;
    }

    /**
     * Adds a ghost
     *
     * @param Ghost $ghost
     * @return $this
     */
    public function addGhost(Ghost $ghost)
    {
        $this->ghosts[] = clone $ghost;
        return $this;
    }

    /**
     * Removes a ghost
     *
     * @param Ghost $ghost
     * @return $this
     */
    public function removeGhost(Ghost $ghost)
    {
        foreach ($this->ghosts as $key => $item) {
            if ($ghost == $item) {
                $this->killedGhosts[] = $ghost;
                unset($this->ghosts[$key]);
                break;
            }
        }
        return $this;
    }

    /**
     * Finds the ghosts at a position in the maze
     *
     * @param Position $pos
     * @return Ghost[]
     */
    public function ghostsAtPosition(Position $pos)
    {
        $result = [];
        foreach ($this->ghosts as $ghost) {
            if ($ghost->position()->equals($pos)) {
                $result[] = $ghost;
            }
        }
        return $result;
    }

    /**
     * Finds the players at a position in the maze
     *
     * @param Position $pos
     * @return Player[]
     */
    public function playersAtPosition(Position $pos)
    {
        $result = [];
        foreach ($this->players as $player) {
            if ($player->position()->equals($pos)) {
                $result[] = $player;
            }
        }
        return $result;
    }

    /**
     * Get the current classification (based on playes score)
     *
     * @return Player[]
     */
    public function classification()
    {
        $playersCopy = $this->players;
        usort($playersCopy, function (Player $p1, Player $p2) {
            // Order by score in the first time
            $condition = $p2->score() <=> $p1->score();
            if (0 == $condition) {
                // Order by timestamp when the same score
                $condition = $p2->timestamp()->getTimestamp() <=> $p1->timestamp()->getTimestamp();
            }
            return $condition;
        });
        return $playersCopy;
    }

    /**
     * Get the player number
     *
     * @param Player $player
     * @return int
     */
    public function playerNum(Player $player)
    {
        $index = 0;
        foreach ($this->players as $p) {
            ++$index;
            if ($player->uuid() == $p->uuid()) {
                return $index;
            }
        }
        return -1;
    }
}
