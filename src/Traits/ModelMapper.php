<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Traits;

use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\Model\Interfaces\IStrategyModelMapper;

/**
 * Trait ModelMapper
 * @package Cratia\ORM\Model\Traits
 */
trait ModelMapper
{
    /**
     * @var IStrategyModelMapper
     */
    private $_strategyToMapper = null;

    /**
     * @return IStrategyModelMapper
     */
    public function getStrategyToMapper(): IStrategyModelMapper
    {
        return $this->_strategyToMapper;
    }

    /**
     * @param IStrategyModelMapper $strategyToMapper
     * @return $this
     */
    public function setStrategyToMapper(IStrategyModelMapper $strategyToMapper)
    {
        $this->_strategyToMapper = $strategyToMapper;
        return $this;
    }

    /**
     * @param $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->getStrategyToMapper()->setFrom($this, $from);
        return $this;
    }

    /**
     * @return ITable
     */
    public function getFrom(): ITable
    {
        return $this->getStrategyToMapper()->getFrom($this);
    }

    /**
     * @return IRelation[]
     */
    public function getRelations()
    {
        return $this->getStrategyToMapper()->getRelations($this);
    }

    /**
     * @param IRelation $relation
     * @return $this
     */
    public function addRelation(IRelation $relation)
    {
        $this->getStrategyToMapper()->addRelation($this, $relation);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getKeys()
    {
        return $this->getStrategyToMapper()->getKeys($this);
    }

    /**
     * @param string $property
     * @return IField
     */
    public function getField(string $property): IField
    {
        return $this->getStrategyToMapper()->getField($this, $property);
    }

}