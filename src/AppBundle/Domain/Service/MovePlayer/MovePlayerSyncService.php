<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Direction;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Class to a service to move a single player in the game
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MovePlayerSyncService implements MovePlayerServiceInterface
{
    /** @var AskNextMovementInterface */
    protected $askNextMovementService;

    /**
     * MoveSinglePlayer constructor.
     *
     * @param AskNextMovementInterface $askNextMovementService
     */
    public function __construct(AskNextMovementInterface $askNextMovementService)
    {
        $this->askNextMovementService = $askNextMovementService;
    }

    /**
     * Moves the player
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=success, false=error
     * @throws MovePlayerException
     */
    public function move(Player& $player, Game $game)
    {
        // Reads the next movement of the player: "up", "down", "left" or "right".
        $direction = $this->askNextMovementService->askNextMovement($player, $game);
        if (!$direction) {
            return false;
        }

        // Computes the new position
        $position = $this->computeNewPosition($player->position(), $direction);
        if (!$this->validatePosition($position, $game->maze())) {
            return false;
        }

        $player->move($position);
        return true;
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
            || $maze[$y][$x]->getContent() == MazeCell::CELL_WALL) {
            return false;
        }

        return true;
    }
}
