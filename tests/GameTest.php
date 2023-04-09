<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku;

use Generator;
use Kpicaza\Sudoku\Game;
use Kpicaza\Sudoku\Grid;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testCheckForIncompatibleSudokuSolution(): void
    {
        $input = fopen('tests/examples/4x4-no-comply.csv', 'r');

        $game = Game::fromSolutionGrid(Grid::fromCsvResource($input));

        $this->assertSame('The input doesn\'t comply with Sudoku\'s rules.', $game->solutionStatus());
    }

    public function testCheckForPotentialSudokuSolution(): void
    {
        $input = fopen('tests/examples/4x4-comply.csv', 'r');

        $game = Game::fromSolutionGrid(Grid::fromCsvResource($input));

        $this->assertSame('The input complies with Sudoku\'s rules.', $game->solutionStatus());
    }

    /** @dataProvider getInvalidSolutionCsv */
    public function testIncorrectSolutionForGivenInitialGrid($solutionFile): void
    {
        $initialGridResource = fopen('tests/examples/9x9-initial-grid.csv', 'r');
        $solution = fopen($solutionFile, 'r');

        $game = Game::fromSolutionGrid(Grid::fromCsvResource($solution), Grid::fromCsvResource($initialGridResource));

        $this->assertSame('The proposed solution is incorrect.', $game->solutionStatus());
    }

    public function testCorrectSolutionForGivenInitialGrid(): void
    {
        $initialGridResource = fopen('tests/examples/9x9-initial-grid.csv', 'r');
        $solution = fopen('tests/examples/9x9-initial-grid-valid.csv', 'r');

        $game = Game::fromSolutionGrid(Grid::fromCsvResource($solution), Grid::fromCsvResource($initialGridResource));

        $this->assertSame('The proposed solution is correct.', $game->solutionStatus());
    }

    /** @dataProvider getVInitialAndSolutionCsv */
    public function testCanSolveInitialGrid(string $initialGridFile, string $solutionGridFile): void
    {
        $initialGridResource = fopen($initialGridFile, 'r');
        $solution = file_get_contents($solutionGridFile);

        $game = Game::fromInitialGrid(Grid::fromCsvResource($initialGridResource));

        $this->assertSame($solution, $game->solutionGrid->grid->toCsvString());
    }

    public function testCanNotSolveInvalidInitialGrid(): void
    {
        $initialGridResource = fopen('tests/examples/9x9-initial-grid-no-valid-4.csv', 'r');

        $game = Game::fromInitialGrid(Grid::fromCsvResource($initialGridResource));

        $this->assertSame('The Sudoku is not solvable.', $game->solutionStatus());
    }

    /** @dataProvider getDimensionsAndBlankSpaces */
    public function testProduceASolvableInitialGridWith(int $dimensions, int $blankSpaces): void
    {
        $game = Game::withBlockSizeAndBlankSpaces($dimensions, $blankSpaces);

        $this->assertSame('The proposed solution is correct.', $game->solutionStatus());
        $anotherGame = Game::fromInitialGrid(new Grid($game->initialGrid->matrix));
        $this->assertSame('The proposed solution is correct.', $anotherGame->solutionStatus());
    }

    public static function getInvalidSolutionCsv(): Generator
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

    public static function getVInitialAndSolutionCsv(): Generator
    {
        yield 'Example 1: 4x4' => [
            'tests/examples/4x4-initial-grid.csv',
            'tests/examples/4x4-comply.csv',
        ];

        yield 'Example 2: 9x9' => [
            'tests/examples/9x9-initial-grid.csv',
            'tests/examples/9x9-initial-grid-valid.csv',
        ];
    }

    public static function getDimensionsAndBlankSpaces(): Generator
    {
        yield 'Block size: 2, White spaces: 11' => [2, 11];
        yield 'Block size: 3, White spaces: 51' => [3, 51];
        yield 'Block size: 3, White spaces: 61' => [3, 61];
        yield 'Block size: 3, White spaces: 71' => [3, 71];
    }
}
