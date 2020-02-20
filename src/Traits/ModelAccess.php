<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Traits;


use Cratia\ORM\Model\Interfaces\IStrategyModelAccess;

/**
 * Trait ModelAccess
 * @package Cratia\ORM\Model\Traits
 */
trait ModelAccess
{
    /**
     * The Strategy PropertyAccess component provides function to read and write from/to an object using a simple
     * string notation.
     * @var IStrategyModelAccess
     */
    private $_strategyToAccess = null;

    /**
     * @inheritDoc
     */
    public function __isset($name): bool
    {
        return $this->getStrategyToAccess()->_isset($this, $name);
    }

    /**
     * @inheritDoc
     */
    public function __set($name, $value): self
    {
        $this->getStrategyToAccess()->_set($this, $name, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        return $this->getStrategyToAccess()->_get($this, $name);
    }

    /**
     * @return IStrategyModelAccess|null
     */
    public function getStrategyToAccess(): ?IStrategyModelAccess
    {
        return $this->_strategyToAccess;
    }

    /**
     * @param IStrategyModelAccess $strategyPropertyAccess
     * @return $this;
     */
    public function setStrategyToAccess(IStrategyModelAccess $strategyPropertyAccess)
    {
        $this->_strategyToAccess = $strategyPropertyAccess;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasStrategyToAccess(): bool
    {
        if (is_null($this->getStrategyToAccess())) {
            return false;
        }
        return true;
    }
}