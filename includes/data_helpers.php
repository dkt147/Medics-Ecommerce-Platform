<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function getDefaultOrdersRows()
{
    return [
        ['tracking_no' => 'LP0012847263', 'order_ref' => 'ORD-9812', 'date' => '2026-07-20', 'customer' => 'Ahmed Raza', 'city' => 'Karachi', 'weight' => '1.2 kg', 'cod_amount' => 'PKR 2,500', 'status' => 'Delivered', 'delivery_date' => '2026-07-22', 'returned_by' => ''],
        ['tracking_no' => 'LP0012846901', 'order_ref' => 'ORD-9811', 'date' => '2026-07-20', 'customer' => 'Fatima Malik', 'city' => 'Lahore', 'weight' => '0.8 kg', 'cod_amount' => 'PKR 1,800', 'status' => 'Delivered', 'delivery_date' => '2026-07-21', 'returned_by' => ''],
        ['tracking_no' => 'LP0012845234', 'order_ref' => 'ORD-9808', 'date' => '2026-07-19', 'customer' => 'Usman Khan', 'city' => 'Islamabad', 'weight' => '2.5 kg', 'cod_amount' => 'PKR 5,200', 'status' => 'In Transit', 'delivery_date' => '', 'returned_by' => ''],
        ['tracking_no' => 'LP0012843190', 'order_ref' => 'ORD-9803', 'date' => '2026-07-18', 'customer' => 'Aisha Siddiqui', 'city' => 'Rawalpindi', 'weight' => '1.0 kg', 'cod_amount' => 'PKR 3,100', 'status' => 'Returned', 'delivery_date' => '', 'returned_by' => 'Leopard'],
        ['tracking_no' => 'LP0012841000', 'order_ref' => 'ORD-9799', 'date' => '2026-07-18', 'customer' => 'Bilal Hussain', 'city' => 'Faisalabad', 'weight' => '3.1 kg', 'cod_amount' => 'PKR 7,800', 'status' => 'Delivered', 'delivery_date' => '2026-07-20', 'returned_by' => ''],
        ['tracking_no' => 'LP0012839400', 'order_ref' => 'ORD-9795', 'date' => '2026-07-17', 'customer' => 'Sara Iqbal', 'city' => 'Multan', 'weight' => '0.5 kg', 'cod_amount' => '', 'status' => 'Prepaid', 'delivery_date' => '2026-07-19', 'returned_by' => ''],
        ['tracking_no' => 'LP0012837100', 'order_ref' => 'ORD-9790', 'date' => '2026-07-17', 'customer' => 'Hassan Butt', 'city' => 'Karachi', 'weight' => '1.8 kg', 'cod_amount' => 'PKR 4,400', 'status' => 'Pending', 'delivery_date' => '', 'returned_by' => ''],
        ['tracking_no' => 'LP0012834200', 'order_ref' => 'ORD-9785', 'date' => '2026-07-16', 'customer' => 'Zainab Noor', 'city' => 'Lahore', 'weight' => '0.9 kg', 'cod_amount' => 'PKR 2,200', 'status' => 'Returned', 'delivery_date' => '', 'returned_by' => 'Customer'],
    ];
}

function normalizeHeaderKey($header)
{
    $headerText = strtolower(trim((string) $header));
    $normalized = preg_replace('/[^a-z0-9]+/', '_', $headerText);
    $normalized = trim($normalized, '_');

    $aliases = [
        'tracking_no' => 'tracking_no',
        'tracking' => 'tracking_no',
        'tracking_number' => 'tracking_no',
        'order_ref' => 'order_ref',
        'order_id' => 'order_ref',
        'date' => 'date',
        'customer' => 'customer',
        'city' => 'city',
        'weight' => 'weight',
        'cod_amount' => 'cod_amount',
        'amount' => 'cod_amount',
        'status' => 'status',
        'delivery_date' => 'delivery_date',
        'delivery' => 'delivery_date',
        'returned_by' => 'returned_by',
        'return_type' => 'returned_by',
    ];

    return $aliases[$normalized] ?? $normalized;
}

