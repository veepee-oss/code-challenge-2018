<?php

namespace AppBundle\Domain\Entity\Contest;

use AppBundle\Domain\Entity\Game\Game;
use Ramsey\Uuid\Uuid;

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
    /** @var string the UUID of the match */
    private $uuid;

    /** @var string the UUID of the round */
    private $roundUuid;

    /** @var string the UUID of the game */
    private $gameUuid;

    /** @var int the status of the match */
    private $status;

    /** @var Result[] the results of the match */
    private $results;

    /** @var Game|null */
    private $game;

    /** @var int the constants for the match statuses */
    const STATUS_NOT_STARTED = 0;
    const STATUS_FINISHED = 16;
    const STATUS_VALIDATED = 32;

    /**
     * Match constructor
     *
     * @param string|null $uuid
     * @param string $roundUuid
     * @param string $gameUuid
     * @param int|null $status
     * @param Result[] $results
     * @throws \Exception
     */
    public function __construct(?string $uuid, string $roundUuid, string $gameUuid, ?int $status, array $results = [])
    {
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
        $this->roundUuid = $roundUuid;
        $this->gameUuid = $gameUuid;
        $this->status = $status ?? self::STATUS_NOT_STARTED;
        $this->results = [];
        $this->game = null;

        /** @var Result $result */
        foreach ($results as $result) {
            $this->results[] = clone $result;
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
    public function roundUuid(): string
    {
        return $this->roundUuid;
    }

    /**
     * @return string
     */
    public function gameUuid(): string
    {
        return $this->gameUuid;
    }

    /**
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * Return if the status is finished
     *
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->status >= self::STATUS_FINISHED;
    }

    /**
     * Return if the status is validated
     *
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->status == self::STATUS_VALIDATED;
    }

    /**
     * Set the status finished
     *
     * @return Match
     */
    public function setFinished(): Match
    {
        $this->status = self::STATUS_FINISHED;
        return $this;
    }

    /**
     * Set the status validated
     *
     * @return Match
     */
    public function setValidated(): Match
    {
        $this->status = self::STATUS_VALIDATED;
        return $this;
    }

    /**
     * @return Result[]
     */
    public function results(): array
    {
        return $this->results;
    }

    /**
     * Updates a result
     *
     * @param string $player
     * @param int $score
     * @return Match
     */
    public function setResultScore(string $player, int $score): Match
    {
        foreach ($this->results as $result) {
            if ($player == $result->player()) {
                $result->setScore($score);
            }
        }
        return $this;
    }

    /**
     * @return Game|null
     */
    public function game(): ?Game
    {
        return $this->game;
    }

    /**
     * @param Game|null $game
     * @return Match
     */
    public function setGame(Game $game): Match
    {
        $this->game = $game;
        return $this;
    }
}
