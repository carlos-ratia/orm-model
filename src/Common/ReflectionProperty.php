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
    public function isAllowUnderscore(): bool
    {
        if ($this->getDocComment() === false) {
            return false;
        } else {
            return strpos($this->getDocComment(), self::ALLOW_UNDERSCORE) !== false;
        }
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        if ($this->getDocComment() === false) {
            return false;
        } else {
            return strpos($this->getDocComment(), self::EXCLUDE_TO_RENDER) !== false;
        }
    }

    /**
     * @return bool
     */
    public function isKey(): bool
    {
        if ($this->getDocComment() === false) {
            return false;
        } else {
            return strpos($this->getDocComment(), self::KEY) !== false;
        }
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        if ($this->getDocComment() === false) {
            return false;
        } else {
            return strpos($this->getDocComment(), self::REQUIRED) !== false;
        }
    }

    /**
     * @return bool
     */
    public function isAutoIncremental(): bool
    {
        if ($this->getDocComment() === false) {
            return false;
        } else {
            return strpos($this->getDocComment(), self::AUTOINCREMENTAL) !== false;
        }
    }

    /**
     * @return bool
     */
    public function isNoQueryable(): bool
    {
        if ($this->getDocComment() === false) {
            return false;
        } else {
            return strpos($this->getDocComment(), self::NOQUERYABLE) !== false;
        }
    }
}