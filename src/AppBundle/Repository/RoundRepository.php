<?php

namespace AppBundle\Repository;

use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Entity\Contest\Round;
use AppBundle\Domain\Repository\RoundRepositoryInterface;
use AppBundle\Entity\Round as RoundEntity;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;

/**
 * Doctrine Repository: RoundRepository
 *
 * @package AppBundle\Repository
 */
class RoundRepository extends EntityRepository implements RoundRepositoryInterface
{
    /**
     * Removes a round
     *
     * @param mixed $round
     * @return RoundRepositoryInterface
     * @throws ORMException
     * @throws InvalidArgumentException
     */
    public function removeRound($round): RoundRepositoryInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        /** @var RoundEntity $round */
        $round = $this->findRoundEntity($round);
        $em->remove($round);

        /** @var MatchRepository $matchRepo */
        $matchRepo = $em->getRepository('AppBundle:Match');

        /** @var Match[] $matches */
        $matches = $matchRepo->findBy([
            'roundUuid' => $round->getUuid()
        ]);

        /** @var Match $match */
        foreach ($matches as $match) {
            $matchRepo->removeMatch($match);
        }

        return $this;
    }

    /**
     * Find Round entity
     *
     * @param mixed $round
     * @return RoundEntity
     * @throws InvalidArgumentException
     */
    protected function findRoundEntity($round): RoundEntity
    {
        if ($round instanceof RoundEntity) {
            return $round;
        }

        if ($round instanceof Round) {
            return $this->findOneBy([
                'uuid' => $round->uuid()
            ]);
        }

        if (is_string($round)) {
            return $this->findOneBy([
                'uuid' => $round
            ]);
        }

        throw new InvalidArgumentException('$round is invalid!');
    }
}
