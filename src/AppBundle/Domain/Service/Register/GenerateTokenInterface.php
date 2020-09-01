<?php

namespace AppBundle\Domain\Service\Register;

use AppBundle\Domain\Entity\Contest\Competitor;

/**
 * Interface to a service to generate an unique token to validate the user
 *
 * @package AppBundle\Domain\Service\Register
 */
interface GenerateTokenInterface
{
    /**
     * Adds the token to a competitor
     *
     * @param Competitor $competitor
     * @return void
     */
    public function addToken(Competitor $competitor);
}
