<?php

namespace AppBundle\Repository;

use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Repository\MatchRepositoryInterface;
use AppBundle\Entity\Match as MatchEntity;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;

/**
 * Doctrine Repository: MatchRepository
 *
 * @package AppBundle\Repository
 */
class MatchRepository extends EntityRepository implements MatchRepositoryInterface
{
    /**
     * Removes a match
     *
     * @param mixed $match
     * @return MatchRepositoryInterface
     * @throws InvalidArgumentException
     * @throws ORMException
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
