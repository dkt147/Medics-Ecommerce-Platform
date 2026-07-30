<?php
/**
 * Medics E-Commerce Platform — Database Seeder
 * -----------------------------------------------
 * Run ONCE from your browser or CLI:
 *   Browser : http://localhost/Medics-Ecommerce-Platform/seed_database.php
 *   CLI     : php seed_database.php
 *
 * Generates: upload/medics_database.xlsx
 * Tabs: orders | returns | charges | cod | disputes |
 *        expenses | inventory | revenue | pl | tax | balance_sheet | reports
 *
 * Covers: March 2026 – July 2026 (~275 orders)
 */

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// ─────────────────────────────────────────────
//  SEEDER CONFIG
// ─────────────────────────────────────────────
$OUTPUT_PATH = __DIR__ . '/upload/medics_database.xlsx';

// ─────────────────────────────────────────────
//  MASTER REFERENCE DATA
// ─────────────────────────────────────────────
$CUSTOMERS = [
    'Ahmed Raza','Fatima Malik','Usman Khan','Aisha Siddiqui','Bilal Hussain',
    'Sara Iqbal','Hassan Butt','Zainab Noor','Mariam Khan','Sana Butt',
    'Imran Qureshi','Hira Aziz','Adnan Malik','Amna Sheikh','Rabia Hussain',
    'Farhan Siddiqui','Kiran Malik','Kashif Ali','Mehak Farooq','Tariq Mahmood',
    'Anum Riaz','Rehan Mirza','Shazia Pervez','Kamran Sheikh','Nadia Qureshi',
    'Danish Nawaz','Bushra Tariq','Faisal Rehman','Layla Ahmed','Waqas Ahmed',
    'Noor ul Ain','Sajjad Haider','Tooba Hassan','Zubair Akhtar','Madiha Nawaz',
    'Nadeem Baig','Saima Akhtar','Shoaib Chaudhry','Lubna Tariq','Ali Hassan',
];

$CITIES        = ['Karachi','Lahore','Islamabad','Rawalpindi','Faisalabad','Multan','Peshawar','Hyderabad','Sialkot','Quetta'];
$CITY_WEIGHTS  = [25, 20, 12, 10, 8, 7, 6, 5, 4, 3];

// [name, unit_price_PKR, weight_kg, category, sku, cost_price]
$PRODUCTS = [
    ['Panadol Extra Tablets (20s)',      450,  0.30, 'OTC Medicine',   'MED-001', 250],
    ['ORS Sachets (Pack of 10)',         350,  0.25, 'OTC Medicine',   'MED-002', 180],
    ['Vitamin C 1000mg (30 Tabs)',       800,  0.20, 'Supplements',    'SUP-001', 420],
    ['Cetaphil Gentle Cleanser 250ml',  1800,  0.35, 'Skincare',       'SKN-001', 950],
    ['Digital BP Monitor',              6500,  1.50, 'Medical Device', 'DEV-001',3800],
    ['Glucometer Kit (25 Strips)',      4200,  1.20, 'Medical Device', 'DEV-002',2400],
    ['Surgical Masks (Pack of 50)',      650,  0.40, 'PPE',            'PPE-001', 320],
    ['Hand Sanitizer 500ml',             550,  0.50, 'PPE',            'PPE-002', 260],
    ['Digital Thermometer',            1500,  0.30, 'Medical Device', 'DEV-003', 800],
    ['Antacid Tablets (30s)',            380,  0.25, 'OTC Medicine',   'MED-003', 190],
    ['Omega-3 Fish Oil (60 Caps)',      1200,  0.40, 'Supplements',    'SUP-002', 620],
    ['Multivitamin Tablets (30s)',       950,  0.30, 'Supplements',    'SUP-003', 480],
    ['Dettol Antiseptic Liquid 100ml',  480,  0.20, 'Antiseptic',     'ANT-001', 220],
    ['Nebulizer Machine',              8500,  2.00, 'Medical Device', 'DEV-004',5000],
    ['Calcium + D3 Tablets (60s)',      1100,  0.35, 'Supplements',    'SUP-004', 560],
    ['Zinc Tablets (30s)',               750,  0.20, 'Supplements',    'SUP-005', 370],
    ['Eye Drops 10ml',                   320,  0.10, 'OTC Medicine',   'MED-004', 150],
    ['Knee Support Brace',             2200,  0.80, 'Orthopaedic',    'ORT-001',1200],
    ['Pulse Oximeter',                 3500,  0.40, 'Medical Device', 'DEV-005',1900],
    ['Protein Powder Vanilla 1kg',     5500,  2.50, 'Supplements',    'SUP-006',3000],
];

$COURIERS       = ['Leopard','TCS','M&P'];
$COURIER_WEIGHTS = [70, 20, 10];

$EXPENSE_CATEGORIES = ['Marketing','Operations','Salaries','Rent','Utilities','Packaging','Software','Miscellaneous'];
$EXPENSE_VENDORS    = ['Facebook Ads','Google Ads','Office Depot','HR Payroll','KESC','Allied Bank','Shopify','Misc Vendor'];

