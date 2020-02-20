<?php
declare(strict_types=1);


namespace Common;


use Cratia\ORM\Model\Common\ReflectionModel;
use Cratia\ORM\Model\Common\ReflectionProperty;
use ReflectionException;
use Tests\Cratia\ORM\Model\EntityTest2;
use Tests\Cratia\ORM\Model\TestCase;

/**
 * Class ReflectionPropertyTest
 * @package Common
 */
class ReflectionPropertyTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testIsKey1()
    {
        $model = new EntityTest2();
        $r = new ReflectionModel($model);
        /** @var ReflectionProperty $property */
        $property = $r->getProperty('id');
        $this->assertTrue($property->isKey());
        $this->assertTrue($property->isAutoIncremental());
        $this->assertFalse($property->isRequired());
        $this->assertTrue($property->isField());
        $this->assertFalse($property->isHidden());
        $this->assertFalse($property->isNoQueryable());
        $this->assertFalse($property->isAllowUnderscore());

        $property = $r->getProperty('id_connection');
        $this->assertFalse($property->isKey());
        $this->assertTrue($property->isRequired());
        $this->assertTrue($property->isField());
        $this->assertFalse($property->isHidden());
        $this->assertFalse($property->isNoQueryable());
        $this->assertFalse($property->isAllowUnderscore());

        $property = $r->getProperty('network_service');
        $this->assertFalse($property->isKey());
        $this->assertFalse($property->isRequired());
        $this->assertTrue($property->isField());
        $this->assertFalse($property->isHidden());
        $this->assertFalse($property->isNoQueryable());
        $this->assertFalse($property->isAllowUnderscore());
    }

}