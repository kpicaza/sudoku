<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

final class PencilMarkTechnique
{
    public static function place(Grid $grid): ?Move
    {
        $pencilMarks = $grid->pencilMarks;
        foreach ($pencilMarks as $row => $cols) {
            foreach ($cols as $col => $pencilMark) {
                if (1 !== count($pencilMark)) {
                    continue;
                }
                return new Move(new Position($row, $col, $grid->getBlockIndex($row, $col)), end($pencilMark));
            }
        }

        return null;
    }
}
