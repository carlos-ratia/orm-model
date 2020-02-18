<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model\Traits;


use Cratia\ORM\Model\Strategies\Access\AccessBase;
use Tests\Cratia\ORM\Model\Model2;
use Tests\Cratia\ORM\Model\TestCase;

class ModelAccessTest extends TestCase
{
    public function testGet1()
    {
        $model = new Model2(10, 20, 30);
        $this->assertFalse($model->hasStrategyToAccess());
        $model->setStrategyToAccess(new AccessBase());
        $this->assertEqualsCanonicalizing(10, $model->{'property_1'});
        $this->assertEqualsCanonicalizing(20, $model->{'property_2'});
        $this->assertEqualsCanonicalizing(30, $model->{'property_3'});

        $this->assertInstanceOf(AccessBase::class, $model->getStrategyToAccess());
        $this->assertTrue($model->hasStrategyToAccess());

    }


    public function testSet1()
    {
        $model = new Model2();
        $this->assertFalse($model->hasStrategyToAccess());
        $model->setStrategyToAccess(new AccessBase());

        $model->{'property_1'} = 11;
        $model->{'property_2'} = 22;
        $model->{'property_3'} = 33;

        $this->assertEqualsCanonicalizing(11, $model->{'property_1'});
        $this->assertEqualsCanonicalizing(22, $model->{'property_2'});
        $this->assertEqualsCanonicalizing(33, $model->{'property_3'});

        $this->assertInstanceOf(AccessBase::class, $model->getStrategyToAccess());
        $this->assertTrue($model->hasStrategyToAccess());


    }

    public function testIsset1()
    {
        $model = new Model2(10, null, 20);
        $this->assertFalse($model->hasStrategyToAccess());
        $model->setStrategyToAccess(new AccessBase());

        $isset1 = isset($model->{'property_1'});
        $isset2 = isset($model->{'property_2'});
        $isset3 = isset($model->{'property_3'});

        $this->assertEqualsCanonicalizing(10, $model->{'property_1'});
        $this->assertEqualsCanonicalizing(null, $model->{'property_2'});
        $this->assertNull($model->{'property_2'});
        $this->assertEqualsCanonicalizing(20, $model->{'property_3'});


        $this->assertEquals(true, $isset1);
        $this->assertEquals(false, $isset2);
        $this->assertEquals(true, $isset3);

        $this->assertInstanceOf(AccessBase::class, $model->getStrategyToAccess());
        $this->assertTrue($model->hasStrategyToAccess());

    }

    public function testIsset2()
    {
        $model = new Model2();
        $this->assertFalse($model->hasStrategyToAccess());
        $model->setStrategyToAccess(new AccessBase());

        $isset1 = isset($model->{'property_1'});
        $isset2 = isset($model->{'property_2'});
        $isset3 = isset($model->{'property_3'});

        $this->assertNull($model->{'property_1'});
        $this->assertNull($model->{'property_2'});
        $this->assertNull($model->{'property_3'});

        $this->assertEquals(false, $isset1);
        $this->assertEquals(false, $isset2);
        $this->assertEquals(false, $isset3);

        $this->assertInstanceOf(AccessBase::class, $model->getStrategyToAccess());
        $this->assertTrue($model->hasStrategyToAccess());
    }

}