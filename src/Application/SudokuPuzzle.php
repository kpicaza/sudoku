<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Application;

use Kpicaza\Sudoku\Domain\Model\Grid;

final readonly class SudokuPuzzle
{
    public function __construct(
        public Grid $initialGrid,
        public Grid $solutionGrid,
    ) {
    }
}
