<?php

namespace AppBundle\Domain\Service\Contest;

use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Entity\Contest\Round;

/**
 * Interface to a service to calculate the score of each player of a match or round
 *
 * @package AppBundle\Domain\Service\Contest
 */
interface ScoreCalculatorInterface
{
    /**
     * Calculates the score of each player for a match
     *
     * @param Match $match
     * @return $this
     * @throws ScoreCalculatorException
     */
    public function calculateMatchScore(Match $match): ScoreCalculatorInterface;

    /**
     * Calculates the score of each player for a round
     *
     * @param Round $round
     * @param Match[] $matches
     * @return $this
     * @throws ScoreCalculatorException
     */
    public function calculateRoundScore(Round $round, array $matches): ScoreCalculatorInterface;
}
