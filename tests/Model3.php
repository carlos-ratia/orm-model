<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Interfaces\IModelRead;
use Cratia\ORM\Model\Interfaces\IStrategyModelRead;
use Cratia\ORM\Model\Strategies\Access\AccessBase;
use Cratia\ORM\Model\Traits\ModelAccess;
use Cratia\ORM\Model\Traits\ModelReader;
use Psr\Log\LoggerInterface;


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
        $this->_strategyToAccess = new AccessBase();
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

    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     * @return IModel
     */
    public function inject(IAdapter $adapter, ?LoggerInterface $logger): IModel
    {
        if (!is_null($this->getStrategyToRead()) && ($this->getStrategyToRead() instanceof IStrategyModelRead)) {
            $this->getStrategyToRead()->inject($adapter, $logger);
        }
        return $this;
    }
}