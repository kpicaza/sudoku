<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku\Domain\Handler;

use Kpicaza\Sudoku\Domain\Command\CreatePuzzleCommand;
use Kpicaza\Sudoku\Domain\Handler\CreatePuzzle;
use Kpicaza\Sudoku\Domain\Model\Game;
use Kpicaza\Sudoku\Domain\UncheckedPuzzleRepository;
use PHPUnit\Framework\TestCase;

final class CreatePuzzleTest extends TestCase
{
    public function testCreateAndStoreRawPuzzles(): void
    {
        $command = CreatePuzzleCommand::withBlockSizeAndBlankSpaces(
            3,
            51
        );

        $repository = $this->createMock(UncheckedPuzzleRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Game::class));

        $handler = new CreatePuzzle($repository);

        $handler->handle($command);
    }
}
