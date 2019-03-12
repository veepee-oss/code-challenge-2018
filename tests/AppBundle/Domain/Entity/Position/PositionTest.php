<?php

namespace Tests\AppBundle\Domain\Entity\Position;

use AppBundle\Domain\Entity\Position\Direction;
use AppBundle\Domain\Entity\Position\Position;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for domain entity Position
 *
 * @package Tests\AppBundle\Domain\Entity\Position
 */
class PositionTest extends TestCase
{
    /**
     * Test getX and getY
     */
    public function testGetXY()
    {
        $pos = new Position(3, 4);

        $this->assertEquals(3, $pos->y());
        $this->assertEquals(4, $pos->x());
    }

    /**
     * Test two positions are equal if they have the same components
     */
    public function testEqualsWhenEquals()
    {
        $pos1 = new Position(1, 2);
        $pos2 =  new Position(1, 2);

        $this->assertTrue($pos1->equals($pos2));
    }

    /**
     * Test two positions are equal when conning the position
     */
    public function testEqualsWhenClonning()
    {
        $pos1 = new Position(1, 2);
        $pos2 =  clone $pos1;

        $this->assertTrue($pos1->equals($pos2));
    }

    /**
     * Test two positions are not equal if they don't have the same components
     */
    public function testEqualsWhenDifferent()
    {
        $pos1 = new Position(1, 2);
        $pos2 =  new Position(2, 2);

        $this->assertFalse($pos1->equals($pos2));
    }

    /**
     * Test move up
     */
    public function testMoveUp()
    {
        $pos = new Position(0, 0);
        $result = Position::move($pos, Direction::UP);

        $this->assertEquals(0, $pos->y());
        $this->assertEquals(-1, $result->y());
    }

    /**
     * Test move down
     */
    public function testMoveDown()
    {
        $pos = new Position(0, 0);
        $result = Position::move($pos, Direction::DOWN);

        $this->assertEquals(0, $pos->y());
        $this->assertEquals(1, $result->y());
    }

    /**
     * Test move left
     */
    public function testMoveLeft()
    {
        $pos = new Position(0, 0);
        $result = Position::move($pos, Direction::LEFT);

        $this->assertEquals(0, $pos->x());
        $this->assertEquals(-1, $result->x());
    }

    /**
     * Test move right
     */
    public function testMoveRight()
    {
        $pos = new Position(0, 0);
        $result = Position::move($pos, Direction::RIGHT);

        $this->assertEquals(0, $pos->x());
        $this->assertEquals(1, $result->x());
    }

    /**
     * Test move nowhere
     */
    public function testMoveNowhere()
    {
        $pos = new Position(0, 0);
        $result = Position::move($pos, null);

        $this->assertEquals(0, $pos->x());
        $this->assertEquals(0, $result->x());
    }

    /**
     * Test serialize()
     */
    public function testSerialize()
    {
        $pos = new Position(2, 7);
        $expected = [
            'y' => 2,
            'x' => 7
        ];

        $this->assertEquals($expected, $pos->serialize());
    }

    /**
     * Test unserialize
     */
    public function testUnserializeWhenActualValues()
    {
        $pos = new Position(9, 3);
        $source = [
            'y' => 9,
            'x' => 3
        ];


        $this->assertTrue($pos->equals(Position::unserialize($source)));
    }

    /**
     * Test unserialize
     */
    public function testUnserializeWhenEmpty()
    {
        $pos = new Position(0, 0);
        $source = [];


        $this->assertTrue($pos->equals(Position::unserialize($source)));
    }
}
