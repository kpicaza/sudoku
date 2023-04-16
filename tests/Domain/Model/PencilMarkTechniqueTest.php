<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku\Domain\Model;

use Generator;
use Kpicaza\Sudoku\Domain\Model\Move;
use Kpicaza\Sudoku\Domain\Model\PencilMarkTechnique;
use Kpicaza\Sudoku\Domain\Model\Position;
use Kpicaza\Sudoku\Infrastructure\Format\CsvGridFactory;
use PHPUnit\Framework\TestCase;

final class PencilMarkTechniqueTest extends TestCase
{
    /** @dataProvider getExpectedMove */
    public function testPlaceAMoveUsingPencilMarkTechnique(Move $expectedMove): void
    {
        $input = 'tests/examples/9x9-pencilmark-grid.csv';

        $grid = CsvGridFactory::fromFileLocation($input);

        $move = PencilMarkTechnique::place($grid);

        $this->assertEquals($expectedMove, $move);
    }

    public static function getExpectedMove(): Generator
    {
        yield 'Find the 7 in the third block' => [
            new Move(new Position(1, 4, 1), 7)
        ];
    }
}
