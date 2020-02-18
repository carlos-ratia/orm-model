<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Traits;



use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\Model\Collection;
use Cratia\ORM\Model\Interfaces\IStrategyModelRead;

/**
 * Trait ModelReader
 */
trait ModelReader
{
    /**
     * @var IStrategyModelRead
     */
    private $_strategyToRead = null;

    /**
     * @return IStrategyModelRead
     */
    public function getStrategyToRead()
    {
        return $this->_strategyToRead;
    }

    /**
     * @param IStrategyModelRead $strategyReader
     * @return $this
     */
    public function setStrategyToRead(IStrategyModelRead $strategyReader)
    {
        $this->_strategyToRead = $strategyReader;
        return $this;
    }

    /**
     * @return $this
     */
    public function load()
    {
        $this->getStrategyToRead()->load($this);
        return $this;
    }

    /**
     * @param IQuery $query
     * @return Collection
     */
    public function read(IQuery $query): Collection
    {
        return $this->getStrategyToRead()->read($this, $query);
    }

    /**
     * @return bool
     */
    public function hasStrategyToRead()
    {
        if (is_null($this->getStrategyToRead())) {
            return false;
        }
        return true;
    }
}