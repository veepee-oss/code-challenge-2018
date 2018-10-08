<?php

namespace AppBundle\Domain\Service\MovePlayer;

/**
 * Interface AskPlayerNameInterface
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface AskPlayerNameInterface
{
    /**
     * Asks for the name of the player
     *
     * @param string $url    the base URL to call
     * @param string $player the player UUID
     * @param string $game   the game UUID (optional)
     * @return array['name', 'email'] The player name and email
     * @throws MovePlayerException
     */
    public function askPlayerName(string $url, string $player, string $game = null) : array;
}
