<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Strategies\Read;


use Cratia\ORM\DBAL\Adapter\Interfaces\IAdapter;
use Cratia\ORM\DBAL\Interfaces\IQueryDTO;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IQueryDelete;
use Cratia\ORM\DQL\Interfaces\IQueryInsert;
use Cratia\ORM\DQL\Interfaces\IQueryUpdate;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\DQL\QueryDelete;
use Cratia\ORM\DQL\QueryInsert;
use Cratia\ORM\DQL\QueryUpdate;
use Cratia\ORM\Model\Common\ReflectionModel;
use Cratia\ORM\Model\Common\ReflectionProperty;
use Cratia\ORM\Model\Events\Payloads\EventModelErrorPayload;
use Cratia\ORM\Model\Events\Payloads\EventModelPayload;
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
     * @return IQueryInsert
     * @throws Exception
     */
    protected function createQueryToCreate(IModel $model, array $fields): IQueryInsert
    {
        $query = new QueryInsert($model->getFrom());
        /** @var ReflectionProperty $field */
        foreach ($fields as $field) {
            $key = $field->getName();
            $query
                ->addField(
                    $model->getField($key),
                    $model->{$key}
                );
        }
        return $query;
    }

    /**
     * @param IModel $model
     * @param $king
     * @param IQueryInsert|IQueryUpdate|IQueryDelete $query
     * @return IQueryDTO
     * @throws DBALException
     */
    protected function executeQuery(IModel $model, $king, $query)
    {
        $time = -microtime(true);
        try {
            $dto = $this->getQueryExecute()->executeNonQuery($king, $query);
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
     * @return IQueryUpdate
     * @throws Exception
     */
    public function createQueryToUpdate(IModel $model, array $fields): IQueryUpdate
    {
        $query = new QueryUpdate($model->getFrom());
        /** @var ReflectionProperty $field */
        foreach ($fields as $field) {
            $query
                ->addField(
                    $model->getField($field->getName()),
                    $model->{$field->getName()}
                );
        }
        $keys = $model->getKeys();
        /** @var string $key */
        foreach ($keys as $key) {
            $query->addFilter(
                Filter::eq(
                    $model->getField($key),
                    $model->{$key})
            );
        }
        return $query;
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
     * @return IQueryDelete
     * @throws Exception
     */
    public function createQueryToDelete(IModel $model, array $fields): IQueryDelete
    {
        $query = new QueryDelete($model->getFrom());
        /** @var ReflectionProperty $field */
        foreach ($fields as $field) {
            $key = $field->getName();
            $query->addFilter(
                Filter::eq(
                    $model->getField($key),
                    $model->{$key})
            );
        }
        return $query;
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
            ->then(function (IQueryInsert $query) use ($model) {
                return $this->executeQuery($model, IAdapter::CREATE, $query);
            })
            ->tap(function (IQueryDTO $dto) use ($model) {
                $this->notify(Events::ON_MODEL_CREATED, new EventModelPayload($model, new Query(), $dto));
            })
            ->then(function (IQueryDTO $dto) use ($model) {
                return (string)$dto->getResult();
            })
            ->then(function (string $affectedRows) use ($model) {
                return $affectedRows;
            })
            ->tapCatch(function (DBALException $e) use (&$model) {
                $this->notify(Events::ON_ERROR, new EventModelErrorPayload($e));
            })
            ->tapCatch(function (Exception $e) use (&$model) {
                $this->notify(Events::ON_ERROR, new EventModelErrorPayload($e));
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
            ->then(function (IQueryUpdate $query) use ($model) {
                return $this->executeQuery($model, IAdapter::UPDATE, $query);
            })
            ->tap(function (IQueryDTO $dto) use ($model) {
                $this->notify(Events::ON_MODEL_UPDATED, new EventModelPayload($model, new Query(), $dto));
            })
            ->then(function (IQueryDTO $dto) use ($model) {
                return (bool)$dto->getResult();
            })
            ->then(function (bool $affectedRows) use ($model) {
                return $affectedRows;
            })
            ->tapCatch(function (DBALException $e) {
                $this->notify(Events::ON_ERROR, new EventModelErrorPayload($e));
            })
            ->tapCatch(function (Exception $e) {
                $this->notify(Events::ON_ERROR, new EventModelErrorPayload($e));
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
            ->then(function (IQueryDelete $query) use ($model) {
                return $this->executeQuery($model, IAdapter::DELETE, $query);
            })
            ->tap(function (IQueryDTO $dto) use ($model) {
                $this->notify(Events::ON_MODEL_DELETED, new EventModelPayload($model, new Query(), $dto));
            })
            ->then(function (IQueryDTO $dto) use ($model) {
                return (bool)$dto->getResult();
            })
            ->tapCatch(function (DBALException $e) {
                $this->notify(Events::ON_ERROR, new EventModelErrorPayload($e));
            })
            ->tapCatch(function (Exception $e) {
                $this->notify(Events::ON_ERROR, new EventModelErrorPayload($e));
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
                return (new QueryDelete($model->getFrom()))
                    ->addFilter($filter);
            })
            ->then(function (IQueryDelete $query) use ($model) {
                return $this->executeQuery($model, IAdapter::DELETE, $query);
            })
            ->tap(function (IQueryDTO $dto) use ($model) {
                $this->notify(Events::ON_MODEL_DELETED, new EventModelPayload($model, new Query(), $dto));
            })
            ->then(function (IQueryDTO $dto) use ($model) {
                return (bool)$dto->getResult();
            })
            ->tapCatch(function (DBALException $e) {
                $this->notify(Events::ON_ERROR, new EventModelErrorPayload($e));
            })
            ->tapCatch(function (Exception $e) {
                $this->notify(Events::ON_ERROR, new EventModelErrorPayload($e));
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

