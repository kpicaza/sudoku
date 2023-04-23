<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Kpicaza\Sudoku\Application\SudokuPuzzles;
use Kpicaza\Sudoku\Domain\UncheckedPuzzleRepository;
use Kpicaza\Sudoku\Infrastructure\Dbal\DbalSudokuPuzzles;
use Kpicaza\Sudoku\Infrastructure\Dbal\DbalUncheckedPuzzleRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->set(UncheckedPuzzleRepository::class, DbalUncheckedPuzzleRepository::class)
        ->public()
    ;
    $services
        ->set(SudokuPuzzles::class, DbalSudokuPuzzles::class)
        ->public()
    ;

    $services
        ->set(Connection::class, Connection::class)
        ->factory([DriverManager::class, 'getConnection'])
        ->arg('$params', [
            'dbname' => 'sqlite',
            'path' => '/home/kpicaza/Server/sudoku/var/database.sqlite3',
            'driver' => 'pdo_sqlite',
        ]);

    $services
        ->load('Kpicaza\\Sudoku\\Infrastructure\\', '../src/Infrastructure')
        ->exclude('../src/Infrastructure/{Symfony,Format}')
        ->autoconfigure(true)
        ->autowire(true)
        ->public();

    $services
        ->load('Kpicaza\\Sudoku\\Domain\\Handler\\', '../src/Domain/Handler')
        ->autoconfigure(true)
        ->autowire(true)
        ->public();
};
