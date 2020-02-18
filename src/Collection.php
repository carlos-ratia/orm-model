<?php
declare(strict_types=1);


namespace Cratia\ORM\Model;

use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\Model\Interfaces\IModel;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Collection
 * @package App\Application\Models\ORM\Model
 */
class Collection extends ArrayCollection
{
    /**
     * @var int
     */
    private $found;

    /**
     * @var ISql
     */
    private $sql;

    /**
     * @var IModel
     */
    private $model;

    /**
     * Collection constructor.
     * @param IModel $model
     * @param int $found
     * @param ISql $sql
     * @param array $rows
     */
    public function __construct(IModel $model, int $found, ISql $sql, array $rows)
    {
        $this->model = $model;
        $this->found = $found;
        $this->sql = $sql;
        parent::__construct($rows);
    }

    /**
     * @return int
     */
    public function getFound(): int
    {
        return $this->found;
    }

    /**
     * @return ISql
     */
    public function getSql(): ISql
    {
        return $this->sql;
    }

    /**
     * @return IModel
     */
    public function getModel(): IModel
    {
        return $this->model;
    }

    /**
     * @param array $elements
     * @return $this|ArrayCollection
     */
    protected function createFrom(array $elements)
    {
        return new static($this->model, $this->found, $this->sql, $elements);
    }
}