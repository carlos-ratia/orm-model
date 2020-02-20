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
    public function update(): bool;
}