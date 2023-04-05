<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

class Grid
{

    private readonly array $matrix;
    public readonly int $size;
    public readonly int $squareSize;

    public function __construct($rawMatrix = null)
    {
        $matrix = [];
        if (null !== $rawMatrix) {
            while ($row = fgetcsv($rawMatrix)) {
                array_pop($row);
                $matrix[] = $row;
            }
        }

        $this->isValid($matrix);
        $this->matrix = $matrix;
        $this->size = count($matrix);
        $this->squareSize = (int)sqrt($this->size);
    }

    private function isValid(array $solution): void
    {
        $rowsCount = count($solution);
        $squareNumber = sqrt($rowsCount);
        $isSquare = $squareNumber === floor($squareNumber);
        if (false === $isSquare) {
            throw new \InvalidArgumentException('Invalid grid.');
        }
        foreach ($solution as $row) {
            $rowCount = count($row);
            if ($rowsCount !== $rowCount) {
                throw new \InvalidArgumentException('Invalid grid.');
            }
        }
    }

    public function verticalGrid(): array
    {
        $matrix = $this->matrix;
        array_unshift($matrix, null);
        $matrix = call_user_func_array('array_map', $matrix);

        return $matrix;

    }

    public function squareGrid(): array
    {
        $tmpMatrix = $this->matrix;
        $matrix = [];
        $index = 0;
        $initialIndex = 0;
        foreach ($tmpMatrix as $row) {
            while ($partialRow = array_splice($row, 0, $this->squareSize)) {
                $matrix[$index] = array_merge($matrix[$index]  ?? [], $partialRow);

                if (count($matrix[$index]) === $this->size) {
                    $initialIndex++;
                }

                $index++;

                if ($index % $this->squareSize === 0) {
                    $index = $initialIndex;
                }

            }
        }

        return $matrix;
    }

    public function horizontalGrid(): array
    {
        return $this->matrix;
    }

    public function canBeSolvedWith(Grid $solutionGrid): bool
    {
        $grid =  array_map(fn($matrix) => array_filter($matrix,  fn($item) => is_numeric($item)), $this->matrix);
        $solutionGridMatrix = $solutionGrid->matrix;

        foreach ($grid as $key => $line) {
            foreach ($line as $index => $item) {
                if ($solutionGridMatrix[$key][$index] !== $item) {
                    return false;
                }
            }
        }

        return true;
    }

    public function isFullFilled(): bool
    {
        return ($this->size * $this->size) === count(
            array_merge(
                ...array_map(fn($matrix) => array_filter($matrix,  fn($item) => is_numeric($item)), $this->matrix)
            )
        );
    }
}
