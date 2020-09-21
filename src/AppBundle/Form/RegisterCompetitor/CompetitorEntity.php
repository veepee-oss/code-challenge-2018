<?php

namespace AppBundle\Form\RegisterCompetitor;

use AppBundle\Domain\Entity\Contest\Competitor;
use AppBundle\Entity\Contest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Form entity: CompetitorEntity
 *
 * @package AppBundle\Form\RegisterCompetitor
 */
class CompetitorEntity
{
    /**
     * @var Contest
     * @Assert\NotBlank(groups={"default", "admin"})
     */
    private $contest = null;

    /**
     * @var string
     * @Assert\NotBlank(groups={"default", "admin"})
     * @Assert\Email(groups={"default", "admin"})
     */
    private $email = null;

    /**
     * @var string
     * @Assert\NotBlank(groups={"admin"})
     */
    private $name = null;

    /**
     * @var string
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Url(groups={"default", "admin"})
     */
    private $url = null;

    /**
     * Converts the entity to a domain entity
     *
     * @return Competitor
     * @throws \Exception
     */
    public function toDomainEntity(): Competitor
    {
        return new Competitor(
            null,
            $this->contest->getUuid(),
            $this->email,
            $this->name,
            $this->url,
            null,
            null
        );
    }

    /**
     * @return Contest|null
     */
    public function getContest(): ?Contest
    {
        return $this->contest;
    }

    /**
     * @param Contest $contest
     * @return CompetitorEntity
     */
    public function setContest(?Contest $contest): CompetitorEntity
    {
        $this->contest = $contest;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return CompetitorEntity
     */
    public function setEmail(?string $email): CompetitorEntity
    {
        $this->email = $email;
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
     * @return CompetitorEntity
     */
    public function setName(string $name): CompetitorEntity
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return CompetitorEntity
     */
    public function setUrl(?string $url): CompetitorEntity
    {
        $this->url = $url;
        return $this;
    }
}
