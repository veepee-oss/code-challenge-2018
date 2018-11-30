<?php

namespace AppBundle\Form\CreateGame;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PlayerEntity
 *
 * @package AppBundle\Form\CreateGame
 */
class PlayerEntity
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $url;

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
