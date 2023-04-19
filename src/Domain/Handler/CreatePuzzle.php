<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Handler;

use Kpicaza\Sudoku\Domain\Command\CreatePuzzleCommand;
use Kpicaza\Sudoku\Domain\Model\Game;
use Kpicaza\Sudoku\Domain\UncheckedPuzzleRepository;

final readonly class CreatePuzzle
{
    public function __construct(
        private UncheckedPuzzleRepository $repository
    ) {
    }

    public function handle(CreatePuzzleCommand $command): Game
    {
        $game = Game::withBlockSizeAndBlankSpacesNoSolutionChecks(
            $command->blockSize,
            $command->blankSpaces
        );

        $this->repository->save($game);

        return $game;
    }
}
