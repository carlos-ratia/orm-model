<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model\Infraestructure\Persistence;


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use Cratia\ORM\DBAL\MysqlAdapter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\FetchMode;
use Psr\Log\LoggerInterface;

/**
 * Class DataBaseAdapter
 * @package App\Persistence
 */
class DataBase extends MysqlAdapter
{
    /**
     * Adapter constructor.
     * @throws DBALException
     */
    public function __construct()
    {
        $connectionParams = array(
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'host' => $_ENV['DB_HOST'],
            'driver' => 'pdo_mysql',
            'charset' => $_ENV['DB_CHARSET']
        );
        parent::__construct($connectionParams, null, null);
    }
}