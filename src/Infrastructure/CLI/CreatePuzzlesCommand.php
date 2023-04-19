<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Infrastructure\CLI;

use Kpicaza\Sudoku\Domain\Command\CreatePuzzleCommand;
use Kpicaza\Sudoku\Domain\Handler\CreatePuzzle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePuzzlesCommand extends Command
{
    public function __construct(private readonly CreatePuzzle $createPuzzle)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this->setName('sudoku:create-puzzles')
            ->addArgument(
                'block-size',
                InputArgument::OPTIONAL,
                'The block size of the Sudoku Puzzle',
                3
            )
            ->addArgument(
                'whites-spaces',
                InputArgument::OPTIONAL,
                'The number of white spaces for the the Sudoku Puzzle',
                51
            )
            ->addArgument(
                'number',
                InputArgument::OPTIONAL,
                'The number of Sudoku Puzzles to create',
                1
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $game = $this->createPuzzle->handle(
            CreatePuzzleCommand::withBlockSizeAndBlankSpaces(
                (int)$input->getArgument('block-size'),
                (int)$input->getArgument('whites-spaces'),
            )
        );

        $output->writeln($game->id());

        return Command::SUCCESS;
    }
}
