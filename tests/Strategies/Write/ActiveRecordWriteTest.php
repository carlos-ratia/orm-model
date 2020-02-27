<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model\Strategies\Write;


use Cratia\ORM\DBAL\Adapter\Interfaces\IAdapter;
use Cratia\ORM\Model\Strategies\Read\ActiveRecordRead;
use Cratia\ORM\Model\Strategies\Read\ActiveRecordWrite;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;
use Tests\Cratia\ORM\Model\EntityTest2;
use Tests\Cratia\ORM\Model\EntityTest3;
use Tests\Cratia\ORM\Model\TestCase;

/**
 * Class ActiveRecordWriteTest
 * @package Tests\Cratia\ORM\Model\Strategies\Write
 */
class ActiveRecordWriteTest extends TestCase
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testCreate1()
    {
        $modelCreate = new EntityTest2();
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';
        $modelCreate->{'error_exception'} = 'TEST';

        $write = new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );

        $id = $write->create($modelCreate);

        $this->assertIsString($id);

        $modelLoad = new EntityTest2(intval($id));
        $reader = new ActiveRecordRead(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );
        $reader->load($modelLoad);

        $this->assertEquals($modelCreate->{'id_connection'}, $modelLoad->{'id_connection'});
        $this->assertEquals($modelCreate->{'network_params'}, $modelLoad->{'network_params'});
        $this->assertEquals($modelCreate->{'network_service'}, $modelLoad->{'network_service'});
        $this->assertEquals($modelCreate->{'error_exception'}, $modelLoad->{'error_exception'});
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testCreate2()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error in Cratia\ORM\Model\Strategies\Read\ActiveRecordWrite::validateRequiredFields(Tests\Cratia\ORM\Model\EntityTest2...) -> The field (error_exception) is NULL, EMPTY or not DEFINED.');
        $this->expectExceptionCode(0);

        $modelCreate = new EntityTest2();
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';

        $write = new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );

        $write->create($modelCreate);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testCreate3()
    {
        $this->expectException(DBALException::class);

        $modelCreate = new EntityTest3();
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';

        $write = new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );

        $write->create($modelCreate);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testUpdate1()
    {
        $modelCreate = new EntityTest2();
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';
        $modelCreate->{'error_exception'} = 'TEST';

        $write = new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );

        $id = $write->create($modelCreate);

        $this->assertIsString($id);

        $modelLoad = new EntityTest2(intval($id));
        $reader = new ActiveRecordRead(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );
        $reader->load($modelLoad);

        $this->assertEquals($modelCreate->{'id_connection'}, $modelLoad->{'id_connection'});
        $this->assertEquals($modelCreate->{'network_params'}, $modelLoad->{'network_params'});
        $this->assertEquals($modelCreate->{'network_service'}, $modelLoad->{'network_service'});
        $this->assertEquals($modelCreate->{'error_exception'}, $modelLoad->{'error_exception'});

        $modelLoad->{'network_params'} = 'UPDATE';
        $modelLoad->{'network_service'} = 'UPDATE';
        $modelLoad->{'error_exception'} = 'UPDATE';

        $result = $write->update($modelLoad);

        $this->assertTrue($result);

        $modelLoad = $reader->load($modelLoad);

        $this->assertEquals($modelCreate->{'id_connection'}, $modelLoad->{'id_connection'});
        $this->assertEquals('UPDATE', $modelLoad->{'network_params'});
        $this->assertEquals('UPDATE', $modelLoad->{'network_service'});
        $this->assertEquals('UPDATE', $modelLoad->{'error_exception'});
    }

    public function testInject1()
    {
        $writer = new ActiveRecordWrite();

        $this->assertNull($writer->getAdapter());
        $this->assertNull($writer->getLogger());

        $this->assertNotNull($this->getContainer()->get(IAdapter::class));
        $this->assertNotNull($this->getContainer()->get(IAdapter::class));

        $writer->inject($this->getContainer()->get(IAdapter::class), $this->getContainer()->get(LoggerInterface::class));

        $this->assertNotNull($writer->getAdapter());
        $this->assertNotNull($writer->getLogger());

    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testDelete1()
    {
        $modelCreate = new EntityTest2();
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';
        $modelCreate->{'error_exception'} = 'TEST';

        $write = new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );

        $id = $write->create($modelCreate);

        $this->assertIsString($id);

        $modelLoad = new EntityTest2(intval($id));
        $reader = new ActiveRecordRead(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );
        $reader->load($modelLoad);

        $result = $write->delete($modelLoad);

        $this->assertTrue($result);

        $no_exist = false;

        try {
            $reader->load($modelLoad);
        } catch (Exception $e) {
            $no_exist = true;
        }

        $this->assertTrue($no_exist);
    }
}