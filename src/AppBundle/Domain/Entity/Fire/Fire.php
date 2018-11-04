<?php

namespace AppBundle\Domain\Entity\Fire;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Position\Direction;

/**
 * Domain entity Fire
 *
 * @package AppBundle\Domain\Entity\Fire
 */
class Fire
{
    /** @var string Fire directions */
    const UP = 'fire-up';
    const DOWN = 'fire-down';
    const LEFT = 'fire-left';
    const RIGHT = 'fire-right';
    const NONE = null;

    /** @var int Default fire range */
    const DEFAULT_FIRE_RANGE = Game::DEFAULT_VIEW_RANGE;

    /**
     * Get if is firing
     *
     * @param null|string $fire
     * @return bool
     */
    public static function firing(?string $fire) : bool
    {
        switch ($fire) {
            case self::UP:
            case self::DOWN:
            case self::LEFT:
            case self::RIGHT:
                return true;

            default:
                break;
        }
        return false;
    }

    /**
     * Get the fire direction
     *
     * @param null|string $fire
     * @return null|string
     */
    public static function direction(?string $fire) : ?string
    {
        $dir = substr($fire, 5);
        switch ($dir) {
            case Direction::UP:
            case Direction::DOWN:
            case Direction::LEFT:
            case Direction::RIGHT:
                return $dir;

            default:
                break;
        }

        return self::NONE;
    }
}
