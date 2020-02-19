<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\Model\Collection;
use Psr\Log\LoggerInterface;

/**
 * Interface IModel
 * @package Cratia\ORM\Model\Interfaces
 */
interface IModel
{
    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool;

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name);

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set($name, $value);

    /**
     * @return ITable
     */
    public function getFrom(): ITable;

    /**
     * @return string[]
     */
    public function getKeys();

    /**
     * @return $this
     */
    public function load();

    /**
     * @param IQuery $query
     * @return Collection
     */
    public function read(IQuery $query): Collection;

    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     * @return IModel
     */
    public function inject(IAdapter $adapter, LoggerInterface $logger = null): IModel;
}