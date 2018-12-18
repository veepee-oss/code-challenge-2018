<?php

namespace AppBundle\Domain\Entity\Competitor;

use Ramsey\Uuid\Uuid;

/**
 * Domain entity: Competitor
 *
 * @package AppBundle\Domain\Entity\Competitor
 */
class Competitor
{
    /** @var string the UUID of the competitor */
    protected $uuid;

    /** @var string the UUID of the contest */
    protected $contest;

    /** @var string the email of the competitor */
    protected $email;

    /** @var string the URL of the API to move the competitor */
    protected $url;

    /** @var bool if the competitor has been validated */
    protected $validated;

    /** @var string the token to validate the competitor's email */
    protected $validateToken;

    /**
     * Competitor constructor
     *
     * @param string|null $uuid
     * @param string      $contest
     * @param string      $email
     * @param string      $url
     * @param bool|null   $validated
     * @param string|null $validateToken
     * @throws \Exception
     */
    public function __construct(
        ?string $uuid,
        string $contest,
        string $email,
        string $url,
        ?bool $validated,
        ?string $validateToken
    ) {
        $this->uuid = $uuid ?: Uuid::uuid4()->toString();
        $this->contest = $contest;
        $this->email = $email;
        $this->url = $url;
        $this->validated = $validated ?? false;
        $this->validateToken = $validateToken;
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
    public function contest(): string
    {
        return $this->contest;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function validated(): bool
    {
        return $this->validated;
    }

    /**
     * @return string|null
     */
    public function getValidateToken(): ?string
    {
        return $this->validateToken;
    }
}
