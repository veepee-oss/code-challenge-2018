<?php

namespace AppBundle\Entity;

use AppBundle\Domain\Entity\Contest as DomainContest;
use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine Entity: Match
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="match")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MatchRepository")
 *
 * @package AppBundle\Entity
 */
class Match
{
    /**
     * Primary key
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * UUID of the match
     *
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, unique=true, nullable=false)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="round_uuid", type="string", length=36, nullable=false)
     */
    private $roundUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="game_uuid", type="string", length=36, nullable=false)
     */
    private $gameUuid;

    /**
     * Status of the match
     *
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * All the data of the results
     *
     * @var array
     *
     * @ORM\Column(name="results", type="json_array")
     */
    private $results;

    /**
     * Match constructor.
     *
     * @param $source
     */
    public function __construct($source = null)
    {
        if (null === $source) {
            $this->id = null;
            $this->uuid = null;
            $this->roundUuid = null;
            $this->gameUuid = null;
            $this->status = null;
            $this->results = [];
        } elseif ($source instanceof Match) {
            $this->id = $source->getId();
            $this->uuid = $source->getUuid();
            $this->roundUuid = $source->getRoundUuid();
            $this->gameUuid = $source->getGameUuid();
            $this->status = $source->getStatus();
            $this->results = $source->getResults();
        } elseif ($source instanceof DomainContest\Match) {
            $this->id = null;
            $this->fromDomainEntity($source);
        }
    }

    /**
     * Convert entity to a domain match
     *
     * @return DomainContest\Match
     * @throws \Exception
     */
    public function toDomainEntity()
    {
        $resultsArray = [];
        foreach ($this->results as $result) {
            $resultsArray[] = DomainContest\Result::unserialize($result);
        }

        return new DomainContest\Match(
            $this->uuid,
            $this->roundUuid,
            $this->gameUuid,
            $this->status,
            $resultsArray
        );
    }

    /**
     * Update entity from a domain match
     *
     * @param DomainContest\Match $match
     * @return $this
     */
    public function fromDomainEntity(DomainContest\Match $match)
    {
        $this->setUuid($match->uuid());
        $this->setRoundUuid($match->roundUuid());
        $this->setGameUuid($match->gameUuid());
        $this->setStatus($match->status());
        $this->setResults($match->results());

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Match
     */
    public function setId(int $id): Match
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return Match
     */
    public function setUuid(string $uuid): Match
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoundUuid(): string
    {
        return $this->roundUuid;
    }

    /**
     * @param string $roundUuid
     * @return Match
     */
    public function setRoundUuid(string $roundUuid): Match
    {
        $this->roundUuid = $roundUuid;

        return $this;
    }

    /**
     * @return string
     */
    public function getGameUuid(): string
    {
        return $this->gameUuid;
    }

    /**
     * @param string $gameUuid
     * @return Match
     */
    public function setGameUuid(string $gameUuid): Match
    {
        $this->gameUuid = $gameUuid;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Match
     */
    public function setStatus(int $status): Match
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param array $results
     * @return Match
     */
    public function setResults(array $results): Match
    {
        $this->results = [];
        if (null !== $results && count($results) > 0) {
            foreach ($results as $result) {
                if ($result instanceof DomainContest\Result) {
                    $this->results[] = $result->serialize();
                } else {
                    $this->results[] = $result;
                }
            }
        }
        return $this;
    }
}
