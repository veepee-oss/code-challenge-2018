<?php

namespace AppBundle\Entity;

use AppBundle\Domain\Entity\Game as DomainGame;
use AppBundle\Domain\Entity\Ghost as DomainGhost;
use AppBundle\Domain\Entity\Maze as DomainMaze;
use AppBundle\Domain\Entity\Player as DomainPlayer;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Entity Game
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * Primary key
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * UUID of the game
     *
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, unique=true)
     */
    private $uuid;

    /**
     * Name of the game
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=48)
     */
    private $name;

    /**
     * Status of the game: playing, paused, ...
     *
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * Width of the maze
     *
     * @var int
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * Height of the maze
     *
     * @var int
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;

    /**
     * Frequency of new ghosts (0=none)
     *
     * @var int
     *
     * @ORM\Column(name="ghost_rate", type="integer", options={"default"=0})
     */
    protected $ghostRate;

    /**
     * Minimum ghosts any time (0=none)
     *
     * @var int
     *
     * @ORM\Column(name="min_ghosts", type="integer", options={"default"=0})
     */
    protected $minGhosts;

    /**
     * Number of moves done
     *
     * @var int
     *
     * @ORM\Column(name="moves_made", type="integer", options={"default"=0})
     */
    protected $moves;

    /**
     * Limit of movements to do
     *
     * @var int
     *
     * @ORM\Column(name="moves_limit", type="integer", options={"default"=500})
     */
    protected $limit;

    /**
     * All the data of the maze
     *
     * @var array
     *
     * @ORM\Column(name="maze", type="json_array")
     */
    private $maze;

    /**
     * All the data of the players
     *
     * @var array
     *
     * @ORM\Column(name="players", type="json_array")
     */
    private $players;

    /**
     * All the data of the ghosts
     *
     * @var array
     *
     * @ORM\Column(name="ghosts", type="json_array", nullable=true)
     */
    private $ghosts;

    /**
     * All the data of the ghosts
     *
     * @var array
     *
     * @ORM\Column(name="killed_ghosts", type="json_array", nullable=true)
     */
    private $killedGhosts;

    /**
     * Game constructor.
     *
     * @param $source
     * @throws \Exception
     */
    public function __construct($source = null)
    {
        if (null === $source) {
            $this->id = null;
            $this->uuid = Uuid::uuid4()->toString();
            $this->name = $this->uuid;
            $this->status = null;
            $this->width = null;
            $this->height = null;
            $this->moves = 0;
            $this->limit = DomainGame\Game::DEFAULT_MOVES_LIMIT;
            $this->ghostRate = 0;
            $this->minGhosts = 0;
            $this->maze = array();
            $this->players = array();
            $this->ghosts = array();
            $this->killedGhosts = array();
        } elseif ($source instanceof Game) {
            $this->id = $source->getId();
            $this->uuid = $source->getUuid();
            $this->name = $source->getName();
            $this->status = $source->getStatus();
            $this->width = $source->getWidth();
            $this->height = $source->getHeight();
            $this->ghostRate = $source->getGhostRate();
            $this->minGhosts = $source->getMinGhosts();
            $this->moves = $source->getMoves();
            $this->limit = $source->getLimit();
            $this->maze = $source->getMaze();
            $this->players = $source->getPlayers();
            $this->ghosts = $source->getGhosts();
            $this->killedGhosts = $source->getKilledGhosts();
        } elseif ($source instanceof DomainGame\Game) {
            $this->id = null;
            $this->fromDomainEntity($source);
        }
    }

    /**
     * Convert entity to a domain game
     *
     * @return DomainGame\Game
     * @throws \Exception
     */
    public function toDomainEntity()
    {
        $mazeObj = new DomainMaze\Maze(
            $this->height,
            $this->width,
            $this->maze
        );

        $playersArray = array();
        foreach ($this->players as $player) {
            $playersArray[] = DomainPlayer\Player::unserialize($player);
        }

        $ghostsArray = array();
        foreach ($this->ghosts as $ghost) {
            $ghostsArray[] = DomainGhost\Ghost::unserialize($ghost);
        }

        $killedGhostsArray = array();
        foreach ($this->killedGhosts as $ghost) {
            $killedGhostsArray[] = DomainGhost\Ghost::unserialize($ghost);
        }

        return new DomainGame\Game(
            $mazeObj,
            $playersArray,
            $ghostsArray,
            $killedGhostsArray,
            $this->ghostRate,
            $this->minGhosts,
            $this->status,
            $this->moves,
            $this->limit,
            $this->uuid,
            $this->name
        );
    }

    /**
     * Update entity from a domain game
     *
     * @param DomainGame\Game $game
     * @return $this
     */
    public function fromDomainEntity(DomainGame\Game $game)
    {
        $this->uuid = $game->uuid();
        $this->name = $game->name();
        $this->status = $game->status();
        $this->setMaze($game->maze());
        $this->setPlayers($game->players());
        $this->setGhosts($game->ghosts());
        $this->setKilledGhosts($game->killedGhosts());
        $this->setGhostRate($game->ghostRate());
        $this->setMinGhosts($game->minGhosts());
        $this->SetLimit($game->limit());
        $this->setMoves($game->moves());
        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set game uuid
     *
     * @param string $uuid
     * @return $this
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Get game uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set game name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get game name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set game status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get game status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set maze width
     *
     * @param int $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * get maze width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set maze height
     *
     * @param int $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get maze height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set min ghosts
     *
     * @param int $minGhosts
     * @return $this
     */
    public function setMinGhosts($minGhosts)
    {
        $this->minGhosts = $minGhosts;
        return $this;
    }

    /**
     * Get min ghosts
     *
     * @return int
     */
    public function getMinGhosts()
    {
        return $this->minGhosts;
    }

    /**
     * Set ghost rate
     *
     * @param int $ghostRate
     * @return $this
     */
    public function setGhostRate($ghostRate)
    {
        $this->ghostRate = $ghostRate;
        return $this;
    }

    /**
     * Get ghost rate
     *
     * @return int
     */
    public function getGhostRate()
    {
        return $this->ghostRate;
    }

    /**
     * Set moves
     *
     * @param int $moves
     * @return $this
     */
    public function setMoves($moves)
    {
        $this->moves = $moves;
        return $this;
    }

    /**
     * Get moves
     *
     * @return int
     */
    public function getMoves()
    {
        return $this->moves;
    }

    /**
     * Set limit
     *
     * @param int $limit
     * @return Game
     */
    public function setLimit(int $limit): Game
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Set maze cell contents
     *
     * @param DomainMaze\Maze|array $maze
     * @return $this
     */
    public function setMaze($maze)
    {
        if (!$maze instanceof DomainMaze\Maze) {
            $this->maze = $maze;
        } else {
            $this->width = $maze->width();
            $this->height = $maze->height();
            $this->maze = array();
            for ($i = 0; $i < $this->height; $i++) {
                $this->maze[$i] = array();
                for ($j = 0; $j < $this->width; $j++) {
                    /** @var DomainMaze\MazeCell $cell */
                    $cell = $maze[$i][$j];
                    $this->maze[$i][$j] = $cell->getContent();
                }
            }
        }
        return $this;
    }

    /**
     * Get maze cell contents
     *
     * @return array
     */
    public function getMaze()
    {
        return $this->maze;
    }

    /**
     * Set players
     *
     * @param array $players
     * @return $this
     */
    public function setPlayers($players = null)
    {
        $this->players = array();
        if (null !== $players && count($players) > 0) {
            foreach ($players as $player) {
                if ($players[0] instanceof DomainPlayer\Player) {
                    $this->players[] = $player->serialize();
                } else {
                    $this->players[] = $player;
                }
            }
        }
        return $this;
    }

    /**
     * Set players
     *
     * @return array
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Set ghosts
     *
     * @param array $ghosts
     * @return $this
     */
    public function setGhosts($ghosts = null)
    {
        $this->ghosts = array();
        if (null !== $ghosts && count($ghosts) > 0) {
            foreach ($ghosts as $ghost) {
                if ($ghost instanceof DomainGhost\Ghost) {
                    $this->ghosts[] = $ghost->serialize();
                } else {
                    $this->ghosts[] = $ghost;
                }
            }
        }
        return $this;
    }

    /**
     * Set ghosts
     *
     * @return array
     */
    public function getGhosts()
    {
        return $this->ghosts;
    }

    /**
     * Set killed ghosts
     *
     * @param array $killedGhosts
     * @return $this
     */
    public function setKilledGhosts($killedGhosts = null)
    {
        $this->killedGhosts = array();
        if (null !== $killedGhosts && count($killedGhosts) > 0) {
            foreach ($killedGhosts as $killedGhost) {
                if ($killedGhost instanceof DomainGhost\Ghost) {
                    $this->killedGhosts[] = $killedGhost->serialize();
                } else {
                    $this->killedGhosts[] = $killedGhost;
                }
            }
        }
        return $this;
    }

    /**
     * Set killed ghosts
     *
     * @return array
     */
    public function getKilledGhosts()
    {
        return $this->killedGhosts;
    }
}
