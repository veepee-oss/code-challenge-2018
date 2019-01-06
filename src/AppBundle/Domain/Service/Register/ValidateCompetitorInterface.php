<?php

namespace AppBundle\Domain\Service\Register;

use AppBundle\Domain\Entity\Contest\Competitor;
use AppBundle\Domain\Entity\Contest\Contest;

/**
 * Interface to a service to validate a competitor for a contest
 *
 * @package AppBundle\Domain\Service\Register
 */
interface ValidateCompetitorInterface
{
    /**
     * Validates the competitor for one contest
     *
     * @param Competitor $competitor
     * @param Contest $contest
     * @return ValidationResults
     */
    public function validate(Competitor $competitor, Contest $contest) : ValidationResults;
}
