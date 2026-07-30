<?php
/**
 * data_helpers.php
 * Central data-access layer for Medics E-Commerce Platform.
 * All pages require_once this file and call the helpers below.
 *
 * Single source of truth: upload/database.xlsx
 * Each sheet maps 1-to-1 to a tab in that file.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

// ─────────────────────────────────────────────
//  CONFIG
// ─────────────────────────────────────────────
define('DB_EXCEL_PATH', __DIR__ . '/../upload/database.xlsx');

// ─────────────────────────────────────────────
//  CORE LOADER  (reads any named sheet)
// ─────────────────────────────────────────────
/**
 * Load all rows from a named sheet in the Excel database.
 * Returns an array of associative arrays keyed by header names
 * normalised to lowercase_with_underscores.
 *
 * @param string $sheetName  Exact sheet tab name (case-insensitive)
 * @param array  $fallback   Returned when file/sheet is missing
 * @return array
 */
function loadSheetRows(string $sheetName, array $fallback = []): array
{
    $path = DB_EXCEL_PATH;

    if (!file_exists($path)) {
        error_log("medics_db: file not found at $path");
        return $fallback;
    }

    try {
        $spreadsheet = IOFactory::load($path);

        // Case-insensitive sheet lookup
        $sheet = null;
        foreach ($spreadsheet->getAllSheets() as $s) {
            if (strtolower($s->getTitle()) === strtolower($sheetName)) {
                $sheet = $s;
                break;
            }
        }

        if ($sheet === null) {
            error_log("medics_db: sheet '$sheetName' not found");
            return $fallback;
        }

        $headers    = [];
        $rows       = [];
        $headerRow  = null;

        foreach ($sheet->getRowIterator() as $row) {
            $ri     = $row->getRowIndex();
            $cells  = [];

            $cellIter = $row->getCellIterator();
            $cellIter->setIterateOnlyExistingCells(false);
            foreach ($cellIter as $cell) {
                $cells[] = $cell->getCalculatedValue();
            }

            // First non-empty row is headers
            if ($headerRow === null) {
                $nonEmpty = array_filter($cells, fn($v) => $v !== null && $v !== '');
                if (empty($nonEmpty)) continue;
                $headerRow = $ri;
                foreach ($cells as $val) {
                    $headers[] = normaliseKey((string) $val);
                }
                continue;
            }

            // Skip blank rows
            if (empty(array_filter($cells, fn($v) => $v !== null && $v !== ''))) continue;

            $record = [];
            foreach ($headers as $ci => $key) {
                $record[$key] = isset($cells[$ci]) ? trim((string) $cells[$ci]) : '';
            }
            $rows[] = $record;
        }

        return $rows;

    } catch (Throwable $e) {
        error_log("medics_db: exception loading '$sheetName': " . $e->getMessage());
        return $fallback;
    }
}

/**
 * Normalise a header string to snake_case key.
 */
function normaliseKey(string $header): string
{
    $lower = strtolower(trim($header));
    $snake = preg_replace('/[^a-z0-9]+/', '_', $lower);
    return trim($snake, '_');
}

// ─────────────────────────────────────────────
//  SHEET LOADERS  (one per tab)
// ─────────────────────────────────────────────

function getOrdersRows(): array
{
    return loadSheetRows('orders', getDefaultOrdersRows());
}

function getReturnsRows(): array
{
    return loadSheetRows('returns');
}

function getChargesRows(): array
{
    return loadSheetRows('charges');
}

function getCODRows(): array
{
    return loadSheetRows('cod');
}

function getDisputesRows(): array
{
    return loadSheetRows('disputes');
}

function getExpensesRows(): array
{
    return loadSheetRows('expenses');
}

function getInventoryRows(): array
{
    return loadSheetRows('inventory');
}

function getRevenueRows(): array
{
    return loadSheetRows('revenue');
}

function getPLRows(): array
{
    return loadSheetRows('pl');
}

function getTaxRows(): array
{
    return loadSheetRows('tax');
}

function getBalanceSheetRows(): array
{
    return loadSheetRows('balance_sheet');
}

function getReportsRows(): array
{
    return loadSheetRows('reports');
}

