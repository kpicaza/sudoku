<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

class Game
{

    public readonly ?Solution $solutionGrid;
    private readonly Grid $initialGrid;
    private string $solutionState;

    public function __construct(?Solution $solutionGrid, ?Grid $initialGrid = null, string $message = '')
    {
        $this->solutionGrid = $solutionGrid;

        $this->initialGrid = $initialGrid ?? new Grid([]);

        $this->solutionState = $message;

        $this->complyRules();
    }

    public static function fromSolutionGrid(Grid $solutionGrid, ?Grid $initialGrid = null): self
    {
        try {
            $solution = new Solution($solutionGrid);
        } catch (\InvalidArgumentException) {
            return new self(null, $initialGrid, 'The proposed solution is incorrect.');
        }

        return new self($solution, $initialGrid);
    }

    public static function fromInitialGrid(Grid $initialGrid): self
    {
        try {
            $solution = Solution::from($initialGrid);
        } catch (\InvalidArgumentException) {
            return new self(null, $initialGrid, 'The Sudoku is not solvable.');
        }

        return new Game($solution, $initialGrid);
    }

    public function solutionStatus(): string
    {
        return $this->solutionState;
    }

    private function complyRules(): void
    {
        if (null === $this->solutionGrid) {
            return;
        }

        $solution = $this->solutionGrid->grid->horizontalGrid();
        $rowsCount = $this->solutionGrid->grid->size;

        if (false === $this->initialGrid->canBeSolvedWith($this->solutionGrid->grid)) {
            $this->solutionState = 'The proposed solution is incorrect.';
            return;
        }

        try {
            $this->validateGrid($solution, $rowsCount);
            $this->validateGrid($this->solutionGrid->grid->verticalMatrix, $rowsCount);
            $this->validateGrid($this->solutionGrid->grid->blockMatrix, $rowsCount);
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

    private function isValidRow(array $row, int $rowCount): bool
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
        foreach ($grid as $row) {
            $validRow = $this->isValidRow($row, $rowsCount);
            if (false === $validRow) {
                throw new \Exception('Row is invalid.');
            }
        }
    }
}
