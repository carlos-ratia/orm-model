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
     */
    private $id;

    /**
     * @var string
     */
    private $network_params;

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
}