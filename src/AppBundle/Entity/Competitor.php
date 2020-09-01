<?php

namespace AppBundle\Entity;

use AppBundle\Domain\Entity\Contest as DomainCompetitor;
use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine Entity: Competitor
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="competitor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompetitorRepository")
 */
class Competitor
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
     * UUID of the competitor
     *
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false, unique=true)
     */
    private $uuid;

    /**
     * UUID of the contest
     *
     * @var string
     *
     * @ORM\Column(name="contest_uuid", type="string", length=36, nullable=false)
     */
    private $contestUuid;

    /**
     * Email of the competitor
     *
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=256, nullable=false)
     */
    private $email;

    /**
     * Name of the competitor
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * URL of the API of the competitor
     *
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=256, nullable=false)
     */
    private $url;

    /**
     * True if the competitor has been validated
     *
     * @var bool
     *
     * @ORM\Column(name="validated", type="boolean", nullable=false)
     */
    private $validated;

    /**
     * Token to validate the competitor's email
     *
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=64, nullable=true)
     */
    private $token;

    /**
     * Competitor constructor
     *
     * @param $source
     * @throws \Exception
     */
    public function __construct($source = null)
    {
        if (null === $source) {
            $this->id = null;
            $this->uuid = null;
            $this->contestUuid = null;
            $this->email = null;
            $this->name = null;
            $this->url = null;
            $this->validated = false;
            $this->token = null;
        } elseif ($source instanceof Competitor) {
            $this->id = $source->getId();
            $this->uuid = $source->getUuid();
            $this->contestUuid = $source->getContestUuid();
            $this->email = $source->getEmail();
            $this->name = $source->getName();
            $this->url = $source->getUrl();
            $this->validated = $source->isValidated();
            $this->token = $source->getToken();
        } elseif ($source instanceof DomainCompetitor\Competitor) {
            $this->id = null;
            $this->fromDomainEntity($source);
        }
    }

    /**
     * Convert entity to a domain competitor
     *
     * @return DomainCompetitor\Competitor
     * @throws \Exception
     */
    public function toDomainEntity()
    {
        return new DomainCompetitor\Competitor(
            $this->uuid,
            $this->contestUuid,
            $this->email,
            $this->name,
            $this->url,
            $this->validated,
            $this->token
        );
    }

    /**
     * Update entity from a domain competitor
     *
     * @param DomainCompetitor\Competitor $competitor
     * @return $this
     */
    public function fromDomainEntity(DomainCompetitor\Competitor $competitor)
    {
        $this->uuid = $competitor->uuid();
        $this->contestUuid = $competitor->contest();
        $this->email = $competitor->email();
        $this->name = $competitor->name();
        $this->url = $competitor->url();
        $this->validated = $competitor->validated();
        $this->token = $competitor->validationToken();
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
     * @return Competitor
     */
    public function setUuid(string $uuid): Competitor
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getContestUuid(): string
    {
        return $this->contestUuid;
    }

    /**
     * @param string $contestUuid
     * @return Competitor
     */
    public function setContestUuid(string $contestUuid): Competitor
    {
        $this->contestUuid = $contestUuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Competitor
     */
    public function setEmail(string $email): Competitor
    {
        $this->email = $email;
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
     * @return Competitor
     */
    public function setName(string $name): Competitor
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Competitor
     */
    public function setUrl(string $url): Competitor
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->validated;
    }

    /**
     * @param bool $validated
     * @return Competitor
     */
    public function setValidated(bool $validated): Competitor
    {
        $this->validated = $validated;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return Competitor
     */
    public function setToken(string $token): Competitor
    {
        $this->token = $token;
        return $this;
    }
}
