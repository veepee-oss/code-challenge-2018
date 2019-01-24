<?php

namespace AppBundle\Domain\Entity\Contest;

use Ramsey\Uuid\Uuid;

/**
 * Domain entity: Round
 *
 * A round of a contest: quarters, semis, final, ...
 * It has participants and matches.
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

    /** @var int the status of the round */
    private $status;

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

    /** @var int number of matches per player to do */
    private $numMatches;

    /** @var Participant[] the participants of the round */
    private $participants;

    /** @var int the constants for the match statuses */
    const STATUS_NOT_STARTED = 0;
    const STATUS_FINISHED = 16;

    /** @var int the default matches per player */
    const DEFAULT_NUM_MATCHES = 3;

    /**
     * Round constructor
     *
     * @param string|null $uuid
     * @param string $contest
     * @param string $name
     * @param int|null $status
     * @param int $height
     * @param int $width
     * @param int $minGhosts
     * @param int $ghostRate
     * @param int $limit
     * @param int $numMatches
     * @param array|null $participants
     * @throws \Exception
     */
    public function __construct(
        ?string $uuid,
        string $contest,
        string $name,
        ?int $status,
        int $height,
        int $width,
        int $minGhosts,
        int $ghostRate,
        int $limit,
        int $numMatches,
        ?array $participants
    ) {
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
        $this->contest = $contest;
        $this->name = $name;
        $this->status = $status ?? self::STATUS_NOT_STARTED;
        $this->height = $height;
        $this->width = $width;
        $this->minGhosts = $minGhosts;
        $this->ghostRate = $ghostRate;
        $this->limit = $limit;
        $this->numMatches = $numMatches;

        $this->participants = [];
        if (null !== $participants) {
            foreach ($participants as $participant) {
                $this->addParticipant($participant);
            }
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
    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->status == self::STATUS_FINISHED;
    }

    /**
     * @return Round
     */
    public function setFinished(): Round
    {
        $this->status = self::STATUS_FINISHED;
        return $this;
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
     * @return int
     */
    public function numMatches(): int
    {
        return $this->numMatches;
    }

    /**
     * @return Participant[]
     */
    public function participants(): array
    {
        return $this->participants;
    }

    /**
     * Adds a participant
     *
     * @param Participant $participant
     * @return Round
     */
    public function addParticipant(Participant $participant): Round
    {
        $this->participants[] = clone $participant;
        return $this;
    }

    /**
     * Resets the scores of the participants of the round
     *
     * @return Round
     */
    public function resetParticipantScores(): Round
    {
        foreach ($this->participants() as $participant) {
            $participant->resetScore();
            $participant->setClassified(false);
        }
        return $this;
    }

    /**
     * Accumulates the results of a match into the round
     *
     * @param Match $match
     * @return Round
     */
    public function calculateParticipantScores(Match $match): Round
    {
        /** @var Result $result */
        foreach ($match->results() as $result) {
            foreach ($this->participants() as $participant) {
                if ($result->competitor() == $participant->competitor()->uuid()) {
                    $participant->addScore($result->score());
                }
            }
        }
        return $this;
    }

    public function calculateParticipantClassification(): Round
    {
        // TODO
        return $this;
    }
}
