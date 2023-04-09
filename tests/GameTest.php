<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku;

use Generator;
use Kpicaza\Sudoku\Domain\Model\Game;
use Kpicaza\Sudoku\Domain\Model\Grid;
use Kpicaza\Sudoku\Infrastructure\Format\CsvGridFactory;
use Kpicaza\Sudoku\Infrastructure\Format\CsvPrinter;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testCheckForIncompatibleSudokuSolution(): void
    {
        $input = 'tests/examples/4x4-no-comply.csv';

        $game = Game::fromSolutionGrid(CsvGridFactory::fromFileLocation($input));

        $this->assertSame('The input doesn\'t comply with Sudoku\'s rules.', $game->solutionStatus());
    }

    public function testCheckForPotentialSudokuSolution(): void
    {
        $input = 'tests/examples/4x4-comply.csv';

        $game = Game::fromSolutionGrid(CsvGridFactory::fromFileLocation($input));

        $this->assertSame('The input complies with Sudoku\'s rules.', $game->solutionStatus());
    }

    /** @dataProvider getInvalidSolutionCsv */
    public function testIncorrectSolutionForGivenInitialGrid($solutionFile): void
    {
        $initialGridResource = 'tests/examples/9x9-initial-grid.csv';

        $game = Game::fromSolutionGrid(
            CsvGridFactory::fromFileLocation($solutionFile),
            CsvGridFactory::fromFileLocation($initialGridResource)
        );

        $this->assertSame('The proposed solution is incorrect.', $game->solutionStatus());
    }

    public function testCorrectSolutionForGivenInitialGrid(): void
    {
        $initialGridResource = 'tests/examples/9x9-initial-grid.csv';
        $solution = 'tests/examples/9x9-initial-grid-valid.csv';

        $game = Game::fromSolutionGrid(
            CsvGridFactory::fromFileLocation($solution),
            CsvGridFactory::fromFileLocation($initialGridResource)
        );

        $this->assertSame('The proposed solution is correct.', $game->solutionStatus());
    }

    /** @dataProvider getVInitialAndSolutionCsv */
    public function testCanSolveInitialGrid(string $initialGridFile, string $solutionGridFile): void
    {
        $solution = file_get_contents($solutionGridFile);

        $game = Game::fromInitialGrid(CsvGridFactory::fromFileLocation($initialGridFile));

        $this->assertSame($solution, CsvPrinter::render($game->solutionGrid->grid));
    }

    public function testCanNotSolveInvalidInitialGrid(): void
    {
        $initialGridResource = 'tests/examples/9x9-initial-grid-no-valid-4.csv';

        $game = Game::fromInitialGrid(CsvGridFactory::fromFileLocation($initialGridResource));

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
