<?php

namespace Cratia\ORM\Model\Interfaces;


use Cratia\ORM\DQL\Interfaces\IFilter;
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

    /**
     * @return bool
     * @throws Exception
     * @throws DBALException
     */
    public function delete(): bool;

    /**
     * @param IFilter $filter
     * @return bool
     * @throws Exception
     * @throws DBALException
     */
    public function deleteBulk(IFilter $filter): bool;

}