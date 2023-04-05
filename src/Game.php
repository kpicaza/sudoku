<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

class Game
{

    private readonly Solution $solutionGrid;
    private readonly Grid $initialGrid;
    private string $solutionState;

    public function __construct($solutionGrid, $initialGrid = null)
    {
        try {
            $grid = new Grid($solutionGrid);
        } catch (\InvalidArgumentException) {
            $this->solutionState =  'The input doesn\'t comply with Sudoku\'s rules.';
            return;
        }
        try {
            $this->solutionGrid = new Solution($grid);
        } catch (\InvalidArgumentException) {
            $this->solutionState = 'The proposed solution is incorrect.';
            return;
        }

        $this->initialGrid = new Grid($initialGrid);

        $this->complyRules();
    }

    public function solutionStatus(): string
    {
        return $this->solutionState;
    }

    private function complyRules(): void
    {
        $solution = $this->solutionGrid->grid->horizontalGrid();
        $rowsCount = $this->solutionGrid->grid->size;

        if (false === $this->initialGrid->canBeSolvedWith($this->solutionGrid->grid)) {
            $this->solutionState = 'The proposed solution is incorrect.';
            return;
        }

        try {
            $this->validateGrid($solution, $rowsCount);
            $this->validateGrid($this->solutionGrid->grid->verticalGrid(), $rowsCount);
            $this->validateGrid($this->solutionGrid->grid->squareGrid(), $rowsCount);
        } catch (\Exception) {
            $this->solutionState =  'The input doesn\'t comply with Sudoku\'s rules.';
            return;
        }

        if (0 === $this->initialGrid->size) {
            $this->solutionState = 'The input complies with Sudoku\'s rules.';
            return;
        }

        $this->solutionState = 'The proposed solution is correct.';
    }

    private function isValidRow(mixed $row, int $rowCount): bool
    {
        for ($i = 1; $i <= $rowCount; $i++) {
            $index = array_search($i, $row);
            if (false === $index) {
                return false;
            }

            unset($row[$index]);
        }

        if (0 === count($row)) {
            return true;
        }

        return false;
    }

    private function validateGrid(array $grid, int $rowsCount): void
    {
        foreach ($grid as $key => $row) {
            $validRow = $this->isValidRow($row, $rowsCount);
            if (false === $validRow) {
                throw new \Exception('Row is invalid.');
            }
        }
    }
}