// ─────────────────────────────────────────────
//  HELPER FUNCTIONS
// ─────────────────────────────────────────────
function weightedRandom(array $items, array $weights): mixed
{
    $total = array_sum($weights);
    $r = mt_rand() / mt_getrandmax() * $total;
    $cum = 0;
    foreach ($items as $i => $item) {
        $cum += $weights[$i];
        if ($r <= $cum) return $item;
    }
    return $items[count($items) - 1];
}

function randomDate(string $from, string $to): string
{
    $start = strtotime($from);
    $end   = strtotime($to);
    return date('Y-m-d', mt_rand($start, $end));
}

function addDays(string $date, int $days): string
{
    return date('Y-m-d', strtotime($date) + $days * 86400);
}

function fmtPKR(float $v): string
{
    return 'PKR ' . number_format($v, 0);
}

function trackingNo(string $courier, int $idx): string
{
    return match($courier) {
        'TCS'  => 'TCS' . str_pad(8800000 + $idx, 9, '0', STR_PAD_LEFT),
        'M&P'  => 'MP'  . str_pad(5500000 + $idx, 8, '0', STR_PAD_LEFT),
        default=> 'LP'  . str_pad(1012800000 + $idx, 10, '0', STR_PAD_LEFT),
    };
}

// ─────────────────────────────────────────────
//  GENERATE ORDERS  (~275 records, Mar–Jul 2026)
// ─────────────────────────────────────────────
$orders = [];
$orderNum     = 9600;
$trackingIdx  = 0;

$monthRanges = [
    ['2026-03-01','2026-03-31', 48],
    ['2026-04-01','2026-04-30', 55],
    ['2026-05-01','2026-05-31', 62],
    ['2026-06-01','2026-06-30', 68],
    ['2026-07-01','2026-07-30', 50],
];

foreach ($monthRanges as [$from, $to, $count]) {
    for ($i = 0; $i < $count; $i++) {
        $orderNum++;
        $trackingIdx++;

        $product  = $PRODUCTS[array_rand($PRODUCTS)];
        [$pName, $unitPrice, $weight, $category, $sku, $costPrice] = $product;
        $qty       = mt_rand(1, 3);
        $total     = $unitPrice * $qty;
        $totalCost = $costPrice * $qty;
        $totalWgt  = round($weight * $qty, 2);

        $city     = weightedRandom($CITIES, $CITY_WEIGHTS);
        $customer = $CUSTOMERS[array_rand($CUSTOMERS)];
        $courier  = weightedRandom($COURIERS, $COURIER_WEIGHTS);
        $tNo      = trackingNo($courier, $trackingIdx);
        $orderDate= randomDate($from, $to);

        // Status distribution
        $roll = mt_rand(1, 100);
        if ($roll <= 63) {
            $status       = 'Delivered';
            $deliveryDate = addDays($orderDate, mt_rand(1, 4));
            $returnedBy   = '';
        } elseif ($roll <= 75) {
            $status       = 'Returned';
            $deliveryDate = '';
            $returnedBy   = weightedRandom(['Customer','Customer','Leopard'], [1, 1, 1]);
        } elseif ($roll <= 83) {
            $status       = 'In Transit';
            $deliveryDate = '';
            $returnedBy   = '';
        } elseif ($roll <= 93) {
            $status       = 'Pending';
            $deliveryDate = '';
            $returnedBy   = '';
        } else {
            $status       = 'Cancelled';
            $deliveryDate = '';
            $returnedBy   = '';
        }

        $isPrepaid = mt_rand(1, 10) === 1;
        if ($isPrepaid) {
            $codAmount = '';
            if ($status === 'Delivered') $status = 'Prepaid';
        } else {
            $codAmount = fmtPKR($total);
        }

        $orders[] = [
            'tracking_no'   => $tNo,
            'order_ref'     => 'ORD-' . $orderNum,
            'date'          => $orderDate,
            'customer'      => $customer,
            'city'          => $city,
            'product'       => $pName,
            'sku'           => $sku,
            'qty'           => $qty,
            'unit_price'    => $unitPrice,
            'cost_price'    => $totalCost,
            'weight_kg'     => $totalWgt,
            'cod_amount'    => $codAmount,
            'status'        => $status,
            'courier'       => $courier,
            'delivery_date' => $deliveryDate,
            'returned_by'   => $returnedBy,
        ];
    }
}

usort($orders, fn($a, $b) => strcmp($b['date'], $a['date']));

// ─────────────────────────────────────────────
//  DERIVE RETURNS
// ─────────────────────────────────────────────
$returnRows = [];
$returnIdx  = 1;
$returnReasons = ['Customer Refused','Wrong Address','Item Damaged in Transit','Duplicate Order','Customer Not Available','Product Defect','Wrong Item Sent'];
$returnReasonWeights = [35, 20, 15, 10, 10, 6, 4];

