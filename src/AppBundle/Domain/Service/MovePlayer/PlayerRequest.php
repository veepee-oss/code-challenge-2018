<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Ghost\Ghost;
use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Class PlayerRequest
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class PlayerRequest implements PlayerRequestInterface
{
    /**
     * Creates the request data to send to the player. The request data will be a json object.
     *
     * {
     *     "game": {
     *         "id": "uuid"
     *     },
     *     "player": {
     *         "id": "uuid",
     *         "name": "string",
     *         "position": {
     *             "y": "int",
     *             "x": "int"
     *         },
     *         "previous": {
     *             "y": "int",
     *             "x": "int"
     *         },
     *         "area": {
     *             "y1": "int",
     *             "x1": "int",
     *             "y2": "int",
     *             "x2": "int"
     *         }
     *     },
     *     "maze": {
     *         "size": {
     *             "height": "int",
     *             "width": "int"
     *         },
     *         "walls": [
     *             {
     *                 "y": "int",
     *                 "x": "int"
     *             }
     *         ]
     *     },
     *     "players": [
     *         {
     *             "y": "int",
     *             "x": "int",
     *             "fire: "bool"
     *         }
     *     ],
     *     "ghosts": [
     *         {
     *             "y": "int",
     *             "x": "int",
     *             "neutral": "bool"
     *         }
     *     ]
     * }
     *
     * @param Player $player    The player data.
     * @param Game   $game      The game data.
     * @param int    $viewRange The view distance.
     * @param bool   $asArray   Return as array or string
     * @return string|array Request in json format or array format
     */
    public function create(Player $player, Game $game, $viewRange = self::DEFAULT_VIEW_RANGE, $asArray = false)
    {
        $maze = $game->maze();
        $height = $maze->height();
        $width = $maze->width();
        $pos = $player->position();
        $prev = $player->previous();

        $size = 1 + ($viewRange * 2);
        while ($size > $height || $size > $width) {
            --$viewRange;
            $size = 1 + ($viewRange * 2);
        }

        $y1 = $pos->y() - $viewRange;
        $y2 = $pos->y() + $viewRange;
        $x1 = $pos->x() - $viewRange;
        $x2 = $pos->x() + $viewRange;

        if ($y1 < 0) {
            $y1 = 0;
        } elseif ($y2 >= $height) {
            $y2 = $height - 1;
        }

        if ($x1 < 0) {
            $x1 = 0;
        } elseif ($x2 >= $width) {
            $x2 = $width - 1;
        }

        $data = array(
            'game'      => array(
                'id'        => $player->uuid()
            ),
            'player'    => array(
                'id'        => $player->uuid(),
                'name'      => $player->name(),
                'position'  => array(
                    'y'         => $pos->y(),
                    'x'         => $pos->x()
                ),
                'previous'  => array(
                    'y'         => $prev->y(),
                    'x'         => $prev->x()
                ),
                'area'      => array(
                    'y1'        => $y1,
                    'x1'        => $x1,
                    'y2'        => $y2,
                    'x2'        => $x2
                )
            ),
            'maze'      => array(
                'size'      => array(
                    'height'    => $height,
                    'width'     => $width
                ),
                'walls'     => $this->getVisibleWalls($game->maze(), $y1, $x1, $y2, $x2),
            ),
            'players'   => $this->getVisiblePlayers($game->players(), $player, $y1, $x1, $y2, $x2),
            'ghosts'    => $this->getVisibleGhosts($game->ghosts(), $y1, $x1, $y2, $x2)
        );

        if ($asArray) {
            return $data;
        }

        return json_encode($data);
    }

    /**
     * Get the visible walls array
     *
     * @param Maze $maze
     * @param int  $y1
     * @param int  $x1
     * @param int  $y2
     * @param int  $x2
     * @return array
     */
    public function getVisibleWalls(Maze $maze, int $y1, int $x1, int $y2, int $x2)
    {
        $walls = array();
        for ($y = $y1; $y <= $y2; ++$y) {
            for ($x = $x1; $x <= $x2; ++$x) {
                if ($maze[$y][$x]->getContent() == MazeCell::CELL_WALL) {
                    $walls[] = array(
                        'y' => $y,
                        'x' => $x
                    );
                }
            }
        }
        return $walls;
    }

    /**
     * Get the visible players array
     *
     * @param Player[] $allPlayers
     * @param Player   $current;
     * @param int      $y1
     * @param int      $x1
     * @param int      $y2
     * @param int      $x2
     * @return array
     */
    public function getVisiblePlayers(array $allPlayers, Player $current, int $y1, int $x1, int $y2, int $x2)
    {
        $players = [];
        foreach ($allPlayers as $player) {
            $playerPos = $player->position();
            if ($player->uuid() != $current->uuid()
                && !$player->isKilled()
                && $playerPos->y() >= $y1
                && $playerPos->y() <= $y2
                && $playerPos->x() >= $x1
                && $playerPos->x() <= $x2) {
                $players[] = array(
                    'y' => $playerPos->y(),
                    'x' => $playerPos->x(),
                    'fire' => !$player->isReloading()
                );
            }
        }
        return $players;
    }

    /**
     * Get the visible ghosts array
     *
     * @param Ghost[] $allGhosts
     * @param int     $y1
     * @param int     $x1
     * @param int     $y2
     * @param int     $x2
     * @return array
     */
    public function getVisibleGhosts(array $allGhosts, int $y1, int $x1, int $y2, int $x2)
    {
        $ghosts = [];
        foreach ($allGhosts as $ghost) {
            $ghostPos = $ghost->position();
            if ($ghostPos->y() >= $y1
                && $ghostPos->y() <= $y2
                && $ghostPos->x() >= $x1
                && $ghostPos->x() <= $x2) {
                $ghosts[] = array(
                    'y' => $ghostPos->y(),
                    'x' => $ghostPos->x(),
                    'neutral' => $ghost->isNeutral()
                );
            }
        }
        return $ghosts;
    }
}