// ─────────────────────────────────────────────
//  FALLBACK SAMPLE DATA  (used if Excel missing)
// ─────────────────────────────────────────────
function getDefaultOrdersRows(): array
{
    return [
        ['tracking_no'=>'LP0012847263','order_ref'=>'ORD-9812','date'=>'2026-07-20','customer'=>'Ahmed Raza',    'city'=>'Karachi',    'product'=>'Panadol Extra Tablets (20s)','sku'=>'MED-001','qty'=>'2','unit_price'=>'450','cost_price'=>'500','weight_kg'=>'0.60','cod_amount'=>'PKR 900', 'status'=>'Delivered', 'courier'=>'Leopard','delivery_date'=>'2026-07-22','returned_by'=>''],
        ['tracking_no'=>'LP0012846901','order_ref'=>'ORD-9811','date'=>'2026-07-20','customer'=>'Fatima Malik',  'city'=>'Lahore',     'product'=>'Vitamin C 1000mg (30 Tabs)',  'sku'=>'SUP-001','qty'=>'1','unit_price'=>'800','cost_price'=>'420','weight_kg'=>'0.20','cod_amount'=>'PKR 800', 'status'=>'Delivered', 'courier'=>'Leopard','delivery_date'=>'2026-07-21','returned_by'=>''],
        ['tracking_no'=>'LP0012845234','order_ref'=>'ORD-9808','date'=>'2026-07-19','customer'=>'Usman Khan',   'city'=>'Islamabad',  'product'=>'Digital BP Monitor',          'sku'=>'DEV-001','qty'=>'1','unit_price'=>'6500','cost_price'=>'3800','weight_kg'=>'1.50','cod_amount'=>'PKR 6500','status'=>'In Transit','courier'=>'Leopard','delivery_date'=>'',          'returned_by'=>''],
        ['tracking_no'=>'LP0012843190','order_ref'=>'ORD-9803','date'=>'2026-07-18','customer'=>'Aisha Siddiqui','city'=>'Rawalpindi', 'product'=>'ORS Sachets (Pack of 10)',    'sku'=>'MED-002','qty'=>'2','unit_price'=>'350','cost_price'=>'360','weight_kg'=>'0.50','cod_amount'=>'PKR 700', 'status'=>'Returned',  'courier'=>'Leopard','delivery_date'=>'',          'returned_by'=>'Customer'],
        ['tracking_no'=>'LP0012841000','order_ref'=>'ORD-9799','date'=>'2026-07-18','customer'=>'Bilal Hussain', 'city'=>'Faisalabad', 'product'=>'Glucometer Kit (25 Strips)',  'sku'=>'DEV-002','qty'=>'1','unit_price'=>'4200','cost_price'=>'2400','weight_kg'=>'1.20','cod_amount'=>'PKR 4200','status'=>'Delivered', 'courier'=>'TCS',    'delivery_date'=>'2026-07-20','returned_by'=>''],
        ['tracking_no'=>'LP0012839400','order_ref'=>'ORD-9795','date'=>'2026-07-17','customer'=>'Sara Iqbal',   'city'=>'Multan',     'product'=>'Hand Sanitizer 500ml',        'sku'=>'PPE-002','qty'=>'3','unit_price'=>'550','cost_price'=>'780','weight_kg'=>'1.50','cod_amount'=>'',         'status'=>'Prepaid',   'courier'=>'Leopard','delivery_date'=>'2026-07-19','returned_by'=>''],
        ['tracking_no'=>'LP0012837100','order_ref'=>'ORD-9790','date'=>'2026-07-17','customer'=>'Hassan Butt',  'city'=>'Karachi',    'product'=>'Pulse Oximeter',              'sku'=>'DEV-005','qty'=>'1','unit_price'=>'3500','cost_price'=>'1900','weight_kg'=>'0.40','cod_amount'=>'PKR 3500','status'=>'Pending',   'courier'=>'Leopard','delivery_date'=>'',          'returned_by'=>''],
        ['tracking_no'=>'LP0012834200','order_ref'=>'ORD-9785','date'=>'2026-07-16','customer'=>'Zainab Noor',  'city'=>'Lahore',     'product'=>'Omega-3 Fish Oil (60 Caps)', 'sku'=>'SUP-002','qty'=>'2','unit_price'=>'1200','cost_price'=>'1240','weight_kg'=>'0.80','cod_amount'=>'PKR 2400','status'=>'Returned',  'courier'=>'Leopard','delivery_date'=>'',          'returned_by'=>'Leopard'],
    ];
}

