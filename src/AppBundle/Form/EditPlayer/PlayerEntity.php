<?php

namespace AppBundle\Form\EditPlayer;

use AppBundle\Domain\Entity\Player\Player;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Form entity: PlayerEntity
 *
 * @package AppBundle\Form\EditPlayer
 */
class PlayerEntity
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email ()
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $url;

    /**
     * PlayerEntity constructor.
     *
     * @param Player $player
     */
    public function __construct(Player $player)
    {
        $this->name = $player->name();
        $this->email = $player->email();
        $this->url = $player->url();
    }

    /**
     * Converts the entity to a domain entity
     *
     * @param Player $source
     * @return Player
     */
    public function toDomainEntity(Player $source) : Player
    {
        return $source
            ->setUrl($this->url)
            ->setPlayerIds($this->name, $this->email);
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
     * @return PlayerEntity
     */
    public function setName(string $name): PlayerEntity
    {
        $this->name = $name;
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
     * @return PlayerEntity
     */
    public function setEmail(string $email): PlayerEntity
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
