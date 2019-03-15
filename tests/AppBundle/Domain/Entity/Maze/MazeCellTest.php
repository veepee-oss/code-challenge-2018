<?php

namespace Tests\AppBundle\Domain\Entity\Maze;

use AppBundle\Domain\Entity\Maze\MazeCell;

/**
 * Unit test for domain entity MazeCell
 *
 * @package Tests\AppBundle\Domain\Entity\Maze
 */
class MazeCellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get cell content
     */
    public function testGetContent()
    {
        $cell = new MazeCell(3);
        $this->assertEquals(3, $cell->getContent());
    }

    /**
     * Test set cell content
     */
    public function testSetContent()
    {
        $cell = new MazeCell(5);
        $this->assertEquals(5, $cell->getContent());

        $cell->setContent(9);
        $this->assertEquals(9, $cell->getContent());
    }

    /**
     * Test cell is empty when the content is 0
     */
    public function testIsEmpty()
    {
        $cell = new MazeCell(0);
        $this->assertTrue($cell->isEmpty());
    }

    /**
     * Test cell is empty when the content isn't 0
     */
    public function testIsNotEmpty()
    {
        $cell = new MazeCell(7);
        $this->assertFalse($cell->isEmpty());
    }
}
