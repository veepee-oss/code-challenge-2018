<?php

namespace Tests\AppBundle\Domain\Entity\Position;

use AppBundle\Domain\Entity\Position\Direction;
use AppBundle\Domain\Entity\Position\Position;
use PHPUnit\Framework\TestCase;

/**
/**
 * Unit test for domain entity Direction
 *
 * @package Tests\AppBundle\Domain\Entity\Position
 */
class DirectionTest extends TestCase
{
    /**
     * Test getDirections
     */
    public function testGetDirections()
    {
        $result = Direction::directions();

        $this->assertInternalType('array', $result);
        $this->assertCount(4, $result);
    }

    /**
     * Test compute when going down
     */
    public function testComputeDown()
    {
        $curr = new Position(0, 0);
        $prev = new Position(-5, 0);
        $result = Direction::compute($curr, $prev);

        $this->assertEquals(Direction::DOWN, $result);
    }

    /**
     * Test compute when going up
     */
    public function testComputeUp()
    {
        $curr = new Position(0, 0);
        $prev = new Position(5, 0);
        $result = Direction::compute($curr, $prev);

        $this->assertEquals(Direction::UP, $result);
    }

    /**
     * Test compute when going left
     */
    public function testComputeLeft()
    {
        $curr = new Position(0, 0);
        $prev = new Position(0, 5);
        $result = Direction::compute($curr, $prev);

        $this->assertEquals(Direction::LEFT, $result);
    }

    /**
     * Test compute when going right
     */
    public function testComputeRight()
    {
        $curr = new Position(0, 0);
        $prev = new Position(0, -5);
        $result = Direction::compute($curr, $prev);

        $this->assertEquals(Direction::RIGHT, $result);
    }

    /**
     * Test compute when the same position
     */
    public function testComputeSamePosition()
    {
        $curr = new Position(0, 0);
        $prev = new Position(0, 0);
        $result = Direction::compute($curr, $prev);

        $this->assertEquals(Direction::STOPPED, $result);
    }

    /**
     * Test turnLeft when heading up
     */
    public function testTurnLeftFromUpReturnsLeft()
    {
        $this->assertEquals(Direction::LEFT, Direction::turnLeft(Direction::UP));
    }

    /**
     * Test turnLeft when heading down
     */
    public function testTurnLeftFromDonwReturnsRight()
    {
        $this->assertEquals(Direction::RIGHT, Direction::turnLeft(Direction::DOWN));
    }

    /**
     * Test turnRight when heading up
     */
    public function testTurnRightFromUpReturnsRight()
    {
        $this->assertEquals(Direction::RIGHT, Direction::turnRight(Direction::UP));
    }

    /**
     * Test turnRight when heading down
     */
    public function testTurnRightFromDownReturnsLeft()
    {
        $this->assertEquals(Direction::LEFT, Direction::turnRight(Direction::DOWN));
    }

    /**
     * Test turnBack when heading up
     */
    public function testTurnBackFromUpReturnsDown()
    {
        $this->assertEquals(Direction::DOWN, Direction::turnBack(Direction::UP));
    }

    /**
     * Test turnBack when heading down
     */
    public function testTurnBackFromDownReturnsUp()
    {
        $this->assertEquals(Direction::UP, Direction::turnBack(Direction::DOWN));
    }
}
