<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Command;

final readonly class SolvePuzzleCommand
{
    private function __construct(
        public string $id
    ) {
    }

    public static function fromId(string $id): self
    {
        return new self($id);
    }
}
