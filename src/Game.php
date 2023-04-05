<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

class Game
{

    private readonly array $solution;
    private string $solutionState;

    public function __construct($solution)
    {
        $result = [];
        while ($row = fgetcsv($solution)) {
            $result[] = array_filter($row);
        }

        $this->complyRules($result);
    }

    public function toString(): string
    {
        return $this->solutionState;
    }

    private function complyRules(array $solution): void
    {
        $this->solution = $solution;
        $rowsCount = count($solution);
        $squareNumber = sqrt($rowsCount);
        $isSquare = $squareNumber === floor($squareNumber);
        if (false === $isSquare) {
            $this->solutionState =  'The input doesn\'t comply with Sudoku\'s rules.';
            return;
        }
        foreach ($solution as $row) {
            $rowCount = count($row);
            if ($rowsCount !== $rowCount) {
                $this->solutionState =  'The input doesn\'t comply with Sudoku\'s rules.';
                return;
            }
            $validRow = $this->isValidRow($row, $rowCount);
            if (false === $validRow) {
                $this->solutionState =  'The input doesn\'t comply with Sudoku\'s rules.';
                return;
            }
        }

        $verticalSolution = $this->rotatedSolution();
        foreach ($verticalSolution as $verticalRow) {
            $rowCount = count($verticalRow);
            $validRow = $this->isValidRow($verticalRow, $rowCount);
            if (false === $validRow) {
                $this->solutionState =  'The input doesn\'t comply with Sudoku\'s rules.';
                return;
            }
        }

        $squareSolution = $this->squareSolution((int)$squareNumber, $rowsCount);
        foreach ($squareSolution as $squareRow) {
            $rowCount = count($squareRow);
            $validRow = $this->isValidRow($squareRow, $rowCount);
            if (false === $validRow) {
                $this->solutionState =  'The input doesn\'t comply with Sudoku\'s rules.';
                return;
            }
        }

        $this->solutionState = 'The input complies with Sudoku\'s rules.';
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

    private function rotatedSolution(): array
    {
        $matrix = $this->solution;
        array_unshift($matrix, null);
        $matrix = call_user_func_array('array_map', $matrix);

        return $matrix;
    }

    private function squareSolution(int $squareSize, int $rowSize): array
    {
        $tmpMatrix = $this->solution;
        $matrix = [];
        $index = 0;
        $initialIndex = 0;
        foreach ($tmpMatrix as $row) {
            while ($partialRow = array_splice($row, 0, $squareSize)) {
                dump('Initial', $initialIndex);
                dump('index', $index);
                dump('value', $partialRow);

                $matrix[$index] = array_merge($matrix[$index]  ?? [], $partialRow);

                if (count($matrix[$index]) === $rowSize) {
                    $initialIndex ++;
                }

                $index++;

                if (sqrt($index) === floor($squareSize) || $index === $squareSize) {
                    $index = $initialIndex;
                }

            }
        }

        return $matrix;
    }
}
