<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Interface IStrategyModelWrite
 * @package Cratia\ORM\Model\Interfaces
 */
interface IStrategyModelWrite
{
    const CREATE = 'ActiveRecordWrite::CREATE';
    const UPDATE = 'ActiveRecordWrite::UPDATE';

    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     * @return $this
     */
    public function inject(IAdapter $adapter, LoggerInterface $logger = null);

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

//    /**
//     * Return the last inserted id in a create method
//     *
//     * @return int
//     */
//    public function getLastInsertedId();
//
//    /**
//     * @param IModel $model
//     * @return boolean
//     */
//    public function delete(IModel $model): bool;
//
//    /**
//     * @param IModel $model
//     * @param IFilter $filter
//     * @return bool
//     */
//    public function deleteBulk($model, IFilter $filter): bool;
}
