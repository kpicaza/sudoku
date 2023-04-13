<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load('Kpicaza\\Sudoku\\Infrastructure\\', '../src/Infrastructure')
        ->exclude('../src/Infrastructure/{Symfony,Format}')
        ->autoconfigure(true)
        ->autowire(true)
        ->public();
};
