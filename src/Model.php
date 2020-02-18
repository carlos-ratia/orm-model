<?php
declare(strict_types=1);


namespace Cratia\ORM\Model;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Interfaces\IStrategyModelRead;
use Cratia\ORM\Model\Strategies\Access\AccessBase;
use Cratia\ORM\Model\Strategies\Mapper\MapperBase;
use Cratia\ORM\Model\Traits\ModelAccess;
use Cratia\ORM\Model\Traits\ModelMapper;
use Cratia\ORM\Model\Traits\ModelReader;
use Psr\Log\LoggerInterface;

/**
 * Class Model
 * @package App\Application\Models\ORM\Model
 */
abstract class Model implements IModel
{
    use ModelMapper;
    use ModelAccess;
    use ModelReader;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->_strategyToAccess = new AccessBase();
        $this->_strategyToMapper = new MapperBase();
    }

    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     * @return $this
     */
    public function inject(IAdapter $adapter, ?LoggerInterface $logger): IModel
    {
        if (!is_null($this->getStrategyToRead()) && ($this->getStrategyToRead() instanceof IStrategyModelRead)) {
            $this->getStrategyToRead()->inject($adapter, $logger);
        }
        return $this;
    }

}