// ─────────────────────────────────────────────
//  EXCEL IMPORT HELPER  (for orders.php upload)
// ─────────────────────────────────────────────

/**
 * Try to import rows from an uploaded Excel file, mapping headers flexibly.
 * Returns the parsed rows or empty array on failure.
 */
function importOrdersFromUpload(string $tmpPath): array
{
    $expectedKeys = ['tracking_no','order_ref','date','customer','city','product','qty','weight_kg','cod_amount','status','courier','delivery_date','returned_by'];

    $aliasMap = [
        'tracking_no'    => 'tracking_no', 'tracking'   => 'tracking_no', 'tracking_number' => 'tracking_no',
        'order_ref'      => 'order_ref',   'order_id'   => 'order_ref',   'order'           => 'order_ref',
        'date'           => 'date',        'order_date' => 'date',
        'customer'       => 'customer',    'customer_name' => 'customer',  'name'            => 'customer',
        'city'           => 'city',
        'product'        => 'product',     'product_name'  => 'product',   'item'            => 'product',
        'qty'            => 'qty',         'quantity'   => 'qty',
        'weight'         => 'weight_kg',   'weight_kg'  => 'weight_kg',
        'cod_amount'     => 'cod_amount',  'amount'     => 'cod_amount',   'cod'             => 'cod_amount',
        'status'         => 'status',
        'courier'        => 'courier',     'courier_name' => 'courier',
        'delivery_date'  => 'delivery_date','delivery'  => 'delivery_date',
        'returned_by'    => 'returned_by', 'return_type'=> 'returned_by',
    ];

    try {
        $spreadsheet = IOFactory::load($tmpPath);
        $sheet       = $spreadsheet->getActiveSheet();
        $headers     = [];
        $rows        = [];

        foreach ($sheet->getRowIterator() as $row) {
            $cells = [];
            $ci    = $row->getCellIterator();
            $ci->setIterateOnlyExistingCells(false);
            foreach ($ci as $cell) {
                $cells[] = $cell->getCalculatedValue();
            }

            if (empty($headers)) {
                $nonEmpty = array_filter($cells, fn($v) => $v !== null && $v !== '');
                if (empty($nonEmpty)) continue;
                foreach ($cells as $v) {
                    $k          = normaliseKey((string) $v);
                    $headers[]  = $aliasMap[$k] ?? $k;
                }
                continue;
            }

            if (empty(array_filter($cells, fn($v) => $v !== null && $v !== ''))) continue;

            $record = [];
            foreach ($headers as $ci => $key) {
                $record[$key] = isset($cells[$ci]) ? trim((string) $cells[$ci]) : '';
            }
            $rows[] = $record;
        }

        return $rows;
    } catch (Throwable $e) {
        error_log('importOrdersFromUpload error: ' . $e->getMessage());
        return [];
    }
}

// ─────────────────────────────────────────────
//  FILTER HELPERS
// ─────────────────────────────────────────────

function statusMatches(string $rowStatus, string $filterStatus): bool
{
    if ($filterStatus === '' || strtolower($filterStatus) === 'all status') return true;
    return strtolower(trim($rowStatus)) === strtolower(trim($filterStatus));
}

function cityMatches(string $rowCity, string $filterCity): bool
{
    if ($filterCity === '' || strtolower($filterCity) === 'all cities') return true;
    return strtolower(trim($rowCity)) === strtolower(trim($filterCity));
}

function dateInRange(string $rowDate, string $from, string $to): bool
{
    if ($from !== '' && $rowDate < $from) return false;
    if ($to   !== '' && $rowDate > $to)   return false;
    return true;
}

// ─────────────────────────────────────────────
//  CALCULATION HELPERS
// ─────────────────────────────────────────────

