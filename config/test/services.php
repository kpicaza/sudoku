<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->set(Connection::class, Connection::class)
        ->factory([DriverManager::class, 'getConnection'])
        ->arg('$params', [
            'dbname' => 'sqlite',
            'path' => '/home/kpicaza/Server/sudoku/var/test_database.sqlite3',
            'driver' => 'pdo_sqlite',
        ]);
};
