<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Handler;

use Kpicaza\Sudoku\Domain\Command\SolvePuzzleCommand;
use Kpicaza\Sudoku\Domain\Model\Game;
use Kpicaza\Sudoku\Domain\UncheckedPuzzleRepository;

final readonly class SolvePuzzle
{
    public function __construct(
        private UncheckedPuzzleRepository $repository
    ) {
    }

    public function handle(SolvePuzzleCommand $command): void
    {
        $game = $this->repository->get($command->id);

        $solvedGame = Game::fromInitialGrid($game->initialGrid);

        $game->addSolution($solvedGame->solution);

        $this->repository->save($game);
    }
}
