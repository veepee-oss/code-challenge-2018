<?php

namespace AppBundle\Domain\Repository;

/**
 * Interface to a repository of Match entities
 *
 * @package AppBundle\Domain\Repository
 */
interface MatchRepositoryInterface
{
    /**
     * Removes a match
     *
     * @param mixed $match
     * @return MatchRepositoryInterface
     */
    public function removeMatch($match): MatchRepositoryInterface;

}
