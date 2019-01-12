<?php

namespace AppBundle\Domain\Entity\Contest;

/**
 * Domain entity: Participant
 *
 * A participant of a round of a contest
 *
 * @package AppBundle\Domain\Entity\Contest
 */
class Participant
{
    /** @var Competitor the competitor data */
    private $competitor;

    /** @var int the score achieved */
    private $score;

    /** @var bool whether if it is classified for next round */
    private $classified;

    /**
     * Participant constructor
     *
     * @param Competitor $competitor
     * @param int $score
     * @param bool $classified
     */
    public function __construct(Competitor $competitor, ?int $score, ?bool $classified)
    {
        $this->competitor = $competitor;
        $this->score = $score ?? 0;
        $this->classified = $classified ?? false;
    }

    /**
     * @return Competitor
     */
    public function competitor(): Competitor
    {
        return $this->competitor;
    }

    /**
     * @return int
     */
    public function score(): int
    {
        return $this->score;
    }

    /**
     * @return bool
     */
    public function classified(): bool
    {
        return $this->classified;
    }
}
