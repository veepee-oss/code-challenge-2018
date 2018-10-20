<?php

namespace AppBundle\Domain\Entity\Maze;

use AppBundle\Domain\Entity\Position\Direction;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Domain entity MazeObject
 *
 * @package AppBundle\Domain\Entity\Maze
 */
class MazeObject
{
    /** @var Position */
    protected $position;

    /** @var Position */
    protected $previous;

    /**
     * MazeObject constructor.
     *
     * @param Position $position
     * @param Position $previous
     */
    public function __construct(Position $position, Position $previous = null)
    {
        $this->position = clone $position;
        if ($previous) {
            $this->previous = clone $previous;
        } else {
            $this->previous = clone $position;
        }
    }

    /**
     * Get current position
     *
     * @return Position
     */
    public function position() : Position
    {
        return $this->position;
    }

    /**
     * Get previous position
     *
     * @return Position
     */
    public function previous() : Position
    {
        return $this->previous;
    }

    /**
     * Get current direction
     *
     * @return string|null
     */
    public function direction() : ?string
    {
        if (null == $this->position
            || null == $this->previous) {
            return Direction::STOPPED;
        }

        return Direction::direction(
            $this->position,
            $this->previous
        );
    }

    /**
     * Moves the maze object
     *
     * @param Position $position
     * @return $this
     */
    public function move(Position $position) : MazeObject
    {
        $this->previous = clone $this->position;
        $this->position = clone $position;
        return $this;
    }
}
