<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model\Traits;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\Model\Strategies\Read\ActiveRecordRead;
use Cratia\ORM\Model\Strategies\Read\ActiveRecordWrite;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;
use Tests\Cratia\ORM\Model\EntityTest2;
use Tests\Cratia\ORM\Model\EntityTest3;
use Tests\Cratia\ORM\Model\Model3;
use Tests\Cratia\ORM\Model\TestCase;

/**
 * Class ModelWriterTest
 * @package Tests\Cratia\ORM\Model\Traits
 */
class ModelWriterTest extends TestCase
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws DBALException
     */
    public function testCreate1()
    {
        $modelCreate = new Model3();

        $this->assertFalse($modelCreate->hasStrategyToWrite());

        $modelCreate->setStrategyToWrite(
            new ActiveRecordWrite(
                $this->getContainer()->get(IAdapter::class),
                $this->getContainer()->get(LoggerInterface::class)
            )
        );

        $this->assertTrue($modelCreate->hasStrategyToWrite());

        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';
        $modelCreate->{'error_exception'} = 'TEST';


        $id = $modelCreate->create();

        $this->assertIsString($id);

        $modelLoad = new Model3(intval($id));
        $modelLoad->setStrategyToRead(new ActiveRecordRead(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));
        $modelLoad->load();

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
        $this->expectExceptionMessage('Error in Cratia\ORM\Model\Strategies\Read\ActiveRecordWrite::validateRequiredFields(Tests\Cratia\ORM\Model\Model3...) -> The field (error_exception) is NULL, EMPTY or not DEFINED.');
        $this->expectExceptionCode(0);

        $modelCreate = new Model3();
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';

        $this->assertFalse($modelCreate->hasStrategyToWrite());

        $modelCreate->setStrategyToWrite(
            new ActiveRecordWrite(
                $this->getContainer()->get(IAdapter::class),
                $this->getContainer()->get(LoggerInterface::class)
            )
        );

        $this->assertTrue($modelCreate->hasStrategyToWrite());

        $modelCreate->create();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testUpdate1()
    {
        $modelCreate = new Model3();
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';
        $modelCreate->{'error_exception'} = 'TEST';

        $modelCreate->setStrategyToWrite(new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));

        $id = $modelCreate->create();

        $this->assertIsString($id);

        $modelLoad = new EntityTest2(intval($id));
        $modelLoad->setStrategyToRead(new ActiveRecordRead(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));
        $modelLoad->setStrategyToWrite(new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));
        $modelLoad->load();

        $this->assertEquals($modelCreate->{'id_connection'}, $modelLoad->{'id_connection'});
        $this->assertEquals($modelCreate->{'network_params'}, $modelLoad->{'network_params'});
        $this->assertEquals($modelCreate->{'network_service'}, $modelLoad->{'network_service'});
        $this->assertEquals($modelCreate->{'error_exception'}, $modelLoad->{'error_exception'});

        $modelLoad->{'network_params'} = 'UPDATE';
        $modelLoad->{'network_service'} = 'UPDATE';
        $modelLoad->{'error_exception'} = 'UPDATE';

        $result = $modelLoad->update();

        $this->assertIsBool($result);
        $this->assertTrue($result);

        $modelLoad->load();

        $this->assertEquals($modelCreate->{'id_connection'}, $modelLoad->{'id_connection'});
        $this->assertEquals('UPDATE', $modelLoad->{'network_params'});
        $this->assertEquals('UPDATE', $modelLoad->{'network_service'});
        $this->assertEquals('UPDATE', $modelLoad->{'error_exception'});
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testUpdate2()
    {
        $modelCreate = new Model3();
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';
        $modelCreate->{'error_exception'} = 'TEST';

        $modelCreate->setStrategyToWrite(new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));

        $id = $modelCreate->create();

        $this->assertIsString($id);

        $modelLoad = new EntityTest2(intval($id));
        $modelLoad->setStrategyToRead(new ActiveRecordRead(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));
        $modelLoad->setStrategyToWrite(new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));
        $modelLoad->load();

        $this->assertEquals($modelCreate->{'id_connection'}, $modelLoad->{'id_connection'});
        $this->assertEquals($modelCreate->{'network_params'}, $modelLoad->{'network_params'});
        $this->assertEquals($modelCreate->{'network_service'}, $modelLoad->{'network_service'});
        $this->assertEquals($modelCreate->{'error_exception'}, $modelLoad->{'error_exception'});

        $modelLoad->{'network_params'} = 'UPDATE';
        $modelLoad->{'network_service'} = 'UPDATE';
        $modelLoad->{'error_exception'} = 'UPDATE';

        $result = $modelLoad->update();

        $this->assertIsBool($result);
        $this->assertTrue($result);

        $modelLoad->load();

        $this->assertEquals($modelCreate->{'id_connection'}, $modelLoad->{'id_connection'});
        $this->assertEquals('UPDATE', $modelLoad->{'network_params'});
        $this->assertEquals('UPDATE', $modelLoad->{'network_service'});
        $this->assertEquals('UPDATE', $modelLoad->{'error_exception'});
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testDelete1()
    {
        $modelCreate = new Model3();
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';
        $modelCreate->{'error_exception'} = 'TEST';

        $modelCreate->setStrategyToWrite(new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));

        $id = $modelCreate->create();

        $this->assertIsString($id);

        $modelLoad = new Model3(intval($id));
        $modelLoad->setStrategyToRead(new ActiveRecordRead(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));
        $modelLoad->setStrategyToWrite(new ActiveRecordWrite(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        ));
        $modelLoad->load();
        $result = $modelLoad->delete();
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testDelete2()
    {
        $modelCreate = new EntityTest3();
        $modelCreate->inject(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );
        $modelCreate->{'id_connection'} = 1;
        $modelCreate->{'network_params'} = 'TEST';
        $modelCreate->{'network_service'} = 'TEST';
        $modelCreate->{'error_exception'} = 'TEST';

        $id = $modelCreate->create();

        $this->assertIsString($id);

        $modelLoad = new EntityTest3(intval($id));
        $modelLoad->inject(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );
        $modelLoad->load();
        $result = $modelLoad->delete();
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testDelete3()
    {
        $model = new EntityTest3();
        $model->inject(
            $this->getContainer()->get(IAdapter::class),
            $this->getContainer()->get(LoggerInterface::class)
        );
        $result = $model->deleteBulk(Filter::eq($model->getField('disabled'), true));
        $this->assertIsBool($result);
    }

}
