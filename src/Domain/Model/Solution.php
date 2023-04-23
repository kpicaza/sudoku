<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Domain\Model;

use InvalidArgumentException;
use Kpicaza\Sudoku\Infrastructure\Format\CsvPrinter;

final readonly class Solution
{
    public function __construct(public Grid $grid)
    {
        if (false === self::isFullFilled($this->grid)) {
            throw new InvalidArgumentException('Solution grid is not full filled.');
        }
    }

      public static function fromInitial(Grid $grid): self
      {
          $gridMatrix = clone $grid;

          $tries = 0;
          $filled = 0;
          $maxTries = $grid->size * $grid->size * $grid->blockSize;
          do {
              $grid = self::fillGrid($grid);
              $isFullFilled = Solution::isFullFilled($grid ?? $gridMatrix);
              if (false === $isFullFilled && null === $grid) {
                  $grid = $gridMatrix;
                  $tries++;
                  $filled = 0;
                  continue;
              }

              if (null === $grid) {
                  $grid = $gridMatrix;
                  $tries++;
                  $filled = 0;
                  continue;
              }
              if ($maxTries === $tries) {
                  break;
              }

              $filled++;
          } while (false === Solution::isFullFilled($grid));

          return new self($grid);
      }

      /**
       * @param array<int, array<int, string>> $matrix
       */
      public static function fillGrid(Grid $grid): ?Grid
      {
          for ($row = 0; $row < $grid->size; $row++) {
              $block = $grid->getBlockIndex($row, $row);
              $position = new Position($row, $row, $block);
              $move = SinglePositionTechnique::place($position, $grid);
              if ($move instanceof Move) {
                  return $grid->move($move);
              }

              $block = $grid->getBlockIndex($row, $row);
              $position = new Position($row, $row, $block);
              $move = SingeCandidateTechnique::place($position, $grid);
              if ($move instanceof Move) {
                  return $grid->move($move);
              }
          }

          $move = PencilMarkTechnique::place($grid);
          if ($move instanceof Move) {
              return $grid->move($move);
          }

          $move = CandidateLinesTechnique::place($grid);
          if ($move instanceof Move) {
              return $grid->move($move);
          }

          for ($row = 0; $row < $grid->size; $row++) {
              $block = $grid->getBlockIndex($row, $row);
              $position = new Position($row, $row, $block);
              $move = BackTracingTechnique::place($position, $grid);
              if ($move instanceof Move) {
                  return $grid->move($move);
              }
          }

          return null;
      }

      public static function isFullFilled(Grid $grid): bool
      {
          return ($grid->size * $grid->size) === count(
              array_merge(
                  ...array_map(
                      fn ($matrix) => array_filter($matrix, fn ($item) => is_numeric($item)),
                      $grid->matrix
                  )
              )
          );
      }

    public static function hasSingleSolution(Grid $grid): bool
    {
        $sample1Grid = self::fromInitial($grid);
        $sample1 = CsvPrinter::render($sample1Grid->grid);
        $sample2Grid = self::fromInitial($grid);
        $sample2 = CsvPrinter::render($sample2Grid->grid);
        $sample3Grid = self::fromInitial($grid);
        $sample3 = CsvPrinter::render($sample3Grid->grid);

        return $sample1 === $sample2 && $sample2 === $sample3;
    }

    public function equalTo(Solution $solution): bool
    {
        return $this->grid->matrix === $solution->grid->matrix;
    }
}
