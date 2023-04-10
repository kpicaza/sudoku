<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

class SingeCandidateTechnique
{
    public static function place(Position $position, Grid $grid): ?Move
    {
        $row = $grid->matrix[$position->row];

        $lockedNumbersInVertical = $grid->getRowNumbers($grid->matrix[$position->row]);
        foreach ($row as $col => $number) {
            if (is_numeric($number)) {
                continue;
            }
            $block = $grid->getBlockIndex($position->row, $col);
            $lockedNumbersInABlock = $grid->getRowNumbers($grid->blockMatrix[$block]);
            $lockedNumbersInAHorizontal = $grid->getRowNumbers($grid->verticalMatrix[$col]);

            $lockedNumbers = array_unique(
                array_merge($lockedNumbersInABlock, $lockedNumbersInVertical, $lockedNumbersInAHorizontal)
            );

            $possibleNumbers = array_diff($grid->numbers, $lockedNumbers);

            if (1 === count($possibleNumbers)) {
                return new Move(new Position($position->row, $col, $block), end($possibleNumbers));
            }
        }

        return null;
    }
}
