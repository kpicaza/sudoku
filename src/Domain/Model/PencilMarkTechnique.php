<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

final class PencilMarkTechnique
{
    public static function place(Grid $grid): ?Move
    {
        $pencilMarks = $grid->pencilMarks;
        foreach ($pencilMarks as $pencilMark) {
            if (1 !== count($pencilMark->pencilMark)) {
                continue;
            }
            return new Move(
                $pencilMark->position,
                $pencilMark->pencilMark[array_key_first($pencilMark->pencilMark)]
            );
        }

        return null;
    }
}
