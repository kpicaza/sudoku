<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain;

use Kpicaza\Sudoku\Domain\Model\Game;

interface UncheckedPuzzleRepository
{
    public function save(Game $game): void;

    public function get(string $gameId): Game;
}
