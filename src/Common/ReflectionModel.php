<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Common;


use ReflectionClass;
use ReflectionException;

/**
 * Class ReflectionModel
 */
class ReflectionModel extends ReflectionClass
{
    /**
     * ReflectionModel constructor.
     * @param $model
     * @throws ReflectionException
     */
    public function __construct($model)
    {
        parent::__construct($model);
    }

    /**
     * @param null $filter
     * @return ReflectionProperty[]
     */
    public function getProperties($filter = null)
    {
        /** @var \ReflectionProperty[] $props */
        $props = parent::getProperties($filter);
        return array_map(function (\ReflectionProperty $prop) {
            return new ReflectionProperty($prop->class, $prop->name);
        }, $props);
    }
}
