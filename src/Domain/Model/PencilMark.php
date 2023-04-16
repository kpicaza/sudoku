<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

readonly class PencilMark
{
    /** @param array<int> $pencilMark */
    public function __construct(
        public Position $position,
        public array $pencilMark
    ) {
    }
}
