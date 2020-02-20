<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Strategies\Mapper;


use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\DQL\TableNull;
use Cratia\ORM\Model\Common\ReflectionProperty;
use Cratia\ORM\Model\Interfaces\IStrategyModelMapper;
use Cratia\ORM\Model\Strategies;
use Cratia\ORM\Model\Common\ReflectionModel;


/**
 * Class MapperBase
 * @package Cratia\ORM\Model\Strategies\Mapper
 */
class MapperBase implements IStrategyModelMapper
{
    /**
     * @var ITable
     */
    private $_from;

    /**
     * StrategyMapperBase constructor.
     */
    public function __construct()
    {
        $this->_from = new TableNull();
    }

    /**
     * @param $model
     * @param mixed $table
     * @return $this
     */
    public function setFrom($model, $table)
    {
        if ($table instanceof ITable) {
            $this->_from = $table;
        } else {
            $this->_from = new Table($table, $table);
        }
        return $model;
    }

    /**
     * @param $model
     * @return ITable
     */
    public function getFrom($model)
    {
        return $this->_from;
    }

    /**
     * @param $model
     * @return IRelation[]
     */
    public function getRelations($model)
    {
        return $this->getFrom($model)->getRelations();
    }

    /**
     * @param $model
     * @param IRelation $relation
     * @return $this
     */
    public function addRelation($model, IRelation $relation)
    {
        $this->getFrom($model)->addRelation($relation);
        return $model;
    }

    /**
     * @param $model
     * @return array
     */
    public function getKeys($model)
    {
        $rc = new ReflectionModel($model);
        $properties = $rc->getProperties();
        $keys = [];
        /** @var ReflectionProperty $property */
        foreach ($properties as $property) {
            if ($property->isKey()) {
                $keys[] = $property->getName();
            }
        }
        return $keys;
    }

    /**
     * @param $model
     * @param $property
     * @return IField
     */
    public function getField($model, $property): IField
    {
        return Field::column($this->getFrom($model), $property, $property);
    }
}
