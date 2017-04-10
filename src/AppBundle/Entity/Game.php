<?php

namespace AppBundle\Entity;

use AppBundle\Domain\Entity\Game as DomainGame;
use AppBundle\Domain\Entity\Maze as DomainMaze;
use AppBundle\Domain\Entity\Player as DomainPlayer;

use AppBundle\Domain\Entity\Position\Position;
use Doctrine\ORM\Mapping as ORM;
use J20\Uuid\Uuid;

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
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, unique=true)
     */
    private $uuid;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;

    /**
     * @var int
     *
     * @ORM\Column(name="start_y", type="integer")
     */
    private $startY;

    /**
     * @var int
     *
     * @ORM\Column(name="start_x", type="integer")
     */
    private $startX;

    /**
     * @var int
     *
     * @ORM\Column(name="goal_y", type="integer")
     */
    private $goalY;

    /**
     * @var int
     *
     * @ORM\Column(name="goal_x", type="integer")
     */
    private $goalX;

    /**
     * @var array
     *
     * @ORM\Column(name="maze", type="json_array")
     */
    private $maze;

    /**
     * @var array
     *
     * @ORM\Column(name="players", type="json_array")
     */
    private $players;

    /**
     * Game constructor.
     *
     * @param $source
     */
    public function __construct($source = null)
    {
        if (null === $source) {
            $this->id = null;
            $this->uuid = Uuid::v4();
            $this->status = null;
            $this->width = null;
            $this->height = null;
            $this->startY = null;
            $this->startX = null;
            $this->goalY = null;
            $this->goalX = null;
            $this->maze = array();
            $this->players = array();
        } elseif ($source instanceof Game) {
            $this->id = $source->getId();
            $this->uuid = $source->getUuid();
            $this->status = $source->getStatus();
            $this->width = $source->getWidth();
            $this->height = $source->getHeight();
            $this->startY = $source->getStartY();
            $this->startX = $source->getStartX();
            $this->goalY = $source->getGoalY();
            $this->goalX = $source->getGoalX();
            $this->maze = $source->getMaze();
            $this->players = $source->getPlayers();
        } elseif ($source instanceof DomainGame\Game) {
            $this->id = null;
            $this->uuid = $source->uuid();
            $this->status = $source->status();
            $this->setMaze($source->maze());
            $this->setPlayers($source->players());
        }
    }

    /**
     * Convert entity
     *
     * @return DomainGame\Game
     */
    public function toDomainEntity()
    {
        $maze = new DomainMaze\Maze($this->width, $this->height, $this->maze);
        $maze->setStart(new Position($this->startY, $this->startX));
        $maze->setGoal(new Position($this->goalY, $this->goalX));

        $players = array();
        foreach ($this->players as $player) {
            switch ($player['type']) {
                case DomainPlayer\Player::TYPE_API:
                    $players[] = DomainPlayer\ApiPlayer::unserialize($player);
                    break;

                case DomainPlayer\Player::TYPE_BOT:
                    $players[] = DomainPlayer\BotPlayer::unserialize($player);
                    break;
            }
        }

        return new DomainGame\Game($maze, $players, $this->status, $this->uuid);
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
     * Get maze start Y coordinate
     *
     * @return int
     */
    public function getStartY()
    {
        return $this->startY;
    }

    /**
     * Set maze start Y coordinate
     *
     * @param int $startY
     * @return $this
     */
    public function setStartY($startY)
    {
        $this->startY = $startY;
        return $this;
    }

    /**
     * Get maze start X coordinate
     *
     * @return int
     */
    public function getStartX()
    {
        return $this->startX;
    }

    /**
     * Set maze start X coordinate
     *
     * @param int $startX
     * @return $this
     */
    public function setStartX($startX)
    {
        $this->startX = $startX;
        return $this;
    }

    /**
     * Get maze goal Y coordinate
     *
     * @return int
     */
    public function getGoalY()
    {
        return $this->goalY;
    }

    /**
     * Set maze goal Y coordinate
     *
     * @param int $goalY
     * @return $this
     */
    public function setGoalY($goalY)
    {
        $this->goalY = $goalY;
        return $this;
    }

    /**
     * Get maze goal X coordinate
     *
     * @return int
     */
    public function getGoalX()
    {
        return $this->goalX;
    }

    /**
     * Set maze goal X coordinate
     *
     * @param int $goalX
     * @return $this
     */
    public function setGoalX($goalX)
    {
        $this->goalX = $goalX;
        return $this;
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
            $this->startY = $maze->start()->y();
            $this->startX = $maze->start()->x();
            $this->goalY = $maze->goal()->y();
            $this->goalX = $maze->goal()->x();
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
     * Get players
     *
     * @param array $players
     * @return $this
     */
    public function setPlayers($players = null)
    {
        $this->players = array();
        if (null !== $players && count($players) > 0) {
            if (!$players[0] instanceof DomainPlayer\Player) {
                $this->players = $players;
            } else {
                for ($i = 0; $i < count($players); $i++) {
                    $this->players[] = $players[$i]->serialize();
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
}
