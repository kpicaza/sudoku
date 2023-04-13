<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $container->extension('framework', [
        'secret' => '%env(resolve:APP_SECRET)%',
        'test' => true,
    ]);
    $container->extension('webpack_encore', [
        'output_path' => 'build'
    ]);
};
