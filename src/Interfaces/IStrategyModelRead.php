<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;

use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\Model\Collection;
use Psr\Log\LoggerInterface;

/**
 * Interface IStrategyModelRead
 * @package App\Application\Models\ORM\Model\Interfaces
 */
interface IStrategyModelRead
{
    /**
     * @param IModel $model
     * @return IModel
     */
    public function load(IModel $model): IModel;

    /**
     * @param IModel $model
     * @param IQuery $query
     * @return Collection
     */
    public function read(IModel $model, IQuery $query): Collection;

    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     */
    public function inject(IAdapter $adapter, LoggerInterface $logger = null);
}