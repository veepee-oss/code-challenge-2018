<?php

namespace AppBundle\Domain\Entity\Position;

/**
 * Domain entity Position
 *
 * @package AppBundle\Domain\Entity\Player
 */
class Position
{
    /** @var int */
    protected $y;

    /** @var int */
    protected $x;

    /**
     * Position constructor.
     *
     * @param int $y
     * @param int $x
     */
    public function __construct(int $y, int $x)
    {
        $this->y = $y;
        $this->x = $x;
    }

    /**
     * Get Y
     *
     * @return int
     */
    public function y() : int
    {
        return $this->y;
    }

    /**
     * Get X
     *
     * @return int
     */
    public function x() : int
    {
        return $this->x;
    }

    /**
     * Moves a position in a direction
     *
     * @param string|null $dir
     * @return $this
     */
    public function moveTo(?string $dir) : Position
    {
        switch ($dir) {
            case Direction::UP:
                --$this->y;
                break;

            case Direction::DOWN:
                ++$this->y;
                break;

            case Direction::LEFT:
                --$this->x;
                break;

            case Direction::RIGHT:
                ++$this->x;
                break;

            default:
                break;
        }
        return $this;
    }

    /**
     * Moves a position in a direction, returning a new object.
     *
     * @param Position $pos
     * @param string|null $dir
     * @return Position
     */
    public static function move(Position $pos, ?string $dir) : Position
    {
        $new = clone $pos;
        return $new->moveTo($dir);
    }

    /**
     * Return true if is the same position
     *
     * @param Position $pos
     * @return bool
     */
    public function equals(Position $pos) : bool
    {
        return ($this->y() == $pos->y()
            && $this->x() == $pos->x());
    }

    /**
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return array(
            'y' => $this->y(),
            'x' => $this->x()
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return Position
     */
    public static function unserialize(array $data) : Position
    {
        return new static(
            intval($data['y'] ?? 0),
            intval($data['x'] ?? 0)
        );
    }
}
