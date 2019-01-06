<?php

namespace AppBundle\Domain\Entity\Contest;

/**
 * Domain entity: Round
 *
 * A round of a contest: quarters, semis, final, ...
 * It has competitors and matches.
 *
 * @package AppBundle\Domain\Entity\Contest
 */
class Round
{
    /** @var string the UUID of the round */
    private $uuid;

    /** @var string the UUID of the contest */
    private $contest;

    /** @var string the name of the round */
    private $name;

    /** @var int the height of th maze  */
    private $height;

    /** @var int the width of the maze */
    private $width;

    /** @var int the minimum ghosts any time */
    private $minGhosts;

    /** @var int the frequency of new ghosts */
    private $ghostRate;

    /** @var int the limit of movements to do */
    private $limit;

    /** @var Competitor[] the competitors of the round */
    private $competitors;

    /** @var Match[] the matches of the round */
    private $matches;

    /**
     * Round constructor
     *
     * @param string $uuid
     * @param string $contest
     * @param string $name
     * @param int    $height
     * @param int    $width
     * @param int    $minGhosts
     * @param int    $ghostRate
     * @param int    $limit
     * @param array  $competitors
     * @param array  $matches
     */
    public function __construct(
        string $uuid,
        string $contest,
        string $name,
        int $height,
        int $width,
        int $minGhosts,
        int $ghostRate,
        int $limit,
        array $competitors,
        array $matches
    ) {
        $this->uuid = $uuid;
        $this->contest = $contest;
        $this->name = $name;
        $this->height = $height;
        $this->width = $width;
        $this->minGhosts = $minGhosts;
        $this->ghostRate = $ghostRate;
        $this->limit = $limit;

        $this->competitors = [];
        foreach ($competitors as $competitor) {
            $this->competitors[] = clone $competitor;
        }

        $this->matches = [];
        foreach ($matches as $match) {
            $this->matches[] = clone $match;
        }
    }

    /**
     * @return string
     */
    public function uuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function contest(): string
    {
        return $this->contest;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function height(): int
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function width(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function minGhosts(): int
    {
        return $this->minGhosts;
    }

    /**
     * @return int
     */
    public function ghostRate(): int
    {
        return $this->ghostRate;
    }

    /**
     * @return int
     */
    public function limit(): int
    {
        return $this->limit;
    }

    /**
     * @return Competitor[]
     */
    public function competitors(): array
    {
        return $this->competitors;
    }

    /**
     * @return Match[]
     */
    public function matches(): array
    {
        return $this->matches;
    }
}
