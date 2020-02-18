<?php


namespace Tests\Cratia\ORM\Model\Strategies\Mapper;


use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Relation;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\DQL\TableNull;
use Cratia\ORM\Model\Interfaces\IStrategyModelMapper;
use Cratia\ORM\Model\Strategies\Mapper\MapperBase;
use Tests\Cratia\ORM\Model\Model1;
use Tests\Cratia\ORM\Model\Model5;
use Tests\Cratia\ORM\Model\TestCase;

/**
 * Class ModelMapperTest
 * @package Tests\Cratia\ORM\Model\Strategies\Mapper
 */
class ModelMapperTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGetKeys1()
    {
        $model = new Model5(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $strategy = new MapperBase();

        $keys = $strategy->getKeys($model);
        $this->assertIsArray($keys);
        $this->assertEqualsCanonicalizing(['property_1', 'property_2'], $keys);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetKeys2()
    {
        $model = new Model1(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $strategy = new MapperBase();

        $keys = $strategy->getKeys($model);
        $this->assertIsArray($keys);
        $this->assertEmpty($keys);
        $this->assertEqualsCanonicalizing([], $keys);
    }

    public function testSetFrom1()
    {
        $model = new Model1(1, 2, 3);
        $table = new Table($_ENV['TABLE_TEST'], "test1");
        /** @var IStrategyModelMapper $strategy */
        $strategy = new MapperBase();
        $strategy->setFrom($model, $table);
        $this->assertInstanceOf(Table::class, $strategy->getFrom($model));
        $this->assertEqualsCanonicalizing($table, $strategy->getFrom($model));
    }

    public function testSetFrom2()
    {
        $model = new Model1(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $strategy = new MapperBase();
        $strategy->setFrom($model, $_ENV['TABLE_TEST']);
        $this->assertInstanceOf(Table::class, $strategy->getFrom($model));
        $this->assertEqualsCanonicalizing(new Table($_ENV['TABLE_TEST'], $_ENV['TABLE_TEST']), $strategy->getFrom($model));

    }

    public function testGetFrom1()
    {
        $model = new Model1(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $strategy = new MapperBase();
        $this->assertInstanceOf(TableNull::class, $strategy->getFrom($model));
    }

    public function testGetFrom2()
    {
        $model = new Model1(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $strategy = new MapperBase();
        $strategy->setFrom($model, $_ENV['TABLE_TEST']);
        $this->assertInstanceOf(Table::class, $strategy->getFrom($model));
        $this->assertEqualsCanonicalizing(new Table($_ENV['TABLE_TEST'], $_ENV['TABLE_TEST']), $strategy->getFrom($model));
    }

    public function testGetRelations1()
    {
        $model = new Model1(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $strategy = new MapperBase();
        $strategy->setFrom($model, $_ENV['TABLE_TEST']);
        $this->assertEqualsCanonicalizing([], $strategy->getRelations($model));
    }

    public function testGetRelations2()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");
        $field11 = Field::column($table1, "id_brand_table_1", "id_brand_table_1");

        $table2 = new Table("table_2", "t2");
        $field20 = Field::column($table2, "id", "consumer_id");

        $table3 = new Table("brand_table_1", "bt1");
        $field30 = Field::column($table3, "id", "brand_consumer_id");

        $relations =
            [
                Relation::inner($field10, $field20),
                Relation::inner($field11, $field30)
            ];

        $table2 = new Table("table_1", "t1");
        $table2
            ->addRelation($relations[0])
            ->addRelation($relations[1]);

        $model = new Model1(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $strategy = new MapperBase();
        $strategy->setFrom($model, $table1);
        $strategy->addRelation($model, $relations[0]);
        $this->assertEqualsCanonicalizing([$relations[0]], $strategy->getRelations($model));
        $strategy->addRelation($model, $relations[1]);
        $this->assertEqualsCanonicalizing($table2, $strategy->getFrom($model));
    }
}