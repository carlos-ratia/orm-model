<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Strategies\Read;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DBAL\Interfaces\IQueryDTO;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\FilterGroup;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\DQL\Sql;
use Cratia\ORM\Model\Common\ReflectionModel;
use Cratia\ORM\Model\Common\ReflectionProperty;
use Cratia\ORM\Model\Events\EventErrorPayload;
use Cratia\ORM\Model\Events\EventPayload;
use Cratia\ORM\Model\Events\Events;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Interfaces\IStrategyModelWrite;
use Cratia\ORM\Model\Strategies\ActiveRecord;
use Cratia\Pipeline;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;
use ReflectionException;

/**
 * Class ActiveRecordWrite
 */
class ActiveRecordWrite extends ActiveRecord implements IStrategyModelWrite
{

    /**
     * ActiveRecordRead constructor.
     * @param IAdapter|null $adapter
     * @param LoggerInterface|null $logger
     * @param EventManager|null $eventManager
     */
    public function __construct(IAdapter $adapter = null, ?LoggerInterface $logger = null, ?EventManager $eventManager = null)
    {
        parent::__construct($adapter, $logger, $eventManager);
//        $this->attach(new StorageCacheObserver());
    }

    /**
     * @param IModel $model
     * @return ReflectionProperty[]
     * @throws ReflectionException
     */
    protected function getRequiredFields(IModel $model): array
    {
        $result = [];
        /** @var ReflectionModel $r */
        $r = new ReflectionModel($model);
        /** @var ReflectionProperty[] $properties */
        $properties = $r->getProperties();
        /** @var ReflectionProperty $property */
        foreach ($properties as $property) {
            if ($property->isRequired()) {
                $result[$property->getName()] = $property;
            }
        }
        return $result;
    }

    /**
     * @param IModel $model
     * @return ReflectionProperty[]
     * @throws ReflectionException
     */
    protected function getKeysFields(IModel $model): array
    {
        $result = [];
        /** @var ReflectionModel $r */
        $r = new ReflectionModel($model);
        /** @var ReflectionProperty[] $properties */
        $properties = $r->getProperties();
        /** @var ReflectionProperty $property */
        foreach ($properties as $property) {
            if (
                $property->isKey() &&
                !$property->isAutoIncremental()
            ) {
                $result[$property->getName()] = $property;
            }
        }
        return $result;
    }

    /**
     * @param IModel $model
     * @return ReflectionProperty[]
     * @throws ReflectionException
     */
    protected function getFieldsToWrite(IModel $model): array
    {
        $result = [];
        /** @var ReflectionModel $r */
        $r = new ReflectionModel($model);
        /** @var ReflectionProperty[] $properties */
        $properties = $r->getProperties();
        /** @var ReflectionProperty $property */
        foreach ($properties as $property) {
            if (
                $property->isField() &&
                !$property->isAutoIncremental() &&
                !$property->isNoQueryable() &&
                !$property->isAutoIncremental() &&
                isset($model->{$property->getName()}) &&
                !is_null($model->{$property->getName()})
            ) {
                $result[$property->getName()] = $property;
            }
        }
        return $result;
    }

    /**
     * @param IModel $model
     * @throws Exception
     */
    protected function validateKeyFields(IModel $model): void
    {
        /** @var ReflectionProperty $key */
        foreach ($this->getKeysFields($model) as $property) {
            $key = $property->getName();
            if (!isset($model->{$key}) ||
                is_null($model->{$key}) ||
                empty($model->{$key})
            ) {
                $method = __METHOD__;
                $class = get_class($model);
                throw new Exception("Error in {$method}({$class}...) -> The field ({$key}) is NULL, EMPTY or not DEFINED.");
            }
        }
    }

