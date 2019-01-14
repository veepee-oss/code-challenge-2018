<?php

namespace AppBundle\Entity;

use AppBundle\Domain\Entity\Game as DomainGame;
use AppBundle\Domain\Entity\Contest as DomainContest;
use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine Entity: Round
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="round")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoundRepository")
 */
class Round
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
     * UUID of the round
     *
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, unique=true, nullable=false)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="contest_uuid", type="string", length=36, nullable=false)
     */
    private $contestUuid;

    /**
     * Name of the round
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=16, nullable=false)
     */
    private $name;

    /**
     * Status of the round
     *
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * Height of the board
     *
     * @var int
     *
     * @ORM\Column(name="height", type="integer", nullable=false)
     */
    private $height;

    /**
     * Width of the board
     *
     * @var int
     *
     * @ORM\Column(name="width", type="integer", nullable=false)
     */
    private $width;

    /**
     * Minimum ghosts any time (0=none)
     *
     * @var int
     *
     * @ORM\Column(name="min_ghosts", type="integer", nullable=false)
     */
    private $minGhosts;

    /**
     * Frequency of new ghosts (0=none)
     *
     * @var int
     *
     * @ORM\Column(name="ghost_rate", type="integer", nullable=false)
     */
    private $ghostRate;

    /**
     * Limit of movements to do
     *
     * @var int
     *
     * @ORM\Column(name="moves_limit", type="integer", nullable=false)
     */
    private $limit;

    /**
     * Number of matches per player to do
     *
     * @var int
     *
     * @ORM\Column(name="num_matches", type="integer", nullable=false)
     */
    private $numMatches;

    /**
     * All the data of the participants
     *
     * @var array
     *
     * @ORM\Column(name="participants", type="json_array")
     */
    private $participants;

    /**
     * Round constructor.
     *
     * @param $source
     */
    public function __construct($source = null)
    {
        if (null === $source) {
            $this->id = null;
            $this->uuid = null;
            $this->contestUuid = null;
            $this->name = null;
            $this->status = null;
            $this->height = DomainGame\Game::DEFAULT_MAZE_HEIGHT;
            $this->width = DomainGame\Game::DEFAULT_MAZE_WIDTH;
            $this->minGhosts = DomainGame\Game::DEFAULT_MIN_GHOSTS;
            $this->ghostRate = DomainGame\Game::DEFAULT_GHOST_RATE;
            $this->limit = DomainGame\Game::DEFAULT_MOVES_LIMIT;
            $this->numMatches = DomainContest\Round::DEFAULT_NUM_MATCHES;
            $this->participants = [];
        } elseif ($source instanceof Round) {
            $this->id = $source->getId();
            $this->uuid = $source->getUuid();
            $this->contestUuid = $source->getContestUuid();
            $this->name = $source->getName();
            $this->status = $source->getStatus();
            $this->height = $source->getHeight();
            $this->width = $source->getWidth();
            $this->minGhosts = $source->getMinGhosts();
            $this->ghostRate = $source->getGhostRate();
            $this->limit = $source->getLimit();
            $this->numMatches = $source->getNumMatches();
            $this->participants = $source->getParticipants();
        } elseif ($source instanceof DomainContest\Round) {
            $this->id = null;
            $this->fromDomainEntity($source);
        }
    }

    /**
     * Convert entity to a domain round
     *
     * @return DomainContest\Round
     * @throws \Exception
     */
    public function toDomainEntity()
    {
        $participantsArray = [];
        foreach ($this->participants as $participant) {
            $participantsArray[] = DomainContest\Participant::unserialize($participant);
        }

        return new DomainContest\Round(
            $this->uuid,
            $this->contestUuid,
            $this->name,
            $this->status,
            $this->height,
            $this->width,
            $this->minGhosts,
            $this->ghostRate,
            $this->limit,
            $this->numMatches,
            $participantsArray
        );
    }

    /**
     * Update entity from a domain round
     *
     * @param DomainContest\Round $round
     * @return $this
     */
    public function fromDomainEntity(DomainContest\Round $round)
    {
        $this->setUuid($round->uuid());
        $this->setContestUuid($round->contest());
        $this->setName($round->name());
        $this->setStatus($round->status());
        $this->setHeight($round->height());
        $this->setWidth($round->width());
        $this->setMinGhosts($round->minGhosts());
        $this->setGhostRate($round->ghostRate());
        $this->SetLimit($round->limit());
        $this->setNumMatches($round->numMatches());
        $this->setParticipants($round->participants());

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Round
     */
    public function setId(int $id): Round
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return Round
     */
    public function setUuid(string $uuid): Round
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getContestUuid(): string
    {
        return $this->contestUuid;
    }

    /**
     * @param string $contestUuid
     * @return Round
     */
    public function setContestUuid(string $contestUuid): Round
    {
        $this->contestUuid = $contestUuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Round
     */
    public function setName(string $name): Round
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Round
     */
    public function setStatus(int $status): Round
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return Round
     */
    public function setHeight(int $height): Round
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return Round
     */
    public function setWidth(int $width): Round
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinGhosts(): int
    {
        return $this->minGhosts;
    }

    /**
     * @param int $minGhosts
     * @return Round
     */
    public function setMinGhosts(int $minGhosts): Round
    {
        $this->minGhosts = $minGhosts;
        return $this;
    }

    /**
     * @return int
     */
    public function getGhostRate(): int
    {
        return $this->ghostRate;
    }

    /**
     * @param int $ghostRate
     * @return Round
     */
    public function setGhostRate(int $ghostRate): Round
    {
        $this->ghostRate = $ghostRate;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return Round
     */
    public function setLimit(int $limit): Round
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumMatches(): int
    {
        return $this->numMatches;
    }

    /**
     * @param int $numMatches
     * @return Round
     */
    public function setNumMatches(int $numMatches): Round
    {
        $this->numMatches = $numMatches;
        return $this;
    }

    /**
     * @return array
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }

    /**
     * @param array $participants
     * @return Round
     */
    public function setParticipants($participants): Round
    {
        $this->participants = [];
        if (null !== $participants && count($participants) > 0) {
            foreach ($participants as $participant) {
                if ($participant instanceof DomainContest\Participant) {
                    $this->participants[] = $participant->serialize();
                } else {
                    $this->participants[] = $participant;
                }
            }
        }
        return $this;
    }
}
