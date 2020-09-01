<?php

namespace AppBundle\Repository;

use AppBundle\Domain\Repository\LoggerRepositoryInterface;
use AppBundle\Entity\Logger as LoggerEntity;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Doctrine Repository: LoggerRepository
 *
 * @package AppBundle\Repository
 */
class LoggerRepository extends EntityRepository implements LoggerRepositoryInterface
{
    /**
     * Removes a logger
     *
     * @param mixed $logger
     * @return LoggerRepositoryInterface
     * @throws InvalidArgumentException
     */
    public function removeLogger($logger): LoggerRepositoryInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        /** @var LoggerEntity $logger */
        $logger = $this->findLoggerEntity($logger);
        $em->remove($logger);

        return $this;
    }

    /**
     * Find Logger entity
     *
     * @param mixed $logger
     * @return LoggerEntity
     * @throws InvalidArgumentException
     */
    protected function findLoggerEntity($logger): LoggerEntity
    {
        if ($logger instanceof LoggerEntity) {
            return $logger;
        }

        if (is_string($logger)) {
            return $this->findOneBy([
                'uuid' => $logger
            ]);
        }

        throw new InvalidArgumentException('$logger is invalid!');
    }
}
