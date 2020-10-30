<?php

namespace AppBundle\Repository;

use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Domain\Repository\ContestRepositoryInterface;
use AppBundle\Entity\Contest as ContestEntity;
use AppBundle\Entity\Competitor as CompetitorEntity;
use AppBundle\Entity\Round as RoundEntity;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;

/**
 * Doctrine Repository: ContestRepository
 *
 * @package AppBundle\Repository
 */
class ContestRepository extends EntityRepository implements ContestRepositoryInterface
{
    /**
     * Removes a contest
     *
     * @param mixed $contest
     * @return ContestRepositoryInterface
     * @throws InvalidArgumentException
     * @throws ORMException
     */
    public function removeContest($contest): ContestRepositoryInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        /** @var ContestEntity $contest */
        $contest = $this->findContestEntity($contest);
        $em->remove($contest);

        /** @var CompetitorRepository $competitorRepo */
        $competitorRepo = $em->getRepository('AppBundle:Competitor');

        /** @var CompetitorEntity[] $competitors */
        $competitors = $competitorRepo->findBy([
            'contestUuid' => $contest->getUuid()
        ]);

        /** @var CompetitorEntity $competitor */
        foreach ($competitors as $competitor) {
            $competitorRepo->removeCompetitor($competitor);
        }

        /** @var RoundRepository $roundRepo */
        $roundRepo = $em->getRepository('AppBundle:Round');

        /** @var RoundEntity[] $rounds */
        $rounds = $roundRepo->findBy([
            'contestUuid' => $contest->getUuid()
        ]);

        /** @var RoundEntity $round */
        foreach ($rounds as $round) {
            $roundRepo->removeRound($round);
        }

        return $this;
    }

    /**
     * Finds the contests with open registration period
     *
     * @return mixed
     * @throws \Exception
     */
    public function findOpenedContests()
    {
        return $this
            ->getFindOpenedContestsQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * Creates a query builder to get the contests with open registration period
     *
     * @return QueryBuilder
     * @throws \Exception
     */
    public function getFindOpenedContestsQueryBuilder() : QueryBuilder
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.registrationStartDate <= :date')
            ->andWhere('c.registrationEndDate >= :date')
            ->orderBy('c.registrationStartDate', 'asc')
            ->setParameter('date', new \DateTime());
    }

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
            ->where('c.contestStartDate <= :date')
            ->andWhere('c.contestEndDate >= :date')
            ->orderBy('c.registrationStartDate', 'asc')
            ->setParameter('date', new \DateTime());
    }

    /**
     * Find Contest entity
     *
     * @param mixed $contest
     * @return ContestEntity
     * @throws InvalidArgumentException
     */
    protected function findContestEntity($contest): ContestEntity
    {
        if ($contest instanceof ContestEntity) {
            return $contest;
        }

        if ($contest instanceof Contest) {
            return $this->findOneBy([
                'uuid' => $contest->uuid()
            ]);
        }

        if (is_string($contest)) {
            return $this->findOneBy([
                'uuid' => $contest
            ]);
        }

        throw new InvalidArgumentException('$contest is invalid!');
    }
}
