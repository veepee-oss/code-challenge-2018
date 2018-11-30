<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Class to move all the players in a game synchronous
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MoveAllPlayersSyncService implements MoveAllPlayersServiceInterface
{
    /** @var  MovePlayerServiceInterface */
    protected $movePlayer;

    /**
     * MoveAllPlayerSyncService constructor
     *
     * @param MovePlayerServiceInterface $movePlayer
     */
    public function __construct(MovePlayerServiceInterface $movePlayer)
    {
        $this->movePlayer = $movePlayer;
    }

    /**
     * Move all the players in a game
     *
     * @param Game $game
     * @return void
     * @throws MovePlayerException
     */
    public function move(Game &$game) : void
    {
        /** @var Player[] $players */
        $players = $game->players();
        shuffle($players);

        foreach ($players as $player) {
            if (!$player->isKilled()) {
                $this->movePlayer->move($player, $game);
            }
        }
    }
}
