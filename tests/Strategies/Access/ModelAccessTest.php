<?php
declare(strict_types=1);


namespace Tests\Cratia\Model\ORM\Strategies\Access;



use Cratia\ORM\Model\Strategies\Access\ModelAccess;
use Tests\Cratia\ORM\Model\Model1;
use Tests\Cratia\ORM\Model\Model4;
use Tests\Cratia\ORM\Model\TestCase;

/**
 * Class ModelAccessTest
 * @package Tests\Crati\Model\ORM\Strategies\Access
 */
class ModelAccessTest extends TestCase
{

    public function testGet1()
    {
        $model = new Model1(10, 20, 30);
        $access = new ModelAccess();
        $access1 = $access->_get($model, 'property_1');
        $access2 = $access->_get($model, 'property_2');
        $access3 = $access->_get($model, 'property_3');
        $this->assertEqualsCanonicalizing(10, $access1);
        $this->assertNotNull($access1);
        $this->assertNotEmpty($access1);
        $this->assertEqualsCanonicalizing(20, $access2);
        $this->assertNotNull($access2);
        $this->assertNotEmpty($access2);
        $this->assertEqualsCanonicalizing(30, $access3);
        $this->assertNotNull($access3);
        $this->assertNotEmpty($access3);
    }

    public function testGet2()
    {
        $model = new Model4(10, 20, 30);
        $access = new ModelAccess();
        $access1 = $access->_get($model, 'property_1');
        $access2 = $access->_get($model, 'property_2');
        $access3 = $access->_get($model, 'property_3');
        $this->assertEqualsCanonicalizing(null, $access1);
        $this->assertNull($access1);
        $this->assertEqualsCanonicalizing(20, $access2);
        $this->assertNotNull($access2);
        $this->assertEqualsCanonicalizing(null, $access3);
        $this->assertNull($access3);
    }

    public function testSet1()
    {
        $model = new Model1();
        $access = new ModelAccess();
        $set1 = $access->_set($model, 'property_1', 11);
        $set2 = $access->_set($model, 'property_2', 22);
        $set3 = $access->_set($model, 'property_3', 33);

        $this->assertEquals(11, $access->_get($model, 'property_1'));
        $this->assertEquals(22, $access->_get($model, 'property_2'));
        $this->assertEquals(33, $access->_get($model, 'property_3'));

        $this->assertEquals(true, $set1);
        $this->assertEquals(true, $set2);
        $this->assertEquals(true, $set3);
    }

    public function testSet2()
    {
        $model = new Model4();
        $access = new ModelAccess();
        $set1 = $access->_set($model, 'property_1', 11);
        $set2 = $access->_set($model, 'property_2', 22);
        $set3 = $access->_set($model, 'property_3', 33);

        $this->assertEquals(null, $access->_get($model, 'property_1'));
        $this->assertEquals(22, $access->_get($model, 'property_2'));
        $this->assertEquals(null, $access->_get($model, 'property_3'));

        $this->assertEquals(false, $set1);
        $this->assertEquals(true, $set2);
        $this->assertEquals(false, $set3);
    }

    public function testIsset1()
    {
        $model = new Model1(10, null, 20);
        $access = new ModelAccess();
        $isset1 = $access->_isset($model, 'property_1');
        $isset2 = $access->_isset($model, 'property_2');
        $isset3 = $access->_isset($model, 'property_3');

        $this->assertEquals(10, $access->_get($model, 'property_1'));
        $this->assertEquals(null, $access->_get($model, 'property_2'));
        $this->assertNull($access->_get($model, 'property_2'));
        $this->assertEquals(20, $access->_get($model, 'property_3'));

        $this->assertEquals(true, $isset1);
        $this->assertEquals(false, $isset2);
        $this->assertEquals(true, $isset3);
    }

    public function testIsset2()
    {
        $model = new Model1();
        $access = new ModelAccess();
        $isset1 = $access->_isset($model, 'property_1');
        $isset2 = $access->_isset($model, 'property_2');
        $isset3 = $access->_isset($model, 'property_3');

        $this->assertNull($access->_get($model, 'property_1'));
        $this->assertNull($access->_get($model, 'property_2'));
        $this->assertNull($access->_get($model, 'property_3'));

        $this->assertEquals(false, $isset1);
        $this->assertEquals(false, $isset2);
        $this->assertEquals(false, $isset3);
    }
}