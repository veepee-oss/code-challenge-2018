<?php

namespace AppBundle\Domain\Service\MazeRender;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Ghost\Ghost;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Position\Direction;

/**
 * Class MazeIconRender
 *
 * @package AppBundle\Domain\Service\MazeRender
 */
class MazeIconRender implements MazeRenderInterface
{
    /**
     * Renders the game's maze with all the players
     *
     * @param Game $game
     * @return string
     */
    public function render(Game $game) : string
    {
        $maze = $game->maze();
        $class = $this->getMazeGlobalCss();
        $html = '<table class="' . $class .'">';

        $rows = $maze->height();
        $cols = $maze->width();

        // For each row...
        for ($row = 0; $row < $rows; ++$row) {
            $html .= '<tr>';

            // For each column...
            for ($col = 0; $col < $cols; ++$col) {
                $class = $this->getEmptyCellCss();

                $cell = $maze[$row][$col]->getContent();
                if ($cell == MazeCell::CELL_WALL) {
                    $class = $this->getMazeWallCss();
                }

                foreach ($game->players() as $index => $player) {
                    if ($player->position()->x() == $col
                        && $player->position()->y() == $row) {
                        $direction = $player->direction();
                        if (!$direction) {
                            $direction = Direction::RIGHT;
                        }

                        if ($player->isKilled()) {
                            $class = $this->getPlayedKilledCss(1 + $index, $direction);
                        } else {
                            $class = $this->getPlayerCss(1 + $index, $direction);
                        }
                    }
                }

                foreach ($game->ghosts() as $index => $ghost) {
                    if ($ghost->position()->x() == $col
                        && $ghost->position()->y() == $row) {
                        $direction = $ghost->direction();
                        if (!$direction) {
                            $direction = Direction::RIGHT;
                        }

                        if ($ghost->isNeutral()) {
                            $class = $this->getGhostNeutralCss($index, $direction);
                        } elseif (Ghost::TYPE_KILLING == $ghost->type()) {
                            $class = $this->getGhostAngryCss($index, $direction);
                        } else {
                            $class = $this->getGhostCss($index, $direction);
                        }
                    }
                }

                $html .= '<td class="' . $class . '"></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

    protected function getMazeGlobalCss()
    {
        return 'x-maze';
    }

    protected function getEmptyCellCss()
    {
        return 'x-empty';
    }

    protected function getMazeWallCss()
    {
        return 'x-wall';
    }

    protected function getPlayerCss($index, $direction)
    {
        return 'x-player' . $index . '-' . $direction;
    }

    protected function getPlayedKilledCss($index, $direction)
    {
        return 'x-killed' . $index;
    }

    protected function getGhostCss($index, $direction)
    {
        return 'x-ghost';
    }

    protected function getGhostNeutralCss($index, $direction)
    {
        return 'x-ghost-neutral';
    }

    protected function getGhostAngryCss($index, $direction)
    {
        return 'x-ghost-bad';
    }
}
