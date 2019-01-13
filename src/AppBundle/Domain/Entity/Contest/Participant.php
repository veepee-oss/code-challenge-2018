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
     * @return bool
     */
    public function classified(): bool
    {
        return $this->classified;
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
        $competitorUrl   = $data['competitorUrl']   ?? null;
        $score           = $data['score']           ?? null;
        $classified      = $data['classified']      ?? null;

        $competitor = new Competitor(
            $competitorUuid,
            $contestUuid,
            $competitorEmail,
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
