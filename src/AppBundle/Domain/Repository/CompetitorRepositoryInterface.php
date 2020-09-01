<?php

namespace AppBundle\Domain\Repository;

/**
 * Interface to a repository of Competitor entities
 *
 * @package AppBundle\Domain\Repository
 */
interface CompetitorRepositoryInterface
{
    /**
     * Removes a competitor
     *
     * @param mixed $competitor
     * @return CompetitorRepositoryInterface
     */
    public function removeCompetitor($competitor): CompetitorRepositoryInterface;

}
