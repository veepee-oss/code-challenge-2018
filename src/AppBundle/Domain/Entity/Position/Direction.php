<?php

namespace AppBundle\Domain\Entity\Position;

/**
 * Domain entity Direction
 *
 * @package AppBundle\Domain\Entity\Position
 */
class Direction
{
    const UP = 'up';
    const DOWN = 'down';
    const LEFT = 'left';
    const RIGHT = 'right';
    const STOPPED = null;

    /**
     * Get directions array
     *
     * @return array
     */
    public static function directions() : array
    {
        return array(
            Direction::UP,
            Direction::RIGHT,
            Direction::DOWN,
            Direction::LEFT
        );
    }

    /**
     * Computes a direction using two positions.
     *
     * @param Position $pos
     * @param Position $prev
     * @return string|null
     */
    public static function compute(Position $pos, Position $prev) : ?string
    {
        $y = $pos->y() - $prev->y();
        $x = $pos->x() - $prev->x();

        if ($y == 0 && $x == 0) {
            $result = Direction::STOPPED;
        } elseif (abs($y) >= abs($x)) {
            if ($y < 0) {
                $result = Direction::UP;
            } else {
                $result = Direction::DOWN;
            }
        } else {
            if ($x < 0) {
                $result = Direction::LEFT;
            } else {
                $result = Direction::RIGHT;
            }
        }
        return $result;
    }

    /**
     * Compute the new direction when turn right
     *
     * @param string $dir
     * @return string|null
     */
    public static function turnRight(string $dir) : ?string
    {
        $directions = static::directions();
        $key = array_search($dir, $directions);
        if (false === $key) {
            return null;
        }
        return $directions[($key + 1) % 4];
    }

    /**
     * Compute the new direction when turn left
     *
     * @param string $dir
     * @return string
     */
    public static function turnLeft(string $dir) : ?string
    {
        $directions = static::directions();
        $key = array_search($dir, $directions);
        if (false === $key) {
            return null;
        }
        return $directions[($key + 3) % 4];
    }

    /**
     * Compute the new direction when go back
     *
     * @param string $dir
     * @return string
     */
    public static function turnBack(string $dir) : ?string
    {
        $directions = static::directions();
        $key = array_search($dir, $directions);
        if (false === $key) {
            return null;
        }
        return $directions[($key + 2) % 4];
    }
}
