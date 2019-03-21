<?php

namespace Tests\AppBundle\Domain\Entity\Ghost;

use AppBundle\Domain\Entity\Ghost\Ghost;
use AppBundle\Domain\Entity\Position\Position;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for domain entity Ghost
 *
 * @package Tests\AppBundle\Domain\Entity\Ghost
 */
class GhostTest extends TestCase
{
    /**
     * Unit test basic constructor
     */
    public function testBasicConstructor()
    {
        $ghost = new Ghost(new Position(0, 0));

        $this->assertEquals(Ghost::TYPE_RANDOM, $ghost->type());
        $this->assertTrue($ghost->isNeutral());
        $this->assertEquals(1, $ghost->display());
    }

    /**
     * Unit test full constructor
     */
    public function testFullConstructor()
    {
        $ghost = new Ghost(
            new Position(0, 0),
            new Position(0, 0),
            Ghost::TYPE_KILLING,
            0,
            100
        );

        $this->assertEquals(Ghost::TYPE_KILLING, $ghost->type());
        $this->assertFalse($ghost->isNeutral());
        $this->assertEquals(100, $ghost->display());
    }

    /**
     * Unit test changeType()
     */
    public function testChangeType()
    {
        $ghost = new Ghost(new Position(0, 0));
        $this->assertEquals(Ghost::TYPE_RANDOM, $ghost->type());

        $ghost->changeType(Ghost::TYPE_KILLING);
        $this->assertEquals(Ghost::TYPE_KILLING, $ghost->type());
    }

    /**
     * Unit test move() when big neutral time
     */
    public function testMoveWhenBigNeutralTime()
    {
        $ghost = new Ghost(
            new Position(0, 0),
            new Position(1, 0),
            null,
            10
        );
        $this->assertTrue($ghost->isNeutral());

        $ghost->move(new Position(-1, 0));
        $this->assertTrue($ghost->isNeutral());
    }

    /**
     * Unit test move() when small neutral time
     */
    public function testMoveWhenSmallNeutralTime()
    {
        $ghost = new Ghost(
            new Position(0, 0),
            new Position(1, 0),
            null,
            1
        );
        $this->assertTrue($ghost->isNeutral());

        $ghost->move(new Position(-1, 0));
        $this->assertFalse($ghost->isNeutral());
    }

    /**
     * Unit test move() when no neutral time
     */
    public function testMoveWhenNoNeutralTime()
    {
        $ghost = new Ghost(
            new Position(0, 0),
            new Position(1, 0),
            null,
            0
        );
        $this->assertFalse($ghost->isNeutral());

        $ghost->move(new Position(-1, 0));
        $this->assertFalse($ghost->isNeutral());
    }

    /**
     * Unit test serialize()
     */
    public function testSerialize()
    {
        $ghost = new Ghost(
            new Position(101, 102),
            new Position(103, 104),
            105,
            106,
            107
        );

        $result = $ghost->serialize();
        $expected = [
            'position' => [
                'y' => 101,
                'x' => 102
            ],
            'previous' => [
                'y' => 103,
                'x' => 104
            ],
            'type' => 105,
            'neutralTime' => 106,
            'display' => 107
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * Unit test unserialize() minimum data
     */
    public function testUnserializeMinimumData()
    {
        $ghost = Ghost::unserialize([
            'position' => [
                'y' => 101,
                'x' => 102
            ]
        ]);

        $this->assertEquals(101, $ghost->position()->y());
        $this->assertEquals(102, $ghost->position()->x());
        $this->assertEquals(101, $ghost->previous()->y());
        $this->assertEquals(102, $ghost->previous()->x());
        $this->assertEquals(Ghost::TYPE_RANDOM, $ghost->type());
        $this->assertTrue($ghost->isNeutral());
    }

    /**
     * Unit test unserialize() full data
     */
    public function testUnserializeFullData()
    {
        $ghost = Ghost::unserialize([
            'position' => [
                'y' => 201,
                'x' => 202
            ],
            'previous' => [
                'y' => 203,
                'x' => 204
            ],
            'type' => 205,
            'neutralTime' => 206,
            'display' => 207
        ]);

        $this->assertEquals(201, $ghost->position()->y());
        $this->assertEquals(202, $ghost->position()->x());
        $this->assertEquals(203, $ghost->previous()->y());
        $this->assertEquals(204, $ghost->previous()->x());
        $this->assertEquals(205, $ghost->type());
        $this->assertTrue($ghost->isNeutral());
        $this->assertEquals(207, $ghost->display());
    }
}
