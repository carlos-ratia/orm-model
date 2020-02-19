<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Strategies\Read;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DBAL\Interfaces\IQueryDTO;
use Cratia\ORM\DBAL\QueryExecute;
use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\GroupBy;
use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\Model\Collection;
use Cratia\ORM\Model\Common\Functions;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Interfaces\IStrategyModelRead;
use Cratia\Pipeline;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class ActiveRecordRead
 * @package Cratia\ORM\Model\Strategies\Read
 */
class ActiveRecordRead implements IStrategyModelRead
{
    /**
     * @var IAdapter|null
     */
    private $adapter;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * ActiveRecordRead constructor.
     * @param IAdapter|null $adapter
     * @param LoggerInterface|null $logger
     */
    public function __construct(IAdapter $adapter = null, LoggerInterface $logger = null)
    {
        $this->adapter = $adapter;
        $this->logger = $logger;
    }

    /**
     * @return IAdapter|null
     */
    public function getAdapter(): ?IAdapter
    {
        return $this->adapter;
    }

    /**
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param IModel $model
     * @return IModel
     */
    public function load(IModel $model): IModel
    {
        Pipeline::try(
            function () use ($model) {
                return $this->checkPrerequisite();
            })
            ->then(function () use ($model) {
                return $this->validModelToLoad($model);
            })
            ->then(function () use ($model) {
                return $this->createQueryToLoad($model);
            })
            ->then(function (IQuery $query) use ($model) {
                return $this->executeQueryToLoad($model, $query);
            })
            ->then(function (IQueryDTO $dto) use (&$model) {
                return $this->setStateModelToLoad($model, $dto);
            })
            ->catch(function (DBALException $e) {
                throw $e;
            })
            ->catch(function (Exception $e) {
                throw $e;
            })();

        return $model;
    }

    /**
     * @throws Exception
     */
    protected function checkPrerequisite()
    {
        if (is_null($this->getAdapter()) || !($this->getAdapter() instanceof IAdapter)) {
            throw new Exception("Error in the " . __METHOD__ . "() -> There is no defined adapter.");
        }
    }

    /**
     * @param IModel $model
     * @return bool
     * @throws Exception
     */
    protected function validModelToLoad(IModel $model): bool
    {
        $return = true;
        foreach ($model->getKeys() as $key) {
            if (is_null($model->{$key})) {
                $return = false;
            }
        }
        if ($return === false) {
            $var_export = json_encode($model->getKeys());
            $class = get_class($model);
            throw new Exception("Error in ActiveRecordRead::load({$class}...)->validModelToLoad(...) -> The key fields ({$var_export}) are NULL or not DEFINED.");
        }
        return true;
    }

    /**
     * @param IModel $model
     * @return IQuery
     * @throws Exception
     */
    protected function createQueryToLoad(IModel $model): IQuery
    {
        $table = $model->getFrom();
        $query = new Query($table);
        foreach ($model->getKeys() as $key) {
            $field = Field::column($table, $key);
            $query
                ->addFilter(Filter::eq($field, $model->{$key}))
                ->addGroupBy(GroupBy::create($field));
        }
        $query
            ->setLimit(1)
            ->setOffset(0);
        return $query;
    }

    /**
     * @param IQuery $query
     * @param IModel $model
     * @return IQueryDTO
     * @throws Exception
     */
    protected function executeQueryToLoad(IModel $model, IQuery $query): IQueryDTO
    {
        $time = -microtime(true);

        $dto = (new QueryExecute($this->getAdapter()))->execute($query);

        $time += microtime(true);
        $this->logRunTime($model, __METHOD__, $time);

        if ($dto->isEmpty()) {
            $key_values = array_map(function ($key) use ($model) {
                return "{$key}: {$model->{$key}}";
            }, $model->getKeys());
            $key_values = implode(', ', $key_values);
            $class = get_class($model);
            throw new Exception("Error in ActiveRecordRead::load({$class}...)->executeQueryToLoad(...) -> The model {$class}({{$key_values}}) not exist.", 412);
        }

        return $dto;
    }

    /**
     * @param IQueryDTO $dto
     * @param IModel $model
     * @return IModel
     */
    protected function setStateModelToLoad(IModel &$model, IQueryDTO $dto): IModel
    {
        foreach ($dto->getRows() as $row) {
            foreach ($row as $attribute => $state) {
                if (!property_exists(get_class($model), $attribute)) {
                    continue;
                }
                $model->{$attribute} = $state;
            }
        }
        return $model;
    }

    /**
     * @param IModel $model
     * @param IQuery $query
     * @return Collection
     */
    public function read(IModel $model, IQuery $query): Collection
    {
        return Pipeline::try(
            function () use ($model) {
                return $this->checkPrerequisite();
            })
            ->then(
            function () use ($query, $model) {
                return $this->createQueryToRead($model, $query);
            })
            ->then(function (IQuery $query) use ($model) {
                return $this->executeQueryToRead($model, $query);
            })
            ->then(function (IQueryDTO $dto) use ($model) {
                return $this->createCollectionToRead($model, $dto);
            })
            ->catch(function (DBALException $e) {
                throw $e;
            })
            ->catch(function (Exception $e) {
                throw $e;
            })
        ();
    }

    /**
     * @param IModel $model
     * @param IQuery $query
     * @return IQuery
     */
    protected function createQueryToRead(IModel $model, IQuery $query): IQuery
    {
        $table = $model->getFrom();
        $_query = new Query($model->getFrom());
        // ADD TABLE OF MODEL
        $_query->addField(Field::table($table));
        // ADD DEFAULT GROUP BYS
        foreach ($model->getKeys() as $key) {
            $field = Field::column($table, $key);
            $_query->addGroupBy(GroupBy::create($field));
        }
        $_query->join($query);
        return $_query;
    }

    /**
     * @param IModel $model
     * @param IQuery $query
     * @return IQueryDTO
     */
    protected function executeQueryToRead(IModel $model, IQuery $query): IQueryDTO
    {
        $time = -microtime(true);

        $dto = (new QueryExecute($this->getAdapter()))->execute($query);

        $time += microtime(true);
        $this->logRunTime($model, __METHOD__, $time);

        return $dto;
    }

    /**
     * @param IModel $model
     * @param IQueryDTO $dto
     * @return Collection
     */
    protected function createCollectionToRead(IModel $model, IQueryDTO $dto): Collection
    {
        return new Collection($model, $dto->getFound(), $dto->getSql(), $dto->getRows());
    }

    /**
     * @param string $__METHOD__
     * @param IModel $model
     * @param $time
     * @return $this
     */
    protected function logRunTime(IModel $model, string $__METHOD__, $time): self
    {
        if (!is_null($this->getLogger())) {
            $get_class = get_class($model);
            $run_time = Functions::pettyRunTime($time);
            $memory = intval(memory_get_usage() / 1024 / 1024) . ' MB';
            $message = "{$__METHOD__}({$get_class}...) -> [Runtime: {$run_time}, Memory: {$memory}]";
            $this->getLogger()->info($message);
        }
        return $this;
    }

    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     */
    public function inject(IAdapter $adapter, LoggerInterface $logger = null)
    {
        $this->adapter = $adapter;
        $this->logger = $logger;
    }
}