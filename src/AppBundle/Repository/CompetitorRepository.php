<?php

namespace AppBundle\Repository;

use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Domain\Repository\CompetitorRepositoryInterface;
use AppBundle\Entity\Competitor as CompetitorEntity;
use AppBundle\Entity\Contest as ContestEntity;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

/**
 * Doctrine Repository: CompetitorRepository
 *
 * @package AppBundle\Repository
 */
class CompetitorRepository extends EntityRepository implements CompetitorRepositoryInterface
{
    /**
     * Removes a competitor
     *
     * @param mixed $competitor
     * @return CompetitorRepositoryInterface
     * @throws ORMException
     * @throws InvalidArgumentException
     */
    public function removeCompetitor($competitor): CompetitorRepositoryInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        /** @var CompetitorEntity $competitor */
        $competitor = $this->findCompetitorEntity($competitor);
        $em->remove($competitor);

        return $this;
    }

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

    /**
     * Find the count of competitors per contest
     *
     * @param array $contests
     * @return array [ 'contestUuid' => string, 'competitorCount' => int]
     */
    public function countPerContest(array $contests = [])
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->select(['c.contestUuid', 'COUNT(c) as competitorCount'])
            ->groupBy('c.contestUuid');

        if (!empty($contests)) {
            $contestUuids = [];
            foreach ($contests as $contest) {
                if ($contest instanceof Contest) {
                    $contestUuids[] = $contest->uuid();
                } elseif ($contest instanceof ContestEntity) {
                    $contestUuids[] = $contest->getUuid();
                } elseif (is_string($contest)) {
                    $contestUuids[] = $contest;
                }
            }
            $qb->where('c.contestUuid IN (:uuids)')
                ->setParameter('uuids', $contestUuids);
        }

        return $qb
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Find Competitor entity
     *
     * @param mixed $competitor
     * @return CompetitorEntity
     * @throws InvalidArgumentException
     */
    protected function findCompetitorEntity($competitor): CompetitorEntity
    {
        if ($competitor instanceof CompetitorEntity) {
            return $competitor;
        }

        if ($competitor instanceof Competitor) {
            return $this->findOneBy([
                'uuid' => $competitor->uuid()
            ]);
        }

        if (is_string($competitor)) {
            return $this->findOneBy([
                'uuid' => $competitor
            ]);
        }

        throw new InvalidArgumentException('$competitor is invalid!');
    }
}
