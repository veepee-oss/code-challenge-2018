<?php

namespace Tests\AppBundle\Domain\Entity\Maze;

use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Maze\MazeRow;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for domain entity MazeRow
 *
 * @package Tests\AppBundle\Domain\Entity\Maze
 */
class MazeRowTest extends TestCase
{
    /**
     * Test constructor multiple cases:
     * - Valid offset
     * - No cells
     *
     * @testWith    [10]
     *              [0]
     */
    public function testConstructor(int $count)
    {
        $row = new MazeRow($count);
        $this->assertEquals($count, $row->count());
    }

    /**
     * Test offset exists multiple cases:
     * - Valid offset
     * - Upper bounds offset
     * - Zero (lower bounds offset)
     *
     * @testWith    [10, 5]
     *              [10, 9]
     *              [10, 0]
     */
    public function testOffsetExists(int $count, $offset)
    {
        $row = new MazeRow($count);
        $this->assertTrue($row->offsetExists($offset));
    }


    /**
     * Test offset doesn't exists multiple cases
     * - Offset out of bounds
     * - Negative offset
     * - No cells
     * - Non integer value offset
     *
     * @testWith    [10, 10]
     *              [10, -1]
     *              [0, 0]
     *              [10, "ww"]
     */
    public function testOffsetDoesntExists(int $count, $offset)
    {
        $row = new MazeRow($count);
        $this->assertFalse($row->offsetExists($offset));
    }

    /**
     * Test get offset when valid offset
     */
    public function testOffsetGetWhenOffsetExist()
    {
        $row = new MazeRow(10);
        $cell = $row->offsetGet(5);
        $this->assertInstanceOf(MazeCell::class, $cell);
    }

    /**
     * Test get offset when invalid offset
     */
    public function testOffsetGetWhenOffsetDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $row = new MazeRow(5);
        $row->offsetGet(10);
    }

    /**
     * Test get offset when valid offset
     */
    public function testOffsetSetWhenOffsetExist()
    {
        $row = new MazeRow(7);
        $row->offsetSet(3, new MazeCell(5));
        $cell = $row->offsetGet(3);

        $this->assertEquals(5, $cell->getContent());
    }

    /**
     * Test get offset when invalid offset
     */
    public function testOffsetSetWhenOffsetDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $row = new MazeRow(3);
        $row->offsetSet(7, new MazeCell(5));
    }

    public function testOffsetUnset()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCurrent()
    {
        $this->markTestSkipped('TODO');
    }

    public function testNext()
    {
        $this->markTestSkipped('TODO');
    }

    public function testKey()
    {
        $this->markTestSkipped('TODO');
    }

    public function testValid()
    {
        $this->markTestSkipped('TODO');
    }

    public function testRewind()
    {
        $this->markTestSkipped('TODO');
    }
}
