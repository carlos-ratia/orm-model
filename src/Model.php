<?php
declare(strict_types=1);


namespace Cratia\ORM\Model;


use Cratia\ORM\DBAL\Adapter\Interfaces\IAdapter;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Interfaces\IStrategyModelRead;
use Cratia\ORM\Model\Interfaces\IStrategyModelWrite;
use Cratia\ORM\Model\Strategies\Access\AccessBase;
use Cratia\ORM\Model\Strategies\Mapper\MapperBase;
use Cratia\ORM\Model\Strategies\Read\ActiveRecordRead;
use Cratia\ORM\Model\Strategies\Read\ActiveRecordWrite;
use Cratia\ORM\Model\Traits\ModelAccess;
use Cratia\ORM\Model\Traits\ModelMapper;
use Cratia\ORM\Model\Traits\ModelReader;
use Cratia\ORM\Model\Traits\ModelWriter;
use Doctrine\Common\EventManager;
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
    use ModelWriter;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->_strategyToAccess = new AccessBase();
        $this->_strategyToMapper = new MapperBase();
        $this->_strategyToRead = new ActiveRecordRead();
        $this->_strategyToWrite = new ActiveRecordWrite();
    }

    /**
     * @param IAdapter $adapter
     * @param LoggerInterface|null $logger
     * @param EventManager|null $eventManager
     * @return $this
     */
    public function inject(IAdapter $adapter, ?LoggerInterface $logger = null, ?EventManager $eventManager = null): IModel
    {
        if (!is_null($this->getStrategyToRead()) && ($this->getStrategyToRead() instanceof IStrategyModelRead)) {
            $this->getStrategyToRead()->inject($adapter, $logger, $eventManager);
        }
        if (!is_null($this->getStrategyToWrite()) && ($this->getStrategyToWrite() instanceof IStrategyModelWrite)) {
            $this->getStrategyToWrite()->inject($adapter, $logger, $eventManager);
        }
        return $this;
    }

}
