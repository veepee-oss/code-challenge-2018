<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Class to validate the player asking for the game
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class ValidatePlayerService implements ValidatePlayerServiceInterface
{
    /** @var AskPlayerNameInterface */
    protected $askPlayerNameService;

    /**
     * ValidatePlayerService constructor.
     *
     * @param AskPlayerNameInterface $askPlayerNameService
     */
    public function __construct(AskPlayerNameInterface $askPlayerNameService)
    {
        $this->askPlayerNameService = $askPlayerNameService;
    }

    /**
     * Validates the player asking for the name
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=success, false=error
     */
    public function validate(Player& $player, Game $game = null)
    {
        try {
            // Asks for the name and email of the player
            $data = $this->askPlayerNameService->askPlayerName(
                $player->url(),
                $player->uuid(),
                $game ? $game->uuid() :  null
            );

            if (!$data) {
                return false;
            }
        } catch (MovePlayerException $exc) {
            return false;
        }

        $player->setPlayerIds($data['name'], $data['email']);
        return true;
    }
}
