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
    protected $startRegistrationDate;

    /** @var \DateTime the end date & time for registration */
    protected $endRegistrationDate;

    /** @var \DateTime the date & time of the contest */
    protected $contestDate;

    /** @var int|null the max competitors */
    protected $maxCompetitors;

    /** @var int|null the current competitors count */
    protected $countCompetitors;

    /**
     * Contest constructor
     *
     * @param string|null    $uuid
     * @param string         $name
     * @param string|null    $description
     * @param string         $emailRestrictionsRegex
     * @param \DateTime      $startRegistrationDate
     * @param \DateTime      $endRegistrationDate
     * @param \DateTime|null $contestDate
     * @param int|null       $maxCompetitors
     * @throws \Exception
     */
    public function __construct(
        ?string $uuid,
        string $name,
        ?string $description,
        ?string $emailRestrictionsRegex,
        \DateTime $startRegistrationDate,
        \DateTime $endRegistrationDate,
        ?\DateTime $contestDate,
        ?int $maxCompetitors
    ) {
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
        $this->name = $name;
        $this->description = $description;
        $this->emailRestrictionsRegex = $emailRestrictionsRegex;
        $this->startRegistrationDate = $startRegistrationDate;
        $this->endRegistrationDate = $endRegistrationDate;
        $this->contestDate = $contestDate;
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
    public function startRegistrationDate(): \DateTime
    {
        return $this->startRegistrationDate;
    }

    /**
     * @return \DateTime
     */
    public function endRegistrationDate(): \DateTime
    {
        return $this->endRegistrationDate;
    }

    /**
     * @return \DateTime|null
     */
    public function contestDate(): ?\DateTime
    {
        return $this->contestDate;
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
