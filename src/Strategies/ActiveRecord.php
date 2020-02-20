<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Strategies;

use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\Model\Common\Functions;
use Cratia\ORM\Model\Interfaces\IModel;
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
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     * @return $this
     */
    public function inject(IAdapter $adapter, LoggerInterface $logger = null)
    {
        $this->adapter = $adapter;
        $this->logger = $logger;
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
}