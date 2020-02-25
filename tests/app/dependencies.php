<?php
declare(strict_types=1);


use Cratia\ORM\DBAL\Interfaces\IAdapter;
use DI\ContainerBuilder;
use Doctrine\Common\EventManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Tests\Cratia\ORM\Model\Infraestructure\Persistence\DataBase;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $logger->pushProcessor(new UidProcessor());
            $logger->pushProcessor(new MemoryUsageProcessor());
            $logger->pushProcessor(new IntrospectionProcessor());

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        IAdapter::class => function () {
            $connectionParams = array(
                'dbname' => $_ENV['DB_NAME'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASSWORD'],
                'host' => $_ENV['DB_HOST'],
                'driver' => 'pdo_mysql',
                'charset' => $_ENV['DB_CHARSET']
            );
            return new \Cratia\ORM\DBAL\MysqlAdapter($connectionParams);
        },

        EventManager::class => function () {
            return new EventManager();
        }
    ]);
};
