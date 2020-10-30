<?php

namespace AppBundle\Repository;

use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Repository\MatchRepositoryInterface;
use AppBundle\Entity\Match as MatchEntity;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Doctrine Repository: MatchRepository
 *
 * @package AppBundle\Repository
 */
class MatchRepository extends EntityRepository implements MatchRepositoryInterface
{
    /**
     * Reads a match from the database
     *
     * @param string $uuid
     * @return Match|null
     */
    public function readMatch(string $uuid): ?Match
    {
        try {
            $matchEntity = $this->findMatchEntity($uuid);
            if (null == $matchEntity) {
                return null;
            }

            return $matchEntity->toDomainEntity();
        } catch (\Exception $exc) {
            return null;
        }
    }

    /**
     * Reads all the matches of a round
     *
     * @param string $roundUuid
     * @return Match[]
     */
    public function readMatches(string $roundUuid): array
    {
        try {
            /** @var MatchEntity[] $matchEntities */
            $matchEntities = $this->findBy([ 'roundUuid' => $roundUuid ]);

            $matches = [];
            foreach ($matchEntities as $matchEntity) {
                $matches[] = $matchEntity->toDomainEntity();
            }

            return $matches;
        } catch (\Exception $exc) {
            return [];
        }
    }

    /**
     * Persists a match in the database
     *
     * @param Match $match
     * @param bool $autoFlush
     * @return MatchRepositoryInterface
     * @throws InvalidArgumentException
     */
    public function persistMatch(Match $match, bool $autoFlush = false): MatchRepositoryInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        $matchEntity = $this->findMatchEntity($match->uuid());
        $matchEntity->fromDomainEntity($match);

        $em->persist($matchEntity);
        if ($autoFlush) {
            $em->flush();
        }

        return $this;
    }

    /**
     * Persists an array of matches in the database
     *
     * @param Match[] $matches
     * @param bool $autoFlush
     * @return MatchRepositoryInterface
     * @throws InvalidArgumentException
     */
    public function persistMatches(array $matches, bool $autoFlush = false): MatchRepositoryInterface
    {
        /** @var Match $match */
        foreach ($matches as $match) {
            $this->persistMatch($match, false);
        }

        if ($autoFlush) {
            /** @var EntityManagerInterface $em */
            $em = $this->getEntityManager();
            $em->flush();
        }

        return $this;
    }

    /**
     * Removes a match
     *
     * @param mixed $match
     * @return MatchRepositoryInterface
     * @throws InvalidArgumentException
     */
    public function removeMatch($match): MatchRepositoryInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        /** @var MatchEntity $match */
        $match = $this->findMatchEntity($match);
        $em->remove($match);

        if (null !== $match->getGameUuid()) {
            /** @var GameRepository $gameRepo */
            $gameRepo = $em->getRepository('AppBundle:Game');
            $gameRepo->removeGame($match->getGameUuid());
        }

        return $this;
    }

    /**
     * Find Match entity
     *
     * @param mixed $match
     * @return MatchEntity
     * @throws InvalidArgumentException
     */
    protected function findMatchEntity($match): MatchEntity
    {
        if ($match instanceof MatchEntity) {
            return $match;
        }

        if ($match instanceof Match) {
            return $this->findOneBy([
                'uuid' => $match->uuid()
            ]);
        }

        if (is_string($match)) {
            return $this->findOneBy([
                'uuid' => $match
            ]);
        }

        throw new InvalidArgumentException('$match is invalid!');
    }
}
