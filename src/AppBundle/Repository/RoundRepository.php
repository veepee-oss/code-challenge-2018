<?php

namespace AppBundle\Repository;

use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Entity\Contest\Round;
use AppBundle\Domain\Repository\RoundRepositoryInterface;
use AppBundle\Entity\Round as RoundEntity;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Doctrine Repository: RoundRepository
 *
 * @package AppBundle\Repository
 */
class RoundRepository extends EntityRepository implements RoundRepositoryInterface
{
    /**
     * Reads a round from the database
     *
     * @param string $uuid
     * @return Round|null
     */
    public function readRound(string $uuid): ?Round
    {
        try {
            $roundEntity = $this->findRoundEntity($uuid);
            if (null == $roundEntity) {
                return null;
            }

            return $roundEntity->toDomainEntity();
        } catch (\Exception $exc) {
            return null;
        }
    }

    /**
     * @param string $contestUuid
     * @return Round[]
     */
    public function readRounds(string $contestUuid): array
    {
        try {
            /** @var RoundEntity[] $roundEntities */
            $roundEntities = $this->findBy([ 'contestUuid' => $contestUuid ]);

            $rounds = [];
            foreach ($roundEntities as $roundEntity) {
                $rounds[] = $roundEntity->toDomainEntity();
            }

            return $rounds;
        } catch (\Exception $exc) {
            return [];
        }
    }

    /**
     * Persists a round in the database
     *
     * @param Round $round
     * @param bool $autoFlush
     * @return RoundRepositoryInterface
     * @throws InvalidArgumentException
     */
    public function persistRound(Round $round, bool $autoFlush = false): RoundRepositoryInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        $roundEntity = $this->findRoundEntity($round->uuid());
        $roundEntity->fromDomainEntity($round);

        $em->persist($roundEntity);
        if ($autoFlush) {
            $em->flush();
        }

        return $this;
    }

    /**
     * Removes a round
     *
     * @param mixed $round
     * @return RoundRepositoryInterface
     * @throws InvalidArgumentException
     */
    public function removeRound($round): RoundRepositoryInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        /** @var RoundEntity $round */
        $roundEntity = $this->findRoundEntity($round);
        $em->remove($roundEntity);

        /** @var MatchRepository $matchRepo */
        $matchRepo = $em->getRepository('AppBundle:Match');

        /** @var Match[] $matches */
        $matches = $matchRepo->findBy([
            'roundUuid' => $roundEntity->getUuid()
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
