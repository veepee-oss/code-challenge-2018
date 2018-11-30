<?php

namespace AppBundle\Command;

use AppBundle\Service\MovePlayer\MovePlayerAsyncConsumer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Console command run the consumer daemon to move the players
 *
 * @package AppBundle\Command
 */
class RunPlayerConsumerCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('app:consumer:run')
            ->setDescription('Run the consumer daemon to move players.');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws LogicException When this abstract method is not implemented
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerInterface $container */
        $container = $this->getContainer();

        /** @var MovePlayerAsyncConsumer $consumer */
        $consumer = $container->get('app.player.move.consumer');
        $consumer->consume();

        return 0;
    }
}
