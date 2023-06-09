<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Infrastructure\Dbal;

use Doctrine\DBAL\Connection;
use Kpicaza\Sudoku\Domain\Model\Game;
use Kpicaza\Sudoku\Domain\Model\Grid;
use Kpicaza\Sudoku\Domain\UncheckedPuzzleRepository;
use Kpicaza\Sudoku\Infrastructure\Format\CsvGridFactory;
use Kpicaza\Sudoku\Infrastructure\Format\CsvPrinter;

/**
 * @psalm-type UncheckedPuzzle array{
 *   id: string,
 *   initial_grid: string,
 *   solution: string,
 *   times_solved: int,
 *   different_solutions: int,
 *   created_at: string,
 * }
 */
final readonly class DbalUncheckedPuzzleRepository implements UncheckedPuzzleRepository
{
    public function __construct(
        private Connection $connection
    ) {
        $this->createSchema();
    }

    public function save(Game $game): void
    {
        $gameId = $game->id();
        $gameExist = $this->findGame($gameId);

        if ($gameExist) {
            $this->connection->executeQuery(
                <<<SQL
                    UPDATE unchecked_puzzles
                    SET times_solved = (times_solved + 1),
                        different_solutions = (different_solutions + :new_solution)
                    WHERE id = :game_id
                SQL,
                [
                    'new_solution' => (int)$game->hasDifferentSolutions(),
                    'game_id' => $gameId,
                ]
            );

            return;
        }

        $this->connection->insert('unchecked_puzzles', [
            'id' => $gameId,
            'initial_grid' => CsvPrinter::render($game->initialGrid),
            'solution' => CsvPrinter::render($game->solution?->grid ?? new Grid([])),
            'times_solved' => 1,
            'different_solutions' => 1,
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
    }

    public function get(string $gameId): Game
    {
        $result = $this->findGame($gameId);
        if (null === $result) {
            throw new \InvalidArgumentException(
                'There is no available game for given id.'
            );
        }

        return Game::fromSolutionGrid(
            CsvGridFactory::fromString($result['solution']),
            CsvGridFactory::fromString($result['initial_grid']),
        );
    }

    private function createSchema(): void
    {
        $tableName = 'unchecked_puzzles';
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist($tableName)) {
            return;
        }

        $schema = $schemaManager->introspectSchema();
        $platform = $this->connection->getDatabasePlatform();
        $table = $schema->createTable($tableName);

        $table->addColumn('id', 'string', ['length' => 36]);

        $table->setPrimaryKey(['id']);
        $table->addColumn('initial_grid', 'string');
        $table->addColumn('solution', 'string');
        $table->addColumn('times_solved', 'bigint', ['length' => 36]);
        $table->addColumn('different_solutions', 'smallint', ['length' => 1]);

        $table->addColumn('created_at', 'datetime');

        $queries = $schema->toSql($platform);
        foreach ($queries as $query) {
            if (false === str_starts_with($query, "CREATE TABLE $tableName")) {
                continue;
            }
            $this->connection->executeQuery($query);
        }
    }

    /** @return UncheckedPuzzle|null */
    private function findGame(string $gameId): ?array
    {
        $query = $this->connection->executeQuery(
            'SELECT * FROM unchecked_puzzles WHERE id = :game_id',
            [
                'game_id' => $gameId,
            ]
        );

        /** @var false|UncheckedPuzzle $result */
        $result = $query->fetchAssociative();

        return $result ?: null;
    }
}
