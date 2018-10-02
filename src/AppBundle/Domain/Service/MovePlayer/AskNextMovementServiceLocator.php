<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Player\Player;

/**
 * Class to locate the properly service to move a player
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class AskNextMovementServiceLocator
{
    /** @var AskNextMovementInterface[] */
    protected $services;

    /**
     * AskNextMovementServiceLocator constructor.
     *
     * @param AskNextMovementInterface[] $services
     * @throws MovePlayerException
     */
    public function __construct(array $services)
    {
        foreach ($services as $service) {
            if (!$service instanceof AskNextMovementInterface) {
                throw new MovePlayerException(
                    'Class ' . $service . ' is not an instance of ' . AskNextMovementInterface::class
                );
            }
        }
        $this->services = $services;
    }

    /**
     * Locates the right service to move a player
     *
     * @param Player $player
     * @return AskNextMovementInterface
     * @throws MovePlayerException
     */
    public function locate(Player $player)
    {
        if (!array_key_exists($player->type(), $this->services)) {
            throw new MovePlayerException(
                'AskNextMovementInterface service not found for class ' . get_class($player)
            );
        }

        return $this->services[$player->type()];
    }
}