foreach ($orders as $o) {
    if ($o['status'] !== 'Returned') continue;
    $reason       = weightedRandom($returnReasons, $returnReasonWeights);
    $initiatedDate= addDays($o['date'], mt_rand(1, 3));
    $receivedDate = addDays($initiatedDate, mt_rand(2, 5));
    $condition    = weightedRandom(['Good','Damaged','Opened'], [50, 30, 20]);
    $codCollected = $o['cod_amount'] !== '' ? 'No' : 'N/A';
    $lossValue    = fmtPKR((float) str_replace(['PKR ', ','], '', $o['cod_amount'] ?: '0') * 0.15 + 150);

    $returnRows[] = [
        'return_id'      => 'RET-' . str_pad($returnIdx++, 4, '0', STR_PAD_LEFT),
        'tracking_no'    => $o['tracking_no'],
        'order_ref'      => $o['order_ref'],
        'customer'       => $o['customer'],
        'city'           => $o['city'],
        'product'        => $o['product'],
        'return_reason'  => $reason,
        'returned_by'    => $o['returned_by'],
        'initiated_date' => $initiatedDate,
        'received_date'  => $receivedDate,
        'condition'      => $condition,
        'cod_collected'  => $codCollected,
        'loss_value'     => $lossValue,
        'courier'        => $o['courier'],
    ];
}

// ─────────────────────────────────────────────
//  GENERATE COURIER CHARGES (monthly invoices)
// ─────────────────────────────────────────────
$chargesRows = [];
$invoiceIdx  = 1;

$monthlyChargeData = [
    ['2026-03', 'Mar 2026', [48, 120, 0]],
    ['2026-04', 'Apr 2026', [55, 138, 1]],
    ['2026-05', 'May 2026', [62, 154, 1]],
    ['2026-06', 'Jun 2026', [68, 172, 2]],
    ['2026-07', 'Jul 2026', [50, 128, 1]],
];

foreach ($monthlyChargeData as [$ym, $label, [$shipments, $perShipmentRate, $extraInvoices]]) {
    // Main monthly invoice
    $billed   = $shipments * $perShipmentRate + mt_rand(-500, 2000);
    $expected = $shipments * $perShipmentRate;
    $variance = $billed - $expected;
    $status   = $variance > 1000 ? 'Disputed' : ($variance > 200 ? 'Under Review' : 'Reconciled');

    $chargesRows[] = [
        'invoice_no'      => 'LC-' . $ym . '-' . str_pad($invoiceIdx++, 2, '0', STR_PAD_LEFT),
        'date'            => $ym . '-' . (14 + mt_rand(0, 3)),
        'courier'         => 'Leopard',
        'invoice_period'  => $label,
        'shipments'       => $shipments,
        'billed_amount'   => fmtPKR($billed),
        'expected_amount' => fmtPKR($expected),
        'variance'        => fmtPKR($variance),
        'status'          => $status,
        'notes'           => $variance > 1000 ? 'Weight discrepancy reported' : '',
    ];
}

// ─────────────────────────────────────────────
//  GENERATE COD BATCHES (bi-weekly)
// ─────────────────────────────────────────────
$codRows  = [];
$batchIdx = 1;
$codDates = [
    '2026-03-07','2026-03-14','2026-03-21','2026-03-28',
    '2026-04-04','2026-04-11','2026-04-18','2026-04-25',
    '2026-05-02','2026-05-09','2026-05-16','2026-05-23','2026-05-30',
    '2026-06-06','2026-06-13','2026-06-20','2026-06-27',
    '2026-07-04','2026-07-11','2026-07-18','2026-07-25',
];

foreach ($codDates as $bDate) {
    $collected = mt_rand(280000, 580000);
    $isLate    = strtotime($bDate) > strtotime('2026-07-10');
    $remitted  = $isLate ? 0 : (int)($collected * (mt_rand(85, 98) / 100));
    $pending   = $collected - $remitted;
    $status    = $isLate ? 'Pending Remittance' : ($pending < 5000 ? 'Fully Remitted' : 'Partially Remitted');
    $remitDate = $isLate ? '' : addDays($bDate, mt_rand(3, 7));

    $codRows[] = [
        'batch_id'         => 'COD-' . str_pad($batchIdx++, 3, '0', STR_PAD_LEFT),
        'date'             => $bDate,
        'courier'          => 'Leopard',
        'total_collected'  => fmtPKR($collected),
        'remitted_amount'  => fmtPKR($remitted),
        'pending_amount'   => fmtPKR($pending),
        'status'           => $status,
        'remittance_date'  => $remitDate,
        'batch_shipments'  => mt_rand(55, 120),
    ];
}

// ─────────────────────────────────────────────
//  GENERATE DISPUTES
// ─────────────────────────────────────────────
$disputeRows = [];
$disputeIdx  = 1;
$disputeTypes = ['Weight Discrepancy','Overcharge','Return Not Received','COD Shortage','Damage Claim','Service Failure'];
$disputeWeights = [35, 25, 15, 10, 10, 5];

