<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku\Domain\Handler;

use Kpicaza\Sudoku\Domain\Command\SolvePuzzleCommand;
use Kpicaza\Sudoku\Domain\Handler\SolvePuzzle;
use Kpicaza\Sudoku\Domain\Model\Game;
use Kpicaza\Sudoku\Domain\UncheckedPuzzleRepository;
use Kpicaza\Sudoku\Infrastructure\Format\CsvGridFactory;
use PHPUnit\Framework\TestCase;

final class SolvePuzzleTest extends TestCase
{
    public function testSolvesAPuzzleAndUpgradePossibleSolutionCount(): void
    {
        $game = Game::fromInitialGrid(
            CsvGridFactory::fromFileLocation(
                'tests/examples/9x9-initial-grid.csv'
            )
        );
        $id = $game->id();
        $repository = $this->createMock(UncheckedPuzzleRepository::class);
        $repository->expects($this->once())
            ->method('get')
            ->with($id)
            ->willReturn($game);
        $repository->expects($this->once())
            ->method('save')
            ->with($game);

        $command = SolvePuzzleCommand::fromId($id);

        $handler = new SolvePuzzle($repository);

        $handler->handle($command);
    }
}
