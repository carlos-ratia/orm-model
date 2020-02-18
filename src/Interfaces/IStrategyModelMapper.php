<?php

namespace Cratia\ORM\Model\Interfaces;


use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ITable;
use ReflectionException;

/**
 * Class MapperBase
 * @package Cratia\ORM\Model\Strategies\Mapper
 */
interface IStrategyModelMapper
{
    /**
     * @param $model
     * @param mixed $table
     * @return $this
     */
    public function setFrom($model, $table);

    /**
     * @param $model
     * @return ITable
     */
    public function getFrom($model);

    /**
     * @param $model
     * @return IRelation[]
     */
    public function getRelations($model);

    /**
     * @param $model
     * @param IRelation $relation
     * @return $this
     */
    public function addRelation($model, IRelation $relation);

    /**
     * @param  $model
     * @return array
     * @throws ReflectionException
     */
    public function getKeys($model);
}