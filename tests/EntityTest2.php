<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;


use Cratia\ORM\DQL\Table;
use Cratia\ORM\Model\Model;

/**
 * Class EntityTest2
 * @package Tests\Cratia\ORM\Model
 */
class EntityTest2 extends Model
{
    /**
     * @var int|null
     * @key
     * @autoincremental
     */
    private $id;

    /**
     * @var string
     */
    private $network_params;


    private $network_service;

    /**
     * @var int
     * @required
     */
    private $id_connection;

    /**
     * @var string
     * @required
     */
    private $error_exception;


    /**
     * Entity constructor.
     * @param int|null $id
     */
    public function __construct(int $id = null)
    {
        parent::__construct();
        $this->id = $id;
        $this->setFrom(new Table($_ENV['TABLE_TEST'], "test1"));
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
     * @return EntityTest2
     */
    public function setId(?int $id): EntityTest2
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkParams(): string
    {
        return $this->network_params;
    }

    /**
     * @param string $network_params
     * @return EntityTest2
     */
    public function setNetworkParams(string $network_params): EntityTest2
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
     * @return EntityTest2
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
     * @return EntityTest2
     */
    public function setIdConnection(int $id_connection): EntityTest2
    {
        $this->id_connection = $id_connection;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrorException()
    {
        return $this->error_exception;
    }

    /**
     * @param mixed $error_exception
     * @return EntityTest2
     */
    public function setErrorException($error_exception)
    {
        $this->error_exception = $error_exception;
        return $this;
    }

}