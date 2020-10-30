<?php

namespace AppBundle\Domain\Entity\Contest;

use Ramsey\Uuid\Uuid;

/**
 * Domain entity: Contest
 *
 * A contest is a competition event. It has competitors and rounds.
 *
 * @package AppBundle\Domain\Entity\Contest
 */
class Contest
{
    /** @var string the UUID of the contest */
    protected $uuid;

    /** @var string the name of the contest */
    protected $name;

    /** @var string the description of the contest */
    protected $description;

    /** @var string the regular expression to restricting the emails */
    protected $emailRestrictionsRegex;

    /** @var \DateTime the start date & time for registering */
    protected $registrationStartDate;

    /** @var \DateTime the end date & time for registration */
    protected $registrationEndDate;

    /** @var \DateTime the start date & time of the contest */
    protected $contestStartDate;

    /** @var \DateTime the end date & time of the contest */
    protected $contestEndDate;

    /** @var int|null the max competitors */
    protected $maxCompetitors;

    /** @var int|null the current competitors count - externally set */
    protected $countCompetitors;

    /**
     * Contest constructor
     *
     * @param string|null    $uuid
     * @param string         $name
     * @param string|null    $description
     * @param string|null    $emailRestrictionsRegex
     * @param \DateTime      $registrationStartDate
     * @param \DateTime      $registrationEndDate
     * @param \DateTime|null $contestStartDate
     * @param \DateTime|null $contestEndDate
     * @param int|null       $maxCompetitors
     * @throws \Exception
     */
    public function __construct(
        ?string $uuid,
        string $name,
        ?string $description,
        ?string $emailRestrictionsRegex,
        \DateTime $registrationStartDate,
        \DateTime $registrationEndDate,
        ?\DateTime $contestStartDate,
        ?\DateTime $contestEndDate,
        ?int $maxCompetitors
    ) {
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
        $this->name = $name;
        $this->description = $description;
        $this->emailRestrictionsRegex = $emailRestrictionsRegex;
        $this->registrationStartDate = $registrationStartDate;
        $this->registrationEndDate = $registrationEndDate;
        $this->contestStartDate = $contestStartDate;
        $this->contestEndDate = $contestEndDate;
        $this->maxCompetitors = $maxCompetitors;
        $this->countCompetitors = null;
    }

    /**
     * @return string
     */
    public function uuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function emailRestrictionsRegex(): ?string
    {
        return $this->emailRestrictionsRegex;
    }

    /**
     * @return \DateTime
     */
    public function registrationStartDate(): \DateTime
    {
        return $this->registrationStartDate;
    }

    /**
     * @return \DateTime
     */
    public function registrationEndDate(): \DateTime
    {
        return $this->registrationEndDate;
    }

    /**
     * @return \DateTime|null
     */
    public function contestStartDate(): ?\DateTime
    {
        return $this->contestStartDate;
    }

    /**
     * @return \DateTime|null
     */
    public function contestEndDate(): ?\DateTime
    {
        return $this->contestEndDate;
    }

    /**
     * @return int|null
     */
    public function maxCompetitors(): ?int
    {
        return $this->maxCompetitors;
    }

    /**
     * @return int|null
     */
    public function countCompetitors(): ?int
    {
        return $this->countCompetitors;
    }

    /**
     * @param int $countCompetitors
     * @return $this
     */
    public function setCountCompetitors(int $countCompetitors): Contest
    {
        $this->countCompetitors = $countCompetitors;
        return $this;
    }
}
