<?php
declare(strict_types=1);


namespace Cratia\ORM\Model;


use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Traits\ModelAccess;
use Cratia\ORM\Model\Traits\ModelReader;

/**
 * Class Model
 * @package App\Application\Models\ORM\Model
 */
abstract class Model implements IModel
{
    use ModelAccess;
    use ModelReader;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->_strategyToAccess = new \Cratia\ORM\Model\Strategies\Access\ModelAccess();
    }

    /**
     * @inheritDoc
     */
    abstract public function getFrom(): ITable;

    /**
     * @inheritDoc
     */
    abstract public function getKeys();
}