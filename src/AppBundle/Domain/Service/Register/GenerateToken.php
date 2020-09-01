<?php

namespace AppBundle\Domain\Service\Register;

use AppBundle\Domain\Entity\Contest\Competitor;

/**
 * Service to generate an unique token to validate the user
 *
 * @package AppBundle\Domain\Service\Register
 */
class GenerateToken implements GenerateTokenInterface
{
    /** @var string */
    private $secret;

    /**
     * GenerateToken constructor
     *
     * @param string $secret
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * Adds the token to a competitor
     *
     * @param Competitor $competitor
     * @return void
     */
    public function addToken(Competitor $competitor)
    {
        $data = $this->secret . $competitor->uuid() . $competitor->email();
        $token = hash('sha256', $data);
        $competitor->setValidationToken($token);
    }
}
