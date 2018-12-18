<?php

namespace AppBundle\Domain\Entity\Contest;

use Ramsey\Uuid\Uuid;

/**
 * Domain entity: Contest
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
    protected $starRegistrationDate;

    /** @var \DateTime the end date & time for registration */
    protected $endRegistrationDate;

    /** @var \DateTime the date & time of the contest */
    protected $contestDate;

    /**
     * Contest constructor
     *
     * @param string|null    $uuid
     * @param string         $name
     * @param string|null    $description
     * @param string         $emailRestrictionsRegex
     * @param \DateTime      $starRegistrationDate
     * @param \DateTime      $endRegistrationDate
     * @param \DateTime|null $contestDate
     * @throws \Exception
     */
    public function __construct(
        ?string $uuid,
        string $name,
        ?string $description,
        ?string $emailRestrictionsRegex,
        \DateTime $starRegistrationDate,
        \DateTime $endRegistrationDate,
        ?\DateTime $contestDate
    ) {
        $this->uuid = $uuid ?: Uuid::uuid4()->toString();
        $this->name = $name;
        $this->description = $description;
        $this->emailRestrictionsRegex = $emailRestrictionsRegex;
        $this->starRegistrationDate = $starRegistrationDate;
        $this->endRegistrationDate = $endRegistrationDate;
        $this->contestDate = $contestDate;
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
    public function starRegistrationDate(): \DateTime
    {
        return $this->starRegistrationDate;
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
}
