<?php

namespace AppBundle\Domain\Service\Contest;

use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Entity\Contest\Round;

/**
 * Interface to a service to create the matches for a round, including the games
 *
 * @package AppBundle\Domain\Service\Contest
 */
interface MatchManagerInterface
{
    /**
     * Creates all the matches of a round
     *
     * @param Round $round
     * @return Match[]
     */
    public function createMatches(Round $round): array;
}
