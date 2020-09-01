<?php

namespace AppBundle\Form\CreateRound;

use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Domain\Entity\Contest\Round;
use AppBundle\Domain\Entity\Game\Game;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Form entity: RoundEntity
 *
 * @package AppBundle\Form\CreateRound
 */
class RoundEntity
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $contest = null;

    /**
     * @var string|null
     */
    private $sourceRound = null;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=0, max=16)
     */
    private $name = null;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=10, max=25)
     */
    private $height = Game::DEFAULT_MAZE_HEIGHT;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=10, max=50)
     */
    private $width = Game::DEFAULT_MAZE_WIDTH;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=0, max=50)
     */
    private $minGhosts = Game::DEFAULT_MIN_GHOSTS;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=0, max=100)
     */
    private $ghostRate = Game::DEFAULT_GHOST_RATE;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=100, max=5000)
     */
    private $limit = Game::DEFAULT_MOVES_LIMIT;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=1, max=10)
     */
    private $matchesPerPlayer = Round::DEFAULT_MATCHES_PER_PLAYER;


    /**
     * RoundEntity constructor
     *
     * @param Contest $contest
     */
    public function __construct(Contest $contest)
    {
        $this->contest = $contest->uuid();
    }

    /**
     * Converts the entity to a domain entity
     *
     * @return Round
     * @throws \Exception
     */
    public function toDomainEntity(): Round
    {
        return new Round(
            null,
            $this->contest,
            $this->name,
            null,
            $this->height,
            $this->width,
            $this->minGhosts,
            $this->ghostRate,
            $this->limit,
            $this->matchesPerPlayer,
            []
        );
    }

    /**
     * @return string
     */
    public function getContest(): ?string
    {
        return $this->contest;
    }

    /**
     * @param string $contest
     * @return RoundEntity
     */
    public function setContest(string $contest): RoundEntity
    {
        $this->contest = $contest;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSourceRound(): ?string
    {
        return $this->sourceRound;
    }

    /**
     * @param string|null $sourceRound
     * @return RoundEntity
     */
    public function setSourceRound(?string $sourceRound): RoundEntity
    {
        $this->sourceRound = $sourceRound;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RoundEntity
     */
    public function setName(string $name): RoundEntity
    {
        $this->name = $name;
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
     * @return RoundEntity
     */
    public function setHeight(int $height): RoundEntity
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
     * @return RoundEntity
     */
    public function setWidth(int $width): RoundEntity
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
     * @return RoundEntity
     */
    public function setMinGhosts(int $minGhosts): RoundEntity
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
     * @return RoundEntity
     */
    public function setGhostRate(int $ghostRate): RoundEntity
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
     * @return RoundEntity
     */
    public function setLimit(int $limit): RoundEntity
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getMatchesPerPlayer(): int
    {
        return $this->matchesPerPlayer;
    }

    /**
     * @param int $matchesPerPlayer
     * @return RoundEntity
     */
    public function setMatchesPerPlayer(int $matchesPerPlayer): RoundEntity
    {
        $this->matchesPerPlayer = $matchesPerPlayer;
        return $this;
    }
}
