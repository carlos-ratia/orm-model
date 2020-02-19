<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model\Strategies\Read;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\Model\Strategies\Read\ActiveRecordRead;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;
use Tests\Cratia\ORM\Model\EntityTest1;
use Tests\Cratia\ORM\Model\TestCase;


/**
 * Class ActiveRecordReadTest
 * @package Tests\Cratia\ORM\Model\Strategies\Read
 */
class ActiveRecordReadTest extends TestCase
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testRead1()
    {
        $model = new EntityTest1();
        $field10 = Field::column($model->getFrom(), "id");
        $field11 = Field::column($model->getFrom(), "id_connection", "connection_id");
        $field12 = Field::callback(
            function (array $rawRow) {
                $newRow = $rawRow;
                $newRow['connection_id'] = $rawRow['id_connection'];
                return $newRow;
            },
            'connection_id');

        $query = new Query();
        $query
            ->addField($field10)
            ->addField($field11)
            ->addField($field12)
            ->setLimit(1);

        $reader = new ActiveRecordRead($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));
        $collection = $reader->read($model, $query);
        $this->assertInstanceOf(EntityTest1::class, $collection->getModel());
        $this->assertIsArray($collection->getValues());
        $this->assertNotEmpty($collection->getValues());
        $this->assertIsInt($collection->getFound());
        $this->assertInstanceOf(ISql::class, $collection->getSql());
        $this->assertEquals([], $collection->getSql()->getParams());
        $this->assertEquals("SELECT SQL_CALC_FOUND_ROWS test1.*, test1.id AS id, test1.id_connection AS connection_id, 'CALLBACK' AS connection_id FROM {$_ENV['TABLE_TEST']} AS test1 GROUP BY test1.id LIMIT 1 OFFSET 0", $collection->getSql()->getSentence());
        $this->assertNotEmpty($collection->getValues());
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testRead2()
    {
        $this->expectException(DBALException::class);
        $this->expectExceptionMessage("An exception occurred while executing 'SELECT SQL_CALC_FOUND_ROWS test1.*, test1.id AS id, test1.id_connection_error AS error, test1.id_connection AS connection_id, 'CALLBACK' AS connection_id FROM {$_ENV['TABLE_TEST']} AS test1 GROUP BY test1.id LIMIT 1 OFFSET 0");
        $this->expectExceptionCode(0);

        $model = new EntityTest1();
        $field10 = Field::column($model->getFrom(), "id");
        $field11 = Field::column($model->getFrom(), "id_connection_error", "error");
        $field12 = Field::column($model->getFrom(), "id_connection", "connection_id");
        $field13 = Field::callback(
            function (array $rawRow) {
                $newRow = $rawRow;
                $newRow['connection_id'] = $rawRow['id_connection'];
                return $newRow;
            },
            'connection_id');

        $query = new Query();
        $query
            ->addField($field10)
            ->addField($field11)
            ->addField($field12)
            ->addField($field13)
            ->setLimit(1);

        $reader = new ActiveRecordRead($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));
        $reader->read($model, $query);
    }

    public function testRead3()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in the Cratia\ORM\Model\Strategies\Read\ActiveRecordRead::checkPrerequisite() -> There is no defined adapter.");
        $this->expectExceptionCode(0);

        $model = new EntityTest1();
        $field10 = Field::column($model->getFrom(), "id");
        $field11 = Field::column($model->getFrom(), "id_connection_error", "error");
        $field12 = Field::column($model->getFrom(), "id_connection", "connection_id");
        $field13 = Field::callback(
            function (array $rawRow) {
                $newRow = $rawRow;
                $newRow['connection_id'] = $rawRow['id_connection'];
                return $newRow;
            },
            'connection_id');

        $query = new Query();
        $query
            ->addField($field10)
            ->addField($field11)
            ->addField($field12)
            ->addField($field13)
            ->setLimit(1);

        $reader = new ActiveRecordRead();
        $reader->read($model, $query);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad1()
    {
        $modelOrigin = (new EntityTest1(1));
        $reader = new ActiveRecordRead($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));
        $modelLoad = $reader->load($modelOrigin);
        $this->assertEqualsCanonicalizing($modelLoad, $modelOrigin);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad2()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in ActiveRecordRead::load(Tests\Cratia\ORM\Model\EntityTest1...)->validModelToLoad(...) -> The key fields ([\"id\"]) are NULL or not DEFINED.");
        $this->expectExceptionCode(0);
        $model = new EntityTest1();
        $reader = new ActiveRecordRead($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));
        $reader->load($model);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad3()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in ActiveRecordRead::load(Tests\Cratia\ORM\Model\EntityTest1...)->executeQueryToLoad(...) -> The model Tests\Cratia\ORM\Model\EntityTest1({id: -1}) not exist.");
        $this->expectExceptionCode(412);
        $model = new EntityTest1(-1);
        $reader = new ActiveRecordRead($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));
        $reader->load($model);
    }

    public function testLoad4()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in the Cratia\ORM\Model\Strategies\Read\ActiveRecordRead::checkPrerequisite() -> There is no defined adapter.");
        $this->expectExceptionCode(0);
        $model = new EntityTest1(-1);
        $reader = new ActiveRecordRead();
        $reader->load($model);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad5()
    {
        $modelOrigin = (new EntityTest1(1));
        $reader = new ActiveRecordRead();
        $reader->inject($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));
        $modelLoad = $reader->load($modelOrigin);
        $this->assertEqualsCanonicalizing($modelLoad, $modelOrigin);
    }
}