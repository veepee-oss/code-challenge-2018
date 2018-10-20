<?php

namespace AppBundle\Domain\Entity\Maze;

/**
 * Domain entity MazeCell
 *
 * @package AppBundle\Domain\Entity\Maze
 */
class MazeCell
{
    const CELL_EMPTY = 0x00;
    const CELL_WALL = 0x88;

    /** @var int */
    protected $content;

    /**
     * MazeCell constructor.
     *
     * @param int $content
     */
    public function __construct(int $content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getContent() : int
    {
        return $this->content;
    }

    /**
     * @param int $content
     * @return MazeCell
     */
    public function setContent(int $content) : MazeCell
    {
        $this->content = $content;
        return $this;
    }
}
