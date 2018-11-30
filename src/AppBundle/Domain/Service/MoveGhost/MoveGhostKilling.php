<?php

namespace AppBundle\Domain\Service\MoveGhost;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Ghost\Ghost;
use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Direction;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Class MoveGhostKilling
 *
 * @package AppBundle\Domain\Service\MoveGhost
 */
class MoveGhostKilling extends MoveGhost
{
    /** @var Player[] the players array */
    private $players;

    /** @var int the height of the maze */
    private $height;

    /** @var int the width of the maze */
    private $width;

    /** @var array */
    private $content;

    /** @var int */
    private $iter;

    /**
     * Computes the next movement of the ghost: "up", "down", "left" or "right".
     *
     * @param Ghost $ghost
     * @param Game  $game
     * @return string The next movement
     */
    protected function computeNextMovement(Ghost $ghost, Game $game)
    {
        // Extract some vars
        $this->players = $game->players();
        $this->height = $game->height();
        $this->width = $game->width();

        // Available moves
        $maze = $game->maze();
        $pos = clone $ghost->position();
        $dir = $ghost->direction();
        $minIter = PHP_INT_MAX;

        $moves = Direction::directions();
        foreach ($moves as $move) {
            $newPos = Position::move($pos, $move);
            if ($maze[$newPos->y()][$newPos->x()]->isEmpty()) {
                if ($this->isPlayerKilled($newPos)) {
                    return $move;
                }
                $resultIter = $this->testMove($maze, $pos, $move);
                if ($resultIter > 0 && $resultIter < $minIter) {
                    $minIter = $resultIter;
                    $dir = $move;
                }
            }
        }

        return $dir;
    }

    /**
     * Test move and get iterations to player
     *
     * @param Maze     $maze
     * @param Position $position
     * @param string   $dir
     * @return int
     */
    private function testMove(Maze $maze, Position $position, $dir)
    {
        $pos = clone $position;
        $this->content = array();
        for ($y = 0; $y < $this->height; ++$y) {
            $this->content[$y] = array();
            for ($x = 0; $x < $this->width; ++$x) {
                if (!$maze[$y][$x]->isEmpty()) {
                    $this->content[$y][$x] = -1;
                } else {
                    $this->content[$y][$x] = 0;
                }
            }
        }

        $this->iter = 1;
        while (1) {
            $dir = $this->nextMove($pos, $dir);
            if ($dir == Direction::STOPPED) {
                return 0;
            }

            $pos->moveTo($dir);
            if ($this->isPlayerKilled($pos)) {
                return $this->iter;
            }
        }

        return 0;
    }

    /**
     * @param Position $position
     * @param string   $dir
     * @return string|null
     */
    private function nextMove(Position $position, $dir)
    {
        $pos = clone $position;
        if (0 == $this->content[$pos->y()][$pos->x()]) {
            $this->content[$pos->y()][$pos->x()] = $this->iter++;
        }

        // Compute posible directions and positions
        $forwardDir = $dir;
        $rightDir = Direction::turnRight($dir);
        $leftDir = Direction::turnLeft($dir);
        $backDir = Direction::turnBack($dir);

        $forwardPos = Position::move($pos, $forwardDir);
        $rightPos = Position::move($pos, $rightDir);
        $leftPos = Position::move($pos, $leftDir);
        $backPos = Position::move($pos, $backDir);

        // Test if player reached
        foreach ($this->players as $player) {
            if (!$player->isKilled()
                && !$player->isPowered()) {
                if ($forwardPos->equals($player->position())) {
                    return $forwardDir;
                }

                if ($rightPos->equals($player->position())) {
                    return $rightDir;
                }

                if ($leftPos->equals($player->position())) {
                    return $leftDir;
                }

                if ($backPos->equals($player->position())) {
                    return $backDir;
                }
            }
        }

        // Go forward if possible
        if ($this->isValidPosition($forwardPos, true)) {
            return $forwardDir;
        }

        // Turn right if possible
        if ($this->isValidPosition($rightPos, true)) {
            return $rightDir;
        }

        // Turn left if possible
        if ($this->isValidPosition($leftPos, true)) {
            return $leftDir;
        }

        // Else: go back
        $moves = array();

        $currentContent = $this->content[$pos->y()][$pos->x()];
        $this->content[$pos->y()][$pos->x()] = -2;
        $this->iter = $currentContent + 1;

        if ($this->isValidPosition($forwardPos, false)) {
            $forwardContent = $this->content[$forwardPos->y()][$forwardPos->x()];
            if ($forwardContent > 0 && $forwardContent < $currentContent) {
                $moves[$forwardContent] = $forwardDir;
            }
        }

        if ($this->isValidPosition($rightPos, false)) {
            $rightContent = $this->content[$rightPos->y()][$rightPos->x()];
            if ($rightContent > 0 && $rightContent < $currentContent) {
                $moves[$rightContent] = $rightDir;
            }
        }


        if ($this->isValidPosition($leftPos, false)) {
            $leftContent = $this->content[$leftPos->y()][$leftPos->x()];
            if ($leftContent > 0 && $leftContent < $currentContent) {
                $moves[$leftContent] = $leftDir;
            }
        }

        if ($this->isValidPosition($backPos, false)) {
            $backContent = $this->content[$backPos->y()][$backPos->x()];
            if ($backContent > 0 && $backContent < $currentContent) {
                $moves[$backContent] = $backDir;
            }
        }

        if (!empty($moves)) {
            ksort($moves, SORT_NUMERIC);
            $moves = array_reverse($moves);
            return reset($moves);
        }

        return Direction::STOPPED;
    }

    /**
     * Checks if a position is valid
     *
     * @param Position $position
     * @param bool     $onlyEmpty
     * @return bool
     */
    private function isValidPosition(Position $position, $onlyEmpty = false)
    {
        $pos = clone $position;

        // Test if player reached
        if ($this->isPlayerKilled($pos)) {
            return true;
        }

        if ($pos->y() < 1 || $pos->y() > $this->height - 2) {
            return false;
        }

        if ($pos->x() < 1 || $pos->x() > $this->width - 2) {
            return false;
        }

        $currentContent = $this->content[$pos->y()][$pos->x()];
        if ($currentContent < 0) {
            return false;
        }

        if ($onlyEmpty && $currentContent != 0) {
            return false;
        }

        return true;
    }

    /**
     * Checks if a movement kill a player
     *
     * @param Position $position
     * @return bool
     */
    private function isPlayerKilled(Position $position)
    {
        $pos = clone $position;
        foreach ($this->players as $player) {
            if (!$player->isKilled()
                && !$player->isPowered()
                && $pos->equals($player->position())) {
                return true;
            }
        }
        return false;
    }
}
