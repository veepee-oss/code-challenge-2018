<?php

namespace AppBundle\Form\CreateContest;

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
     * @return string
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
     * @return string
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
     * @return string
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
     * @return \DateTime
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
     * @return \DateTime
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
     * @return \DateTime
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
}
