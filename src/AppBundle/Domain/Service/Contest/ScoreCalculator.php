<?php

namespace AppBundle\Domain\Service\Contest;

use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Entity\Contest\Result;

/**
 * Service to calculate the score of each player of a match or round
 *
 * @package AppBundle\Domain\Service\Contest
 */
class ScoreCalculator implements ScoreCalculatorInterface
{
    /**
     * Calculates the score of each player for a match
     *
     * @param Match $match
     * @return void
     * @throws ScoreCalculatorException
     */
    public function calculateMatchScore(Match $match): void
    {
        $game = $match->game();
        if (null == $game) {
            throw new ScoreCalculatorException('$game is mandatory!');
        }

        if (!$game->finished()) {
            throw new ScoreCalculatorException('The game is not finished!');
        }

        $players = $game->classification();

        $awards = Result::awards();
        $countAwards = count($awards);
        $currentAward = 0;

        $prevPlayerScore = PHP_INT_MAX;
        $prevAward = 0;

        foreach ($players as $player) {
            $award = ($currentAward < $countAwards) ? $awards[$currentAward++] : 0;
            if ($prevPlayerScore == $player->score()) {
                $award = $prevAward;
            }
            $match->setResultScore($player->uuid(), $award);
            $prevPlayerScore = $player->score();
            $prevAward = $award;
        }

        if (!$match->isFinished()) {
            $match->setFinished();
        }
    }
}
