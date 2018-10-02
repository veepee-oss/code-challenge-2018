<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;

/**
 * Interface to a service to move all the players in a game
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface MoveAllPlayersServiceInterface
{
    /**
     * Move all the players in a game
     *
     * @param Game $game
     * @return void
     * @throws MovePlayerException
     */
    public function move(Game &$game);
}
