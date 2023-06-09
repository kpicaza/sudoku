<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

/**
 * @psalm-type Matrix array<int, array<int, string>>
 */
final readonly class Grid
{
    /** @var Matrix */
    public array $matrix;
    /** @var Matrix */
    public array $verticalMatrix;
    /** @var Matrix */
    public array $blockMatrix;
    public int $size;
    public int $blockSize;
    /** @var array<int>  */
    public array $numbers;
    /** @var array<PencilMark> */
    public array $pencilMarks;

    /** @param Matrix $matrix */
    public function __construct(array $matrix)
    {
        $this->size = count($matrix);
        $this->blockSize = (int)sqrt($this->size);
        $this->numbers = range(1, $this->size);
        $this->matrix = $matrix;
        $this->verticalMatrix = $this->verticalGrid();
        $this->blockMatrix = $this->blockGrid();
        $this->pencilMarks = $this->pencilMarks();
    }

    public static function fillEmptyGrid(int $size, int $blockSize): self
    {
        $matrix = [];
        for ($row = 0; $row < $size; $row++) {
            $matrix[$row] = array_fill(0, $size, ' ');
        }

        $numbers = array_map(static fn (int $number) => (string)$number, range(1, $size));
        shuffle($numbers);

        for ($i = 0;$i < $blockSize;$i++) {
            $matrix[$i] = $numbers;
            $tmpNumbers = array_splice($numbers, 0, $blockSize);
            shuffle($tmpNumbers);
            shuffle($tmpNumbers);
            $numbers = array_merge($numbers, $tmpNumbers);
        }

        return new self($matrix);
    }

    /** @param Matrix $matrix */
    public static function addGaps(Grid $grid, int $blankSpaces, int $size): self
    {
        $startingBlankSpaces = $blankSpaces;
        $matrix = $grid->matrix;
        $usedIndexes = [];
        while (0 < $blankSpaces) {
            do {
                $rowIndex = random_int(0, $size - 1);
                $colIndex = random_int(0, $size - 1);
            } while (in_array([$rowIndex, $colIndex], $usedIndexes, true));

            $lastNumber = $matrix[$rowIndex][$colIndex];
            $usedIndexes[] = [$rowIndex, $colIndex];
            if (false === is_numeric($lastNumber)) {
                continue;
            }

            $matrix[$rowIndex][$colIndex] = ' ';

            try {
                if (Solution::hasSingleSolution(new Grid($matrix))) {
                    $blankSpaces--;
                    continue;
                }
            } catch (\Exception) {
            }

            $matrix[$rowIndex][$colIndex] = $lastNumber;
            if ($size * $size === count($usedIndexes)) {
                $matrix = $grid->matrix;
                $usedIndexes = [];
                $blankSpaces = $startingBlankSpaces;
            }
        }

        return new self($matrix);
    }

    /** @return Matrix */
    private function verticalGrid(): array
    {
        $matrix = [];
        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                $matrix[$col][] = $this->matrix[$row][$col];
            }
        }

        return $matrix;
    }

    /** @return Matrix */
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

    /**
     * @return array<PencilMark>
     */
    private function pencilMarks(): array
    {
        $pencilMarks = [];
        foreach ($this->matrix as $row => $cols) {
            $horizontalLockedNumbers = $this->getRowNumbers($cols);
            foreach ($cols as $col => $value) {
                if (is_numeric($value)) {
                    continue;
                }

                $block = $this->getBlockIndex($row, $col);
                $verticalLockedNumbers = $this->getRowNumbers($this->verticalMatrix[$col]);
                $blockLockedNumbers = $this->getRowNumbers($this->blockMatrix[$block]);
                $lockedNumbers = array_unique(array_merge(
                    $horizontalLockedNumbers,
                    $verticalLockedNumbers,
                    $blockLockedNumbers
                ));

                $availableNumbers = array_diff($this->numbers, $lockedNumbers);
                $pencilMarks[] = new PencilMark(
                    new Position($row, $col, $block),
                    $availableNumbers
                );
            }
        }

        return $pencilMarks;
    }

    public function canBeSolvedWith(Grid $solutionGrid): bool
    {
        $grid = array_map(fn ($matrix) => array_filter($matrix, fn ($item) => is_numeric($item)), $this->matrix);
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

        $matrix[$move->position->row][$move->position->col] = (string)$move->value;

        return new self($matrix);
    }

    public function isAvailablePosition(int $missingNumber, Position $position): bool
    {
        if (in_array((string)$missingNumber, $this->matrix[$position->row], true)) {
            return false;
        }

        if (in_array((string)$missingNumber, $this->verticalMatrix[$position->col], true)) {
            return false;
        }

        if (in_array((string)$missingNumber, $this->blockMatrix[$position->block], true)) {
            return false;
        }

        return true;
    }

    public function tryNextMove(Move $move): ?Move
    {
        if ($this->isAvailablePosition($move->value, $move->position)) {
            return $move;
        }

        return null;
    }

    /**
     * @param array<int, string> $row
     * @return array<int, string>
     */
    public function getRowNumbers(array $row): array
    {
        return array_filter($row, static fn ($number) => is_numeric($number));
    }

    public function getBlockIndex(int $row, int $col): int
    {
        return (int)(
            floor($row / $this->blockSize) * $this->blockSize
            + floor($col / $this->blockSize)
        );
    }
}
