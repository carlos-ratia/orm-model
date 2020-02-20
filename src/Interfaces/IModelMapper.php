<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ITable;

/**
 * Interface IModelMapper
 * @package Cratia\ORM\Model\Interfaces
 */
interface IModelMapper
{
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
     * @return $this
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
}