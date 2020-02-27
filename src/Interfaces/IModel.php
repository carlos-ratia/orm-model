<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;


use Cratia\ORM\DBAL\Adapter\Interfaces\IAdapter;
use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\Model\Collection;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Interface IModel
 * @package Cratia\ORM\Model\Interfaces
 */
interface IModel
{
    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     * @param EventManager|null $eventManager
     * @return IModel
     */
    public function inject(IAdapter $adapter, ?LoggerInterface $logger = null, ?EventManager $eventManager = null): IModel;

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool;

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set($name, $value);

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name);

    /**
     * @return IStrategyModelAccess|null
     */
    public function getStrategyToAccess(): ?IStrategyModelAccess;

    /**
     * @param IStrategyModelAccess $strategyPropertyAccess
     * @return IModel;
     */
    public function setStrategyToAccess(IStrategyModelAccess $strategyPropertyAccess);

    /**
     * @return bool
     */
    public function hasStrategyToAccess(): bool;

    /**
     * @return IStrategyModelMapper|null
     */
    public function getStrategyToMapper(): ?IStrategyModelMapper;

    /**
     * @param IStrategyModelMapper $strategyToMapper
     * @return IModel
     */
    public function setStrategyToMapper(IStrategyModelMapper $strategyToMapper);

    /**
     * @param $from
     * @return $this
     */
    public function setFrom($from);

    /**
     * @return ITable
     */
    public function getFrom(): ITable;

    /**
     * @return IRelation[]
     */
    public function getRelations();

    /**
     * @param IRelation $relation
     * @return IModel
     */
    public function addRelation(IRelation $relation);

    /**
     * @return string[]
     */
    public function getKeys();

    /**
     * @param string $property
     * @return IField
     */
    public function getField(string $property): IField;

    /**
     * @return IStrategyModelRead|null
     */
    public function getStrategyToRead(): ?IStrategyModelRead;

    /**
     * @param IStrategyModelRead $strategyReader
     * @return IModel
     */
    public function setStrategyToRead(IStrategyModelRead $strategyReader);

    /**
     * @return IModel
     */
    public function load();

    /**
     * @param IQuery $query
     * @return Collection
     */
    public function read(IQuery $query): Collection;

    /**
     * @return bool
     */
    public function hasStrategyToRead(): bool;

    /**
     * @return IStrategyModelWrite|null
     */
    public function getStrategyToWrite(): ?IStrategyModelWrite;

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
    public function create(): string;

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