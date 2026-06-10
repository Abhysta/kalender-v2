<?php

namespace App\Traits;

trait ParsesCsvOrExcel
{
    protected function parseCsvOrExcel(string $path, string $ext): array
    {
        if (in_array(strtolower($ext), ['xlsx', 'xls'])) {
            if (! class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                return [];
            }
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            if (empty($sheet)) {
                return [];
            }
            $headers = array_map('trim', $sheet[0]);
            $result  = [];
            foreach (array_slice($sheet, 1) as $row) {
                if (array_filter($row, fn($v) => $v !== null && $v !== '')) {
                    $result[] = array_combine($headers, array_map('strval', $row));
                }
            }
            return $result;
        }

        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }
        $headers = null;
        $rows    = [];
        while (($line = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                $headers = array_map('trim', $line);
                continue;
            }
            if (count($line) < count($headers)) {
                $line = array_pad($line, count($headers), '');
            }
            $row = array_combine($headers, array_map('trim', $line));
            if (array_filter($row)) {
                $rows[] = $row;
            }
        }
        fclose($handle);
        return $rows;
    }
}
