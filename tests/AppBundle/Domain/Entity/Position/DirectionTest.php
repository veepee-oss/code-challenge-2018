<?php

namespace Tests\AppBundle\Domain\Entity\Position;

use AppBundle\Domain\Entity\Position\Direction;
use AppBundle\Domain\Entity\Position\Position;
use PHPUnit\Framework\TestCase;

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
     * Test turnLeft multiple cases
     * @testWith    ["up", "left" ]
     *              ["down", "right"]
     *              ["left", "down"]
     *              ["right", "up"]
     *              ["other", null]
     */
    public function testTurnLeftMultipleCases(string $original, ?string $expected)
    {
        $this->assertEquals($expected, Direction::turnLeft($original));
    }

    /**
     * Test turnRight multiple cases
     * @testWith    ["up", "right"]
     *              ["down", "left"]
     *              ["right", "down"]
     *              ["left", "up"]
     *              ["other", null]
     */
    public function testTurnRightMultipleCases(string $original, ?string $expected)
    {
        $this->assertEquals($expected, Direction::turnRight($original));
    }

    /**
     * Test turnBack multiple cases
     * @testWith    ["up", "down"]
     *              ["down", "up"]
     *              ["right", "left"]
     *              ["left", "right"]
     *              ["other", null]
     */
    public function testturnBackMultipleCases(string $original, ?string $expected)
    {
        $this->assertEquals($expected, Direction::turnBack($original));
    }
}
