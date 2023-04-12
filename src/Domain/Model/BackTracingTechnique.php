<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

class BackTracingTechnique
{
    public static function place(Position $position, Grid $grid): ?Move
    {
        $numbers = $grid->numbers;
        $row = $grid->matrix[$position->row];
        foreach ($row as $col => $value) {
            if (is_numeric($grid->matrix[$position->row][$col])) {
                continue;
            }
            $block = $grid->getBlockIndex($position->row, $col);
            shuffle($numbers);
            foreach ($numbers as $number) {
                return $grid->tryNextMove(new Move(new Position($position->row, $col, $block), $number));
            }
        }

        return null;
    }
}
