<?php

namespace AppBundle\Command;

use AppBundle\Domain\Service\GameEngine\ConsumerDaemonManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to run/stop and get status of the consumer daemons
 *
 * @package AppBundle\Command
 */
class ManagePlayerConsumerCommand extends ContainerAwareCommand
{
    /** @var ConsumerDaemonManagerInterface */
    private $consumerDaemonManager;

    /**
     * ManageGameEngineCommand constructor.
     *
     * @param ConsumerDaemonManagerInterface $consumerDaemonManager
     */
    public function __construct(ConsumerDaemonManagerInterface $consumerDaemonManager)
    {
        $this->consumerDaemonManager = $consumerDaemonManager;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('app:consumer:manage')
            ->setDescription('Manages the consumer daemons.')
            ->addArgument('cmd', InputArgument::REQUIRED, 'Command: run, stop, status')
            ->addArgument('num', InputArgument::OPTIONAL, 'Number of consumers to start/stop')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forces restart of the consumers');
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
                $this->stop($input, $output);
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
        $num = $input->getArgument('num');
        $force = $input->getOption('force');

        if (null === $num) {
            $num = 1 + $this->consumerDaemonManager->getProcessCount();
            $output->writeln('<comment>Starting new player consumer daemon...</comment>');
        } elseif ($force) {
            $output->writeln('<comment>Starting ' . $num . ' new consumers...</comment>');
        } else {
            $output->writeln('<comment>Ensuring there are at least ' . $num . ' consumers...</comment>');
        }

        $this->consumerDaemonManager->start($num, $force);
    }

    /**
     * Stops the game engine
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function stop(InputInterface $input, OutputInterface $output)
    {
        $num = $input->getArgument('num');
        if (null === $num) {
            $output->writeln('<comment>Stopping all the consumer daemons...</comment>');
        } else {
            $output->writeln('<comment>Stopping ' . $num . ' engine daemons...</comment>');
        }
        $this->consumerDaemonManager->stop($num);
    }

    /**
     * Shows the status of the process
     *
     * @param OutputInterface $output
     * @return void
     */
    private function status(OutputInterface $output)
    {
        $processIds = $this->consumerDaemonManager->getProcessIds();
        if (empty($processIds)) {
            $output->writeln('<error>No consumer daemon running found!</error>');
        } else {
            $output->writeln('<info>' . count($processIds) . '</info> consumer daemons found!');
            $output->writeln('The process ids are:');
            foreach ($processIds as $processId) {
                $output->writeln('- <comment>' . $processId . '</comment>');
            }
        }
    }
}
