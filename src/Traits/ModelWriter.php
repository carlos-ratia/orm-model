<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Traits;

use Cratia\ORM\Model\Interfaces\IStrategyModelWrite;
use Doctrine\DBAL\DBALException;

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
     * @return IStrategyModelWrite
     */
    public function getStrategyToWrite()
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
     * @return string
     * @throws DBALException
     */
    public function create()
    {
        return $this->getStrategyToWrite()->create($this);
    }

    /**
     * @return bool
     * @throws DBALException
     */
    public function update()
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