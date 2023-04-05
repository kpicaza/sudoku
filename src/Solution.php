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
}