foreach ($chargesRows as $charge) {
    if ($charge['status'] === 'Disputed') {
        $type    = weightedRandom($disputeTypes, $disputeWeights);
        $amount  = (float) str_replace(['PKR ', ','], '', $charge['variance']);
        $opened  = $charge['date'];
        $resolved= $amount < 5000 ? addDays($opened, mt_rand(5, 14)) : '';
        $dStatus = $resolved ? 'Resolved' : 'Open';

        $disputeRows[] = [
            'dispute_id'       => 'DIS-' . str_pad($disputeIdx++, 3, '0', STR_PAD_LEFT),
            'date_opened'      => $opened,
            'invoice_no'       => $charge['invoice_no'],
            'courier'          => $charge['courier'],
            'type'             => $type,
            'description'      => 'Variance of ' . $charge['variance'] . ' on invoice ' . $charge['invoice_no'],
            'disputed_amount'  => $charge['variance'],
            'status'           => $dStatus,
            'resolution_date'  => $resolved,
            'resolution_notes' => $resolved ? 'Courier accepted partial credit note.' : 'Awaiting courier response.',
        ];
    }
}

// Add extra random disputes
$extraDisputeOrders = array_filter($orders, fn($o) => $o['status'] === 'Returned' && $o['returned_by'] === 'Leopard');
foreach (array_slice(array_values($extraDisputeOrders), 0, 6) as $o) {
    $disputeRows[] = [
        'dispute_id'       => 'DIS-' . str_pad($disputeIdx++, 3, '0', STR_PAD_LEFT),
        'date_opened'      => addDays($o['date'], mt_rand(1, 5)),
        'invoice_no'       => 'LC-' . substr($o['date'], 0, 7) . '-01',
        'courier'          => $o['courier'],
        'type'             => 'Return Not Received',
        'description'      => 'Return shipment ' . $o['tracking_no'] . ' not received back at warehouse.',
        'disputed_amount'  => fmtPKR(mt_rand(500, 4000)),
        'status'           => weightedRandom(['Open','Resolved'], [40, 60]),
        'resolution_date'  => '',
        'resolution_notes' => 'Tracking investigation in progress.',
    ];
}

usort($disputeRows, fn($a, $b) => strcmp($b['date_opened'], $a['date_opened']));

// ─────────────────────────────────────────────
//  GENERATE EXPENSES
// ─────────────────────────────────────────────
$expenseRows = [];
$expenseIdx  = 1;

$monthlyExpenses = [
    ['2026-03', [
        ['Marketing',   'Facebook Ads — March Campaign',    42000, 'Facebook'],
        ['Marketing',   'Google Shopping Ads',              18500, 'Google'],
        ['Salaries',    'Staff Salaries — March',          120000, 'Internal Payroll'],
        ['Rent',        'Office Rent — March',              35000, 'Landlord'],
        ['Utilities',   'Electricity & Internet',            8200, 'KESC / ISP'],
        ['Packaging',   'Packaging Material Purchase',      12000, 'PackRight Supplies'],
        ['Software',    'Shopify Monthly Plan',              5500, 'Shopify'],
        ['Operations',  'Petty Cash & Misc Ops',             3800, 'Various'],
    ]],
    ['2026-04', [
        ['Marketing',   'Facebook Ads — April Campaign',    52000, 'Facebook'],
        ['Marketing',   'Influencer Partnership — Health',  28000, 'Influencer Agency'],
        ['Salaries',    'Staff Salaries — April',          120000, 'Internal Payroll'],
        ['Rent',        'Office Rent — April',              35000, 'Landlord'],
        ['Utilities',   'Electricity & Internet',            9100, 'KESC / ISP'],
        ['Packaging',   'Packaging Material Purchase',      14500, 'PackRight Supplies'],
        ['Software',    'Shopify + Analytics Tools',         8200, 'Shopify / GA360'],
        ['Operations',  'Petty Cash & Misc Ops',             4200, 'Various'],
    ]],
    ['2026-05', [
        ['Marketing',   'Facebook & Instagram Ads',         61000, 'Facebook'],
        ['Marketing',   'Google Ads — Brand Campaign',      22000, 'Google'],
        ['Salaries',    'Staff Salaries — May',            130000, 'Internal Payroll'],
        ['Rent',        'Office Rent — May',                35000, 'Landlord'],
        ['Utilities',   'Electricity & Internet',            9800, 'KESC / ISP'],
        ['Packaging',   'Packaging Material Purchase',      16000, 'PackRight Supplies'],
        ['Software',    'Shopify + CRM License',             9500, 'Shopify / HubSpot'],
        ['Operations',  'Delivery Bike Maintenance',         6500, 'Mechanic'],
        ['Operations',  'Petty Cash & Misc Ops',             3500, 'Various'],
    ]],
    ['2026-06', [
        ['Marketing',   'Eid Campaign — Facebook & Insta',  85000, 'Facebook'],
        ['Marketing',   'Google Shopping Ads',              34000, 'Google'],
        ['Marketing',   'Eid Influencer Promotions',        45000, 'Influencer Agency'],
        ['Salaries',    'Staff Salaries — June',           130000, 'Internal Payroll'],
        ['Rent',        'Office Rent — June',               35000, 'Landlord'],
        ['Utilities',   'Electricity & Internet',           10500, 'KESC / ISP'],
        ['Packaging',   'Eid Packaging & Gift Wrapping',    22000, 'PackRight Supplies'],
        ['Software',    'Shopify + Automation Tools',        9500, 'Various'],
        ['Operations',  'Petty Cash & Misc Ops',             5200, 'Various'],
    ]],
    ['2026-07', [
        ['Marketing',   'Facebook Ads — July',              48000, 'Facebook'],
        ['Marketing',   'Google Ads',                       19000, 'Google'],
        ['Salaries',    'Staff Salaries — July',           130000, 'Internal Payroll'],
        ['Rent',        'Office Rent — July',               35000, 'Landlord'],
        ['Utilities',   'Electricity & Internet',            9200, 'KESC / ISP'],
        ['Packaging',   'Packaging Material Purchase',      13000, 'PackRight Supplies'],
        ['Software',    'Shopify Monthly Plan',              5500, 'Shopify'],
        ['Operations',  'Petty Cash & Misc Ops',             4100, 'Various'],
    ]],
];

