<?php

namespace AppBundle\Domain\Repository;

/**
 * Interface to a repository of Round entities
 *
 * @package AppBundle\Domain\Repository
 */
interface RoundRepositoryInterface
{
    /**
     * Removes a round
     *
     * @param mixed $round
     * @return RoundRepositoryInterface
     */
    public function removeRound($round): RoundRepositoryInterface;

}
