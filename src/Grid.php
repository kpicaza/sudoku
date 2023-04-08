<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

class Grid
{

    public readonly array $matrix;
    public readonly array $verticalMatrix;
    public readonly array $blockMatrix;
    public readonly int $size;
    public readonly int $blockSize;
    private readonly array $numbers;

    public function __construct(array $matrix)
    {
        $this->isValid($matrix);
        $this->size = count($matrix);
        $this->blockSize = (int)sqrt($this->size);
        $this->numbers = range(1, $this->size);
        $this->matrix = $matrix;
        $this->verticalMatrix = $this->verticalGrid();
        $this->blockMatrix = $this->blockGrid();
    }

    public static function fromCsvResource($initialGridResource): self
    {
        $matrix = [];
        if (null !== $initialGridResource) {
            while ($row = fgetcsv($initialGridResource)) {
                array_pop($row);
                $matrix[] = $row;
            }
        }

        return new self($matrix);
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

    private function verticalGrid(): array
    {
        $matrix = $this->matrix;
        if (0 === count($this->matrix)) {
            return [];
        }
        array_unshift($matrix, null);
        $matrix = call_user_func_array('array_map', $matrix);

        return $matrix;

    }

    private function blockGrid(): array
    {
        $tmpMatrix = $this->matrix;
        $matrix = [];
        $index = 0;
        $initialIndex = 0;
        foreach ($tmpMatrix as $row) {
            while ($partialRow = array_splice($row, 0, $this->blockSize)) {
                $matrix[$index] = array_merge($matrix[$index]  ?? [], $partialRow);

                if (count($matrix[$index]) === $this->size) {
                    $initialIndex++;
                }

                $index++;

                if ($index % $this->blockSize === 0) {
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

    public function nextMove(): ?Move
    {
        $foo = $this->nextMoveByTriangulation();

        return $foo;
    }

    private function nextMoveByTriangulation(): ?Move
    {
        foreach ($this->matrix as $row => $cols) {
            $lockedNumbersInVertical = array_filter($this->matrix[$row], fn($verticalNumber) => is_numeric($verticalNumber));
            foreach ($cols as $col => $number) {
                if (is_numeric($number)) {
                    continue;
                }
                $block = (int)(floor($row / $this->blockSize) * $this->blockSize + floor($col / $this->blockSize));
                $lockedNumbersInABlock = array_filter($this->blockMatrix[$block], fn($blockNumber) => is_numeric($blockNumber));
                $lockedNumbersInAHorizontal = [];
                for ($i = 0; $i < $this->size; $i++) {
                    if (false === is_numeric($this->matrix[$i][$col])) {
                        continue;
                    }
                    $lockedNumbersInAHorizontal[] = $this->matrix[$i][$col];
                }

                $lockedNumbers = array_unique(array_merge($lockedNumbersInABlock, $lockedNumbersInVertical,$lockedNumbersInAHorizontal));

                $possibleNumbers = array_diff($this->numbers, $lockedNumbers);

                if (1 === count($possibleNumbers)) {
                    return new Move($row, $col, $block, end($possibleNumbers));
                }
            }
        }

        return null;
    }

    public function toCsvString(): string
    {
        $csv = '';

        foreach ($this->matrix as $key => $row) {
            $csv .= implode(',', $row) . ',' . ($key +1 === $this->size ? '' : PHP_EOL);
        }

        return $csv;
    }
}
