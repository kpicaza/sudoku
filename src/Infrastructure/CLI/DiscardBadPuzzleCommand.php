<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Infrastructure\CLI;

use Kpicaza\Sudoku\Domain\Command\SolvePuzzleCommand;
use Kpicaza\Sudoku\Domain\Handler\SolvePuzzle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiscardBadPuzzleCommand extends Command
{
    public function __construct(private readonly SolvePuzzle $solvePuzzle)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this->setName('sudoku:discard-bad-puzzle')
            ->addArgument(
                'puzzle_id',
                InputArgument::REQUIRED,
                'The Sudoku Puzzle Identifier',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->solvePuzzle->handle(
            SolvePuzzleCommand::fromId(
                (string)$input->getArgument('puzzle_id'),
            )
        );

        return Command::SUCCESS;
    }
}
