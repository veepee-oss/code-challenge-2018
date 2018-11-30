<?php

namespace AppBundle\Service\GameEngine;

use AppBundle\Domain\Service\GameEngine\GameDaemonManagerInterface;

/**
 * Tool to manage the game daemons
 *
 * @package AppBundle\Service\GameEngine
 */
class GameDaemonManager implements GameDaemonManagerInterface
{
    public const CONSOLE = __DIR__ . '/../../../../bin/console';
    public const COMMAND = 'app:engine:run';

    /**
     * Starts the game engine daemon
     *
     * $ nohup php app/console app:engine:run > /dev/null 2> /dev/null &
     *
     * @param bool $force
     * @return void
     */
    public function start($force = false)
    {
        if ($force) {
            $this->stop();
        }

        if (!$this->isRunning()) {
            $command = 'nohup '
                . 'php ' . realpath(static::CONSOLE)
                . ' ' . static::COMMAND
                . ' > /dev/null 2> /dev/null &';

            @shell_exec($command);
        }
    }

    /**
     * Stops the game engine daemon
     *
     * @return void
     */
    public function stop()
    {
        do {
            $processId = $this->getProcessId();
            if ($processId > 0) {
                $command = 'kill -9 ' . $processId;
                @shell_exec($command);
            }
        } while ($processId > 0);
    }

    /**
     * Checks if the game engine daemon isd running
     *
     * @return bool true=running, 0=not running
     */
    public function isRunning()
    {
        $processId = $this->getProcessId();
        return (false !== $processId);
    }

    /**
     * Returns the process id of the game engine daemon
     *
     * @return int|false
     */
    public function getProcessId()
    {
        $result = $this->findProcess();
        if (!$result || !intval($result)) {
            return false;
        }

        return intval($result);
    }

    /**
     * Finds the process status and return the process ID
     *
     * $ ps ax -w | grep app:engine:run | grep -v 'grep' | awk '{print $1}'
     *
     * @return string|false
     */
    public function findProcess()
    {
        $command = 'ps ax -w'
            . ' | grep \'' . static::COMMAND . '\''
            . ' | grep -v \'grep\''
            . ' | awk \'{print $1}\'';

        $result = @shell_exec($command);
        if (empty($result)) {
            return false;
        }

        return $result;
    }
}
