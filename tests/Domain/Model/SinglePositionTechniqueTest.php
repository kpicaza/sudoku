<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku\Domain\Model;

use Generator;
use Kpicaza\Sudoku\Domain\Model\Move;
use Kpicaza\Sudoku\Domain\Model\Position;
use Kpicaza\Sudoku\Domain\Model\SinglePositionTechnique;
use Kpicaza\Sudoku\Infrastructure\Format\CsvGridFactory;
use PHPUnit\Framework\TestCase;

final class SinglePositionTechniqueTest extends TestCase
{
    /** @dataProvider getExpectedMove */
    public function testPlaceAMoveUsingSinglePositionTechnique(Move $expectedMove): void
    {
        $input = 'tests/examples/9x9-single-position-grid.csv';

        $grid = CsvGridFactory::fromFileLocation($input);

        $position = new Position(
            $expectedMove->row,
            $expectedMove->col,
            $grid->getBlockIndex($expectedMove->row, $expectedMove->col)
        );

        $move = SinglePositionTechnique::place($position, $grid);

        $this->assertEquals($expectedMove, $move);
    }

    public static function getExpectedMove(): Generator
    {
        yield 'Find the 7 in the third block' => [
            new Move(3, 7, 5, 7)
        ];
        yield 'Find the 7 in the first block' => [
            new Move(1, 0, 0, 7)
        ];
    }
}
