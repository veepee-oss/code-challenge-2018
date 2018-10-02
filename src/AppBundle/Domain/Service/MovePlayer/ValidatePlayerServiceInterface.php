<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Interface to a service to validate the player asking for the game
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface ValidatePlayerServiceInterface
{
    /**
     * Validates the player asking for the name
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=success, false=error
     * @throws MovePlayerException
     */
    public function validate(Player& $player, Game $game = null);
}
