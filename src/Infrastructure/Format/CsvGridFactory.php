<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Infrastructure\Format;

use Kpicaza\Sudoku\Domain\Model\Grid;

final class CsvGridFactory
{
    public static function fromFileLocation(string $filePath): Grid
    {
        $fileResource = fopen($filePath, 'r');

        $matrix = [];
        while ($row = fgetcsv($fileResource)) {
            array_pop($row);
            $matrix[] = $row;
        }

        return new Grid($matrix);
    }
}
