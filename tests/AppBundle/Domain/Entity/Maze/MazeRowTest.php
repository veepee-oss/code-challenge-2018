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
     * Test walk a row using foreach
     */
    public function testArrayAccessInterface()
    {
        $row = new MazeRow(7);
        for ($i = 0; $i < 7; $i++) {
            $content = 9 - $i;
            $row[$i] = new MazeCell($content);
        }

        $content = 9;
        for ($i = 0; $i < 7; $i++) {
            $this->assertEquals($content, $row[$i]->getContent());
            $content--;
        }
    }

    /**
     * Test walk a row using foreach
     */
    public function testIteratorInterface()
    {
        $row = new MazeRow(5);
        for ($i = 0; $i < 5; $i++) {
            $content = 5 - $i;
            $row[$i] = new MazeCell($content);
        }

        $content = 5;
        foreach ($row as $cell) {
            $this->assertEquals($content, $cell->getContent());
            $content--;
        }
    }

    /**
     * Test constructor multiple cases:
     * - Valid offset
     * - No cells
     *
     * @param int $count
     * @testWith    [10]
     *              [5]
     *              [0]
     */
    public function testCountableInterface(int $count)
    {
        $row = new MazeRow($count);
        $this->assertEquals($count, $row->count());
        $this->assertEquals($count, count($row));
    }

    /**
     * Test offset exists multiple cases:
     * - Valid offset
     * - Upper bounds offset
     * - Zero (lower bounds offset)
     *
     * @param int   $count
     * @param mixed $offset
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
     * @param int   $count
     * @param mixed $offset
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
     * Test set offset when valid offset
     */
    public function testOffsetSetWhenOffsetExist()
    {
        $row = new MazeRow(7);
        $row->offsetSet(3, new MazeCell(5));
        $cell = $row->offsetGet(3);

        $this->assertEquals(5, $cell->getContent());
    }

    /**
     * Test set offset when invalid offset
     */
    public function testOffsetSetWhenOffsetDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $row = new MazeRow(3);
        $row->offsetSet(7, new MazeCell(5));
    }

    /**
     * Test unset offset when valid offset
     */
    public function testOffsetUnsetWhenOffsetExist()
    {
        $row = new MazeRow(5);
        $row->offsetSet(1, new MazeCell(3));
        $row->offsetUnset(1);

        $this->assertEquals(0, $row[1]->getContent());
    }

    /**
     * Test unset offset when invalid offset
     */
    public function testOffsetUnsetWhenOffsetDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $row = new MazeRow(5);
        $row->offsetUnset(-1);
    }

    /**
     * Test current (\ArrayAccess interface)
     */
    public function testCurrent()
    {
        $row = new MazeRow(5);
        $row[0] = new MazeCell(0x80);
        $cell = $row->current();

        $this->assertEquals(0x80, $cell->getContent());
    }

    /**
     * Test next (\ArrayAccess interface)
     */
    public function testNext()
    {
        $row = new MazeRow(5);
        $row[0] = new MazeCell(0x80);
        $row[1] = new MazeCell(0x66);
        $row->next();
        $cell = $row->current();

        $this->assertEquals(0x66, $cell->getContent());
    }

    /**
     * Test key (\ArrayAccess interface)
     */
    public function testKey()
    {
        $row = new MazeRow(5);
        $i = 0;

        foreach ($row as $cell) {
            $this->assertEquals($i, $row->key());
            $i++;
        }
    }

    /**
     * Test valid (\ArrayAccess interface)
     */
    public function testValid()
    {
        $row = new MazeRow(3);

        foreach ($row as $cell) {
            $this->assertTrue($row->valid());
        }

        $this->assertFalse($row->valid());
    }

    /**
     * Test rewind (\ArrayAccess interface)
     */
    public function testRewind()
    {
        $row = new MazeRow(3);
        $this->assertEquals(0, $row->key());

        $row->next();
        $this->assertEquals(1, $row->key());

        $row->rewind();
        $this->assertEquals(0, $row->key());
    }
}
