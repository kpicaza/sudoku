<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku\Domain\Model;

use Generator;
use Kpicaza\Sudoku\Domain\Model\CandidateLinesTechnique;
use Kpicaza\Sudoku\Domain\Model\Move;
use Kpicaza\Sudoku\Domain\Model\Position;
use Kpicaza\Sudoku\Infrastructure\Format\CsvGridFactory;
use PHPUnit\Framework\TestCase;

final class CandidateLinesTechniqueTest extends TestCase
{
    /** @dataProvider getExpectedMove */
    public function testPlaceAMoveUsingCandidateLinesTechnique(Move $expectedMove, string $input): void
    {
        $grid = CsvGridFactory::fromFileLocation($input);

        $move = CandidateLinesTechnique::place($grid);

        $this->assertEquals($expectedMove, $move);
    }

    public static function getExpectedMove(): Generator
    {
        yield 'Find in vertical candidate lines' => [
            new Move(new Position(2, 7, 2), 2),
            'tests/examples/9x9-candidate-lines-grid.csv'
        ];

        yield 'Find in horizontal candidate lines' => [
            new Move(new Position(1, 2, 0), 2),
            'tests/examples/9x9-candidate-lines-horizontal-grid.csv'
        ];
    }
}
