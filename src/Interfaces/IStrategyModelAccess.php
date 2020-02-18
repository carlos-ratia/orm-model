<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;

/**
 * Interface IStrategyModelAccess
 * @package Cratia\ORM\Model\Interfaces
 */
interface IStrategyModelAccess
{
    /**
     * @param $model
     * @param $name
     * @return null|mixed
     */
    public function _get($model, $name);

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return bool
     */
    public function _set($model, $name, $value): bool;

    /**
     * @param $model
     * @param $name
     * @return bool
     */
    public function _isset($model, $name): bool;
}