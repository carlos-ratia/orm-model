<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;

use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class TestCase
 * @package Tests\Cratia\Rest
 */
class TestCase extends PHPUnit_TestCase
{

    protected function getContainer(): Container
    {
        // Instantiate PHP-DI ContainerBuilder
        $containerBuilder = new ContainerBuilder();

        // Container intentionally not compiled for tests.

        // Set up settings
        $settings = require __DIR__ . '/../tests/app/settings.php';
        $settings($containerBuilder);

        // Set up dependencies
        $dependencies = require __DIR__ . '/../tests/app/dependencies.php';
        $dependencies($containerBuilder);

        // Build PHP-DI Container instance
        $container = $containerBuilder->build();

        return $container;
    }
}