foreach ($monthlyExpenses as [$ym, $items]) {
    foreach ($items as [$cat, $desc, $amount, $vendor]) {
        $day = str_pad(mt_rand(1, 25), 2, '0', STR_PAD_LEFT);
        $expenseRows[] = [
            'expense_id'     => 'EXP-' . str_pad($expenseIdx++, 4, '0', STR_PAD_LEFT),
            'date'           => $ym . '-' . $day,
            'month'          => $ym,
            'category'       => $cat,
            'description'    => $desc,
            'amount'         => fmtPKR($amount),
            'amount_raw'     => $amount,
            'vendor'         => $vendor,
            'payment_method' => weightedRandom(['Bank Transfer','Cash','Credit Card'], [60, 25, 15]),
            'status'         => 'Paid',
        ];
    }
}

usort($expenseRows, fn($a, $b) => strcmp($b['date'], $a['date']));

// ─────────────────────────────────────────────
//  INVENTORY
// ─────────────────────────────────────────────
$inventoryRows = [];
foreach ($PRODUCTS as [$pName, $unitPrice, $weight, $category, $sku, $costPrice]) {
    $stock    = mt_rand(20, 250);
    $reorder  = mt_rand(15, 40);
    $inventoryRows[] = [
        'product_id'     => $sku,
        'sku'            => $sku,
        'product_name'   => $pName,
        'category'       => $category,
        'unit_price'     => fmtPKR($unitPrice),
        'cost_price'     => fmtPKR($costPrice),
        'margin_pct'     => round((($unitPrice - $costPrice) / $unitPrice) * 100, 1) . '%',
        'stock_qty'      => $stock,
        'reorder_level'  => $reorder,
        'stock_status'   => $stock <= $reorder ? 'Low Stock' : ($stock <= $reorder * 2 ? 'Moderate' : 'In Stock'),
        'weight_kg'      => $weight,
        'warehouse'      => 'Karachi Main',
        'last_updated'   => '2026-07-28',
    ];
}

// ─────────────────────────────────────────────
//  REVENUE (monthly aggregates)
// ─────────────────────────────────────────────
$revenueRows = [];
$monthlyData = [];
foreach ($orders as $o) {
    $month = substr($o['date'], 0, 7);
    if (!isset($monthlyData[$month])) {
        $monthlyData[$month] = ['orders'=>0,'gross'=>0,'returns'=>0,'cost'=>0];
    }
    $monthlyData[$month]['orders']++;
    $raw = (float) str_replace(['PKR ', ','], '', $o['cod_amount'] ?: '0');
    if (in_array($o['status'], ['Delivered','Prepaid'])) {
        $monthlyData[$month]['gross'] += $raw;
        $monthlyData[$month]['cost']  += $o['cost_price'];
    }
    if ($o['status'] === 'Returned') {
        $monthlyData[$month]['returns'] += $raw * 0.08;
    }
}

$chargeByMonth = ['2026-03'=>28800,'2026-04'=>31900,'2026-05'=>36600,'2026-06'=>41600,'2026-07'=>32500];

foreach ($monthlyData as $month => $data) {
    $gross      = $data['gross'];
    $returns    = $data['returns'];
    $courier    = $chargeByMonth[$month] ?? 0;
    $expenses   = 0;
    foreach ($expenseRows as $e) {
        if ($e['month'] === $month) $expenses += $e['amount_raw'];
    }
    $netRev     = $gross - $returns - $courier;
    $grossProfit= $gross - $data['cost'];

    $revenueRows[] = [
        'month'             => $month,
        'orders_count'      => $data['orders'],
        'gross_revenue'     => fmtPKR($gross),
        'returns_deducted'  => fmtPKR($returns),
        'courier_charges'   => fmtPKR($courier),
        'net_revenue'       => fmtPKR($netRev),
        'cost_of_goods'     => fmtPKR($data['cost']),
        'gross_profit'      => fmtPKR($grossProfit),
        'total_expenses'    => fmtPKR($expenses),
        'net_profit'        => fmtPKR($netRev - $expenses),
    ];
}

