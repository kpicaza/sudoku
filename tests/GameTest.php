<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku;

class GameTest extends \PHPUnit\Framework\TestCase
{
    public function testCheckForIncompatibleSudokuSolution(): void
    {
        $input = fopen('tests/examples/4x4-no-comply.csv', 'r');

        $game = new \Kpicaza\Sudoku\Game($input);

        $this->assertSame('The input doesn\'t comply with Sudoku\'s rules.', $game->toString());
    }

    public function testCheckForPotentialSudokuSolution(): void
    {
        $input = fopen('tests/examples/4x4-comply.csv', 'r');


        $game = new \Kpicaza\Sudoku\Game($input);

        $this->assertSame('The input complies with Sudoku\'s rules.', $game->toString());
    }
}
