<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;


use Cratia\ORM\DBAL\Adapter\Interfaces\IAdapter;
use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Interfaces\IModelRead;
use Cratia\ORM\Model\Interfaces\IModelWriter;
use Cratia\ORM\Model\Interfaces\IStrategyModelMapper;
use Cratia\ORM\Model\Interfaces\IStrategyModelRead;
use Cratia\ORM\Model\Strategies\Access\AccessBase;
use Cratia\ORM\Model\Traits\ModelAccess;
use Cratia\ORM\Model\Traits\ModelReader;
use Cratia\ORM\Model\Traits\ModelWriter;
use Doctrine\Common\EventManager;
use Exception;
use Psr\Log\LoggerInterface;


class Model3 implements IModelRead, IModelWriter, IModel
{
    use ModelAccess;
    use ModelReader;
    use ModelWriter;

    /**
     * @var int|null
     * @key
     * @autoincremental
     */
    private $id;

    /**
     * @var string|null
     */
    private $network_params;


    private $network_service;

    /**
     * @var int
     * @required
     */
    private $id_connection;

    /**
     * @var string|null
     * @required
     */
    private $error_exception;

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
     * @return Model3
     */
    public function setId(?int $id): Model3
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNetworkParams(): ?string
    {
        return $this->network_params;
    }

    /**
     * @param string|null $network_params
     * @return Model3
     */
    public function setNetworkParams(?string $network_params): Model3
    {
        $this->network_params = $network_params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNetworkService()
    {
        return $this->network_service;
    }

    /**
     * @param mixed $network_service
     * @return Model3
     */
    public function setNetworkService($network_service)
    {
        $this->network_service = $network_service;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdConnection(): int
    {
        return $this->id_connection;
    }

    /**
     * @param int $id_connection
     * @return Model3
     */
    public function setIdConnection(int $id_connection): Model3
    {
        $this->id_connection = $id_connection;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getErrorException(): ?string
    {
        return $this->error_exception;
    }

    /**
     * @param string|null $error_exception
     * @return Model3
     */
    public function setErrorException(?string $error_exception): Model3
    {
        $this->error_exception = $error_exception;
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
     * @param EventManager|null $eventManager
     * @return IModel
     */
    public function inject(IAdapter $adapter, ?LoggerInterface $logger = null, ?EventManager $eventManager = null): IModel
    {
        if (!is_null($this->getStrategyToRead()) && ($this->getStrategyToRead() instanceof IStrategyModelRead)) {
            $this->getStrategyToRead()->inject($adapter, $logger);
        }
        return $this;
    }

    /**
     * @param $from
     * @return $this
     * @throws Exception
     */
    public function setFrom($from)
    {
        throw new Exception("Not implemented.");
    }

    /**
     * @return IRelation[]
     * @throws Exception
     */
    public function getRelations()
    {
        throw new Exception("Not implemented.");
    }

    /**
     * @param IRelation $relation
     * @return $this
     * @throws Exception
     */
    public function addRelation(IRelation $relation)
    {
        throw new Exception("Not implemented.");
    }

    /**
     * @param string $property
     * @return IField
     * @throws Exception
     */
    public function getField(string $property): IField
    {
        return Field::column($this->getFrom(), $property, $property);
    }

    /**
     * @return IStrategyModelMapper|null
     */
    public function getStrategyToMapper(): ?IStrategyModelMapper
    {
        // TODO: Implement getStrategyToMapper() method.
    }

    /**
     * @param IStrategyModelMapper $strategyToMapper
     * @return IModel
     */
    public function setStrategyToMapper(IStrategyModelMapper $strategyToMapper)
    {
        // TODO: Implement setStrategyToMapper() method.
    }
}