<?php

namespace AppBundle\Domain\Repository;

/**
 * Interface to a repository of Game entities
 *
 * @package AppBundle\Domain\Repository
 */
interface GameRepositoryInterface
{
    /**
     * Removes a game
     *
     * @param mixed $game
     * @return GameRepositoryInterface
     */
    public function removeGame($game): GameRepositoryInterface;

}
