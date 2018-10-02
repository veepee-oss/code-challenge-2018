<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Player\Player;

/**
 * Class to locate the properly service to ask for the player name
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class AskPlayerNameServiceLocator
{
    /** @var AskPlayerNameInterface[] */
    protected $services;

    /**
     * AskPlayerNameServiceLocator constructor.
     *
     * @param AskPlayerNameInterface[] $services
     * @throws MovePlayerException
     */
    public function __construct(array $services)
    {
        foreach ($services as $service) {
            if (!$service instanceof AskPlayerNameInterface) {
                throw new MovePlayerException(
                    'Class ' . $service . ' is not an instance of ' . AskPlayerNameInterface::class
                );
            }
        }
        $this->services = $services;
    }

    /**
     * Locates the right service to ask for the player name
     *
     * @param Player $player
     * @return AskPlayerNameInterface
     * @throws MovePlayerException
     */
    public function locate(Player $player)
    {
        if (!array_key_exists($player->type(), $this->services)) {
            throw new MovePlayerException(
                'AskPlayerNameInterface service not found for class ' . get_class($player)
            );
        }

        return $this->services[$player->type()];
    }
}
