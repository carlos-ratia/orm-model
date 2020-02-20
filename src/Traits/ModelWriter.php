<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Traits;

use Cratia\ORM\Model\Interfaces\IStrategyModelWrite;
use Doctrine\DBAL\DBALException;
use Exception;

/**
 * Trait ModelWriter
 * @package Cratia\ORM\Model\Traits
 */
trait ModelWriter
{
    /**
     * @var IStrategyModelWrite
     */
    private $_strategyToWrite = null;

    /**
     * @return IStrategyModelWrite|null
     */
    public function getStrategyToWrite(): ?IStrategyModelWrite
    {
        return $this->_strategyToWrite;
    }

    /**
     * @param IStrategyModelWrite $strategyWriter
     * @return $this
     */
    public function setStrategyToWrite(IStrategyModelWrite $strategyWriter)
    {
        $this->_strategyToWrite = $strategyWriter;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasStrategyToWrite()
    {
        if (is_null($this->getStrategyToWrite())) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     * @throws Exception
     * @throws DBALException
     */
    public function create(): string
    {
        return $this->getStrategyToWrite()->create($this);
    }

    /**
     * @return bool
     * @throws Exception
     * @throws DBALException
     */
    public function update(): bool
    {
        return $this->getStrategyToWrite()->update($this);
    }

//    /**
//     * @return bool
//     */
//    public function delete()
//    {
//        return $this->getStrategyToWrite()->delete($this);
//    }

//    /**
//     * @param IFilter $filter
//     * @return bool|mixed
//     */
//    public function deleteBulk(IFilter $filter)
//    {
//        return $this->getStrategyToWrite()->deleteBulk($this, $filter);
//    }
}