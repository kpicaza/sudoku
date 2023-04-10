<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

final readonly class Position
{
    public function __construct(
        public int $row,
        public int $col,
        public int $block
    ) {
    }
}
