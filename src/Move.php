<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

final class Move
{
    public function __construct(
        public readonly int $row,
        public readonly int $col,
        public readonly int $block,
        public readonly int $value,
    ) {
    }
}
