<?php

declare(strict_types=1);

namespace Kpicaza\Sudoku\Infrastructure\Format;

use InvalidArgumentException;
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

    public static function fromString(string $csvString): Grid
    {
        $matrix = [];

        $rows = str_getcsv($csvString, "\n");

        foreach ($rows as $stringRow) {
            if (null === $stringRow) {
                throw new InvalidArgumentException();
            }

            $row = explode(',', $stringRow);
            array_pop($row);
            $matrix[] = $row;
        }

        return new Grid($matrix);
    }
}
