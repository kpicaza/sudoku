<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku\Functional;

use Kpicaza\Sudoku\Domain\UncheckedPuzzleRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CreatePuzzlesTest extends BaseTestCase
{
    public function testCreatePuzzlesByCLI(): void
    {
        $application = new Application($this->symfonyKernel);

        /** @var UncheckedPuzzleRepository $repository */
        $repository = $this->container->get(UncheckedPuzzleRepository::class);

        $command = $application->find('sudoku:create-puzzles');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'block-size' => 3,
            'whites-spaces' => 51,
            'number' => 1,
        ]);
        $commandTester->assertCommandIsSuccessful();
        $output = trim($commandTester->getDisplay());
        $game = $repository->get($output);

        $this->assertSame(3, $game->initialGrid->blockSize);
    }
}
