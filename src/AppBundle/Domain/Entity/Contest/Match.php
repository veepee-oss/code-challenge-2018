<?php

namespace AppBundle\Domain\Entity\Contest;

/**
 * Domain entity: Match
 *
 * A match of a round of a contest.
 * Contains the results of the match.
 *
 * @package AppBundle\Domain\Entity\Contest
 */
class Match
{
    /** @var int the constants for the match statuses */
    const STATUS_NOT_STARTED = 0;
    const STATUS_FINISHED = 16;
    const STATUS_VALIDATED = 32;

    /** @var string the UUID of the match */
    private $uuid;

    /** @var string the UUID of the round */
    private $round;

    /** @var string the UUID of the game */
    private $game;

    /** @var int the status of the match */
    private $status;

    /** @var Result[] the results of the match */
    private $results;

    /**
     * Match constructor
     *
     * @param string $uuid
     * @param string $round
     * @param string $game
     * @param int $status
     * @param Result[] $results
     */
    public function __construct(string $uuid, string $round, string $game, int $status, array $results)
    {
        $this->uuid = $uuid;
        $this->round = $round;
        $this->game = $game;
        $this->status = $status;
        $this->results = $results;
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
    public function round(): string
    {
        return $this->round;
    }

    /**
     * @return string
     */
    public function game(): string
    {
        return $this->game;
    }

    /**
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return Result[]
     */
    public function results(): array
    {
        return $this->results;
    }
}
