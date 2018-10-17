<?php

namespace AppBundle\Command;

use AppBundle\Domain\Service\GameEngine\GameDaemonManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to run/stop and get status of the game engine
 *
 * @package AppBundle\Command
 */
class ManageGameEngineCommand extends ContainerAwareCommand
{
    /** @var GameDaemonManagerInterface */
    private $gameDaemonManager;

    /**
     * ManageGameEngineCommand constructor.
     *
     * @param GameDaemonManagerInterface $gameDaemonManager
     */
    public function __construct(GameDaemonManagerInterface $gameDaemonManager)
    {
        $this->gameDaemonManager = $gameDaemonManager;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('app:engine:manage')
            ->setDescription('Manages the game engine daemon.')
            ->addArgument('cmd', InputArgument::REQUIRED, 'Command: run, stop, status')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forces start a new game engine');
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
        $cmd = $input->getArgument('cmd');
        switch ($cmd) {
            case 'start':
                $this->start($input, $output);
                $this->status($output);
                break;

            case 'stop':
                $this->stop($output);
                $this->status($output);
                break;

            case 'status':
                $this->status($output);
                break;

            default:
                throw new InvalidArgumentException($cmd . ' is not a valid command: start, stop, status');
        }
        $output->writeln('');
        return 0;
    }

    /**
     * Starts the game engine
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function start(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');
        if ($force || !$this->gameDaemonManager->isRunning()) {
            $output->writeln('<comment>Starting the game engine daemon...</comment>');
        }
        $this->gameDaemonManager->start($force);
    }

    /**
     * Stops the game engine
     *
     * @param OutputInterface $output
     * @return void
     */
    private function stop(OutputInterface $output)
    {
        if ($this->gameDaemonManager->isRunning()) {
            $output->writeln('<comment>Stopping the game engine daemon...</comment>');
        }
        $this->gameDaemonManager->stop();
    }

    /**
     * Shows the status of the process
     *
     * @param OutputInterface $output
     * @return void
     */
    private function status(OutputInterface $output)
    {
        $processId = $this->gameDaemonManager->getProcessId();
        if (!$processId) {
            $output->writeln('The game engine is <error>not running</error>!');
        } else {
            $output->writeln('The game engine is <info>running</info>!');
            $output->writeln('The process id is: <comment>' . $processId . '</comment>');
        }
    }
}
