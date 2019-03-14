<?php

namespace Tests\AppBundle\Domain\Entity\Fire;

use AppBundle\Domain\Entity\Fire\Fire;

/**
 * Unit test for domain entity Fire
 *
 * @package Tests\AppBundle\Domain\Entity\Fire
 */
class FireTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test direction multiple cases
     * @testWith    ["fire-up", "up" ]
     *              ["fire-down", "down"]
     *              ["fire-left", "left"]
     *              ["fire-right", "right"]
     *              ["other", null]
     */
    public function testDirection(string $original, ?string $expected)
    {
        $this->assertEquals($expected, Fire::direction($original));
    }

    /**
     * Test direction multiple cases
     * @testWith    ["fire-up", true ]
     *              ["fire-down", true]
     *              ["fire-left", true]
     *              ["fire-right", true]
     *              ["other", false]
     */
    public function testFiring(string $original, bool $expected)
    {
        $this->assertEquals($expected, Fire::firing($original));
    }
}
