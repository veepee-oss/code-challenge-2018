<?php

namespace Tests\AppBundle\Domain\Entity\Maze;

use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Position\Position;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for domain entity MazeObject
 *
 * @package Tests\AppBundle\Domain\Entity\Maze
 */
class MazeObjectTest extends TestCase
{
    /**
     * Unit test __construct()
     */
    public function testConstructorWhenOnlyCurrentPositionProvided()
    {
        $pos = new Position(3, 5);
        $obj = new MazeObject($pos);

        $position = $obj->position();
        $previous = $obj->previous();

        $this->assertEquals($pos, $position);
        $this->assertNotSame($pos, $position);

        $this->assertEquals($pos, $previous);
    }

    /**
     * Unit test __construct()
     */
    public function testConstructorWhenCurrentAndPreviousPositionProvided()
    {
        $prv = new Position(4, 9);
        $pos = new Position(3, 5);
        $obj = new MazeObject($pos, $prv);

        $position = $obj->position();
        $previous = $obj->previous();


        $this->assertEquals($pos, $position);
        $this->assertNotSame($pos, $position);

        $this->assertEquals($prv, $previous);
        $this->assertNotSame($prv, $previous);
    }

    /**
     * Unit test direction()
     *
     * @param int $y1
     * @param int $x1
     * @param int $y2
     * @param int $x2
     * @param string|null $dir
     * @testWith    [0, 0, 0, 0, null]
     *              [0, 0, -1, 0, "up"]
     *              [0, 0, 1, 0, "down"]
     *              [0, 0, 0, 1, "right"]
     *              [0, 0, 0, -1, "left"]
     */
    public function testDirection(int $y1, int $x1, int $y2, int $x2, ?string $dir)
    {
        $prv = new Position($y1, $x1);
        $pos = new Position($y2, $x2);
        $obj = new MazeObject($pos, $prv);

        $this->assertEquals($dir, $obj->direction());
    }

    /**
     * Unit test move()
     */
    public function testMove()
    {
        $prv = new Position(4, 9);
        $pos = new Position(3, 5);
        $new = new Position(1, 2);
        $obj = new MazeObject($pos, $prv);
        $obj->move($new);

        $this->assertEquals($new, $obj->position());
        $this->assertEquals($pos, $obj->previous());
    }
}
