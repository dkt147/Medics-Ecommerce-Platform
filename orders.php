<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$excelFilePath = __DIR__ . '/upload/orders_data.xlsx';
$sampleRows = [
    ['tracking_no' => 'LP0012847263', 'order_ref' => 'ORD-9812', 'date' => '2026-07-20', 'customer' => 'Ahmed Raza', 'city' => 'Karachi', 'weight' => '1.2 kg', 'cod_amount' => 'PKR 2,500', 'status' => 'Delivered', 'delivery_date' => '2026-07-22', 'returned_by' => ''],
    ['tracking_no' => 'LP0012846901', 'order_ref' => 'ORD-9811', 'date' => '2026-07-20', 'customer' => 'Fatima Malik', 'city' => 'Lahore', 'weight' => '0.8 kg', 'cod_amount' => 'PKR 1,800', 'status' => 'Delivered', 'delivery_date' => '2026-07-21', 'returned_by' => ''],
    ['tracking_no' => 'LP0012845234', 'order_ref' => 'ORD-9808', 'date' => '2026-07-19', 'customer' => 'Usman Khan', 'city' => 'Islamabad', 'weight' => '2.5 kg', 'cod_amount' => 'PKR 5,200', 'status' => 'In Transit', 'delivery_date' => '', 'returned_by' => ''],
    ['tracking_no' => 'LP0012843190', 'order_ref' => 'ORD-9803', 'date' => '2026-07-18', 'customer' => 'Aisha Siddiqui', 'city' => 'Rawalpindi', 'weight' => '1.0 kg', 'cod_amount' => 'PKR 3,100', 'status' => 'Returned', 'delivery_date' => '', 'returned_by' => 'Leopard'],
    ['tracking_no' => 'LP0012841000', 'order_ref' => 'ORD-9799', 'date' => '2026-07-18', 'customer' => 'Bilal Hussain', 'city' => 'Faisalabad', 'weight' => '3.1 kg', 'cod_amount' => 'PKR 7,800', 'status' => 'Delivered', 'delivery_date' => '2026-07-20', 'returned_by' => ''],
    ['tracking_no' => 'LP0012839400', 'order_ref' => 'ORD-9795', 'date' => '2026-07-17', 'customer' => 'Sara Iqbal', 'city' => 'Multan', 'weight' => '0.5 kg', 'cod_amount' => '', 'status' => 'Prepaid', 'delivery_date' => '2026-07-19', 'returned_by' => ''],
    ['tracking_no' => 'LP0012837100', 'order_ref' => 'ORD-9790', 'date' => '2026-07-17', 'customer' => 'Hassan Butt', 'city' => 'Karachi', 'weight' => '1.8 kg', 'cod_amount' => 'PKR 4,400', 'status' => 'Pending', 'delivery_date' => '', 'returned_by' => ''],
    ['tracking_no' => 'LP0012834200', 'order_ref' => 'ORD-9785', 'date' => '2026-07-16', 'customer' => 'Zainab Noor', 'city' => 'Lahore', 'weight' => '0.9 kg', 'cod_amount' => 'PKR 2,200', 'status' => 'Returned', 'delivery_date' => '', 'returned_by' => 'Customer'],
];

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

function normalizeRowsFromExcel($path, $fallbackRows)
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

