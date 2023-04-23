<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Application;

interface SudokuPuzzles
{
    public function findOneRandom(): SudokuPuzzle;
}
