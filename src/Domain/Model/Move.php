<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

final readonly class Move
{
    public function __construct(
        public Position $position,
        public int $value,
    ) {
    }
}
