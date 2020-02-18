<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;


use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Interfaces\IModelRead;
use Cratia\ORM\Model\Traits\ModelAccess;
use Cratia\ORM\Model\Traits\ModelReader;


class Model3 implements IModelRead, IModel
{
    use ModelAccess;
    use ModelReader;

    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $network_params;

    /**
     * Entity constructor.
     * @param int|null $id
     */
    public function __construct(int $id = null)
    {
        $this->id = $id;
        $this->_strategyToAccess = new \Cratia\ORM\Model\Strategies\Access\ModelAccess();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkParams(): ?string
    {
        return $this->network_params;
    }

    /**
     * @param string $network_params
     * @return $this
     */
    public function setNetworkParams(string $network_params): self
    {
        $this->network_params = $network_params;
        return $this;
    }

    /**
     * @return ITable
     */
    public function getFrom(): ITable
    {
        return new Table($_ENV['TABLE_TEST'], "test1");
    }

    /**
     * @return string[]
     */
    public function getKeys()
    {
        return ['id'];
    }
}