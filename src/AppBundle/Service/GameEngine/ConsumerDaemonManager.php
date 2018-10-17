<?php

namespace AppBundle\Service\GameEngine;

use AppBundle\Domain\Service\GameEngine\ConsumerDaemonManagerInterface;

/**
 * Tool to manage the consumer daemons
 *
 * @package AppBundle\Service\GameEngine
 */
class ConsumerDaemonManager implements ConsumerDaemonManagerInterface
{
    public const CONSOLE = GameDaemonManager::CONSOLE;
    public const COMMAND = 'app:consumer:run';

    /**
     * Starts the consumer daemons
     *
     * @param int $num
     * @param bool $force
     * @return void
     */
    public function start(int $num = 1, bool $force = false): void
    {
        $command = 'nohup '
            . 'php ' . realpath(static::CONSOLE)
            . ' ' . static::COMMAND
            . ' > /dev/null 2> /dev/null &';

        if ($force) {
            $this->stop(null);
        }

        $count = $num - $this->getProcessCount();
        while ($count-- > 0) {
            @shell_exec($command);
        }
    }

    /**
     * Stops some of the player consumer daemons
     *
     * @param int $num
     * @return void
     */
    public function stop(int $num = null): void
    {
        $processIds = $this->getProcessIds();
        $count = count($processIds);
        if (null === $num || $num > $count) {
            $num = $count;
        }

        for ($i = 0; $i < $num; ++ $i) {
            $processId = $processIds[$i];
            $command= 'kill -9 ' . $processId;
            @shell_exec($command);
        }
    }

    /**
     * Checks ow many consumer daemons are running
     *
     * @return int
     */
    public function getProcessCount(): int
    {
        return count($this->getProcessIds());
    }

    /**
     * Returns the process id of all the consumer daemons
     *
     * @return int[]
     */
    public function getProcessIds(): array
    {
        $command = 'ps ax -w'
            . ' | grep \'' . static::COMMAND . '\''
            . ' | grep -v \'grep\''
            . ' | awk \'{print $1}\'';

        $processIds = [];
        $result = @shell_exec($command);
        if (!empty($result)) {
            $processIds = explode(PHP_EOL, $result);
            foreach ($processIds as $index => $processId) {
                if (intval($processId) < 1) {
                    unset($processIds[$index]);
                }
            }
        }

        return array_values($processIds);
    }
}