function exportRowsToExcel($rows)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $headers = ['Tracking No.', 'Order Ref', 'Date', 'Customer', 'City', 'Weight', 'COD Amount', 'Status', 'Delivery Date', 'Returned By'];
    $sheet->fromArray([$headers], null, 'A1');

    $rowNumber = 2;
    foreach ($rows as $row) {
        $sheet->fromArray([[
            $row['tracking_no'] ?? '',
            $row['order_ref'] ?? '',
            $row['date'] ?? '',
            $row['customer'] ?? '',
            $row['city'] ?? '',
            $row['weight'] ?? '',
            $row['cod_amount'] ?? '',
            $row['status'] ?? '',
            $row['delivery_date'] ?? '',
            $row['returned_by'] ?? '',
        ]], null, 'A' . $rowNumber);
        $rowNumber++;
    }

    $filename = 'shipment-register-' . date('Ymd-Hi') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
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

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'import') {
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            $errorCode = $_FILES['excel_file']['error'] ?? 'no_file';
            $message = 'Import failed. Please select a valid Excel file. Error code: ' . $errorCode;
        } else {
            $tmpName = $_FILES['excel_file']['tmp_name'];
            $saved = false;
            if (is_uploaded_file($tmpName)) {
                $saved = @move_uploaded_file($tmpName, $excelFilePath);
            }
            if (!$saved) {
                $saved = @copy($tmpName, $excelFilePath);
            }

            if ($saved) {
                $importedRows = normalizeRowsFromExcel($excelFilePath, []);
                $savedSize = file_exists($excelFilePath) ? filesize($excelFilePath) : 0;
                if (empty($importedRows)) {
                    $debug = $GLOBALS['excelImportDebug'];
                    $message = 'Imported file is not in the expected format. '
                        . 'Error=' . ($debug['error'] ?? 'none') . ', '
                        . 'Detected sheet=' . ($debug['sheet'] ?? 'none') . ', '
                        . 'header row=' . ($debug['header_row'] ?? 'none') . ', '
                        . 'matches=' . count(array_intersect($debug['headers'], ['tracking_no','order_ref','date','customer','city','weight','cod_amount','status','delivery_date','returned_by'])) . ', '
                        . 'data rows=' . ($debug['data_rows'] ?? 0) . ', '
                        . 'saved_size=' . $savedSize . '.';
                } else {
                    $message = 'Excel file imported successfully. The dashboard is now using the uploaded data.';
                    $rows = $importedRows;
                }
            } else {
                $message = 'Import failed while saving the uploaded file. Check upload permissions and file size.';
            }
        }
    } elseif ($_POST['action'] === 'export') {
        $rows = normalizeRowsFromExcel($excelFilePath, $sampleRows);
        exportRowsToExcel($rows);
    }
}

$rows = normalizeRowsFromExcel($excelFilePath, $sampleRows);

$searchQuery = trim($_POST['search'] ?? $_GET['search'] ?? '');
$selectedStatus = trim($_POST['status'] ?? $_GET['status'] ?? '');
$selectedCity = trim($_POST['city'] ?? $_GET['city'] ?? '');
$fromDate = trim($_POST['from_date'] ?? $_GET['from_date'] ?? '');
$toDate = trim($_POST['to_date'] ?? $_GET['to_date'] ?? '');
$selectedOrderRef = trim($_GET['order_ref'] ?? '');

$displayRows = $rows;
if ($searchQuery !== '') {
    $searchTerm = strtolower($searchQuery);
    $displayRows = array_values(array_filter($displayRows, function ($row) use ($searchTerm) {
        return stripos(strtolower((string) ($row['tracking_no'] ?? '')), $searchTerm) !== false
            || stripos(strtolower((string) ($row['order_ref'] ?? '')), $searchTerm) !== false
            || stripos(strtolower((string) ($row['customer'] ?? '')), $searchTerm) !== false;
    }));
}

if ($selectedStatus !== '' && strtolower($selectedStatus) !== 'all status') {
    $displayRows = array_values(array_filter($displayRows, function ($row) use ($selectedStatus) {
        return statusMatches($row['status'] ?? '', $selectedStatus);
    }));
}

if ($selectedCity !== '' && strtolower($selectedCity) !== 'all cities') {
    $displayRows = array_values(array_filter($displayRows, function ($row) use ($selectedCity) {
        return strtolower(trim((string) ($row['city'] ?? ''))) === strtolower($selectedCity);
    }));
}

if ($selectedOrderRef !== '') {
    $displayRows = array_values(array_filter($displayRows, function ($row) use ($selectedOrderRef) {
        $orderRef = trim((string) ($row['order_ref'] ?? ''));
        $trackingNo = trim((string) ($row['tracking_no'] ?? ''));
        return $orderRef === $selectedOrderRef || $trackingNo === $selectedOrderRef;
    }));
}

if ($fromDate !== '') {
    $displayRows = array_values(array_filter($displayRows, function ($row) use ($fromDate) {
        return ($row['date'] ?? '') >= $fromDate;
    }));
}

if ($toDate !== '') {
    $displayRows = array_values(array_filter($displayRows, function ($row) use ($toDate) {
        return ($row['date'] ?? '') <= $toDate;
    }));
}

