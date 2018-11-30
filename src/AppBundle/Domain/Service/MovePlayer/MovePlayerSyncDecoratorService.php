<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Class to a service to move a single player in the game
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MovePlayerSyncDecoratorService implements MovePlayerServiceInterface
{
    /** @var MovePlayerServiceInterface */
    protected $movePlayerService;

    /** @var PlayerRequestInterface */
    protected $playerRequestService;

    /** @var AskNextMovementInterface */
    protected $askNextMovementService;

    /**
     * MoveSinglePlayer constructor.
     *
     * @param MovePlayerServiceInterface $movePlayerService
     * @param AskNextMovementInterface $askNextMovementService
     * @param PlayerRequestInterface $playerRequestService
     */
    public function __construct(
        MovePlayerServiceInterface $movePlayerService,
        PlayerRequestInterface $playerRequestService,
        AskNextMovementInterface $askNextMovementService
    ) {
        $this->movePlayerService = $movePlayerService;
        $this->playerRequestService = $playerRequestService;
        $this->askNextMovementService = $askNextMovementService;
    }

    /**
     * Moves the player
     *
     * @param Player $player the player to move
     * @param Game   $game   the game where belongs
     * @param string $move   the move to do
     * @return bool true=success, false=error
     * @throws MovePlayerException
     */
    public function move(Player& $player, Game $game, string $move = null) : bool
    {
        if (null === $move) {
            // Create request data
            $requestData = $this->playerRequestService->create($player, $game);

            // Reads the next movement of the player: "up", "down", "left" or "right".
            $move = $this->askNextMovementService->askNextMovement(
                $player->url(),
                $player->uuid(),
                $game->uuid(),
                $requestData
            );
        }

        // Moves the player (default action)
        return $this->movePlayerService->move($player, $game, $move);
    }
}