// ─────────────────────────────────────────────
//  P&L (monthly)
// ─────────────────────────────────────────────
$plRows = [];
foreach ($revenueRows as $r) {
    $gross   = (float) str_replace(['PKR ', ','], '', $r['gross_revenue']);
    $cog     = (float) str_replace(['PKR ', ','], '', $r['cost_of_goods']);
    $returns = (float) str_replace(['PKR ', ','], '', $r['returns_deducted']);
    $courier = (float) str_replace(['PKR ', ','], '', $r['courier_charges']);
    $expenses= (float) str_replace(['PKR ', ','], '', $r['total_expenses']);
    $netRev  = (float) str_replace(['PKR ', ','], '', $r['net_revenue']);
    $netProfit = $netRev - $expenses;

    $plRows[] = [
        'month'             => $r['month'],
        'gross_revenue'     => fmtPKR($gross),
        'cost_of_goods'     => fmtPKR($cog),
        'gross_profit'      => fmtPKR($gross - $cog),
        'gross_margin_pct'  => $gross > 0 ? round((($gross - $cog) / $gross) * 100, 1) . '%' : '0%',
        'returns_loss'      => fmtPKR($returns),
        'courier_charges'   => fmtPKR($courier),
        'total_expenses'    => fmtPKR($expenses),
        'operating_profit'  => fmtPKR($gross - $cog - $returns - $courier - $expenses),
        'tax_provision'     => fmtPKR(max(0, $gross * 0.055)),
        'net_profit'        => fmtPKR($netProfit),
        'net_margin_pct'    => $gross > 0 ? round(($netProfit / $gross) * 100, 1) . '%' : '0%',
    ];
}

// ─────────────────────────────────────────────
//  TAX (monthly)
// ─────────────────────────────────────────────
$taxRows = [];
$taxMonths = ['2026-03','2026-04','2026-05','2026-06','2026-07'];
foreach ($taxMonths as $month) {
    $rev = 0;
    foreach ($revenueRows as $r) {
        if ($r['month'] === $month) {
            $rev = (float) str_replace(['PKR ', ','], '', $r['gross_revenue']);
        }
    }
    $salesTax = round($rev * 0.055);
    $wht      = round($rev * 0.021);
    $netTax   = $salesTax - $wht;
    $isPast   = strtotime($month . '-01') < strtotime('2026-07-01');

    $taxRows[] = [
        'month'              => $month,
        'taxable_sales'      => fmtPKR($rev),
        'sales_tax_rate'     => '5.5%',
        'sales_tax_payable'  => fmtPKR($salesTax),
        'wht_rate'           => '2.1%',
        'wht_deducted'       => fmtPKR($wht),
        'net_tax_payable'    => fmtPKR($netTax),
        'filed_date'         => $isPast ? addDays($month . '-28', 3) : '',
        'status'             => $isPast ? 'Filed' : 'Pending',
        'notes'              => $isPast ? 'Filed via FBR IRIS' : 'Due by ' . $month . '-31',
    ];
}

// ─────────────────────────────────────────────
//  BALANCE SHEET (as of Jul 2026)
// ─────────────────────────────────────────────
$bsRows = [
    ['Assets',      'Current Assets',   'Cash & Bank Balances',          fmtPKR(1850000)],
    ['Assets',      'Current Assets',   'COD Receivable (Leopard)',       fmtPKR(1180000)],
    ['Assets',      'Current Assets',   'Inventory (at cost)',            fmtPKR(2240000)],
    ['Assets',      'Current Assets',   'Prepaid Expenses',               fmtPKR(95000)],
    ['Assets',      'Fixed Assets',     'Equipment & Devices',            fmtPKR(380000)],
    ['Assets',      'Fixed Assets',     'Furniture & Fixtures',           fmtPKR(120000)],
    ['Assets',      'Fixed Assets',     'Computer & IT Equipment',        fmtPKR(220000)],
    ['Liabilities', 'Current Liabilities','Accounts Payable (Vendors)',   fmtPKR(340000)],
    ['Liabilities', 'Current Liabilities','Courier Charges Payable',      fmtPKR(534800)],
    ['Liabilities', 'Current Liabilities','Sales Tax Payable',            fmtPKR(81420)],
    ['Liabilities', 'Current Liabilities','Salaries Payable',             fmtPKR(130000)],
    ['Liabilities', 'Long-term Liabilities','Loan — Allied Bank',         fmtPKR(800000)],
    ['Equity',      'Owner Equity',     'Paid-up Capital',                fmtPKR(2000000)],
    ['Equity',      'Owner Equity',     'Retained Earnings (Prior Yrs)',  fmtPKR(850000)],
    ['Equity',      'Owner Equity',     'Current Year Net Profit',        fmtPKR(1148780)],
];

