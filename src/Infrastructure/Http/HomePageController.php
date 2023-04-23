<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Infrastructure\Http;

use Kpicaza\Sudoku\Application\SudokuPuzzles;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomePageController
{
    public function __construct(
        private readonly SudokuPuzzles $sudokuPuzzles,
        private readonly Environment $template,
    ) {
    }

    #[Route('/', name: 'home_page')]
    public function index(): Response
    {
        $puzzle = $this->sudokuPuzzles->findOneRandom();

        return new Response($this->template->render('index.html.twig', [
            'block_size' => 3,
            'blank_spaces' => 51,
            'grid' => $puzzle->initialGrid->matrix

        ]));
    }
}
