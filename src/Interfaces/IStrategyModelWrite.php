<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Interface IStrategyModelWrite
 * @package Cratia\ORM\Model\Interfaces
 */
interface IStrategyModelWrite
{

    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     * @param EventManager|null $eventManager
     * @return $this
     */
    public function inject(IAdapter $adapter, ?LoggerInterface $logger = null, ?EventManager $eventManager = null);

    /**
     * @param IModel $model
     * @return string
     * @throws Exception
     * @throws DBALException
     */
    public function create(IModel $model): string;

    /**
     * @param IModel $model
     * @return boolean
     * @throws Exception
     * @throws DBALException
     */
    public function update(IModel $model): bool;

    /**
     * @param IModel $model
     * @return bool
     * @throws Exception
     * @throws DBALException
     */
    public function delete(IModel $model): bool;

    /**
     * @param $model
     * @param IFilter $filter
     * @return bool
     * @throws Exception
     * @throws DBALException
     */
    public function deleteBulk(IModel $model, IFilter $filter): bool;

}
