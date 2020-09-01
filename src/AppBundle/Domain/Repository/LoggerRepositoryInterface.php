<?php

namespace AppBundle\Domain\Repository;

/**
 * Interface to a repository of Logger entities
 *
 * @package AppBundle\Domain\Repository
 */
interface LoggerRepositoryInterface
{
    /**
     * Removes a logger
     *
     * @param mixed $logger
     * @return LoggerRepositoryInterface
     */
    public function removeLogger($logger): LoggerRepositoryInterface;
}
