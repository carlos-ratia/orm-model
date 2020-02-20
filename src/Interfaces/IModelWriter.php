<?php

namespace Cratia\ORM\Model\Interfaces;


use Doctrine\DBAL\DBALException;
use Exception;

/**
 * Trait ModelWriter
 * @package Cratia\ORM\Model\Traits
 */
interface IModelWriter
{
    /**
     * @return IStrategyModelWrite
     */
    public function getStrategyToWrite();

    /**
     * @param IStrategyModelWrite $strategyWriter
     * @return $this
     */
    public function setStrategyToWrite(IStrategyModelWrite $strategyWriter);

    /**
     * @return bool
     */
    public function hasStrategyToWrite();

    /**
     * @return string
     * @throws Exception
     * @throws DBALException
     */
    public function create();

    /**
     * @return bool
     * @throws Exception
     * @throws DBALException
     */
    public function update();
}