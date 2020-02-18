<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model\Traits;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\Model\Interfaces\IModel;
use Cratia\ORM\Model\Strategies\Read\ActiveRecordRead;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;
use Tests\Cratia\ORM\Model\Model3;
use Tests\Cratia\ORM\Model\TestCase;

/**
 * Class ModelReaderTest
 * @package Traits
 */
class ModelReaderTest extends TestCase
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testRead1()
    {
        $model = new Model3();
        $model->setStrategyToRead(
            new ActiveRecordRead(
                $this->getContainer()->get(IAdapter::class),
                $this->getContainer()->get(LoggerInterface::class)
            )
        );

        $field10 = Field::column($model->getFrom(), "id");
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
            ->addField($field12)
            ->setLimit(1);

        $collection = $model->read($query);
        $this->assertInstanceOf(Model3::class, $collection->getModel());
        $this->assertIsArray($collection->getValues());
        $this->assertNotEmpty($collection->getValues());
        $this->assertIsInt($collection->getFound());
        $this->assertInstanceOf(ISql::class, $collection->getSql());
        $this->assertEquals([], $collection->getSql()->getParams());
        $this->assertEquals("SELECT SQL_CALC_FOUND_ROWS test1.*, test1.id AS id, 'CALLBACK' AS connection_id FROM {$_ENV['TABLE_TEST']} AS test1 GROUP BY test1.id LIMIT 1 OFFSET 0", $collection->getSql()->getSentence());
        $this->assertNotEmpty($collection->getValues());
        $this->assertInstanceOf(ActiveRecordRead::class, $model->getStrategyToRead());
        $this->assertTrue($model->hasStrategyToRead());

    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testRead2()
    {
        $this->expectException(DBALException::class);
        $model = new Model3();
        $model->setStrategyToRead(
            new ActiveRecordRead(
                $this->getContainer()->get(IAdapter::class),
                $this->getContainer()->get(LoggerInterface::class)
            )
        );

        $field10 = Field::column($model->getFrom(), "_id");
        $query = new Query();
        $query
            ->addField($field10)
            ->setLimit(1);

        $model->read($query);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testRead4()
    {
        $model = new Model3();
        $model
            ->setStrategyToRead(new ActiveRecordRead())
            ->inject(
                $this->getContainer()->get(IAdapter::class),
                $this->getContainer()->get(LoggerInterface::class)
            );

        $field10 = Field::column($model->getFrom(), "id");
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
            ->addField($field12)
            ->setLimit(1);

        $collection = $model->read($query);
        $this->assertInstanceOf(Model3::class, $collection->getModel());
        $this->assertIsArray($collection->getValues());
        $this->assertNotEmpty($collection->getValues());
        $this->assertIsInt($collection->getFound());
        $this->assertInstanceOf(ISql::class, $collection->getSql());
        $this->assertEquals([], $collection->getSql()->getParams());
        $this->assertEquals("SELECT SQL_CALC_FOUND_ROWS test1.*, test1.id AS id, 'CALLBACK' AS connection_id FROM {$_ENV['TABLE_TEST']} AS test1 GROUP BY test1.id LIMIT 1 OFFSET 0", $collection->getSql()->getSentence());
        $this->assertNotEmpty($collection->getValues());
        $this->assertInstanceOf(ActiveRecordRead::class, $model->getStrategyToRead());
        $this->assertTrue($model->hasStrategyToRead());

    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testRead3()
    {
        $message = __METHOD__ . __LINE__;
        $code = rand(0, 500);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode($code);

        $model = new Model3();
        $model->setStrategyToRead(
            new ActiveRecordRead(
                $this->getContainer()->get(IAdapter::class),
                $this->getContainer()->get(LoggerInterface::class)
            )
        );

        $field10 = Field::column($model->getFrom(), "id");
        $field12 = Field::callback(
            function (array $_) use ($code, $message) {
                throw new Exception($message, $code);
            },
            'connection_id');

        $query = new Query();
        $query
            ->addField($field10)
            ->addField($field12)
            ->setLimit(1);

        $model->read($query);

    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad1()
    {
        $model = new Model3(1);

        $this->assertFalse($model->hasStrategyToRead());
        $this->assertNotNull($model->getId());
        $this->assertNull($model->getNetworkParams());

        $model->setStrategyToRead(
            new ActiveRecordRead(
                $this->getContainer()->get(IAdapter::class),
                $this->getContainer()->get(LoggerInterface::class)
            )
        );
        $model->load();

        $this->assertInstanceOf(IModel::class, $model);
        $this->assertNotNull($model->getId());
        $this->assertNotNull($model->getNetworkParams());

    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad2()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in ActiveRecordRead::load(Tests\Cratia\ORM\Model\Model3...)->validModelToLoad(...) -> The key fields ([\"id\"]) are NULL or not DEFINED.");
        $this->expectExceptionCode(0);
        $model = new Model3();
        $model->setStrategyToRead(
            new ActiveRecordRead(
                $this->getContainer()->get(IAdapter::class),
                $this->getContainer()->get(LoggerInterface::class)
            )
        );
        $model->load();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testLoad3()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in ActiveRecordRead::load(Tests\Cratia\ORM\Model\Model3...)->executeQueryToLoad(...) -> The model Tests\Cratia\ORM\Model\Model3({id: -1}) not exist.");
        $this->expectExceptionCode(412);
        $model = new Model3(-1);
        $model->setStrategyToRead(
            new ActiveRecordRead(
                $this->getContainer()->get(IAdapter::class),
                $this->getContainer()->get(LoggerInterface::class)
            )
        );
        $model->load();
    }

}