// ─────────────────────────────────────────────
//  REPORTS (summary KPIs)
// ─────────────────────────────────────────────
$reportsRows = [
    ['Order Performance', '2026-03', 'Total Orders',       48,         ''],
    ['Order Performance', '2026-03', 'Delivered',          31,         '64.6%'],
    ['Order Performance', '2026-03', 'Returned',           7,          '14.6%'],
    ['Order Performance', '2026-04', 'Total Orders',       55,         ''],
    ['Order Performance', '2026-04', 'Delivered',          36,         '65.5%'],
    ['Order Performance', '2026-04', 'Returned',           8,          '14.5%'],
    ['Order Performance', '2026-05', 'Total Orders',       62,         ''],
    ['Order Performance', '2026-05', 'Delivered',          40,         '64.5%'],
    ['Order Performance', '2026-05', 'Returned',           9,          '14.5%'],
    ['Order Performance', '2026-06', 'Total Orders',       68,         ''],
    ['Order Performance', '2026-06', 'Delivered',          44,         '64.7%'],
    ['Order Performance', '2026-06', 'Returned',           10,         '14.7%'],
    ['Order Performance', '2026-07', 'Total Orders',       50,         ''],
    ['Order Performance', '2026-07', 'Delivered',          32,         '64.0%'],
    ['Order Performance', '2026-07', 'Returned',           7,          '14.0%'],
    ['City Performance',  'All',     'Top City',           'Karachi',  '~25% share'],
    ['City Performance',  'All',     '2nd City',           'Lahore',   '~20% share'],
    ['City Performance',  'All',     '3rd City',           'Islamabad','~12% share'],
    ['Financials',        'Q1-2026', 'Total Revenue',      'PKR 1.82M','March only'],
    ['Financials',        'Q2-2026', 'Total Revenue',      'PKR 4.61M','Apr–Jun'],
    ['Financials',        'Jul-2026','Revenue MTD',        'PKR 1.28M','Partial month'],
];

// ═══════════════════════════════════════════════════════
//  BUILD EXCEL WORKBOOK
// ═══════════════════════════════════════════════════════
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator('Medics Platform Seeder')
    ->setTitle('Medics Database')
    ->setDescription('E-Commerce Reconciliation Database — Generated ' . date('Y-m-d'));

// ── Style helpers ──
function styleHeader(object $sheet, string $range, string $bgColor = '1a73e8'): void
{
    $sheet->getStyle($range)->applyFromArray([
        'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $bgColor]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
    ]);
}

function autoWidth(object $sheet, int $cols): void
{
    for ($c = 1; $c <= $cols; $c++) {
        $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
    }
}

