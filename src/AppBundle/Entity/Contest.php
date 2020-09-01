<?php

namespace AppBundle\Entity;

use AppBundle\Domain\Entity\Contest as DomainContest;
use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine Entity: Contest
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="contest")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContestRepository")
 */
class Contest
{
    /**
     * Primary key
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * UUID of the contest
     *
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false, unique=true)
     */
    private $uuid;

    /**
     * Name of the contest
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=48, nullable=false)
     */
    private $name;

    /**
     * Description of the contest
     *
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * Regular expression to restricting the emails
     *
     * @var string
     *
     * @ORM\Column(name="regex", type="string", length=256, nullable=true)
     */
    private $regex;

    /**
     * Start date & time for registering
     *
     * @var \DateTime
     *
     * @ORM\Column(name="registration_start_date", type="datetimetz", nullable=false)
     */
    private $registrationStartDate;

    /**
     * End date & time for registering
     *
     * @var \DateTime
     *
     * @ORM\Column(name="registration_end_date", type="datetimetz", nullable=false)
     */
    private $registrationEndDate;

    /**
     * Start date & time of the contest
     *
     * @var \DateTime
     *
     * @ORM\Column(name="contest_start_date", type="datetimetz", nullable=true)
     */
    private $contestStartDate;

    /**
     * End date & time of the contest
     *
     * @var \DateTime
     *
     * @ORM\Column(name="contest_end_date", type="datetimetz", nullable=true)
     */
    private $contestEndDate;


    /**
     * Max number of competitors (could be null)
     *
     * @var int
     *
     * @ORM\Column(name="max_competitors", type="integer", nullable=true)
     */
    private $maxCompetitors;

    /**
     * Contest constructor
     *
     * @param $source
     * @throws \Exception
     */
    public function __construct($source = null)
    {
        if (null === $source) {
            $this->id = null;
            $this->uuid = null;
            $this->name = null;
            $this->description = null;
            $this->regex = null;
            $this->registrationStartDate = null;
            $this->registrationEndDate = null;
            $this->contestStartDate = null;
            $this->contestEndDate = null;
            $this->maxCompetitors = null;
        } elseif ($source instanceof Contest) {
            $this->id = $source->getId();
            $this->uuid = $source->getUuid();
            $this->name = $source->getName();
            $this->description = $source->getDescription();
            $this->regex = $source->getRegex();
            $this->registrationStartDate = $source->getRegistrationStartDate();
            $this->registrationEndDate = $source->getRegistrationEndDate();
            $this->contestStartDate = $source->getContestStartDate();
            $this->contestEndDate = $source->getContestEndDate();
            $this->maxCompetitors = $source->getMaxCompetitors();
        } elseif ($source instanceof DomainContest\Contest) {
            $this->id = null;
            $this->fromDomainEntity($source);
        }
    }

    /**
     * Convert entity to a domain contest
     *
     * @return DomainContest\Contest
     * @throws \Exception
     */
    public function toDomainEntity()
    {
        return new DomainContest\Contest(
            $this->uuid,
            $this->name,
            $this->description,
            $this->regex,
            $this->registrationStartDate,
            $this->registrationEndDate,
            $this->contestStartDate,
            $this->contestEndDate,
            $this->maxCompetitors
        );
    }

    /**
     * Update entity from a domain contest
     *
     * @param DomainContest\Contest $contest
     * @return $this
     */
    public function fromDomainEntity(DomainContest\Contest $contest)
    {
        $this->uuid = $contest->uuid();
        $this->name = $contest->name();
        $this->description = $contest->description();
        $this->regex = $contest->emailRestrictionsRegex();
        $this->registrationStartDate = $contest->registrationStartDate();
        $this->registrationEndDate = $contest->registrationEndDate();
        $this->contestStartDate = $contest->contestStartDate();
        $this->contestEndDate = $contest->contestEndDate();
        $this->maxCompetitors = $contest->maxCompetitors();
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
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return Contest
     */
    public function setUuid(string $uuid): Contest
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Contest
     */
    public function setName(string $name): Contest
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Contest
     */
    public function setDescription(string $description): Contest
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegex(): ?string
    {
        return $this->regex;
    }

    /**
     * @param string $regex
     * @return Contest
     */
    public function setRegex(string $regex): Contest
    {
        $this->regex = $regex;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationStartDate(): \DateTime
    {
        return $this->registrationStartDate;
    }

    /**
     * @param \DateTime $registrationStartDate
     * @return Contest
     */
    public function setRegistrationStartDate(\DateTime $registrationStartDate): Contest
    {
        $this->registrationStartDate = $registrationStartDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationEndDate(): \DateTime
    {
        return $this->registrationEndDate;
    }

    /**
     * @param \DateTime $registrationEndDate
     * @return Contest
     */
    public function setRegistrationEndDate(\DateTime $registrationEndDate): Contest
    {
        $this->registrationEndDate = $registrationEndDate;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getContestStartDate(): ?\DateTime
    {
        return $this->contestStartDate;
    }

    /**
     * @param \DateTime $contestStartDate
     * @return Contest
     */
    public function setContestStartDate(\DateTime $contestStartDate): Contest
    {
        $this->contestStartDate = $contestStartDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getContestEndDate(): \DateTime
    {
        return $this->contestEndDate;
    }

    /**
     * @param \DateTime $contestEndDate
     * @return Contest
     */
    public function setContestEndDate(\DateTime $contestEndDate): Contest
    {
        $this->contestEndDate = $contestEndDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxCompetitors(): int
    {
        return $this->maxCompetitors;
    }

    /**
     * @param int $maxCompetitors
     * @return Contest
     */
    public function setMaxCompetitors(int $maxCompetitors): Contest
    {
        $this->maxCompetitors = $maxCompetitors;
        return $this;
    }
}
