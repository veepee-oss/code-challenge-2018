<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

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
            ->getEntityManager()
            ->createQuery("
                SELECT c
                FROM AppBundle:Contest c
                WHERE c.startDate <= :date
                AND c.endDate >= :date
                ORDER BY c.startDate ASC
                ")
            ->setParameter('date', new \DateTime())
            ->getResult();
    }
}
