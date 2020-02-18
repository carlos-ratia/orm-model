<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Common;


use ReflectionException;

/**
 * Class ReflectionProperty
 * @package Cratia\ORM\Model\Common
 */
class ReflectionProperty extends \ReflectionProperty
{
    const EXCLUDE_TO_RENDER = '@hidden';
    const ALLOW_UNDERSCORE = '@allow_underscore';
    const AUTOINCREMENTAL = '@autoincremental';
    const NOQUERYABLE = '@noqueryable';
    const REQUIRED = '@required';
    const KEY = '@key';

    /**
     * ReflectionProperty constructor.
     * @param $class
     * @param $name
     * @throws ReflectionException
     */
    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
    }

    /**
     * @return bool
     */
    public function isField()
    {
        return
            (
                // not internal property or is allowed underscore start property
                $this->getName()[0] !== '_' ||
                $this->isAllowUnderscore()
            );
    }

    /**
     * @return bool
     */
    public function isAllowUnderscore()
    {
        return strpos($this->getDocComment(), self::ALLOW_UNDERSCORE) !== false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return strpos($this->getDocComment(), self::EXCLUDE_TO_RENDER) !== false;
    }

    /**
     * @return bool
     */
    public function isKey()
    {
        return strpos($this->getDocComment(), self::KEY) !== false;
    }

//    public function isExcludeToRender()
//    {
//        return $this->isHidden() &&
//            !\Core\BunkerApiProxy::getInstance()->isDebug();
//    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return strpos($this->getDocComment(), self::REQUIRED) !== false;
    }

    /**
     * @return bool
     */
    public function isAutoIncremental()
    {
        return strpos($this->getDocComment(), self::AUTOINCREMENTAL) !== false;
    }

    /**
     * @return bool
     */
    public function isNoQueryable()
    {
        return strpos($this->getDocComment(), self::NOQUERYABLE) !== false;
    }
}