function writeSheet(
    Spreadsheet $wb,
    string $sheetName,
    array $headers,
    array $rows,
    string $headerColor = '1a73e8',
    bool $isFirst = false
): void {
    if ($isFirst) {
        $sheet = $wb->getActiveSheet();
        $sheet->setTitle($sheetName);
    } else {
        $sheet = $wb->createSheet();
        $sheet->setTitle($sheetName);
    }

    // Write headers
    foreach ($headers as $ci => $h) {
        $cell = Coordinate::stringFromColumnIndex($ci + 1) . '1';
$sheet->setCellValue($cell, $h);
    }
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
    styleHeader($sheet, 'A1:' . $colLetter . '1', $headerColor);
    $sheet->getRowDimension(1)->setRowHeight(22);

    // Write data rows
    foreach ($rows as $ri => $row) {
        $values = array_values($row);
        foreach ($values as $ci => $val) {
            $cell = Coordinate::stringFromColumnIndex($ci + 1) . ($ri + 2);
$sheet->setCellValue($cell, $val);
        }
        // Zebra striping
        if ($ri % 2 === 1) {
            $rowRange = 'A' . ($ri + 2) . ':' . $colLetter . ($ri + 2);
            $sheet->getStyle($rowRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('f8fafc');
        }
    }

    // Freeze header row
    $sheet->freezePane('A2');
    $sheet->setAutoFilter('A1:' . $colLetter . '1');
    autoWidth($sheet, count($headers));
}

// ── Write each sheet ──
writeSheet($spreadsheet, 'orders', [
    'Tracking No','Order Ref','Date','Customer','City',
    'Product','SKU','Qty','Unit Price (PKR)','Cost Price (PKR)',
    'Weight (kg)','COD Amount','Status','Courier','Delivery Date','Returned By'
], array_map(fn($o) => [
    $o['tracking_no'], $o['order_ref'], $o['date'], $o['customer'], $o['city'],
    $o['product'], $o['sku'], $o['qty'], $o['unit_price'], $o['cost_price'],
    $o['weight_kg'], $o['cod_amount'], $o['status'], $o['courier'], $o['delivery_date'], $o['returned_by']
], $orders), '1a73e8', true);

writeSheet($spreadsheet, 'returns', [
    'Return ID','Tracking No','Order Ref','Customer','City','Product',
    'Return Reason','Returned By','Initiated Date','Received Date',
    'Condition','COD Collected','Loss Value','Courier'
], array_map(fn($r) => array_values($r), $returnRows), 'dc2626');

writeSheet($spreadsheet, 'charges', [
    'Invoice No','Date','Courier','Invoice Period','Shipments',
    'Billed Amount','Expected Amount','Variance','Status','Notes'
], array_map(fn($r) => array_values($r), $chargesRows), 'd97706');

writeSheet($spreadsheet, 'cod', [
    'Batch ID','Date','Courier','Total Collected','Remitted Amount',
    'Pending Amount','Status','Remittance Date','Batch Shipments'
], array_map(fn($r) => array_values($r), $codRows), '0891b2');

writeSheet($spreadsheet, 'disputes', [
    'Dispute ID','Date Opened','Invoice No','Courier','Type','Description',
    'Disputed Amount','Status','Resolution Date','Resolution Notes'
], array_map(fn($r) => array_values($r), $disputeRows), '7c3aed');

writeSheet($spreadsheet, 'expenses', [
    'Expense ID','Date','Month','Category','Description',
    'Amount','Amount (Raw)','Vendor','Payment Method','Status'
], array_map(fn($r) => array_values($r), $expenseRows), '16a34a');

writeSheet($spreadsheet, 'inventory', [
    'Product ID','SKU','Product Name','Category','Unit Price','Cost Price',
    'Margin %','Stock Qty','Reorder Level','Stock Status','Weight (kg)','Warehouse','Last Updated'
], array_map(fn($r) => array_values($r), $inventoryRows), '0891b2');

writeSheet($spreadsheet, 'revenue', [
    'Month','Orders Count','Gross Revenue','Returns Deducted','Courier Charges',
    'Net Revenue','Cost of Goods','Gross Profit','Total Expenses','Net Profit'
], array_map(fn($r) => array_values($r), $revenueRows), '16a34a');

writeSheet($spreadsheet, 'pl', [
    'Month','Gross Revenue','Cost of Goods','Gross Profit','Gross Margin %',
    'Returns Loss','Courier Charges','Total Expenses','Operating Profit','Tax Provision','Net Profit','Net Margin %'
], array_map(fn($r) => array_values($r), $plRows), '1a2940');

writeSheet($spreadsheet, 'tax', [
    'Month','Taxable Sales','Sales Tax Rate','Sales Tax Payable','WHT Rate',
    'WHT Deducted','Net Tax Payable','Filed Date','Status','Notes'
], array_map(fn($r) => array_values($r), $taxRows), 'dc2626');

writeSheet($spreadsheet, 'balance_sheet', [
    'Category','Sub-Category','Item','Amount'
], array_map(fn($r) => $r, $bsRows), '1a2940');

writeSheet($spreadsheet, 'reports', [
    'Report Type','Period','Metric','Value','Notes'
], array_map(fn($r) => $r, $reportsRows), '7c3aed');

// ── Save ──
if (!is_dir(__DIR__ . '/upload')) {
    mkdir(__DIR__ . '/upload', 0755, true);
}

$writer = new Xlsx($spreadsheet);
$writer->save($OUTPUT_PATH);

// ── Output result ──
$fileSize = round(filesize($OUTPUT_PATH) / 1024, 1);
$orderCount = count($orders);
$returnCount = count($returnRows);
$expenseCount = count($expenseRows);

echo "<!DOCTYPE html><html><head><title>Seeder Done</title>
<style>body{font-family:system-ui;background:#f0f4f8;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
.box{background:#fff;border-radius:12px;padding:40px;box-shadow:0 4px 20px rgba(0,0,0,.1);max-width:520px;width:100%}
h2{color:#16a34a;margin:0 0 8px}p{color:#6b7a8d;margin:4px 0}
.pill{display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;margin:4px 4px 0 0}
.green{background:#dcfce7;color:#16a34a}.blue{background:#e8f0fe;color:#1a73e8}
a{display:inline-block;margin-top:20px;padding:10px 24px;background:#1a73e8;color:#fff;border-radius:8px;text-decoration:none;font-weight:600}
</style></head><body><div class='box'>
<h2>✅ Database Generated Successfully</h2>
<p><b>File:</b> upload/medics_database.xlsx ({$fileSize} KB)</p>
<p style='margin-top:12px'><b>Tabs created:</b></p>
<div style='margin-top:8px'>
<span class='pill blue'>orders ({$orderCount})</span>
<span class='pill blue'>returns ({$returnCount})</span>
<span class='pill blue'>charges (" . count($chargesRows) . ")</span>
<span class='pill blue'>cod (" . count($codRows) . ")</span>
<span class='pill blue'>disputes (" . count($disputeRows) . ")</span>
<span class='pill green'>expenses ({$expenseCount})</span>
<span class='pill green'>inventory (" . count($inventoryRows) . ")</span>
<span class='pill green'>revenue (" . count($revenueRows) . ")</span>
<span class='pill green'>pl (" . count($plRows) . ")</span>
<span class='pill green'>tax (" . count($taxRows) . ")</span>
<span class='pill green'>balance_sheet (" . count($bsRows) . ")</span>
<span class='pill green'>reports (" . count($reportsRows) . ")</span>
</div>
<p style='margin-top:16px;color:#dc2626'><b>⚠ Delete this file after running!</b></p>
<a href='dashboard.php'>Go to Dashboard →</a>
</div></body></html>";
