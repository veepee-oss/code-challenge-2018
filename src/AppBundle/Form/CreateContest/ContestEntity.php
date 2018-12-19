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
     * @Assert\NotBlank()
     * @Assert\Length(min=0, max=48)
     */
    private $name = null;

    /**
     * @var string
     */
    private $description = null;

    /**
     * @var string
     * @Assert\Length(min=0, max=256)
     */
    private $regex = null;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $startDate = null;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $endDate = null;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     */
    private $contestDate = null;

    /**
     * @var int
     * @Assert\Length(min=2)
     */
    private $maxCompetitors;

    /**
     * ContestEntity constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->startDate = new \DateTime();
        $this->startDate->setTime(0, 0, 0, 0);
        $this->endDate = clone $this->startDate;
        $this->endDate->add(new \DateInterval('P10D'));
        $this->endDate->setTime(23, 59, 59, 0);
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
            null,
            $this->name,
            $this->description,
            $this->regex,
            $this->startDate,
            $this->endDate,
            $this->contestDate,
            $this->maxCompetitors
        );
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
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return ContestEntity
     */
    public function setStartDate(?\DateTime $startDate): ContestEntity
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return ContestEntity
     */
    public function setEndDate(?\DateTime $endDate): ContestEntity
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getContestDate(): ?\DateTime
    {
        return $this->contestDate;
    }

    /**
     * @param \DateTime $contestDate
     * @return ContestEntity
     */
    public function setContestDate(?\DateTime $contestDate): ContestEntity
    {
        $this->contestDate = $contestDate;
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
