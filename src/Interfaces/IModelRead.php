<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Interfaces;


use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\Model\Collection;

/**
 * Interface IModelRead
 * @package Cratia\ORM\Model\Interfaces
 */
interface IModelRead
{
    /**
     * @return mixed
     */
    public function load();

    /**
     * @param IQuery $query
     * @return Collection
     */
    public function read(IQuery $query): Collection;
}