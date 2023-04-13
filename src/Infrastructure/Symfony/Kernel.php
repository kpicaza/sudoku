<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Infrastructure\Symfony;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

use function array_key_exists;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @return array<BundleInterface>
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new WebpackEncoreBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(__DIR__ . '/../../../config/framework.php');
        $container->import(__DIR__ . '/../../../config/services.php');

        if (false === array_key_exists('APP_ENV', $_ENV)) {
            return;
        }

        if ($_ENV['APP_ENV'] === 'test') {
            $container->import(__DIR__ . '/../../../config/test/services.php');
        }

        if ($_ENV['APP_ENV'] === 'dev') {
            $container->import(__DIR__ . '/../../../config/dev/services.php');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(
            '../Http/',
            'annotation',
        );
    }
}
