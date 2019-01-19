<?php

namespace AppBundle\Domain\Entity\Contest;

/**
 * Domain entity: Result
 *
 * A result of a match of a round of a contest
 *
 * @package AppBundle\Domain\Entity\Contest
 */
class Result
{
    /** @var int the constants for the scores */
    const SCORE_GOLD = 10;
    const SCORE_SILVER = 5;
    const SCORE_BRONZE = 3;
    const SCORE_FORTH = 2;
    const SCORE_FIFTH = 1;

    /** @var string the competitor UUID */
    private $competitor;

    /** @var string the player UUID */
    private $player;

    /** @var int the score */
    private $score;

    /**
     * Result constructor
     *
     * @param string $competitor
     * @param string $player
     * @param int $score
     */
    public function __construct(string $competitor, string $player, int $score)
    {
        $this->competitor = $competitor;
        $this->player = $player;
        $this->score = $score;
    }

    /**
     * @return string
     */
    public function competitor(): string
    {
        return $this->competitor;
    }

    /**
     * @return string
     */
    public function player(): string
    {
        return $this->player;
    }

    /**
     * @return int
     */
    public function score(): int
    {
        return $this->score;
    }

    /**
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return array(
            'competitorUuid' => $this->competitor(),
            'playerUuid'     => $this->player(),
            'score'          => $this->score(),
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return Result
     * @throws \Exception
     */
    public static function unserialize(array $data)
    {
        $competitorUuid  = $data['competitorUuid']  ?? null;
        $playerUuid      = $data['playerUuid']      ?? null;
        $score           = $data['score']           ?? null;

        return new static(
            $competitorUuid,
            $playerUuid,
            $score
        );
    }
}
