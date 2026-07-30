<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

define('EXCEL_FILE', __DIR__ . '/upload/Orders.xlsx');

function getOrders()
{
    if (!file_exists(EXCEL_FILE)) {
        return [];
    }

    $spreadsheet = IOFactory::load(EXCEL_FILE);
    $sheet = $spreadsheet->getActiveSheet();

    $rows = $sheet->toArray(null, true, true, true);

    $orders = [];

    $header = [];

    foreach ($rows as $index => $row) {

        if ($index == 1) {
            $header = array_values($row);
            continue;
        }

        if (empty(trim($row['A']))) {
            continue;
        }

        $orders[] = array_combine($header, array_values($row));
    }

    return $orders;
}