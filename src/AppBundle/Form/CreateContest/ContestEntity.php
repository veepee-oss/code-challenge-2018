<?php

namespace AppBundle\Form\CreateContest;

use AppBundle\Domain\Entity\Contest\Contest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Form entity: ContestEntity
 *
 * @package AppBundle\Form\CreateContest
 */
class ContestEntity
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=0, max=48)
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     * @Assert\Length(min=0, max=256)
     */
    private $regex;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $registrationStartDate;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $registrationEndDate;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     */
    private $contestStartDate;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     */
    private $contestEndDate;

    /**
     * @var int
     * @Assert\Length(min=2)
     */
    private $maxCompetitors;

    /**
     * ContestEntity constructor
     *
     * @param Contest $contest
     * @throws \Exception
     */
    public function __construct(Contest $contest = null)
    {
        if (null === $contest) {
            $this->uuid = null;
            $this->name = null;
            $this->description = null;
            $this->regex = null;
            $this->registrationStartDate = new \DateTime();
            $this->registrationStartDate->setTime(0, 0, 0, 0);
            $this->registrationEndDate = clone $this->registrationStartDate;
            $this->registrationEndDate->add(new \DateInterval('P10D'));
            $this->registrationEndDate->setTime(23, 59, 59, 0);
            $this->contestStartDate = null;
            $this->contestEndDate = null;
            $this->maxCompetitors = null;
        } else {
            $this->uuid = $contest->uuid();
            $this->name = $contest->name();
            $this->description = $contest->description();
            $this->regex = $contest->emailRestrictionsRegex();
            $this->registrationStartDate = $contest->registrationStartDate();
            $this->registrationEndDate = $contest->registrationEndDate();
            $this->contestStartDate = $contest->contestStartDate();
            $this->contestEndDate = $contest->contestEndDate();
            $this->maxCompetitors = $contest->maxCompetitors();
        }
    }

    /**
     * Converts the entity to a domain entity
     *
     * @return Contest
     * @throws \Exception
     */
    public function toDomainEntity(): Contest
    {
        return new Contest(
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
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     * @return ContestEntity
     */
    public function setUuid(?string $uuid): ContestEntity
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ContestEntity
     */
    public function setName(?string $name): ContestEntity
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
     * @return ContestEntity
     */
    public function setDescription(?string $description): ContestEntity
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
     * @return ContestEntity
     */
    public function setRegex(?string $regex): ContestEntity
    {
        $this->regex = $regex;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRegistrationStartDate(): ?\DateTime
    {
        return $this->registrationStartDate;
    }

    /**
     * @param \DateTime $registrationStartDate
     * @return ContestEntity
     */
    public function setRegistrationStartDate(?\DateTime $registrationStartDate): ContestEntity
    {
        $this->registrationStartDate = $registrationStartDate;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRegistrationEndDate(): ?\DateTime
    {
        return $this->registrationEndDate;
    }

    /**
     * @param \DateTime $registrationEndDate
     * @return ContestEntity
     */
    public function setRegistrationEndDate(?\DateTime $registrationEndDate): ContestEntity
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
     * @return ContestEntity
     */
    public function setContestStartDate(?\DateTime $contestStartDate): ContestEntity
    {
        $this->contestStartDate = $contestStartDate;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getContestEndDate(): ?\DateTime
    {
        return $this->contestEndDate;
    }

    /**
     * @param \DateTime $contestEndDate
     * @return ContestEntity
     */
    public function setContestEndDate(\DateTime $contestEndDate): ContestEntity
    {
        $this->contestEndDate = $contestEndDate;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxCompetitors(): ?int
    {
        return $this->maxCompetitors;
    }

    /**
     * @param int $maxCompetitors
     * @return ContestEntity
     */
    public function setMaxCompetitors(int $maxCompetitors): ContestEntity
    {
        $this->maxCompetitors = $maxCompetitors;
        return $this;
    }
}
