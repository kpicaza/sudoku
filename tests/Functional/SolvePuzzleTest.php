<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku\Functional;

use Kpicaza\Sudoku\Domain\Model\Game;
use Kpicaza\Sudoku\Domain\UncheckedPuzzleRepository;
use Kpicaza\Sudoku\Infrastructure\Format\CsvGridFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class SolvePuzzleTest extends BaseTestCase
{
    public function testSolvePuzzlesByCLI(): void
    {
        $application = new Application($this->symfonyKernel);

        /** @var UncheckedPuzzleRepository $repository */
        $repository = $this->container->get(UncheckedPuzzleRepository::class);
        $game = Game::fromInitialGrid(CsvGridFactory::fromFileLocation(
            'tests/examples/9x9-initial-grid.csv'
        ));
        $repository->save($game);

        $command = $application->find('sudoku:discard-bad-puzzle');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'puzzle_id' => $game->id(),
        ]);
        $commandTester->assertCommandIsSuccessful();

        $updatedGame = $repository->get($game->id());

        $this->assertSame(false, $updatedGame->hasDifferentSolutions());
    }
}
