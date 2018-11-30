<?php

namespace AppBundle\Domain\Service\MazeBuilder;

use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Maze\MazeCell;

/**
 * Maze builder using recursive division method
 *
 * @package AppBundle\Domain\Service\MazeBuilder
 */
class MazeBuilderRecursiveDivision implements MazeBuilderInterface
{
    /** @var Maze */
    protected $maze = null;

    /** Constants */
    const HORIZONTAL = 1;
    const VERTICAL = 2;

    /**
     * Creates a random maze
     *
     * @param int $height
     * @param int $width
     * @return Maze
     */
    public function buildRandomMaze($height, $width) : Maze
    {
        $this
            ->createMaze($height, $width)
            ->createBorders()
            ->makeDivisions(0, 0, $width - 1, $height - 1);

        return $this->maze;
    }

    /**
     * Creates the empty maze object
     *
     * @param int $height
     * @param int $width
     * @return $this
     */
    protected function createMaze($height, $width)
    {
        $this->maze = new Maze($height, $width);
        return $this;
    }

    /**
     * Creates the borders of the maze
     *
     * @return $this
     */
    protected function createBorders()
    {
        $height = $this->maze->height();
        $width = $this->maze->width();

        $x1 = 0;
        $y1 = 0;
        $x2 = $width - 1;
        $y2 = $height - 1;

        $this->drawVerticalWall($x1, $y1, $y2);
        $this->drawVerticalWall($x2, $y1, $y2);

        $this->drawHorizontalWall($y1, $x1, $x2);
        $this->drawHorizontalWall($y2, $x1, $x2);

        return $this;
    }

    /**
     * Makes the divisions of the maze
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int $orientation
     * @return $this
     */
    protected function makeDivisions($x1, $y1, $x2, $y2, $orientation = null)
    {
        $width = $x2 - $x1 + 1;
        $height = $y2 - $y1 + 1;
        if ($width < 5|| $height < 5) {
            return $this;
        }

        $px = rand($x1 + 2, $x2 - 2);
        $py = rand($y1 + 2, $y2 - 2);

        $orientation = $orientation ?: $this->chooseOrientation($width, $height);
        if (self::HORIZONTAL == $orientation) {
            $this->drawHorizontalWall($py, $x1, $x2);
            $this->maze[$py][$px]->setContent(MazeCell::CELL_EMPTY);
            $orientation = self::VERTICAL;
        } else {
            $this->drawVerticalWall($px, $y1, $y2);
            $this->maze[$py][$px]->setContent(MazeCell::CELL_EMPTY);
            $orientation = self::HORIZONTAL;
        }

        $this->makeDivisions($x1, $y1, $px, $py, $orientation);
        $this->makeDivisions($x1, $py, $px, $y2, $orientation);
        $this->makeDivisions($px, $y1, $x2, $py, $orientation);
        $this->makeDivisions($px, $py, $x2, $y2, $orientation);

        return $this;
    }

    /**
     * Chooses the orientation of a new division wall (vertical or horizontal)
     *
     * @param int $width
     * @param int $height
     * @return int
     */
    protected function chooseOrientation($width, $height)
    {
        if ($width < $height) {
            return self::HORIZONTAL;
        } elseif ($height < $width) {
            return self::VERTICAL;
        } else {
            return (0 == rand(0, 1)) ? self::HORIZONTAL : self::VERTICAL;
        }
    }

    /**
     * Draws a vertical wall between two points
     *
     * @param int $x
     * @param int $y1
     * @param int $y2
     * @param int $wall
     */
    protected function drawVerticalWall($x, $y1, $y2, $wall = MazeCell::CELL_WALL)
    {
        for ($i = $y1; $i <= $y2; $i++) {
            $this->maze[$i][$x]->setContent($wall);
        }
    }

    /**
     * Draws an horizontal wall between two points
     *
     * @param int $y
     * @param int $x1
     * @param int $x2
     * @param int $wall
     */
    protected function drawHorizontalWall($y, $x1, $x2, $wall = MazeCell::CELL_WALL)
    {
        for ($i = $x1; $i <= $x2; $i++) {
            $this->maze[$y][$i]->setContent($wall);
        }
    }
}
