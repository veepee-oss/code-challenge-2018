<?php

namespace AppBundle\Domain\Entity\Maze;

use AppBundle\Domain\Entity\Position\Position;

/**
 * Domain entity Maze
 *
 * @package AppBundle\Domain\Entity\Maze
 */
class Maze implements \ArrayAccess, \Countable, \Iterator
{
    /** @var int the width of the maze */
    protected $width;

    /** @var int the height of the maze */
    protected $height;

    /** @var MazeRow[] the maze rows */
    protected $rows;

    /** @var int */
    protected $pos;

    /**
     * Maze constructor.
     *
     * @param int $height the width of the maze
     * @param int $width  the height of the maze
     * @param array $cells the initial content of the maze
     */
    public function __construct(
        int $height,
        int $width,
        array $cells = null
    ) {
        $this->validateHeight($height);
        $this->validateWidth($width);
        $this->height = $height;
        $this->width = $width;
        $this->rows = [];
        $this->pos = 0;

        if (null == $cells) {
            $cells = [];
        }

        for ($i = 0; $i < $this->height; ++$i) {
            $this->rows[$i] = new MazeRow($this->width);
            for ($j = 0; $j < $this->width; $j++) {
                $this[$i][$j]->setContent($cells[$i][$j] ?? 0);
            }
        }
    }

    /**
     * Get width
     *
     * @return int
     */
    public function width() : int
    {
        return $this->width;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function height() : int
    {
        return $this->height;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param int $offset An offset to check for.
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        $this->validateHeight($offset);
        return ($offset >= 0 && $offset < $this->height);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param int $offset The offset to retrieve.
     * @return MazeRow
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('The height ' . $offset . ' does not exists.');
        }

        return $this->rows[$offset];
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param int $offset The offset to assign the value to.
     * @param MazeRow $value The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('The height ' . $offset . ' does not exists.');
        }

        $this->rows[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param int $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('The height ' . $offset . ' doen\'t exists.');
        }
        $this->rows[$offset] = new MazeRow($this->width);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return $this->height;
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return MazeRow
     */
    public function current()
    {
        return $this[$this->pos];
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->pos;
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return int
     */
    public function key()
    {
        return $this->pos;
    }

    /**
     * Checks if current index is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean Returns true on success or false on failure.
     */
    public function valid()
    {
        return ($this->pos >= 0 && $this->pos < $this->height);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->pos = 0;
    }

    /**
     * Validates the width (integer or string containing an integer)
     *
     * @param int $width
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateWidth($width)
    {
        if (!is_numeric($width) || $width != intval($width)) {
            throw new \InvalidArgumentException('The width ' . $width . ' is not an integer.');
        }
    }

    /**
     * Validates the height (integer or string containing an integer)
     *
     * @param int $height
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateHeight($height)
    {
        if (!is_numeric($height) || $height != intval($height)) {
            throw new \InvalidArgumentException('The height ' . $height . ' is not an integer.');
        }
    }
}
