<?php

namespace AppBundle\Service\Contest;

use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Entity\Contest\Participant;
use AppBundle\Domain\Entity\Contest\Round;
use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\Contest\MatchManagerInterface;
use AppBundle\Domain\Service\GameEngine\GameEngine;
use AppBundle\Domain\Service\MazeBuilder\MazeBuilderInterface;

/**
 * Service to create the matches for a round, including the games
 *
 * @package AppBundle\Service\Contest
 */
class MatchManager implements MatchManagerInterface
{
    /** @var MazeBuilderInterface */
    private $mazeBuilder;

    /** @var GameEngine */
    private $gameEngine;

    /** @var int Constant */
    const MAX_PLAYERS_PER_MATCH = 9;

    /**
     * Creates all the matches of a round
     *
     * @param Round $round
     * @return Match[]
     * @throws \Exception
     */
    public function createMatches(Round $round): array
    {
        /** @var Match[] $matches */
        $matches = [];

        $participants = $round->participants();
        $numPlayers = count($participants);
        $numGroups = ceil((float) $numPlayers / self::MAX_PLAYERS_PER_MATCH);
        $maxPlayers = ceil ((float) $numPlayers / $numGroups);
        $maxGroups =  $numPlayers % $numGroups;
        $minPlayers = $maxPlayers - ($maxGroups > 0 ? 1 : 0);
        $minGroups = $numGroups - $maxGroups;

        for ($groupNum = 1; $groupNum <= $round->numMatches(); ++$groupNum) {
            $matchNum = 1;
            shuffle($participants);
            for ($i = 0; $i < $minGroups; ++$i) {
                $match = $this->createMatch($round, $participants, $minPlayers, $groupNum, $matchNum++);
                $matches[] = $match;

            }
            for ($i = 0; $i < $maxGroups; ++$i) {
                $match = $this->createMatch($round, $participants, $maxPlayers, $groupNum, $matchNum++);
                $matches[] = $match;
            }
        }

        return $matches;
    }

    /**
     * @param Round         $round
     * @param Participant[] $participants
     * @param int           $numPlayers
     * @param int           $groupNum
     * @param int           $matchNum
     * @return Match
     * @throws \Exception
     */
    protected function createMatch(
        Round $round,
        array $participants,
        int $numPlayers,
        int $groupNum,
        int $matchNum
    ): Match {
        /** @var Maze $maze */
        $maze = $this->mazeBuilder->buildRandomMaze(
            $round->height(),
            $round->width()
        );

        /** @var Player[] $players */
        $players = [];
        for ($i = 0; $i < $numPlayers; ++$i) {
            /** @var Participant $participant */
            $participant = next($participants);

            $player = new Player(
                $participant->competitor()->url(),
                $maze->createStartPosition()
            );

            $player->setPlayerIds(
                $participant->competitor()->email(),
                $participant->competitor()->email()
            );

            $players[] = $player;
        }

        $name = $round->name()
            . ' - Group ' . $groupNum
            . ' - Match ' . $matchNum;

        /** @var Game $game */
        $game = $this->gameEngine->create(
            $maze,
            $players,
            $round->ghostRate(),
            $round->minGhosts(),
            $round->limit(),
            $name
        );

        $match = new Match(
            null,
            $round->uuid(),
            $game->uuid(),
            null,
            []
        );

        $match->setGame($game);

        return $match;
    }
}
