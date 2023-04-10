<?php

declare(strict_types=1);

use Antidot\Framework\Application;
use Kpicaza\Sudoku\Domain\Model\Game;
use Psr\Container\ContainerInterface;

/**
 * Setup routes with a single request method:
 *
 * $app->get('/', [App\Handler\HomePageHandler::class], 'home');
 * $app->post('/album', [App\Handler\AlbumCreateHandler::class], 'album.create');
 * $app->put('/album/:id', [App\Handler\AlbumUpdateHandler::class], 'album.put');
 * $app->patch('/album/:id', [App\Handler\AlbumUpdateHandler::class], 'album.patch');
 * $app->delete('/album/:id', [App\Handler\AlbumDeleteHandler::class], 'album.delete');
 */
return static function (Application $app, ContainerInterface $container): void {
    $twig = $container->get(\Antidot\Render\TemplateRenderer::class);
    $app->get('/', [
        fn (): \Psr\Http\Message\ResponseInterface => new \Nyholm\Psr7\Response(200, [], $twig->render(
            'index.html',
            [
                'block_size' => 3,
                'blank_spaces' => 61,
                'grid' => (Game::withBlockSizeAndBlankSpaces(3, 51))->initialGrid->matrix

            ]
        ))
    ], 'home');
};
