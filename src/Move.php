<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

final readonly class Move
{
    public function __construct(
        public int $row,
        public int $col,
        public int $block,
        public int $value,
    ) {
    }
}
