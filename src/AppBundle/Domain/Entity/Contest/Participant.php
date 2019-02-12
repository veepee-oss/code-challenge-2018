<?php

namespace AppBundle\Domain\Entity\Contest;

/**
 * Domain entity: Participant
 *
 * A participant of a round of a contest
 *
 * @package AppBundle\Domain\Entity\Contest
 */
class Participant
{
    /** @var Competitor the competitor data */
    private $competitor;

    /** @var int the score achieved */
    private $score;

    /** @var bool whether if it is classified for next round */
    private $classified;

    /**
     * Participant constructor
     *
     * @param Competitor $competitor
     * @param int $score
     * @param bool $classified
     */
    public function __construct(Competitor $competitor, ?int $score, ?bool $classified)
    {
        $this->competitor = $competitor;
        $this->score = $score ?? 0;
        $this->classified = $classified ?? false;
    }

    /**
     * @return Competitor
     */
    public function competitor(): Competitor
    {
        return $this->competitor;
    }

    /**
     * @return int
     */
    public function score(): int
    {
        return $this->score;
    }

    /**
     * @return Participant
     */
    public function resetScore(): Participant
    {
        $this->score = 0;
        return $this;
    }

    /**
     * @param int $score
     * @return Participant
     */
    public function addScore(int $score): Participant
    {
        $this->score += $score;
        return $this;
    }

    /**
     * @return bool
     */
    public function classified(): bool
    {
        return $this->classified;
    }

    /**
     * @param bool $classified
     * @return Participant
     */
    public function setClassified(bool $classified): Participant
    {
        $this->classified = $classified;
        return $this;
    }

    /**
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return array(
            'competitorUuid'  => $this->competitor()->uuid(),
            'contestUuid'     => $this->competitor()->contest(),
            'competitorEmail' => $this->competitor()->email(),
            'competitorName'  => $this->competitor()->name(),
            'competitorUrl'   => $this->competitor()->url(),
            'score'           => $this->score(),
            'classified'      => $this->classified()
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return Participant
     * @throws \Exception
     */
    public static function unserialize(array $data)
    {
        $competitorUuid  = $data['competitorUuid']  ?? null;
        $contestUuid     = $data['contestUuid']     ?? null;
        $competitorEmail = $data['competitorEmail'] ?? null;
        $competitorName  = $data['competitorName']  ?? null;
        $competitorUrl   = $data['competitorUrl']   ?? null;
        $score           = $data['score']           ?? null;
        $classified      = $data['classified']      ?? null;

        $competitor = new Competitor(
            $competitorUuid,
            $contestUuid,
            $competitorEmail,
            $competitorName,
            $competitorUrl,
            true,
            null
        );

        return new static(
            $competitor,
            $score,
            $classified
        );
    }
}