$totalDispatched = count($displayRows);
$deliveredCount = count(array_filter($displayRows, function ($row) { return strtolower(trim($row['status'] ?? '')) === 'delivered'; }));
$inTransitCount = count(array_filter($displayRows, function ($row) { return strtolower(trim($row['status'] ?? '')) === 'in transit'; }));
$returnedCount = count(array_filter($displayRows, function ($row) { return strtolower(trim($row['status'] ?? '')) === 'returned'; }));
$pendingCount = count(array_filter($displayRows, function ($row) { return strtolower(trim($row['status'] ?? '')) === 'pending'; }));
$cancelledCount = count(array_filter($displayRows, function ($row) { return strtolower(trim($row['status'] ?? '')) === 'cancelled'; }));
$returnedByLeopard = count(array_filter($displayRows, function ($row) { return stripos(trim($row['returned_by'] ?? ''), 'leopard') !== false; }));
$returnedByCustomer = count(array_filter($displayRows, function ($row) { return stripos(trim($row['returned_by'] ?? ''), 'customer') !== false; }));

$deliveredRate = $totalDispatched > 0 ? round(($deliveredCount / $totalDispatched) * 100, 1) : 0;
$returnedRate = $totalDispatched > 0 ? round(($returnedCount / $totalDispatched) * 100, 1) : 0;

$cityCounts = [];
foreach ($displayRows as $row) {
    $city = trim($row['city'] ?? '') ?: 'Unknown';
    $cityCounts[$city] = ($cityCounts[$city] ?? 0) + 1;
}
arsort($cityCounts);
$cityChartLabels = array_keys($cityCounts);
$cityChartValues = array_values($cityCounts);

