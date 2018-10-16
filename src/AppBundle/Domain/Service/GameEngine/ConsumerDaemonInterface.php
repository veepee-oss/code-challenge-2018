<?php

namespace AppBundle\Domain\Service\GameEngine;

/**
 * Interface to a tool to manage the consumer daemons
 *
 * @package AppBundle\Domain\Service\GameEngine
 */
interface ConsumerDaemonInterface
{
    private const CONSOLE = __DIR__ . '/../../../../bin/console';
    private const COMMAND = 'app:code-challenge:engine';
    /**
     * Starts the consumer daemons
     *
     * @param int $num
     * @param bool $force
     * @return void
     */
    public function start(int $num = 1, bool $force = false) : void;

    /**
     * Stops all the consumer daemons or only one
     *
     * @param int $procId
     * @return void
     */
    public function stop(?int $procId) : void;

    /**
     * Checks ow many consumer daemons are running
     *
     * @return int
     */
    public function getProcessCount() : int;

    /**
     * Returns the process id of all the consumer daemons
     *
     * @return int[]
     */
    public function getProcessIds() : array;
}
