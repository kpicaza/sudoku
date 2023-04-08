<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

use InvalidArgumentException;

final class Solution
{
  public function __construct(public readonly Grid $grid)
  {
      if (false === $this->isFullFilled()) {
          throw new InvalidArgumentException('Solution grid is not full filled.');
      }
  }

    public static function fromInitial(Grid $initialGrid): self
    {
        $solvedGrid = $initialGrid->matrix;

        while ($nextMove = $initialGrid->nextMove()) {
            $solvedGrid[$nextMove->row][$nextMove->col] = (string)$nextMove->value;
            $initialGrid = new Grid($solvedGrid);
        }

        return new self(new Grid($solvedGrid));
    }


    public function isFullFilled(): bool
    {
        return ($this->grid->size * $this->grid->size) === count(
                array_merge(
                    ...array_map(
                        fn($matrix) => array_filter($matrix,  fn($item) => is_numeric($item)),
                        $this->grid->matrix
                    )
                )
            );
    }
}
