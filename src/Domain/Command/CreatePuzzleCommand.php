<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Command;

final readonly class CreatePuzzleCommand
{
    public function __construct(
        public int $blockSize,
        public int $blankSpaces
    ) {
    }

    public static function withBlockSizeAndBlankSpaces(int $blockSize, int $blankSpaces): self
    {
        return new self($blockSize, $blankSpaces);
    }
}
