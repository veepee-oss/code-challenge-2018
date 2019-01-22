<?php

namespace AppBundle\Repository;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Repository\GameRepositoryInterface;
use AppBundle\Entity\Game as GameEntity;
use AppBundle\Entity\Logger as LoggerEntity;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Doctrine Repository: GameRepository
 *
 * @package AppBundle\Repository
 */
class GameRepository extends EntityRepository implements GameRepositoryInterface
{
    /**
     * Removes a game
     *
     * @param mixed $game
     * @return GameRepositoryInterface
     * @throws InvalidArgumentException
     */
    public function removeGame($game): GameRepositoryInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        /** @var GameEntity $game */
        $game = $this->findGameEntity($game);
        $em->remove($game);

        /** @var LoggerRepository $loggerRepo */
        $loggerRepo = $em->getRepository('AppBundle:Logger');

        /** @var LoggerEntity[] $loggers */
        $loggers = $loggerRepo->findBy([
            'gameUuid' => $game->getUuid()
        ]);

        /** @var LoggerEntity $logger */
        foreach ($loggers as $logger) {
            $loggerRepo->removeLogger($logger);
        }

        return $this;
    }

    /**
     * Find Game entity
     *
     * @param mixed $game
     * @return GameEntity
     * @throws InvalidArgumentException
     */
    protected function findGameEntity($game): GameEntity
    {
        if ($game instanceof GameEntity) {
            return $game;
        }

        if ($game instanceof Game) {
            return $this->findOneBy([
                'uuid' => $game->uuid()
            ]);
        }

        if (is_string($game)) {
            return $this->findOneBy([
                'uuid' => $game
            ]);
        }

        throw new InvalidArgumentException('$game is invalid!');
    }
}
