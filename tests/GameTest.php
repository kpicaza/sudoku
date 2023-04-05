<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku;

class GameTest extends \PHPUnit\Framework\TestCase
{
    public function testCheckForIncompatibleSudokuSolution(): void
    {
        $input = fopen('tests/examples/4x4-no-comply.csv', 'r');

        $game = new \Kpicaza\Sudoku\Game($input);

        $this->assertSame('The input doesn\'t comply with Sudoku\'s rules.', $game->solutionStatus());
    }

    public function testCheckForPotentialSudokuSolution(): void
    {
        $input = fopen('tests/examples/4x4-comply.csv', 'r');

        $game = new \Kpicaza\Sudoku\Game($input);

        $this->assertSame('The input complies with Sudoku\'s rules.', $game->solutionStatus());
    }

    /** @dataProvider getInvalidSolutionCsv */
    public function testIncorrectSolutionForGivenInitialGrid($solutionFile): void
    {
        $initialGridResource = fopen('tests/examples/9x9-initial-grid.csv', 'r');
        $solution = fopen($solutionFile, 'r');

        $game = new \Kpicaza\Sudoku\Game($solution, $initialGridResource);

        $this->assertSame('The proposed solution is incorrect.', $game->solutionStatus());
    }

    public function testCorrectSolutionForGivenInitialGrid(): void
    {
        $initialGridResource = fopen('tests/examples/9x9-initial-grid.csv', 'r');
        $solution = fopen('tests/examples/9x9-initial-grid-valid.csv', 'r');

        $game = new \Kpicaza\Sudoku\Game($solution, $initialGridResource);

        $this->assertSame('The proposed solution is correct.', $game->solutionStatus());
    }

    public static function getInvalidSolutionCsv(): \Generator
    {
        yield 'Example 1' => [
            'tests/examples/9x9-initial-grid-no-valid-1.csv'
        ];
        yield 'Example 2' => [
            'tests/examples/9x9-initial-grid-no-valid-2.csv'
        ];
        yield 'Example 3' => [
            'tests/examples/9x9-initial-grid-no-valid-3.csv'
        ];
    }
}
