<?php

namespace AppBundle\Domain\Repository;

/**
 * Interface to a repository of Contest entities
 *
 * @package AppBundle\Domain\Repository
 */
interface ContestRepositoryInterface
{
    /**
     * Removes a contest
     *
     * @param mixed $contest
     * @return ContestRepositoryInterface
     */
    public function removeContest($contest): ContestRepositoryInterface;

}
