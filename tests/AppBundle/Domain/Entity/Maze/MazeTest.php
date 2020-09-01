<?php

namespace Tests\AppBundle\Domain\Entity\Maze;

use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Maze\MazeRow;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for domain entity Maze
 *
 * @package Tests\AppBundle\Domain\Entity\Maze
 */
class MazeTest extends TestCase
{
    /**
     * Test maze constructor without cell content
     */
    public function testConstructorWithoutCells()
    {
        $maze = new Maze(3, 2);

        $this->assertEquals(3, $maze->height());
        $this->assertEquals(2, $maze->width());

        $this->assertEquals(0, $maze[0][0]->getContent());
        $this->assertEquals(0, $maze[2][0]->getContent());
        $this->assertEquals(0, $maze[1][1]->getContent());
    }

    /**
     * Test maze constructor with cell content
     */
    public function testConstructorWithCells()
    {
        $cells = [ [ 1, 2, 3 ], [ 4, 5, 6 ], ];
        $maze = new Maze(2, 3, $cells);

        $this->assertEquals(2, $maze->height());
        $this->assertEquals(3, $maze->width());

        $this->assertEquals(1, $maze[0][0]->getContent());
        $this->assertEquals(3, $maze[0][2]->getContent());
        $this->assertEquals(5, $maze[1][1]->getContent());
    }

    /**
     * Test offset exists multiple cases:
     * - Valid offset
     * - Upper bounds offset
     * - Zero (lower bounds offset)
     *
     * @param int   $height
     * @param mixed $offset
     * @testWith    [10, 5]
     *              [10, 9]
     *              [10, 0]
     */
    public function testOffsetExists(int $height, $offset)
    {
        $maze = new Maze($height, 1);
        $this->assertTrue($maze->offsetExists($offset));
    }

    /**
     * Test offset doesn't exists multiple cases
     * - Offset out of bounds
     * - Negative offset
     * - No cells
     * - Non integer value offset
     *
     * @param int   $height
     * @param mixed $offset
     * @testWith    [10, 10]
     *              [10, -1]
     *              [0, 0]
     */
    public function testOffsetDoesntExists(int $height, $offset)
    {
        $maze = new Maze($height, 1);
        $this->assertFalse($maze->offsetExists($offset));
    }

    /**
     * Test offset exists when invalid offset
     */
    public function testOffsetExistsWhenInvalidOffset()
    {
        $this->expectException(\InvalidArgumentException::class);
        $maze = new Maze(1, 1);
        $this->assertFalse($maze->offsetExists("ww"));
    }

    /**
     * Test get offset when valid offset
     */
    public function testOffsetGetWhenOffsetExist()
    {
        $maze = new Maze(8, 7);
        $row = $maze->offsetGet(5);
        $this->assertInstanceOf(MazeRow::class, $row);
        $this->assertEquals(7, $row->count());
    }

    /**
     * Test get offset when invalid offset
     */
    public function testOffsetGetWhenOffsetDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $maze = new Maze(5, 5);
        $maze->offsetGet(10);
    }

    /**
     * Test set offset when valid offset
     */
    public function testOffsetSetWhenOffsetExist()
    {
        $maze = new Maze(3, 2);
        $row = new MazeRow(2);
        $maze->offsetSet(1, $row);

        $this->assertEquals($row, $maze->offsetGet(1));
    }

    /**
     * Test set offset when invalid offset
     */
    public function testOffsetSetWhenOffsetDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $maze = new Maze(3, 2);
        $maze->offsetSet(7, new MazeRow(2));
    }

    /**
     * Test unset offset when valid offset
     */
    public function testOffsetUnsetWhenOffsetExist()
    {
        $maze = new Maze(5, 5);
        $row = $maze->offsetGet(1);
        $maze->offsetUnset(1);
        $result = $maze->offsetGet(1);

        $this->assertEquals($row, $result);
        $this->assertNotSame($row, $result);
    }

    /**
     * Test unset offset when invalid offset
     */
    public function testOffsetUnsetWhenOffsetDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $maze = new Maze(5, 5);
        $maze->offsetUnset(-1);
    }

    /**
     * Test count (\Countable interface)
     */
    public function testCount()
    {
        $maze = new Maze(4, 6);
        $this->assertEquals(4, $maze->count());
    }

    /**
     * Test current (\ArrayAccess interface)
     */
    public function testCurrent()
    {
        $maze = new Maze(5, 6);
        $maze[0] = new MazeRow(4);
        $row = $maze->current();

        $this->assertEquals(4, $row->count());
    }

    /**
     * Test next (\ArrayAccess interface)
     */
    public function testNext()
    {
        $maze = new Maze(5, 5);
        $maze[0] = new MazeRow(1);
        $maze[1] = new MazeRow(2);
        $maze->next();
        $row = $maze->current();

        $this->assertEquals(2, $row->count());
    }

    /**
     * Test key (\ArrayAccess interface)
     */
    public function testKey()
    {
        $maze = new Maze(5, 5);
        $i = 0;

        foreach ($maze as $row) {
            $this->assertEquals($i, $maze->key());
            $i++;
        }
    }

    /**
     * Test valid (\ArrayAccess interface)
     */
    public function testValid()
    {
        $maze = new Maze(3, 3);

        foreach ($maze as $row) {
            $this->assertTrue($maze->valid());
        }

        $this->assertFalse($maze->valid());
    }

    /**
     * Test rewind (\ArrayAccess interface)
     */
    public function testRewind()
    {
        $maze = new Maze(3, 3);
        $this->assertEquals(0, $maze->key());

        $maze->next();
        $this->assertEquals(1, $maze->key());

        $maze->rewind();
        $this->assertEquals(0, $maze->key());
    }

    /**
     * Test create start position happy path
     */
    public function testCreateStartPositionHappyPath()
    {
        $maze = new Maze(10, 10);
        $pos = $maze->createStartPosition();

        $this->assertGreaterThan(0, $pos->y());
        $this->assertGreaterThan(0, $pos->x());
        $this->assertLessThan(9, $pos->x());
        $this->assertLessThan(9, $pos->y());
    }

    /**
     * Test create start position when only 1 available position
     */
    public function testCreateStartPositionLimit()
    {
        $maze = new Maze(3, 3);
        $pos = $maze->createStartPosition();

        $this->assertEquals(1, $pos->y());
        $this->assertEquals(1, $pos->x());
    }

    /**
     * Test create start position when out of range
     */
    public function testCreateStartPositionWhenOutOfRange()
    {
        $this->expectException(\OutOfRangeException::class);
        $maze = new Maze(1, 1);
        $maze->createStartPosition();
    }

    /**
     * Test create start position when some cells not empty
     */
    public function testCreateStartPositionSomeCellsNotEmpty()
    {
        $cells = [
            [1, 1, 1, 1, 1],
            [1, 0, 1, 1, 1],
            [1, 1, 0, 1, 1],
            [1, 1, 1, 0, 1],
            [1, 1, 1, 1, 1]
        ];
        $maze = new Maze(5, 5, $cells);
        $pos = $maze->createStartPosition();

        $this->assertEquals($pos->x(), $pos->y());
    }

    /**
     * Test create start position when No cells not empty
     */
    public function testCreateStartPositionNoCellsNotEmpty()
    {
        $cells = [
            [1, 1, 1],
            [1, 1, 1],
            [1, 1, 1]
        ];
        $maze = new Maze(3, 3, $cells);

        $this->expectException(\OutOfRangeException::class);
        $maze->createStartPosition();
    }
}
