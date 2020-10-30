<?php

namespace AppBundle\Domain\Repository;

use AppBundle\Domain\Entity\Contest\Match;

/**
 * Interface to a repository of Match entities
 *
 * @package AppBundle\Domain\Repository
 */
interface MatchRepositoryInterface
{
    /**
     * Reads a match from the database
     *
     * @param string $uuid
     * @return Match|null
     */
    public function readMatch(string $uuid): ?Match;

    /**
     * @param string $roundUuid
     * @return Match[]
     */
    public function readMatches(string $roundUuid): array;

    /**
     * Persists a match in the database
     *
     * @param Match $match
     * @param bool $autoFlush
     * @return MatchRepositoryInterface
     */
    public function persistMatch(Match $match, bool $autoFlush = false): MatchRepositoryInterface;

    /**
     * Persists an array of matches in the database
     *
     * @param Match[] $matches
     * @param bool $autoFlush
     * @return MatchRepositoryInterface
     */
    public function persistMatches(array $matches, bool $autoFlush = false): MatchRepositoryInterface;

    /**
     * Removes a match
     *
     * @param mixed $match
     * @return MatchRepositoryInterface
     */
    public function removeMatch($match): MatchRepositoryInterface;
}