$statusBreakdown = [
    'Delivered' => $deliveredCount,
    'In Transit' => $inTransitCount,
    'Returned by Leopard' => $returnedByLeopard,
    'Returned by Customer' => $returnedByCustomer,
    'Pending' => $pendingCount,
    'Cancelled' => $cancelledCount,
];

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders &amp; Shipments</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<style>
    :root {
        --primary: #1a73e8;
        --primary-light: #e8f0fe;
        --sidebar-width: 260px;
        --header-bg: #ffffff;
        --body-bg: #f0f4f8;
        --card-bg: #ffffff;
        --text-main: #1a2940;
        --text-muted: #6b7a8d;
        --border: #e2e8f0;
        --success: #16a34a;
        --success-bg: #dcfce7;
        --danger: #dc2626;
        --danger-bg: #fee2e2;
        --warning: #d97706;
        --warning-bg: #fef3c7;
        --info: #0891b2;
        --info-bg: #e0f2fe;
        --shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        --radius: 10px;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        background: var(--body-bg);
        color: var(--text-main);
    }

    /* ── LAYOUT ── */
    .layout {
        display: flex;
        min-height: 100vh;
    }

    .main {
        margin-left: var(--sidebar-width);
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .content {
        padding: 24px 28px;
        flex: 1;
    }

    /* ── CARDS ── */
    .card {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        padding: 20px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title .ct-icon {
        font-size: 16px;
    }

    .mb20 {
        margin-bottom: 20px;
    }

    /* ── KPI GRID ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .kpi-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 18px 20px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 6px;
        position: relative;
        overflow: hidden;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .kpi-card.blue::before {
        background: var(--primary);
    }

    .kpi-card.green::before {
        background: var(--success);
    }

    .kpi-card.red::before {
        background: var(--danger);
    }

    .kpi-card.orange::before {
        background: var(--warning);
    }

    .kpi-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
    }

    .kpi-value.sm {
        font-size: 18px;
    }

    .kpi-meta {
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .kpi-up {
        color: var(--success);
    }

    .kpi-down {
        color: var(--danger);
    }

    /* ── GRID ── */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    /* ── FILTER BAR ── */
    .filter-bar {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .filter-input {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        color: var(--text-main);
    }

    .filter-input:focus {
        border-color: var(--primary);
    }

    .btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn:hover {
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
    }

    .btn-primary:hover {
        background: #1557b0;
    }

    .btn-outline {
        background: #fff;
        color: var(--text-main);
        border: 1px solid var(--border);
    }

    .btn-outline:hover {
        background: #f8fafc;
    }

    .btn-sm {
        padding: 5px 12px;
        font-size: 12px;
    }

    .alert {
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        border: 1px solid transparent;
        font-size: 13px;
    }

    .alert-success {
        background: var(--success-bg);
        color: var(--success);
        border-color: #bbf7d0;
    }

    .alert-danger {
        background: var(--danger-bg);
        color: var(--danger);
        border-color: #fecaca;
    }

    .file-picker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: #fff;
        font-size: 13px;
        color: var(--text-muted);
    }

    .file-picker input {
        display: none;
    }

    /* ── TABLES ── */
    .table-wrap {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    thead th {
        background: #f8fafc;
        padding: 10px 14px;
        text-align: left;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-main);
        font-size: 13px;
    }

    tbody tr:hover {
        background: #f8fafc;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    /* ── BADGES ── */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-green {
        background: var(--success-bg);
        color: var(--success);
    }

    .badge-red {
        background: var(--danger-bg);
        color: var(--danger);
    }

    .badge-yellow {
        background: var(--warning-bg);
        color: var(--warning);
    }

    .badge-blue {
        background: var(--primary-light);
        color: var(--primary);
    }

    .badge-grey {
        background: #f1f5f9;
        color: #64748b;
    }

    /* ── CHART CONTAINER ── */
    .chart-box {
        position: relative;
        height: 240px;
    }

    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #c8d4e0;
        border-radius: 3px;
    }

    @media (max-width: 1200px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

<body>
    <div class="layout">

        <?php include 'includes/sidebar.php'; ?>

        <div class="main">

            <?php include 'includes/navbar.php'; ?>

            <div class="content">
                <div class="page active" id="page-orders">

                    <!-- KPI Row -->
                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Total Dispatched</div>
                            <div class="kpi-value"><?php echo number_format($totalDispatched); ?></div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Delivered</div>
                            <div class="kpi-value"><?php echo number_format($deliveredCount); ?></div>
                            <div class="kpi-meta kpi-up"><?php echo $deliveredRate; ?>% rate</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Returned</div>
                            <div class="kpi-value"><?php echo number_format($returnedCount); ?></div>
                            <div class="kpi-meta kpi-down"><?php echo $returnedRate; ?>% rate</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">In Transit</div>
                            <div class="kpi-value"><?php echo number_format($inTransitCount); ?></div>
                        </div>
                    </div>

                    <!-- Shipment Register -->
                    <div class="card mb20">
                        <div class="card-title"><span class="ct-icon">📦</span> Shipment Register</div>
                        <?php if (!empty($message)) : ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data" class="filter-bar">
                            <input type="text" class="filter-input" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="🔍  Search tracking / order ID..." style="width:240px" />
                            <select class="filter-input" name="status">
                                <option value="All Status" <?php echo $selectedStatus === '' || strtolower($selectedStatus) === 'all status' ? 'selected' : ''; ?>>All Status</option>
                                <option value="Delivered" <?php echo strtolower($selectedStatus) === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="In Transit" <?php echo strtolower($selectedStatus) === 'in transit' ? 'selected' : ''; ?>>In Transit</option>
                                <option value="Returned" <?php echo strtolower($selectedStatus) === 'returned' ? 'selected' : ''; ?>>Returned</option>
                                <option value="Pending" <?php echo strtolower($selectedStatus) === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Cancelled" <?php echo strtolower($selectedStatus) === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <select class="filter-input" name="city">
                                <option value="All Cities" <?php echo $selectedCity === '' || strtolower($selectedCity) === 'all cities' ? 'selected' : ''; ?>>All Cities</option>
                                <option value="Karachi" <?php echo strtolower($selectedCity) === 'karachi' ? 'selected' : ''; ?>>Karachi</option>
                                <option value="Lahore" <?php echo strtolower($selectedCity) === 'lahore' ? 'selected' : ''; ?>>Lahore</option>
                                <option value="Islamabad" <?php echo strtolower($selectedCity) === 'islamabad' ? 'selected' : ''; ?>>Islamabad</option>
                                <option value="Faisalabad" <?php echo strtolower($selectedCity) === 'faisalabad' ? 'selected' : ''; ?>>Faisalabad</option>
                                <option value="Rawalpindi" <?php echo strtolower($selectedCity) === 'rawalpindi' ? 'selected' : ''; ?>>Rawalpindi</option>
                            </select>
                            <input type="date" class="filter-input" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" />
                            <input type="date" class="filter-input" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" />
                            <button type="submit" class="btn btn-outline">Apply Filters</button>
                            <a href="orders.php" class="btn btn-outline">Reset</a>
                            <label class="file-picker">
                                <span>Choose Excel</span>
                                <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" />
                            </label>
                            <button type="submit" name="action" value="import" class="btn btn-primary">Import Excel</button>
                            <button type="submit" name="action" value="export" class="btn btn-outline" style="margin-left:auto">⬇️ Export</button>
                        </form>
                        <?php if (empty($displayRows)) : ?>
                            <div class="alert alert-danger">No shipment records match the selected filters.</div>
                        <?php else : ?>
                            <div class="alert alert-success">Showing <?php echo number_format(count($displayRows)); ?> of <?php echo number_format(count($rows)); ?> shipments.</div>
                        <?php endif; ?>
                        <div class="table-wrap">
                            <?php if (!empty($displayRows)) : ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tracking No.</th>
                                        <th>Order Ref</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>City</th>
                                        <th>Weight</th>
                                        <th>COD Amount</th>
                                        <th>Status</th>
                                        <th>Returned By</th>
                                        <th>Delivery Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($displayRows as $row) : ?>
                                        <tr>
                                            <td><b><?php echo htmlspecialchars($row['tracking_no'] ?? ''); ?></b></td>
                                            <td><?php echo htmlspecialchars($row['order_ref'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['date'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['customer'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['city'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['weight'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['cod_amount'] ?? '—'); ?></td>
                                            <td>
                                                <?php
                                                $status = trim($row['status'] ?? '');
                                                $badgeClass = 'badge-grey';
                                                if (strtolower($status) === 'delivered') {
                                                    $badgeClass = 'badge-green';
                                                } elseif (strtolower($status) === 'in transit') {
                                                    $badgeClass = 'badge-yellow';
                                                } elseif (strtolower($status) === 'returned') {
                                                    $badgeClass = 'badge-red';
                                                } elseif (strtolower($status) === 'pending') {
                                                    $badgeClass = 'badge-grey';
                                                } elseif (strtolower($status) === 'prepaid') {
                                                    $badgeClass = 'badge-blue';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status ?: '—'); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['returned_by'] ?: '—'); ?></td>
                                            <td><?php echo htmlspecialchars($row['delivery_date'] ?: '—'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">🏙️</span> Orders by City</div>
                            <div class="chart-box"><canvas id="cityChart"></canvas></div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📊</span> Status Breakdown</div>
                            <div class="chart-box"><canvas id="statusChart"></canvas></div>
                        </div>
                    </div>

                </div><!-- /page-orders -->
            </div><!-- /content -->

            <?php include 'includes/footer.php'; ?>

        </div><!-- /main -->
    </div><!-- /layout -->
</body>
<script>
    // ── Page title/breadcrumb (navbar shared hai, is liye yahan override kar rahe hain) ──
    document.getElementById('page-title').textContent = 'Orders & Shipments';
    document.getElementById('page-bread').textContent = 'Modules / Orders & Shipments';

    const SUCCESS = '#16a34a';
    const DANGER = '#dc2626';
    const WARNING = '#d97706';
    const INFO = '#0891b2';
    const GREY = '#94a3b8';
    const palette = ['#1a73e8', '#16a34a', '#d97706', '#dc2626', '#0891b2', '#7c3aed', '#db2777'];

    Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7a8d';

    const cityLabels = <?php echo json_encode($cityChartLabels, JSON_UNESCAPED_UNICODE); ?>;
    const cityValues = <?php echo json_encode($cityChartValues, JSON_UNESCAPED_UNICODE); ?>;
    const statusLabels = <?php echo json_encode(array_keys($statusBreakdown), JSON_UNESCAPED_UNICODE); ?>;
    const statusValues = <?php echo json_encode(array_values($statusBreakdown), JSON_UNESCAPED_UNICODE); ?>;

    new Chart(document.getElementById('cityChart'), {
        type: 'bar',
        data: {
            labels: cityLabels.length ? cityLabels : ['No Data'],
            datasets: [{
                label: 'Orders',
                data: cityValues.length ? cityValues : [0],
                backgroundColor: cityLabels.length ? palette.slice(0, cityLabels.length) : [GREY],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        color: '#f1f5f9'
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: [SUCCESS, INFO, DANGER, WARNING, GREY, '#7c3aed'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        font: {
                            size: 11
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
</script>

</html>