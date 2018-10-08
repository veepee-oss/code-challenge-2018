<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Interface to a service to move a single player in the game
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface MovePlayerServiceInterface
{
    /**
     * Moves a single player in the game
     *
     * @param Player $player the player to move
     * @param Game   $game   the game where belongs
     * @param string $move   the move to do
     * @return bool true=success, false=error
     * @throws MovePlayerException
     */
    public function move(Player& $player, Game $game, string $move = null) : bool;
}
