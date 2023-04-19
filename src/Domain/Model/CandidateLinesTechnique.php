<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

final class CandidateLinesTechnique
{
    public static function place(Grid $grid): ?Move
    {
        $blockMarks = [];

        foreach ($grid->pencilMarks as $pencilMark) {
            $blockMarks[$pencilMark->position->block][] = $pencilMark;
        }

        foreach ($blockMarks as $block => $pencilMarks) {
            $blockCandidates = [];
            foreach ($pencilMarks as $pencilMark) {
                foreach ($pencilMark->pencilMark as $candidateNumber) {
                    $blockCandidates[$candidateNumber][] = new Move($pencilMark->position, $candidateNumber);
                }
            }

            foreach ($blockCandidates as $occurrences) {
                if ($grid->blockSize < count($occurrences)) {
                    continue;
                }
                $move = self::findInVertical($occurrences, $block, $grid);
                if ($move instanceof Move) {
                    return $move;
                }
                $move = self::findInHorizontal($occurrences, $block, $grid);
                if ($move instanceof Move) {
                    return $move;
                }
            }
        }

        return null;
    }

    /**
     * @param array<Move> $occurrences
     */
    private static function getVerticalLine(array $occurrences): ?Move
    {
        $verticalLine = null;
        foreach ($occurrences as $occurrence) {
            if (false === isset($prevOccurrence)) {
                $prevOccurrence = $occurrence;
            }

            if ($prevOccurrence->position->row !== $occurrence->position->row
                && $prevOccurrence->position->col === $occurrence->position->col) {
                $verticalLine = $prevOccurrence;
            } else {
                $verticalLine = null;
            }

            $prevOccurrence = $occurrence;
        }

        return $verticalLine;
    }

    /**
     * @param array<Move> $occurrences
     */
    private static function getHorizontalLine(array $occurrences): ?Move
    {
        $horizontalLine = null;
        foreach ($occurrences as $occurrence) {
            if (false === isset($prevOccurrence)) {
                $prevOccurrence = $occurrence;
            }

            if ($prevOccurrence->position->row === $occurrence->position->row
                && $prevOccurrence->position->col !== $occurrence->position->col) {
                $horizontalLine = $occurrence;
            } else {
                $horizontalLine = null;
            }

            $prevOccurrence = $occurrence;
        }

        return $horizontalLine;
    }

    /** @param array<Move> $occurrences */
    private static function findInVertical(array $occurrences, int $block, Grid $grid): ?Move
    {
        $possibleMove = self::getVerticalLine($occurrences);
        if (null === $possibleMove) {
            return null;
        }

        foreach ($grid->pencilMarks as $pencilMark) {
            if ($pencilMark->position->col !== $possibleMove->position->col) {
                continue;
            }

            $currentBlock = $grid->getBlockIndex($pencilMark->position->row, $possibleMove->position->col);
            if ($block === $currentBlock) {
                continue;
            }

            $possibleNumbers = array_diff($pencilMark->pencilMark, [$possibleMove->value]);
            if (1 === count($possibleNumbers)) {
                return new Move(
                    new Position($pencilMark->position->row, $possibleMove->position->col, $currentBlock),
                    end($possibleNumbers)
                );
            }
        }

        return null;
    }

    /** @param array<Move> $occurrences */
    private static function findInHorizontal(mixed $occurrences, int $block, Grid $grid): ?Move
    {
        $possibleMove = self::getHorizontalLine($occurrences);

        if (null === $possibleMove) {
            return null;
        }
        foreach ($grid->pencilMarks as $pencilMark) {
            if ($pencilMark->position->row !== $possibleMove->position->row) {
                continue;
            }

            $currentBlock = $grid->getBlockIndex($possibleMove->position->row, $pencilMark->position->col);
            if ($block === $currentBlock) {
                continue;
            }

            $possibleNumbers = array_diff($pencilMark->pencilMark, [$possibleMove->value]);
            if (1 === count($possibleNumbers)) {
                return new Move(
                    new Position($possibleMove->position->row, $pencilMark->position->col, $currentBlock),
                    end($possibleNumbers)
                );
            }
        }

        return null;
    }
}
