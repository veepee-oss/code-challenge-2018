<?php

namespace AppBundle\Domain\Service\MovePlayer;

/**
 * Interface AskNextMovementInterface
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface AskNextMovementInterface
{
    /**
     * Reads the next movement of the player: "up", "down", "left" or "right".
     *
     * @param string $url     the base URL to call
     * @param string $player  the player UUID
     * @param string $game    the game UUID
     * @param string $request the player request
     * @return string The next movement
     * @throws MovePlayerException
     */
    public function askNextMovement(string $url, string $player, string $game, string $request) : string;
}
