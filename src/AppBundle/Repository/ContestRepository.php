<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Doctrine Repository: ContestRepository
 *
 * @package AppBundle\Repository
 */
class ContestRepository extends EntityRepository
{
    /**
     * Finds the active contests
     *
     * @return mixed
     * @throws \Exception
     */
    public function findActiveContests()
    {
        return $this
            ->getFindActiveContestsQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * Creates a query builder to get the active contests
     *
     * @return QueryBuilder
     * @throws \Exception
     */
    public function getFindActiveContestsQueryBuilder() : QueryBuilder
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.startDate <= :date')
            ->andWhere('c.endDate >= :date')
            ->orderBy('c.startDate', 'asc')
            ->setParameter('date', new \DateTime());
    }
}
