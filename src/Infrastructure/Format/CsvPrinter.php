<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Infrastructure\Format;

use Kpicaza\Sudoku\Domain\Model\Grid;

final class CsvPrinter
{
    public static function render(Grid $grid): string
    {
        $csv = '';

        foreach ($grid->matrix as $key => $row) {
            $csv .= implode(',', $row) . ',' . ($key + 1 === $grid->size ? '' : PHP_EOL);
        }

        return $csv;
    }
}
