<?php

namespace Tests\AppBundle\Domain\Entity\Player;

use AppBundle\Domain\Entity\Fire\Fire;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Position;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for domain entity Player
 *
 * @package Tests\AppBundle\Domain\Entity\Player
 */
class PlayerTest extends TestCase
{
    /**
     * Unit test initial status is regular
     */
    public function testInitialStatusIsRegular()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertEquals(Player::STATUS_REGULAR, $player->status());
    }

    /**
     * Unit test initial status count is zero
     */
    public function testInitialStatusCountIsZero()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertEquals(0, $player->statusCount());
    }

    /**
     * Unit test initial value of firingDir is null
     */
    public function testInitalValueOfFiringDirIsNull()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertEquals(null, $player->firingDir());
    }

    /**
     * Unit test initial fire range is zero
     */
    public function testInitialValueOfFireRangeIsZero()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertEquals(0, $player->fireRange());
    }

    /**
     * Unit test initial score is zero
     */
    public function testInitalScoreIsZero()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertEquals(0, $player->score());
    }

    /**
     * Unit test initial timestamp is set
     */
    public function testInitialTimestampIsSet()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $timestamp = $player->timestamp();
        $this->assertInstanceOf(\DateTime::class, $timestamp);
    }

    /**
     * Unit test initial value of UUID is not null
     */
    public function testInitialValueOfUuidIsNotNull()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertNotNull($player->uuid());
    }

    /**
     * Unit test initial value of name is not null
     */
    public function testInitialValueOfNameIsNotNull()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertNotNull($player->name());
    }

    public function testInitalValueOfEmailIsNull()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertNull($player->email());
    }

    /**
     * Unit test get URL is correct
     */
    public function testGetUrlIsCorrect()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertEquals($url, $player->url());
    }

    /**
     * Unit test initial value of isRespawned is false
     */
    public function testInitialValueOfIsRespawnedIsFalse()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertFalse($player->isRespawned());
    }

    /**
     * Unit test initial value of isPowered is false
     */
    public function testInitialValueOfIsPoweredIsFalse()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertFalse($player->isPowered());
    }

    /**
     * Unit test initial value of isPowered is false
     */
    public function testInitialValueOfIsFiringIsFalse()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertFalse($player->isFiring());
    }

    /**
     * Unit test initial value of isReloading is false
     */
    public function testInitialValueOfIsReloadingIsFalse()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertFalse($player->isReloading());
    }

    /**
     * Unit test initial value of isKilled is false
     */
    public function testInitialValueOfIsKilledIsFalse()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertFalse($player->isKilled());
    }

    /**
     * Unit test setPlayerIds changes the player's name and email
     */
    public function testSetPlayerIdsChangesTheNameAndEmail()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->setPlayerIds("the_name", "email@server.com");

        $this->assertEquals("the_name", $player->name());
        $this->assertEquals("email@server.com", $player->email());
    }

    /**
     * Unit test setPlayerIds accept null email
     */
    public function testSetPlayerIdsAcceptNullEmail()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->setPlayerIds("the_name", null);

        $this->assertEquals("the_name", $player->name());
        $this->assertNull($player->email());
    }

    /**
     * Unit test all statuses when player gets powered
     */
    public function testAllStatusesWhenPlayerGetsPowered()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->powered(5);

        $this->assertTrue($player->isPowered());
        $this->assertEquals(Player::STATUS_POWERED, $player->status());
        $this->assertEquals(5, $player->statusCount());
    }

    /**
     * Unit test all statuses when player gets killed
     */
    public function testAllStatusesWhenPlayerGetsKilled()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->killed(6);

        $this->assertTrue($player->isKilled());
        $this->assertEquals(Player::STATUS_KILLED, $player->status());
        $this->assertEquals(6, $player->statusCount());
    }

    /**
     * Unit test all statuses when player fires
     */
    public function testAllStatusesWhenPlayerFires()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->fire(Fire::DOWN, 3);

        $this->assertTrue($player->isFiring());
        $this->assertTrue($player->isReloading());
        $this->assertEquals(Player::STATUS_RELOADING, $player->status());
        $this->assertEquals(3, $player->statusCount());
    }

    /**
     * Unit test fireDirAtPosition with standard fire range multiple cases
     *
     * @param int $yIni
     * @param int $xIni
     * @param string $fireDir
     * @param int $yPos
     * @param int $xPos
     * @param bool $expected
     * @throws \Exception
     * @testWith    [0, 0, "fire-right", 0, 0, true]
     *              [0, 0, "fire-right", 0, 3, true]
     *              [0, 0, "fire-left", 0, -3, true]
     *              [0, 0, "fire-down", 3, 0, true]
     *              [0, 0, "fire-up", -3, 0, true]
     *              [0, 0, "fire-right", 0, -3, false]
     *              [0, 0, "fire-right", 3, 0, false]
     *              [0, 0, "fire-right", -3, 0, false]
     *              [0, 0, "fire-right", 10, 0, false]
     *              [0, 0, null, 0, 0, false]
     */
    public function testFireDirAtPositionWithStandardFireRange(
        int $yIni,
        int $xIni,
        ?string $fireDir,
        int $yPos,
        int $xPos,
        bool $expected
    ) {
        $url = "https://www.google.com/";
        $pos = new Position($yIni, $xIni);
        $player = new Player($url, $pos);
        if (null !== $fireDir) {
            $player->fire($fireDir);
        }

        $result = $player->fireDirAtPosition(new Position($yPos, $xPos));
        $this->assertTrue($expected == !is_null($result));
    }

    /**
     * Unit test fireDirAtPosition after setFireRange multiple cases
     *
     * @param int $fireRange
     * @param int $yPos
     * @param int $xPos
     * @param bool $expected
     * @throws \Exception
     * @testWith    [10, 0, 0, true]
     *              [10, 0, 5, true]
     *              [10, 0, 10, true]
     *              [10, 0, 11, false]
     *              [0, 0, 0, true]
     *              [0, 0, 1, false]
     */
    public function testFireDirAtPositionChangingFireRange(int $fireRange, int $yPos, int $xPos, bool $expected)
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->fire(Fire::RIGHT);
        $player->setFireRange($fireRange);

        $result = $player->fireDirAtPosition(new Position($yPos, $xPos));
        $this->assertTrue($expected == !is_null($result));
    }

    /**
     *  Unit test move with default status does not change the status
     */
    public function testMoveDefaultStatusDoesNotChangeTheStatus()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $this->assertEquals(Player::STATUS_REGULAR, $player->status());
        $newPos = new Position(1, 0);

        $player->move($newPos);

        $this->assertEquals($newPos, $player->position());
        $this->assertEquals($pos, $player->previous());
        $this->assertEquals(Player::STATUS_REGULAR, $player->status());
    }

    /**
     *  Unit test move decreases the status count
     */
    public function testMoveDecreasesTheStatusCount()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->powered(5);

        $this->assertEquals(Player::STATUS_POWERED, $player->status());
        $this->assertEquals(5, $player->statusCount());

        $player->move(new Position(1, 0));

        $this->assertEquals(Player::STATUS_POWERED, $player->status());
        $this->assertEquals(4, $player->statusCount());
    }

    /**
     *  Unit test move changes the status when statusCount reaches 0
     */
    public function testMoveChangesTheStatusWhenStatusCountReachesZero()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->powered(1);

        $this->assertEquals(Player::STATUS_POWERED, $player->status());
        $this->assertEquals(1, $player->statusCount());

        $player->move(new Position(1, 0));

        $this->assertEquals(Player::STATUS_REGULAR, $player->status());
        $this->assertEquals(0, $player->statusCount());
    }

    /**
     *  Unit test move respawns player when is killed and statusCount reaches 0
     */
    public function testMoveRespawnsPlayerWhenIsKilledAndStatusCountReachesZero()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->killed(1);

        $this->assertEquals(Player::STATUS_KILLED, $player->status());
        $this->assertEquals(1, $player->statusCount());
        $this->assertFalse($player->isRespawned());

        $player->move(new Position(1, 0));

        $this->assertEquals(Player::STATUS_RELOADING, $player->status());
        $this->assertEquals(1, $player->statusCount());
        $this->assertTrue($player->isRespawned());

        $player->move(new Position(1, 0));

        $this->assertEquals(Player::STATUS_REGULAR, $player->status());
        $this->assertEquals(0, $player->statusCount());
    }

    /**
     * @param int $yStart
     * @param int $xStart
     * @param int $yPos
     * @param int $xPos
     * @param string $direction
     * @throws \Exception
     * @testWith    [0, 0, 0, 1, "right"]
     *              [0, 0, 0, -1, "left"]
     *              [0, 0, 1, 0, "down"]
     *              [0, 0, -1, 0, "up"]
     *              [0, 0, 0, 0, null]
     */
    public function testDirectionWhenNotFiring(int $yStart, int $xStart, int $yPos, int $xPos, ?string $direction)
    {
        $url = "https://www.google.com/";
        $pos = new Position($yStart, $xStart);
        $player = new Player($url, $pos);
        $player->move(new Position($yPos, $xPos));

        $this->assertEquals($direction, $player->direction());
    }

    /**
     * @param int $yStart
     * @param int $xStart
     * @param string $fireDir
     * @param string $direction
     * @throws \Exception
     * @testWith    [0, 0, "fire-right", "right"]
     *              [0, 0, "fire-left", "left"]
     *              [0, 0, "fire-down", "down"]
     *              [0, 0, "fire-up", "up"]
     *              [0, 0, "xxx", null]
     */
    public function testDirectionWhenFiring(int $yStart, int $xStart, string $fireDir, ?string $direction)
    {
        $url = "https://www.google.com/";
        $pos = new Position($yStart, $xStart);
        $player = new Player($url, $pos);
        $player->fire($fireDir);

        $this->assertEquals($direction, $player->direction());
    }

    /**
     * Unit test addScore adds score and changes the timestamp
     */
    public function testAddScoreWorksAndChangesTimestamp()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);

        $timestamp = $player->timestamp();
        $player->addScore(33);

        $this->assertEquals(33, $player->score());
        $this->assertGreaterThan($timestamp, $player->timestamp());
    }

    /**
     * Unit test resetAll
     */
    public function testResetAll()
    {
        $url = "https://www.google.com/";
        $pos = new Position(0, 0);
        $player = new Player($url, $pos);
        $player->move(new Position(7, 6));
        $player->fire(Fire::RIGHT, 10);
        $timestamp = $player->timestamp();
        $player->resetAll($pos);

        $this->assertEquals(Fire::NONE, $player->firingDir());
        $this->assertEquals(0, $player->fireRange());
        $this->assertEquals(Player::STATUS_REGULAR, $player->status());
        $this->assertEquals(0, $player->statusCount());
        $this->assertEquals(0, $player->score());
        $this->assertGreaterThan($timestamp, $player->timestamp());
    }

    /**
     * Unit test serialize
     */
    public function testSerialize()
    {
        $url = "https://www.google.com/";
        $pos = new Position(101, 102);
        $prev = new Position(103, 104);
        $player = new Player($url, $pos, $prev);
        $player->fire(Fire::DOWN, 105);
        $player->setFireRange(106);
        $player->addScore(107);
        $player->setPlayerIds('player_name', 'player@server.com');

        $result = $player->serialize();
        $expected = [
            'position' => [
                'y' => 101,
                'x' => 102
            ],
            'previous' => [
                'y' => 103,
                'x' => 104
            ],
            'status' => 4,
            'status_count' => 105,
            'firing_dir' => 'fire-down',
            'fire_range' => 106,
            'score' => 107,
            'timestamp' => $player->timestamp()->format('YmdHisu'),
            'uuid' => $player->uuid(),
            'name' => 'player_name',
            'email' => 'player@server.com',
            'url' => 'https://www.google.com/'
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * Unit test unserialize
     */
    public function testUnserialize()
    {
        $player = Player::unserialize($expected = [
            'position' => [
                'y' => 201,
                'x' => 202
            ],
            'previous' => [
                'y' => 203,
                'x' => 204
            ],
            'status' => 205,
            'status_count' => 206,
            'firing_dir' => '207',
            'fire_range' => 208,
            'score' => 209,
            'timestamp' => '20190101000000000000',
            'uuid' => '210',
            'name' => '211',
            'email' => '212@server.com',
            'url' => 'https://www.213.com/'
        ]);

        $this->assertEquals(201, $player->position()->y());
        $this->assertEquals(202, $player->position()->x());
        $this->assertEquals(203, $player->previous()->y());
        $this->assertEquals(204, $player->previous()->x());
        $this->assertEquals(205, $player->status());
        $this->assertEquals(206, $player->statusCount());
        $this->assertEquals('207', $player->firingDir());
        $this->assertEquals(208, $player->fireRange());
        $this->assertEquals(209, $player->score());
        $this->assertEquals('20190101000000000000', $player->timestamp()->format('YmdHisu'));
        $this->assertEquals('210', $player->uuid());
        $this->assertEquals('211', $player->name());
        $this->assertEquals('212@server.com', $player->email());
        $this->assertEquals('https://www.213.com/', $player->url());

    }
}
