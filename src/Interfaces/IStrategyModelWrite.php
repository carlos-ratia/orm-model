<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;


/**
 * Interface IStrategyModelWrite
 * @package Cratia\ORM\Model\Interfaces
 */
interface IStrategyModelWrite
{
    const CREATE = 'ActiveRecordWrite::CREATE';
    const UPDATE = 'ActiveRecordWrite::UPDATE';

    /**
     * @param IModel $model
     * @return string
     */
    public function create(IModel $model): string;

//    /**
//     * Return the last inserted id in a create method
//     *
//     * @return int
//     */
//    public function getLastInsertedId();
//
    /**
     * @param IModel $model
     * @return boolean
     */
    public function update(IModel $model): bool;
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