function parseCurrencyValue(string $value): float
{
    if ($value === '') return 0.0;
    $clean = str_replace(['PKR', 'Rs.', 'Rs', ',', ' '], '', $value);
    return is_numeric($clean) ? (float) $clean : 0.0;
}

function formatCurrency(float $value, string $prefix = 'PKR '): string
{
    return $prefix . number_format($value, 0);
}

function pct(float $part, float $total, int $decimals = 1): float
{
    return $total > 0 ? round(($part / $total) * 100, $decimals) : 0.0;
}

// ─────────────────────────────────────────────
//  ORDER METRIC AGGREGATION
// ─────────────────────────────────────────────
function getOrderMetrics(array $rows): array
{
    $total       = count($rows);
    $delivered   = 0; $returned = 0; $pending = 0;
    $inTransit   = 0; $cancelled = 0; $prepaid = 0;
    $codDeclared = 0.0;

    foreach ($rows as $r) {
        $s = strtolower(trim($r['status'] ?? ''));
        switch ($s) {
            case 'delivered':  $delivered++;  break;
            case 'returned':   $returned++;   break;
            case 'pending':    $pending++;    break;
            case 'in transit': $inTransit++;  break;
            case 'cancelled':  $cancelled++;  break;
            case 'prepaid':    $prepaid++;    break;
        }
        $codDeclared += parseCurrencyValue($r['cod_amount'] ?? '');
    }

    return [
        'total_orders'    => $total,
        'delivered_count' => $delivered,
        'returned_count'  => $returned,
        'pending_count'   => $pending,
        'in_transit_count'=> $inTransit,
        'cancelled_count' => $cancelled,
        'prepaid_count'   => $prepaid,
        'cod_declared'    => $codDeclared,
        'delivery_rate'   => pct($delivered, $total),
        'return_rate'     => pct($returned, $total),
    ];
}

function getCityCounts(array $rows): array
{
    $counts = [];
    foreach ($rows as $r) {
        $city = trim($r['city'] ?? '') ?: 'Unknown';
        $counts[$city] = ($counts[$city] ?? 0) + 1;
    }
    arsort($counts);
    return $counts;
}

function getStatusBreakdown(array $rows): array
{
    $breakdown = [];
    foreach ($rows as $r) {
        $s = trim($r['status'] ?? '') ?: 'Unknown';
        $breakdown[$s] = ($breakdown[$s] ?? 0) + 1;
    }
    arsort($breakdown);
    return $breakdown;
}

function getMonthlyTrend(array $rows, string $dateKey = 'date'): array
{
    $trend = [];
    foreach ($rows as $r) {
        $month = substr($r[$dateKey] ?? '', 0, 7);
        if ($month === '') continue;
        $trend[$month] = ($trend[$month] ?? 0) + 1;
    }
    ksort($trend);
    return $trend;
}

/**
 * Filter orders array by search, status, city, date range, and order ref.
 */
function filterOrders(
    array  $rows,
    string $search     = '',
    string $status     = '',
    string $city       = '',
    string $fromDate   = '',
    string $toDate     = '',
    string $orderRef   = ''
): array {
    return array_values(array_filter($rows, function ($r) use ($search, $status, $city, $fromDate, $toDate, $orderRef) {
        if ($search !== '') {
            $term = strtolower($search);
            $haystack = strtolower(($r['tracking_no'] ?? '') . ($r['order_ref'] ?? '') . ($r['customer'] ?? '') . ($r['product'] ?? ''));
            if (strpos($haystack, $term) === false) return false;
        }
        if (!statusMatches($r['status'] ?? '', $status))       return false;
        if (!cityMatches($r['city'] ?? '', $city))             return false;
        if (!dateInRange($r['date'] ?? '', $fromDate, $toDate)) return false;
        if ($orderRef !== '' && $r['order_ref'] !== $orderRef && $r['tracking_no'] !== $orderRef) return false;
        return true;
    }));
}

/**
 * Get unique sorted cities from a rows array.
 */
function getUniqueCities(array $rows): array
{
    $cities = array_unique(array_filter(array_map(fn($r) => trim($r['city'] ?? ''), $rows)));
    sort($cities);
    return $cities;
}

