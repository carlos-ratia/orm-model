<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;


use Cratia\ORM\DQL\Table;
use Cratia\ORM\Model\Model;

/**
 * Class EntityTest3
 * @package Tests\Cratia\ORM\Model
 */
class EntityTest3 extends Model
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
     * @var string|null
     */
    private $error_exception;

    /**
     * EntityTest3 constructor.
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
     * @return EntityTest3
     */
    public function setId(?int $id): EntityTest3
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
     * @return EntityTest3
     */
    public function setNetworkParams(string $network_params): EntityTest3
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
     * @return EntityTest3
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
     * @return EntityTest3
     */
    public function setIdConnection(int $id_connection): EntityTest3
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
     * @param string $error_exception
     * @return EntityTest3
     */
    public function setErrorException(string $error_exception): EntityTest3
    {
        $this->error_exception = $error_exception;
        return $this;
    }

}