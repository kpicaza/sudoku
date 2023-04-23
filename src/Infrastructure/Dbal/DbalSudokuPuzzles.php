<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Infrastructure\Dbal;

use Doctrine\DBAL\Connection;
use Kpicaza\Sudoku\Application\SudokuPuzzle;
use Kpicaza\Sudoku\Application\SudokuPuzzles;
use Kpicaza\Sudoku\Infrastructure\Format\CsvGridFactory;

/**
 * @psalm-import-type UncheckedPuzzle from DbalUncheckedPuzzleRepository
 */
final readonly class DbalSudokuPuzzles implements SudokuPuzzles
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function findOneRandom(): SudokuPuzzle
    {
        $query = $this->connection->executeQuery(
            <<<SQL
                SELECT * 
                FROM unchecked_puzzles 
                WHERE times_solved > 1
                    AND different_solutions = 1
                ORDER BY RANDOM()
                LIMIT 1
            SQL
        );

        /** @var UncheckedPuzzle $result */
        $result = $query->fetchAssociative();

        return new SudokuPuzzle(
            CsvGridFactory::fromString($result['initial_grid']),
            CsvGridFactory::fromString($result['solution'])
        );
    }
}
