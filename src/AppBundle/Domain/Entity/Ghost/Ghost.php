<?php

namespace AppBundle\Domain\Entity\Ghost;

use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Class Ghost
 *
 * @package AppBundle\Domain\Entity\Ghost
 */
class Ghost extends MazeObject
{
    /** @var int Ghost types */
    const TYPE_RANDOM = 1;
    const TYPE_KILLING = 2;

    /** @var int Default values */
    const DEFAULT_NEUTRAL_TIME = 5;

    /** @var int the type of ghost */
    protected $type;

    /** @var int the number of moves whiles it's neutral */
    protected $neutralTime;

    /**
     * Ghost constructor.
     *
     * @param int $type
     * @param Position $position
     * @param Position $previous
     * @param int      $neutralTime
     */
    public function __construct(
        Position $position,
        Position $previous = null,
        int $type = null,
        int $neutralTime = null
    ) {
        parent::__construct($position, $previous);
        $this->type = $type ?? self::TYPE_RANDOM;
        $this->neutralTime = $neutralTime ?? self::DEFAULT_NEUTRAL_TIME;
    }

    /**
     * Get type of ghost: neutral, random or killing
     *
     * @return int
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Get if the ghost is neutral
     *
     * @return bool
     */
    public function isNeutral()
    {
        return $this->neutralTime > 0;
    }

    /**
     * Set new ghost type
     *
     * @param $newType
     */
    public function changeType($newType)
    {
        $this->type = $newType;
    }

    /**
     * Moves the player
     *
     * @param Position $position
     * @return $this
     */
    public function move(Position $position) : MazeObject
    {
        parent::move($position);
        if ($this->neutralTime > 0) {
            $this->neutralTime--;
        }
        return $this;
    }

    /**
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return array(
            'position' => $this->position()->serialize(),
            'previous' => $this->previous()->serialize(),
            'type' => $this->type(),
            'neutralTime' => $this->neutralTime
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return Ghost
     */
    public static function unserialize(array $data)
    {
        $previous = $data['previous'] ?? null;

        return new static(
            Position::unserialize($data['position']),
            $previous ? Position::unserialize($previous) : null,
            $data['type'] ?? null,
            $data['neutralTime'] ?? null
        );
    }
}
