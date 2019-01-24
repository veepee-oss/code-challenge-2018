<?php

namespace AppBundle\Domain\Service\Contest;

use AppBundle\Domain\Entity\Contest\Match;

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
     * @return void
     * @throws ScoreCalculatorException
     */
    public function calculateMatchScore(Match $match): void;
}
