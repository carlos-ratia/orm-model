<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Strategies\Read;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\FilterGroup;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Sql;
use Cratia\ORM\Model\Common\ReflectionModel;
use Cratia\ORM\Model\Common\ReflectionProperty;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Interfaces\IStrategyModelWrite;
use Cratia\ORM\Model\Strategies\ActiveRecord;
use Cratia\Pipeline;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;
use ReflectionException;

/**
 * Class ActiveRecordWrite
 */
class ActiveRecordWrite extends ActiveRecord implements IStrategyModelWrite
    //, ISubject
{
//    const EVENT_CREATE = "ActiveRecordWrite::CREATE";
//    const EVENT_DELETE = "ActiveRecordWrite::DELETE";
//    const EVENT_UPDATE = "ActiveRecordWrite::UPDATE";
//
//    use SubjectTrait;

    /**
     * ActiveRecordRead constructor.
     * @param IAdapter|null $adapter
     * @param LoggerInterface|null $logger
     */
    public function __construct(IAdapter $adapter = null, LoggerInterface $logger = null)
    {
        parent::__construct($adapter,$logger);
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
     * @return int|string
     * @throws DBALException
     */
    protected function executeQuery(IModel $model, $king, ISql $sql)
    {
        $time = -microtime(true);
        try {
            $affectedRows = $this->getAdapter()->nonQuery($sql->getSentence(), $sql->getParams());
            if ($king === self::CREATE) {
                $affectedRows = $this->getAdapter()->lastInsertId();
            }
        } catch (Exception $e) {
            throw new DBALException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $time += microtime(true);
        $this->logRunTime($model, __METHOD__, $time);
        return $affectedRows;
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
                return $this->executeQuery($model, self::CREATE, $sql);
            })
            ->catch(function (DBALException $e) {
                throw $e;
            })
            ->catch(function (Exception $e) {
                throw $e;
            })
        ();

//        if ($result) {
//            $this->notify(new Event(self::EVENT_CREATE, $model));
//        }
        return $result;
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
                return $this->executeQuery($model, self::UPDATE, $sql);
            })
            ->catch(function (DBALException $e) {
                throw $e;
            })
            ->catch(function (Exception $e) {
                throw $e;
            })
        ();

//        if ($result) {
//            $this->notify(new Event(self::EVENT_UPDATE, $model));
//        }
        return (bool)$result;
    }

//    /**
//     * @param IModel|IModelAccess $model
//     * @return bool
//     * @throws Exception
//     */
//    public function delete($model)
//    {
//        $keys = $model->getKeys();
//        $where = [];
//        foreach ($keys as $key) {
//            $where[$key] = $model->{$key};
//        }
//        $where_cond = implode(' AND ', array_map(function ($k) {
//            return "$k = :$k";
//        }, array_keys($where)));
//
//        $sql = "DELETE FROM {$model->getFrom()->getSource()} WHERE $where_cond";
//        $result = $this->execute($sql, $where);
//        if ($result) {
//            $this->notify(new Event(self::EVENT_DELETE, $model));
//        }
//        return (bool)$result;
//    }

//    /**
//     * @param IModel $model
//     * @param IFilter $filter
//     * @return bool|mixed
//     * @throws Exception
//     */
//    public function deleteBulk($model, IFilter $filter)
//    {
//        $sql_params = [];
//        /** @var IFilter $filter */
//        $sql_where = $filter->getFilter();
//        if ($filter->getFilterParams() !== false) {
//            $sql_params = array_merge($sql_params, $filter->getFilterParams());
//        }
//
//        $sql = "DELETE FROM {$model->getFrom()->getSource()} WHERE {$sql_where}";
//        $result = $this->execute($sql, $sql_params);
//        if ($result) {
//            $this->notify(new Event(self::EVENT_DELETE, $model));
//        }
//        return (bool)$result;
//    }
//
//
//    public function getPerformance($sql = null, $sql_params = null)
//    {
//        $performance = new stdClass;
//        if (!is_null($sql)) {
//            $performance->sql = $this->formatSql($sql, $sql_params);
//        }
//        $performance->run_time = Functions::pettyRunTime($this->getRunTime());
//        $performance->memmory = intval(memory_get_usage() / 1024 / 1024) . ' MB';
//        $performance->system_load_average = sys_getloadavg();
//        return $performance;
//    }
//
//    protected function formatSql($sql, $params)
//    {
//        foreach ($params as $name => $value) {
//            if ((DateTime::createFromFormat('Y-m-d G:i:s', $value) !== false) || // is datetime
//                (!is_numeric($value))) {
//                $value = "'$value'";
//            }
//            $sql = str_replace(':' . $name, $value, $sql);
//        }
//        return $sql;
//    }
}
