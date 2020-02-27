<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;


use Cratia\ORM\DBAL\Adapter\Interfaces\IAdapter;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class ModelTest
 */
class ModelTest extends TestCase
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testInject1()
    {
        $model = new EntityTest2();

        $this->assertTrue($this->getContainer()->has(IAdapter::class));
        $this->assertTrue($this->getContainer()->has(LoggerInterface::class));

        $this->assertNotNull($this->getContainer()->get(IAdapter::class));
        $this->assertNotNull($this->getContainer()->get(LoggerInterface::class));

        $model->inject($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));

        $this->assertInstanceOf(IAdapter::class, $model->getStrategyToRead()->getAdapter());
        $this->assertInstanceOf(LoggerInterface::class, $model->getStrategyToRead()->getLogger());

        $this->assertNotNull($model->getStrategyToRead()->getAdapter());
        $this->assertNotNull($model->getStrategyToRead()->getLogger());
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad1()
    {
        $modelOrigin = new EntityTest2(1);
        $modelOrigin->inject($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));
        $modelLoad = $modelOrigin->load();
        $this->assertEqualsCanonicalizing($modelLoad, $modelOrigin);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad2()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in ActiveRecordRead::load(Tests\Cratia\ORM\Model\EntityTest2...)->validModelToLoad(...) -> The key fields ([\"id\"]) are NULL or not DEFINED.");
        $this->expectExceptionCode(0);
        $model = new EntityTest2();
        $model->inject($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));
        $model->load();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad3()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in ActiveRecordRead::load(Tests\Cratia\ORM\Model\EntityTest2...)->executeQueryToLoad(...) -> The model Tests\Cratia\ORM\Model\EntityTest2({id: -1}) not exist.");
        $this->expectExceptionCode(412);
        $model = new EntityTest2(-1);
        $model->inject($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));
        $model->load();
    }

    public function testLoad4()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in the Cratia\ORM\Model\Strategies\Read\ActiveRecordRead::checkPrerequisite() -> There is no defined adapter.");
        $this->expectExceptionCode(0);
        $model = new EntityTest2(-1);
        $model->load();
    }
}