function normalizeRowsFromExcel($path, $fallbackRows = [])
{
    $GLOBALS['excelImportDebug'] = [
        'path' => $path,
        'found' => false,
        'sheet' => null,
        'header_row' => null,
        'headers' => [],
        'data_rows' => 0,
        'error' => null,
    ];

    if (!file_exists($path)) {
        $GLOBALS['excelImportDebug']['error'] = 'file_missing';
        return $fallbackRows;
    }

    try {
        $spreadsheet = IOFactory::load($path);
        $rows = [];
        $headers = [];
        $expectedHeaders = [
            'tracking_no', 'order_ref', 'date', 'customer', 'city', 'weight',
            'cod_amount', 'status', 'delivery_date', 'returned_by', 'return_type'
        ];

        $headerRowIndex = 0;
        $activeSheet = null;

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();
                if ($rowIndex > 20) {
                    break;
                }

                $values = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $values[] = $cell->getCalculatedValue();
                }

                $normalized = array_map('normalizeHeaderKey', $values);
                $matchCount = count(array_intersect($normalized, $expectedHeaders));
                if ($matchCount >= 4) {
                    $headers = $normalized;
                    $headerRowIndex = $rowIndex;
                    $activeSheet = $sheet;
                    break 2;
                }
            }
        }

        if (empty($headers) || $headerRowIndex === 0 || $activeSheet === null) {
            $GLOBALS['excelImportDebug']['error'] = 'header_not_found';
            return $fallbackRows;
        }

        $GLOBALS['excelImportDebug']['found'] = true;
        $GLOBALS['excelImportDebug']['sheet'] = $activeSheet->getTitle();
        $GLOBALS['excelImportDebug']['header_row'] = $headerRowIndex;
        $GLOBALS['excelImportDebug']['headers'] = $headers;

        foreach ($activeSheet->getRowIterator($headerRowIndex + 1) as $row) {
            $values = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $values[] = $cell->getCalculatedValue();
            }

            if (empty(array_filter($values, function ($value) {
                return $value !== null && $value !== '';
            }))) {
                continue;
            }

            $record = [];
            foreach ($headers as $position => $header) {
                $record[$header] = isset($values[$position]) ? trim((string) $values[$position]) : '';
            }

            $rows[] = [
                'tracking_no' => $record['tracking_no'] ?? '',
                'order_ref' => $record['order_ref'] ?? '',
                'date' => $record['date'] ?? '',
                'customer' => $record['customer'] ?? '',
                'city' => $record['city'] ?? '',
                'weight' => $record['weight'] ?? '',
                'cod_amount' => $record['cod_amount'] ?? '',
                'status' => $record['status'] ?? '',
                'delivery_date' => $record['delivery_date'] ?? '',
                'returned_by' => $record['returned_by'] ?? '',
            ];
        }

        if (!empty($rows)) {
            $GLOBALS['excelImportDebug']['data_rows'] = count($rows);
            return $rows;
        }
    } catch (Throwable $e) {
        $GLOBALS['excelImportDebug']['error'] = 'exception: ' . $e->getMessage();
    }

    return $fallbackRows;
}

function loadOrdersRows($excelFilePath = null, $fallbackRows = [])
{
    $path = $excelFilePath ?? __DIR__ . '/../upload/orders_data.xlsx';
    $defaults = $fallbackRows ?: getDefaultOrdersRows();
    return normalizeRowsFromExcel($path, $defaults);
}

function statusMatches($rowStatus, $filterStatus)
{
    $normalizedRowStatus = strtolower(trim((string) ($rowStatus ?? '')));
    $normalizedFilterStatus = strtolower(trim((string) $filterStatus));

    if ($normalizedFilterStatus === '' || $normalizedFilterStatus === 'all status') {
        return true;
    }

    return $normalizedRowStatus === $normalizedFilterStatus;
}

function parseCurrencyValue($value)
{
    if ($value === null || $value === '') {
        return 0;
    }

    $text = trim((string) $value);
    $text = str_replace(['PKR', ',', ' '], '', $text);
    return is_numeric($text) ? (float) $text : 0;
}

function formatCurrency($value)
{
    return 'PKR ' . number_format((float) $value, 0);
}

function getOrderMetrics($rows)
{
    $totalOrders = count($rows);
    $deliveredCount = count(array_filter($rows, function ($row) {
        return strtolower(trim($row['status'] ?? '')) === 'delivered';
    }));
    $returnedCount = count(array_filter($rows, function ($row) {
        return strtolower(trim($row['status'] ?? '')) === 'returned';
    }));
    $pendingCount = count(array_filter($rows, function ($row) {
        return strtolower(trim($row['status'] ?? '')) === 'pending';
    }));
    $inTransitCount = count(array_filter($rows, function ($row) {
        return strtolower(trim($row['status'] ?? '')) === 'in transit';
    }));
    $codDeclared = array_sum(array_map('parseCurrencyValue', array_column($rows, 'cod_amount')));

    return [
        'total_orders' => $totalOrders,
        'delivered_count' => $deliveredCount,
        'returned_count' => $returnedCount,
        'pending_count' => $pendingCount,
        'in_transit_count' => $inTransitCount,
        'cod_declared' => $codDeclared,
        'delivery_rate' => $totalOrders > 0 ? round(($deliveredCount / $totalOrders) * 100, 1) : 0,
        'return_rate' => $totalOrders > 0 ? round(($returnedCount / $totalOrders) * 100, 1) : 0,
    ];
}

function getReturnRows($rows)
{
    return array_values(array_filter($rows, function ($row) {
        $status = strtolower(trim($row['status'] ?? ''));
        $returnedBy = trim($row['returned_by'] ?? '');
        return $status === 'returned' || $returnedBy !== '';
    }));
}

function getCityCounts($rows)
{
    $cityCounts = [];
    foreach ($rows as $row) {
        $city = trim($row['city'] ?? '') ?: 'Unknown';
        $cityCounts[$city] = ($cityCounts[$city] ?? 0) + 1;
    }

    arsort($cityCounts);
    return $cityCounts;
}
