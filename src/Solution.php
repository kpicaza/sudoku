<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku;

class Solution
{
  public function __construct(public readonly Grid $grid)
  {
      if (false === $grid->isFullFilled()) {
          throw new \InvalidArgumentException('Solution grid is not full filled.');
      }
  }

    public static function from(Grid $initialGrid): self
    {
        $solvedGrid = $initialGrid->matrix;

        while ($nextMove = $initialGrid->nextMove()) {
            $solvedGrid[$nextMove->row][$nextMove->col] = (string)$nextMove->value;
            $initialGrid = new Grid($solvedGrid);
        }

        return new self(new Grid($solvedGrid));
    }
}
