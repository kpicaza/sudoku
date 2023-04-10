<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

use Exception;
use InvalidArgumentException;

final class Game
{
    public readonly ?Solution $solutionGrid;
    public readonly Grid $initialGrid;
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
        } catch (InvalidArgumentException) {
            return new self(null, $initialGrid, 'The proposed solution is incorrect.');
        }

        return new self($solution, $initialGrid);
    }

    public static function fromInitialGrid(Grid $initialGrid): self
    {
        try {
            $solution = Solution::fromInitial($initialGrid);
        } catch (InvalidArgumentException) {
            return new self(null, $initialGrid, 'The Sudoku is not solvable.');
        }

        return new Game($solution, $initialGrid);
    }

    public static function withBlockSizeAndBlankSpaces(int $blockSize, int $blankSpaces): self
    {
        $size = $blockSize * $blockSize;
        $grid = Grid::fillEmptyGrid($size, $blockSize);
        $solution = Solution::fromInitial($grid);
        $initialGrid = Grid::addGaps($solution->grid->matrix, $blankSpaces, $size);

        return new self($solution, $initialGrid);
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

        $solution = $this->solutionGrid->grid;
        $rowsCount = $this->solutionGrid->grid->size;

        if (false === $this->initialGrid->canBeSolvedWith($this->solutionGrid->grid)) {
            $this->solutionState = 'The proposed solution is incorrect.';
            return;
        }

        try {
            $this->validateGrid($solution->matrix, $rowsCount);
            $this->validateGrid($solution->verticalMatrix, $rowsCount);
            $this->validateGrid($solution->blockMatrix, $rowsCount);
        } catch (Exception) {
            $this->solutionState = 'The input doesn\'t comply with Sudoku\'s rules.';
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
            $index = array_search((string)$i, $row, true);
            if (false === $index) {
                return false;
            }

            unset($row[$index]);
        }

        return 0 === count($row);
    }

    /**
     * @param array<int, array<int, string>> $grid
     */
    private function validateGrid(array $grid, int $rowsCount): void
    {
        foreach ($grid as $row) {
            $validRow = $this->isValidRow($row, $rowsCount);
            if (false === $validRow) {
                throw new Exception('Row is invalid.');
            }
        }
    }
}
