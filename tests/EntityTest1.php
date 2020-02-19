<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;


use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\Model\Model;

/**
 * Class Entity
 * @package App\Application\Models
 */
class EntityTest1 extends Model
{
    /**
     * @var int|null
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
        $this->id = $id;
        parent::__construct();
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
     * @return EntityTest1
     */
    public function setId(?int $id): EntityTest1
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
     * @return EntityTest1
     */
    public function setNetworkParams(string $network_params): EntityTest1
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