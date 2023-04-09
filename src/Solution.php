<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

use InvalidArgumentException;

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
        $gridMatrix = $grid->matrix;

        $numbers = range(1, $grid->size);

        $tries = 0;
        $maxTries = $grid->size * $grid->size * $grid->blockSize;
        do {
            $grid = self::fillGrid($grid->size, $grid->matrix, $numbers);
            if (null === $grid) {
                $grid = new Grid($gridMatrix);
                $tries++;
            }

            if ($tries === $maxTries) {
                break;
            }
        }
        while(false === Solution::isFullFilled($grid));

        return new self($grid);
    }

    public static function fillGrid(int $size, array $matrix, array $numbers): ?Grid
    {
        $grid = new Grid($matrix);

        $move = $grid->tryNextMoveByTriangulation();
        if ($move instanceof Move) {
            return $grid->move($move);
        }

        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                if (is_numeric($grid->matrix[$row][$col])) {
                    continue;
                }
                shuffle($numbers);
                $block = $grid->getBlockIndex($row, $col);
                foreach ($numbers as $number) {
                    $move = $grid->tryNextMove(new Move($row, $col, $block, $number));
                    if ($move instanceof Move) {
                        return $grid->move($move);
                    }
                }
            }
        }

        return null;
    }

    public static function isFullFilled(Grid $grid): bool
    {
        return ($grid->size * $grid->size) === count(
                array_merge(
                    ...array_map(
                        fn($matrix) => array_filter($matrix,  fn($item) => is_numeric($item)),
                        $grid->matrix
                    )
                )
            );
    }
}
