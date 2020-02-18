<?php


namespace Cratia\ORM\Model\Strategies\Access;

use Cratia\ORM\Model\Interfaces\IStrategyModelAccess;
use ReflectionClass;

/**
 * Class AccessBase
 * @package Cratia\ORM\Model\Strategies\Access
 */
class AccessBase implements IStrategyModelAccess
{
    /**
     * @inheritDoc
     */
    public function _get($model, $name)
    {
        if ($this->isPublic($model, $name)) {
            return $model->{$name};
        }
        if ($this->hasMethodToGet($model, $name)) {
            $method = $this->getMethodToGet($name);
            return $model->$method();
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    protected function isPublic($model, string $name): bool
    {
        $r = new ReflectionClass($model);
        return $r->hasProperty($name) && $r->getProperty($name)->isPublic();
    }

    /**
     * @inheritDoc
     */
    protected function hasMethodToGet($model, string $name): bool
    {
        $method = $this->getMethodToGet($name);
        $r = new ReflectionClass($model);
        return $r->hasMethod($method) && $r->getMethod($method)->isPublic();
    }

    /**
     * @inheritDoc
     */
    protected function getMethodToGet(string $name): string
    {
        return 'get' . $this->underscoreToCamelCase($name);
    }

    /**
     * @inheritDoc
     */
    public function _set($model, $name, $value): bool
    {
        if ($this->isPublic($model, $name)) {
            $model->{$name} = $value;
            return true;
        } elseif ($this->hasMethodToSet($model, $name)) {
            $method = $this->getMethodToSet($name);
            $model->$method($value);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    protected function hasMethodToSet($model, string $name): bool
    {
        $method = $this->getMethodToSet($name);
        $r = new ReflectionClass($model);
        return $r->hasMethod($method) && $r->getMethod($method)->isPublic();
    }

    /**
     * @inheritDoc
     */
    protected function getMethodToSet(string $name)
    {
        return 'set' . $this->underscoreToCamelCase($name);
    }

    /**
     * @inheritDoc
     */
    public function _isset($model, $name): bool
    {
        return $this->hasMethodToGet($model, $name) && !is_null($this->_get($model, $name));
    }

    /**
     * @inheritDoc
     */
    protected function underscoreToCamelCase($string)
    {
        $str = str_replace('_', '', ucwords($string, '_'));
        return $str;
    }
}