/**
 * Export any rows array to an Excel download.
 */
/**
 * Extract returned orders from the orders array (legacy helper).
 */
function getReturnRows(array $rows): array
{
    return array_values(array_filter($rows, fn($r) => strtolower(trim($r['status'] ?? '')) === 'returned'));
}

/**
 * Export any rows array to an Excel download.
 */
function exportRowsToExcel(array $rows, string $filename = 'export'): void
{
    if (empty($rows)) return;

    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $headers     = array_keys(reset($rows));
    $sheet->fromArray([$headers], null, 'A1');

    foreach ($rows as $i => $row) {
        $sheet->fromArray([array_values($row)], null, 'A' . ($i + 2));
    }

    $fname = preg_replace('/[^a-zA-Z0-9_-]/', '-', $filename) . '-' . date('Ymd-Hi') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fname . '"');
    header('Cache-Control: max-age=0');

    (new XlsxWriter($spreadsheet))->save('php://output');
    exit;
}


/**
 * Update the status of an order identified by its tracking number.
 *
 * @param string $trackingNo
 * @param string $newStatus
 * @return bool  True on success, false on failure.
 */
function updateOrderStatus($trackingNo, $newStatus) {
    $filePath = __DIR__ . '/../upload/database.xlsx';
    $tempFile = $filePath . '.tmp';

    // Check if the original exists
    if (!file_exists($filePath)) {
        return ['success' => false, 'error' => "File not found: $filePath"];
    }

    // Retry up to 5 times if the copy or rename fails (e.g., locked)
    for ($attempt = 0; $attempt < 5; $attempt++) {
        try {
            // 1️⃣ Copy the original file to a temporary file
            if (!copy($filePath, $tempFile)) {
                usleep(200000); // 0.2 sec
                continue;
            }

            // 2️⃣ Load the temporary file (no lock on original)
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFile);
            $sheet = $spreadsheet->getSheetByName('orders');
            if (!$sheet) {
                @unlink($tempFile);
                return ['success' => false, 'error' => "Sheet 'orders' not found"];
            }

            // 3️⃣ Find the row and update the cell
            $rows = $sheet->toArray();
            $header = array_shift($rows);

            $trackingCol = array_search('Tracking No', $header);
            $statusCol   = array_search('Status', $header);

            if ($trackingCol === false || $statusCol === false) {
                @unlink($tempFile);
                return ['success' => false, 'error' => "Column headers missing"];
            }

            $rowIndex = null;
            foreach ($rows as $idx => $row) {
                if (isset($row[$trackingCol]) && trim($row[$trackingCol]) === trim($trackingNo)) {
                    $rowIndex = $idx + 2;
                    break;
                }
            }

            if ($rowIndex === null) {
                @unlink($tempFile);
                return ['success' => false, 'error' => "Tracking number '$trackingNo' not found"];
            }

            // Convert column index to letter (e.g., 9 -> I)
            $colLetter = '';
            $colIndex = $statusCol + 1;
            while ($colIndex > 0) {
                $mod = ($colIndex - 1) % 26;
                $colLetter = chr(65 + $mod) . $colLetter;
                $colIndex = (int)(($colIndex - $mod) / 26);
            }
            $sheet->setCellValue($colLetter . $rowIndex, $newStatus);

            // 4️⃣ Save the temporary file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($tempFile);

            // 5️⃣ Replace the original with the temporary file (atomic rename)
            if (!rename($tempFile, $filePath)) {
                @unlink($tempFile);
                usleep(200000);
                continue;
            }

            return ['success' => true];

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            @unlink($tempFile);
            // If it's a lock error, retry
            if (strpos($e->getMessage(), 'fopen') !== false || 
                strpos($e->getMessage(), 'Resource temporarily unavailable') !== false) {
                usleep(500000); // 0.5 sec
                continue;
            }
            return ['success' => false, 'error' => "Spreadsheet error: " . $e->getMessage()];
        } catch (Exception $e) {
            @unlink($tempFile);
            return ['success' => false, 'error' => "General error: " . $e->getMessage()];
        }
    }

    // If we exit the loop, we failed after retries
    return ['success' => false, 'error' => 'File busy after 5 attempts'];
}