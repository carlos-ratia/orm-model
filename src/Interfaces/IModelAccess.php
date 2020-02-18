<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;

/**
 * Interface IModelAccess
 * @package Cratia\ORM\Model\Interfaces
 */
interface IModelAccess
{
    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool;

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set($name, $value);

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name);
}