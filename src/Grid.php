<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

final readonly class Grid
{
    public array $matrix;
    public array $verticalMatrix;
    public array $blockMatrix;
    public int $size;
    public int $blockSize;
    private array $numbers;

    public function __construct(array $matrix)
    {
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

    public static function fillEmptyGrid(int $size, int $blockSize): self
    {
        $matrix = [];
        for ($row = 0; $row < $size; $row++) {
            $matrix[$row] = array_fill(0, $size, ' ');
        }

        $numbers = range(1, $size);
        shuffle($numbers);

        for ($i = 0;$i < $blockSize;$i++) {
            $matrix[$i] = $numbers;
            $tmpNumbers = array_splice($numbers, 0, $blockSize);
            $numbers = array_merge($numbers, $tmpNumbers);
        }

        return new self($matrix);
    }

    public static function addGaps(array $matrix, int $blankSpaces, int $size): self
    {
        $usedIndexes = [];
        while (0 < $blankSpaces) {
            do {
                $rowIndex = rand(0, $size -1);
                $colIndex = rand(0, $size -1);
            } while(in_array([$rowIndex, $colIndex], $usedIndexes));

            $matrix[$rowIndex][$colIndex] = ' ';
            $usedIndexes[] = [$rowIndex, $colIndex];
            $blankSpaces--;
        }

        return new self($matrix);
    }

    private function verticalGrid(): array
    {
        $matrix = $this->matrix;
        if (0 === count($this->matrix)) {
            return [];
        }
        array_unshift($matrix, null);

        return call_user_func_array('array_map', $matrix);
    }

    private function blockGrid(): array
    {
        $matrix = [];
        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                $index = $this->getBlockIndex($row, $col);
                $matrix[$index][] = $this->matrix[$row][$col];
            }
        }

        return $matrix;
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

    public function move(Move $move): self
    {
        $matrix = $this->matrix;

        $matrix[$move->row][$move->col] = $move->value;

        return new self($matrix);
    }

    public function tryNextMoveByTriangulation(): ?Move
    {
        foreach ($this->matrix as $row => $cols) {
            $lockedNumbersInVertical = array_filter($this->matrix[$row], fn($verticalNumber) => is_numeric($verticalNumber));
            foreach ($cols as $col => $number) {
                if (is_numeric($number)) {
                    continue;
                }
                $block =$this->getBlockIndex($row, $col);
                $lockedNumbersInABlock = array_filter($this->blockMatrix[$block], fn($blockNumber) => is_numeric($blockNumber));
                $lockedNumbersInAHorizontal = [];
                for ($i = 0; $i < $this->size; $i++) {
                    if (false === is_numeric($this->matrix[$i][$col])) {
                        continue;
                    }
                    $lockedNumbersInAHorizontal[] = $this->matrix[$i][$col];
                }

                $lockedNumbers = array_unique(array_merge($lockedNumbersInABlock, $lockedNumbersInVertical, $lockedNumbersInAHorizontal));

                $possibleNumbers = array_diff($this->numbers, $lockedNumbers);

                if (1 === count($possibleNumbers)) {
                    return new Move($row, $col, $block, end($possibleNumbers));
                }
            }
        }

        return null;
    }

    public function tryNextMove(Move $move): ?Move
    {
        if (in_array($move->value, $this->matrix[$move->row])) {
            return null;
        }

        if (in_array($move->value, $this->verticalMatrix[$move->col])) {
            return null;
        }

        if (in_array($move->value, $this->blockMatrix[$move->block])) {
            return null;
        }

        return $move;
    }

    public function getBlockIndex(int $row, int $col): int
    {
         return (int)(
             floor($row / $this->blockSize) * $this->blockSize
             + floor($col / $this->blockSize)
         );
    }

    public function toCsvString(): string
    {
        $csv = '';

        foreach ($this->matrix as $key => $row) {
            $csv .= implode(',', $row) . ',' . ($key + 1 === $this->size ? '' : PHP_EOL);
        }

        return $csv;
    }
}
