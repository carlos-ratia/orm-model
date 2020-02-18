<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model\Traits;


use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Relation;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\DQL\TableNull;
use Cratia\ORM\Model\Interfaces\IStrategyModelMapper;
use Cratia\ORM\Model\Strategies\Mapper\MapperBase;
use Tests\Cratia\ORM\Model\Model6;
use Tests\Cratia\ORM\Model\Model7;
use Tests\Cratia\ORM\Model\TestCase;

/**
 * Class ModelMapperTest
 * @package Tests\Cratia\ORM\Model\Traits
 */
class ModelMapperTest extends TestCase
{

    public function testGetKeys1()
    {
        $model = new Model6(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $model->setStrategyToMapper(new MapperBase());

        $this->assertIsArray($model->getKeys());
        $this->assertEqualsCanonicalizing(['property_1', 'property_2'], $model->getKeys());
        $this->assertInstanceOf(MapperBase::class, $model->getStrategyToMapper());
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetKeys2()
    {
        $model = new Model7(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $model->setStrategyToMapper(new MapperBase());

        $keys = $model->getKeys();
        $this->assertIsArray($keys);
        $this->assertEmpty($keys);
        $this->assertEqualsCanonicalizing([], $keys);
    }

    public function testSetFrom1()
    {
        $model = new Model6(1, 2, 3);
        $model->setStrategyToMapper(new MapperBase());
        $table = new Table($_ENV['TABLE_TEST'], "test1");
        $model->setFrom($table);
        $this->assertInstanceOf(Table::class, $model->getFrom());
        $this->assertEqualsCanonicalizing($table, $model->getFrom());
    }

    public function testSetFrom2()
    {
        $model = new Model6(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $model->setStrategyToMapper(new MapperBase());
        $model->setFrom($_ENV['TABLE_TEST']);
        $this->assertInstanceOf(Table::class, $model->getFrom());
        $this->assertEqualsCanonicalizing(new Table($_ENV['TABLE_TEST'], $_ENV['TABLE_TEST']), $model->getFrom());
    }

    public function testGetFrom1()
    {
        $model = new Model6(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $model->setStrategyToMapper(new MapperBase());
        $this->assertInstanceOf(TableNull::class, $model->getFrom());
    }

    public function testGetFrom2()
    {
        $model = new Model6(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $model->setStrategyToMapper(new MapperBase());
        $model->setFrom($_ENV['TABLE_TEST']);
        $this->assertInstanceOf(Table::class, $model->getFrom());
        $this->assertEqualsCanonicalizing(new Table($_ENV['TABLE_TEST'], $_ENV['TABLE_TEST']), $model->getFrom());
    }

    public function testGetRelations1()
    {
        $model = new Model6(1, 2, 3);
        /** @var IStrategyModelMapper $strategy */
        $model->setStrategyToMapper(new MapperBase());
        $model->setFrom($_ENV['TABLE_TEST']);
        $this->assertEqualsCanonicalizing([], $model->getRelations());
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

        $model = new Model6(1, 2, 3);
        $model->setStrategyToMapper(new MapperBase());
        $model->setFrom($table1);
        $model->addRelation($relations[0]);
        $this->assertEqualsCanonicalizing([$relations[0]], $model->getRelations());
        $model->addRelation($relations[1]);
        $this->assertEqualsCanonicalizing($table2, $model->getFrom());
    }

}