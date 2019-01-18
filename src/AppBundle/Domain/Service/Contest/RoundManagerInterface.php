<?php

namespace AppBundle\Domain\Service\Contest;

use AppBundle\Domain\Entity\Contest\Round;

/**
 * Interface to a service to find and add the participants to a round
 *
 * @package AppBundle\Domain\Service\Contest
 */
interface RoundManagerInterface
{
    /**
     * Copies all the participants from the source round (or from the contest)
     *
     * @param Round       $round
     * @param string|null $sourceRoundUuid
     * @return Round
     */
    public function addParticipants(Round $round, ?string $sourceRoundUuid) : Round;
}