    /**
     * @param IModel $model
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    protected function validateRequiredFields(IModel $model): void
    {
        $fields = $this->getFieldsToWrite($model);
        $required_fields = $this->getRequiredFields($model);
        /** @var ReflectionProperty $required_field */
        foreach ($required_fields as $required_field) {
            $parameter = $required_field->getName();
            if (!in_array($parameter, array_keys($fields)) ||
                !isset($fields[$parameter]) ||
                !isset($model->{$parameter}) ||
                is_null($model->{$parameter}) ||
                empty($model->{$parameter})
            ) {
                $method = __METHOD__;
                $class = get_class($model);
                throw new Exception("Error in {$method}({$class}...) -> The field ({$required_field->getName()}) is NULL, EMPTY or not DEFINED.");
            }
        }
    }

    /**
     * @param IModel $model
     * @param ReflectionProperty[] $fields
     * @return ISql
     */
    protected function createQueryToCreate(IModel $model, array $fields): ISql
    {
        $sql_columns = array_map(function (ReflectionProperty $property) {
            return "{$property->getName()}";
        }, $fields);
        $sql_columns = implode('`,`', $sql_columns);

        $sql_values = array_map(function () {
            return "?";
        }, $fields);
        $sql_values = implode(',', $sql_values);

        $sql_params = [];
        foreach ($fields as $field) {
            if (is_bool($model->{$field->getName()})) {
                $value = filter_var($model->{$field->getName()}, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            } else {
                $value = $model->{$field->getName()};
            }
            $sql_params[] = $value;
        }

        $sql = new Sql();
        $sql->sentence = "INSERT INTO {$model->getFrom()->getTableSchema()} (`{$sql_columns}`) VALUES ({$sql_values})";
        $sql->params = array_merge([], $sql_params);
        return $sql;
    }

    /**
     * @param IModel $model
     * @param $king
     * @param ISql $sql
     * @return IQueryDTO
     * @throws DBALException
     */
    protected function executeQuery(IModel $model, $king, ISql $sql)
    {
        $time = -microtime(true);
        try {
            $dto = $this->getQueryExecute()->executeNonQuery($king, $sql);
        } catch (Exception $e) {
            throw new DBALException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $time += microtime(true);
        $this->logRunTime($model, __METHOD__, $time);
        return $dto;
    }

    /**
     * @param IModel $model
     * @param ReflectionProperty[] $fields
     * @return ISql
     * @throws Exception
     */
    public function createQueryToUpdate(IModel $model, array $fields): ISql
    {
        $sql_values = array_map(function (ReflectionProperty $property) {
            return "`{$property->getName()}` = ?";
        }, $fields);
        $sql_values = implode(',', $sql_values);

        $sql_params = array_map(function (ReflectionProperty $property) use ($model) {
            if (is_bool($model->{$property->getName()})) {
                $value = filter_var($model->{$property->getName()}, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            } else {
                $value = $model->{$property->getName()};
            }
            return $value;
        }, $fields);
        $sql_params = array_values($sql_params);

        $keys = $model->getKeys();
        $where = FilterGroup::and();
        foreach ($keys as $key) {
            if (is_bool($model->{$key})) {
                $value = filter_var($model->{$key}, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            } else {
                $value = $model->{$key};
            }
            $where->add(
                Filter::eq($model->getField($key), $value)
            );
        }

        $sql = new Sql();
        $sql->sentence = "UPDATE {$model->getFrom()->toSQL()->getSentence()} SET {$sql_values} WHERE {$where->toSQL()->getSentence()}";
        $sql->params = array_merge($model->getFrom()->toSQL()->getParams(), $sql_params, $where->toSQL()->getParams());
        return $sql;
    }

    /**
     * @param IModel $model
     * @return ReflectionProperty[]
     * @throws ReflectionException
     */
    protected function getFieldsToDelete(IModel $model): array
    {
        $result = [];
        /** @var ReflectionModel $r */
        $r = new ReflectionModel($model);
        /** @var ReflectionProperty[] $properties */
        $properties = $r->getProperties();
        /** @var ReflectionProperty $property */
        foreach ($properties as $property) {
            if ($property->isKey()) {
                $result[$property->getName()] = $property;
            }
        }
        return $result;
    }

    /**
     * @param IModel $model
     * @param ReflectionProperty[] $fields
     * @return ISql
     * @throws Exception
     */
    public function createQueryToDelete(IModel $model, array $fields): ISql
    {
        $where = FilterGroup::and();
        /** @var ReflectionProperty $field */
        foreach ($fields as $field) {
            $key = $field->getName();
            if (is_bool($model->{$key})) {
                $value = filter_var($model->{$key}, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            } else {
                $value = $model->{$key};
            }
            $where->add(
                Filter::eq($model->getField($key), $value)
            );
        }
        return $this->createQueryToDeleteByFilter($model, $where);
    }

    /**
     * @param IModel $model
     * @param IFilter $filter
     * @return ISql
     */
    public function createQueryToDeleteByFilter(IModel $model, IFilter $filter): ISql
    {
        $sql = new Sql();
        $sql->sentence = "DELETE {$model->getFrom()->getAs()} FROM {$model->getFrom()->toSQL()->getSentence()} WHERE {$filter->toSQL()->getSentence()}";
        $sql->params = array_merge($model->getFrom()->toSQL()->getParams(), $filter->toSQL()->getParams());
        return $sql;
    }

    /**
     * @param IModel $model
     * @return string
     * @throws Exception
     * @throws DBALException
     */
    public function create(IModel $model): string
    {
        $result = Pipeline::try(
            function () use ($model) {
                $this->validateRequiredFields($model);
            })
            ->then(function () use ($model) {
                $this->validateKeyFields($model);
            })
            ->then(function () use ($model) {
                return $this->getFieldsToWrite($model);
            })
            ->then(function (array $fields) use ($model) {
                return $this->createQueryToCreate($model, $fields);
            })
            ->then(function (ISql $sql) use ($model) {
                return $this->executeQuery($model, IAdapter::CREATE, $sql);
            })
            ->tap(function (IQueryDTO $dto) use ($model) {
                $this->notify(Events::ON_MODEL_CREATED, new EventPayload($model, new Query(), $dto));
            })
            ->then(function (IQueryDTO $dto) use ($model) {
                return (string)$dto->getAffectedRows();
            })
            ->then(function (string $affectedRows) use ($model) {
                return $affectedRows;
            })
            ->tapCatch(function (DBALException $e) use (&$model) {
                $this->notify(Events::ON_ERROR, new EventErrorPayload($e));
            })
            ->tapCatch(function (Exception $e) use (&$model) {
                $this->notify(Events::ON_ERROR, new EventErrorPayload($e));
            })
            ->catch(function (DBALException $e) {
                throw $e;
            })
            ->catch(function (Exception $e) {
                throw $e;
            })
        ();

        return (string)$result;
    }

    /**
     * @param IModel $model
     * @return bool
     * @throws Exception
     * @throws DBALException
     */
    public function update(IModel $model): bool
    {
        $result = Pipeline::try(
            function () use ($model) {
                $this->validateRequiredFields($model);
            })
            ->then(function () use ($model) {
                $this->validateKeyFields($model);
            })
            ->then(function () use ($model) {
                return $this->getFieldsToWrite($model);
            })
            ->then(function (array $fields) use ($model) {
                return $this->createQueryToUpdate($model, $fields);
            })
            ->then(function (ISql $sql) use ($model) {
                return $this->executeQuery($model, IAdapter::UPDATE, $sql);
            })
            ->tap(function (IQueryDTO $dto) use ($model) {
                $this->notify(Events::ON_MODEL_UPDATED, new EventPayload($model, new Query(), $dto));
            })
            ->then(function (IQueryDTO $dto) use ($model) {
                return (bool)$dto->getAffectedRows();
            })
            ->then(function (bool $affectedRows) use ($model) {
                return $affectedRows;
            })
            ->tapCatch(function (DBALException $e) {
                $this->notify(Events::ON_ERROR, new EventErrorPayload($e));
            })
            ->tapCatch(function (Exception $e) {
                $this->notify(Events::ON_ERROR, new EventErrorPayload($e));
            })
            ->catch(function (DBALException $e) {
                throw $e;
            })
            ->catch(function (Exception $e) {
                throw $e;
            })
        ();

        return (bool)$result;
    }

    /**
     * @param IModel $model
     * @return bool
     * @throws Exception
     * @throws DBALException
     */
    public function delete(IModel $model): bool
    {
        $result = Pipeline::try(
            function () use ($model) {
                $this->validateKeyFields($model);
            })
            ->then(function () use ($model) {
                return $this->getFieldsToDelete($model);
            })
            ->then(function (array $fields) use ($model) {
                return $this->createQueryToDelete($model, $fields);
            })
            ->then(function (ISql $sql) use ($model) {
                return $this->executeQuery($model, IAdapter::DELETE, $sql);
            })
            ->tap(function (IQueryDTO $dto) use ($model) {
                $this->notify(Events::ON_MODEL_DELETED, new EventPayload($model, new Query(), $dto));
            })
            ->then(function (IQueryDTO $dto) use ($model) {
                return (bool)$dto->getAffectedRows();
            })
            ->tapCatch(function (DBALException $e) {
                $this->notify(Events::ON_ERROR, new EventErrorPayload($e));
            })
            ->tapCatch(function (Exception $e) {
                $this->notify(Events::ON_ERROR, new EventErrorPayload($e));
            })
            ->catch(function (DBALException $e) {
                throw $e;
            })
            ->catch(function (Exception $e) {
                throw $e;
            })
        ();

        return (bool)$result;
    }

    /**
     * @param IModel $model
     * @param IFilter $filter
     * @return bool
     * @throws Exception
     * @throws DBALException
     */
    public function deleteBulk(IModel $model, IFilter $filter): bool
    {
        $result = Pipeline::try(
            function () use ($filter, $model) {
                return $this->createQueryToDeleteByFilter($model, $filter);
            })
            ->then(function (ISql $sql) use ($model) {
                return $this->executeQuery($model, IAdapter::DELETE, $sql);
            })
            ->tap(function (IQueryDTO $dto) use ($model) {
                $this->notify(Events::ON_MODEL_DELETED, new EventPayload($model, new Query(), $dto));
            })
            ->then(function (IQueryDTO $dto) use ($model) {
                return (bool)$dto->getAffectedRows();
            })
            ->tapCatch(function (DBALException $e) {
                $this->notify(Events::ON_ERROR, new EventErrorPayload($e));
            })
            ->tapCatch(function (Exception $e) {
                $this->notify(Events::ON_ERROR, new EventErrorPayload($e));
            })
            ->catch(function (DBALException $e) {
                throw $e;
            })
            ->catch(function (Exception $e) {
                throw $e;
            })
        ();

        return (bool)$result;
    }
}

