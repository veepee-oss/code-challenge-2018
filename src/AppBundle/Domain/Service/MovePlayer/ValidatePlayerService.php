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
    /** @var AskPlayerNameServiceLocator */
    protected $serviceLocator;

    /**
     * ValidatePlayerService constructor.
     *
     * @param AskPlayerNameServiceLocator $serviceLocator
     */
    public function __construct(AskPlayerNameServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Validates the player asking for the name
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=success, false=error
     * @throws MovePlayerException
     */
    public function validate(Player& $player, Game $game = null)
    {
        /** @var AskPlayerNameInterface $playerService */
        $playerService = $this->serviceLocator->locate($player);

        try {
            // Asks for the name and email of the player
            $data = $playerService->askPlayerName($player, $game);
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
