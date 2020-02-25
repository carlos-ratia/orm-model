<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Strategies;

use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DBAL\QueryExecute;
use Cratia\ORM\Model\Common\Functions;
use Cratia\ORM\Model\Interfaces\IModel;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventManager;
use Psr\Log\LoggerInterface;

/**
 * Class ActiveRecord
 * @package Cratia\ORM\Model\Strategies
 */
class ActiveRecord
{
    /**
     * @var IAdapter|null
     */
    protected $adapter;

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @var EventManager|null
     */
    private $eventManager;

    /**
     * ActiveRecordRead constructor.
     * @param IAdapter|null $adapter
     * @param LoggerInterface|null $logger
     * @param EventManager|null $eventManager
     */
    public function __construct(IAdapter $adapter = null, ?LoggerInterface $logger = null, ?EventManager $eventManager = null)
    {
        $this->adapter = $adapter;
        $this->logger = $logger;
        $this->eventManager = $eventManager;
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
     * @return EventManager|null
     */
    public function getEventManager(): ?EventManager
    {
        return $this->eventManager;
    }

    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     * @param EventManager|null $eventManager
     * @return $this
     */
    public function inject(IAdapter $adapter, ?LoggerInterface $logger = null, ?EventManager $eventManager = null)
    {
        $this->adapter = $adapter;
        $this->logger = $logger;
        $this->eventManager = $eventManager;
        return $this;
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

    protected function getQueryExecute(): QueryExecute
    {
        return new QueryExecute($this->getAdapter(), $this->getLogger(), $this->getEventManager());
    }

    /**
     * @param string $eventName
     * @param EventArgs $event
     * @return $this
     */
    protected function notify(string $eventName, EventArgs $event)
    {
        if (
            !is_null($eventManager = $this->getEventManager()) &&
            ($eventManager instanceof EventManager)
        ) {
            $eventManager->dispatchEvent($eventName, $event);
        }
        return $this;
    }
}