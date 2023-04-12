<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

final class SinglePositionTechnique
{
    private function __construct()
    {
    }

    public static function place(Position $position, Grid $grid): ?Move
    {
        $self = new self();
        $move = $self->findInRow($position, $grid);
        if (null === $move) {
            $move = $self->findInVertical($position, $grid);
        }

        return $move;
    }

    private function findInRow(Position $position, Grid $grid): ?Move
    {
        $row = $grid->matrix[$position->row];

        $missingNumbers = array_diff($grid->numbers, $grid->getRowNumbers($row));
        $rowBlocks = [];
        foreach (array_keys($row) as $col) {
            $rowBlocks[] = $grid->getBlockIndex($position->row, $col);
        }
        $rowBlocks = array_values(array_unique($rowBlocks));
        $options = [];
        foreach ($row as $col => $value) {
            if (is_numeric($value)) {
                continue;
            }

            $block = $grid->getBlockIndex($position->row, $col);
            $tryPosition = new Position($position->row, $col, $block);

            $options[$block] = array_merge(
                $options[$block] ?? [],
                $this->getChoices($missingNumbers, $rowBlocks, $col, $block, $tryPosition, $grid)
            );
        }

        foreach ($options as $block => $option) {
            if (1 === count($option)) {
                return new Move(new Position($position->row, $option[0][0], $block), $option[0][1]);
            }
        }

        return null;
    }

    private function findInVertical(Position $position, Grid $grid): ?Move
    {
        $col = $grid->verticalMatrix[$position->col];

        $missingNumbers = array_diff($grid->numbers, $grid->getRowNumbers($col));
        $colBlocks = [];
        foreach (array_keys($col) as $row) {
            $colBlocks[] = $grid->getBlockIndex($row, $position->col);
        }
        $colBlocks = array_values(array_unique($colBlocks));

        $options = [];
        foreach ($col as $row => $value) {
            if (is_numeric($value)) {
                continue;
            }

            $block = $grid->getBlockIndex($row, $position->col);
            $tryPosition = new Position($row, $position->col, $block);

            $options[$block] = array_merge(
                $options[$block] ?? [],
                $this->getChoices($missingNumbers, $colBlocks, $row, $block, $tryPosition, $grid)
            );
        }

        foreach ($options as $block => $option) {
            if (1 === count($option)) {
                return new Move(new Position($option[0][0], $position->col, $block), $option[0][1]);
            }
        }

        return null;
    }

    /** @param array<int> $colBlocks */
    private function isAvailableInNearBlocks(
        int $missingNumber,
        array $colBlocks,
        int $block,
        Grid $grid
    ): bool {
        foreach ($colBlocks as $colBlock) {
            if ($colBlock === $block) {
                continue;
            }

            if (in_array((string)$missingNumber, $grid->blockMatrix[$colBlock], true)) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @param array<int> $missingNumbers
     * @param array<int> $blocks
     * @return array<array<int>>
     */
    private function getChoices(
        array $missingNumbers,
        array $blocks,
        int $index,
        int $block,
        Position $tryPosition,
        Grid $grid
    ): array {
        $choices = [];
        foreach ($missingNumbers as $missingNumber) {
            if (false === $grid->isAvailablePosition($missingNumber, $tryPosition)) {
                continue;
            }
            if (true === $this->isAvailableInNearBlocks($missingNumber, $blocks, $block, $grid)) {
                continue;
            }

            $choices[] = [$index, $missingNumber];
        }

        return $choices;
    }
}
