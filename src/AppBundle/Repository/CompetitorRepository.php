<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Doctrine Repository: CompetitorRepository
 *
 * @package AppBundle\Repository
 */
class CompetitorRepository extends EntityRepository
{
    /**
     * Finds if there are another competitor with the same url registered
     *
     * @param string $contest
     * @param string $email
     * @param string $url
     * @return int
     * @throws NonUniqueResultException
     */
    public function searchForDuplicateUrl(string $contest, string $email, string $url)
    {
        return $this
            ->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->where('c.contestUuid = :contest')
            ->andWhere('c.email != :email')
            ->andWhere('c.url = :url')
            ->setParameter('contest', $contest)
            ->setParameter('email', $email)
            ->setParameter('url', $url)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
