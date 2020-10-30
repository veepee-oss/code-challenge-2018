<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Fire\Fire;
use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Direction;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Service to move a single player in the game
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MovePlayerService implements MovePlayerServiceInterface
{
    /**
     * Moves the player
     *
     * @param Player      $player the player to move
     * @param Game        $game   the game where it belongs
     * @param string|null $move   the move to do
     * @return bool true=success, false=error
     */
    public function move(Player& $player, Game $game, string $move = null) : bool
    {
        // Validations
        $moved = true;
        if (!$move) {
            $moved = false;
        }

        if (Fire::firing($move)) {
            if (!$player->isReloading()) {
                $player->fire($move);
            } else {
                // Invalid movement while reloading
                $player->move($player->position());
                $moved = false;
            }
        } else {
            // Computes the new position
            $position = $this->computeNewPosition($player->position(), $move);
            if (!$this->validatePosition($position, $game->maze())) {
                $position = $player->position();
                $moved = false;
            }

            // Move the player to the new computed position
            $player->move($position);
        }

        return $moved;
    }

    /**
     * Computes the new position for a movement
     *
     * @param Position $position
     * @param string $direction
     * @return Position
     */
    protected function computeNewPosition(Position $position, $direction)
    {
        $y = $position->y();
        $x = $position->x();
        switch ($direction) {
            case Direction::UP:
                $y--;
                break;

            case Direction::DOWN:
                $y++;
                break;

            case Direction::LEFT:
                $x--;
                break;

            case Direction::RIGHT:
                $x++;
                break;

            default:
                break;
        }

        return new Position($y, $x);
    }

    /**
     * Validates the position in the map
     *
     * @param Position $position
     * @param Maze $maze
     * @return bool
     */
    protected function validatePosition(Position $position, Maze $maze)
    {
        $y = $position->y();
        $x = $position->x();

        if ($y < 0 || $y >= $maze->height()
            || $x < 0 || $x >= $maze->width()
            || !$maze[$y][$x]->isEmpty()) {
            return false;
        }

        return true;
    }
}
