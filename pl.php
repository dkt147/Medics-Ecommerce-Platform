<?php
require_once __DIR__ . '/includes/data_helpers.php';

$plRows      = getPLRows();
$ordersRows  = getOrdersRows();
$expRows     = getExpensesRows();
$metrics     = getOrderMetrics($ordersRows);

// Try to load from PL sheet
$grossRev = 0.0; $returns = 0.0; $netRev = 0.0; $cogs = 0.0; $grossProfit = 0.0;
$freight = 0.0; $returnFreight = 0.0; $codFees = 0.0; $packaging = 0.0;
$warehouse = 0.0; $platform = 0.0; $totalOpex = 0.0; $ebit = 0.0;
$taxExp = 0.0; $netProfit = 0.0;

foreach ($plRows as $r) {
    $grossRev    += parseCurrencyValue($r['gross_revenue'] ?? $r['gross_rev'] ?? '');
    $returns     += parseCurrencyValue($r['returns'] ?? '');
    $netRev      += parseCurrencyValue($r['net_revenue'] ?? $r['net_rev'] ?? '');
    $cogs        += parseCurrencyValue($r['cogs'] ?? $r['cost_of_goods'] ?? '');
    $grossProfit += parseCurrencyValue($r['gross_profit'] ?? '');
    $freight     += parseCurrencyValue($r['freight'] ?? '');
    $returnFreight += parseCurrencyValue($r['return_freight'] ?? '');
    $codFees     += parseCurrencyValue($r['cod_fees'] ?? '');
    $packaging   += parseCurrencyValue($r['packaging'] ?? '');
    $warehouse   += parseCurrencyValue($r['warehouse'] ?? '');
    $platform    += parseCurrencyValue($r['platform_fees'] ?? '');
    $taxExp      += parseCurrencyValue($r['tax'] ?? $r['tax_expense'] ?? '');
    $netProfit   += parseCurrencyValue($r['net_profit'] ?? '');
}

// Fallback: compute from orders + expenses
if ($grossRev == 0) {
    foreach ($ordersRows as $o) {
        $grossRev += parseCurrencyValue($o['cod_amount'] ?? '');
        if (strtolower(trim($o['status'] ?? '')) === 'returned') {
            $returns += parseCurrencyValue($o['cod_amount'] ?? '');
        }
    }
    $netRev = $grossRev - $returns;

    // COGS: ~51% of net revenue typical pharma
    $cogs = $netRev * 0.60;
    $grossProfit = $netRev - $cogs;

    // Expenses from expense sheet or estimates
    $byCategory = [];
    foreach ($expRows as $e) {
        $cat = trim($e['category'] ?? 'Other');
        $amt = parseCurrencyValue($e['amount'] ?? '');
        $byCategory[$cat] = ($byCategory[$cat] ?? 0.0) + $amt;
    }
    $freight       = $byCategory['Freight & Delivery'] ?? $byCategory['Freight'] ?? round($grossRev * 0.034);
    $returnFreight = $byCategory['Return Freight'] ?? round($grossRev * 0.005);
    $codFees       = $byCategory['COD Collection Fee'] ?? round($grossRev * 0.012);
    $packaging     = $byCategory['Packaging'] ?? round($grossRev * 0.005);
    $warehouse     = $byCategory['Salaries'] ?? round($grossRev * 0.016);
    $platform      = $byCategory['Software'] ?? round($grossRev * 0.002);
    $totalOpex     = $freight + $returnFreight + $codFees + $packaging + $warehouse + $platform;
    $ebit          = $grossProfit - $totalOpex;
    $taxExp        = round($netRev * 0.011);  // ~1.1% net tax after ST credits
    $netProfit     = $ebit - $taxExp;
} else {
    $totalOpex = $freight + $returnFreight + $codFees + $packaging + $warehouse + $platform;
    $ebit      = $grossProfit > 0 ? $grossProfit - $totalOpex : $netRev - $cogs - $totalOpex;
    $netProfit = $ebit - $taxExp;
}

// Margin %
$grossMarginPct = $netRev > 0 ? pct($grossProfit, $netRev) : 0;
$ebitMarginPct  = $netRev > 0 ? pct($ebit, $netRev) : 0;
$netMarginPct   = $netRev > 0 ? pct($netProfit, $netRev) : 0;

// Latest month label
$months = [];
foreach ($ordersRows as $o) { $m = substr($o['date'] ?? '', 0, 7); if ($m) $months[$m] = true; }
krsort($months);
$latestMonth = $months ? date('F Y', strtotime(array_key_first($months) . '-01')) : date('F Y');

$chartData = json_encode([
    round($netRev),
    round($grossProfit),
    round($ebit),
    round($netProfit)
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P&amp;L Statement</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        :root {
            --primary: #1a73e8; --primary-light: #e8f0fe; --sidebar-width: 260px;
            --body-bg: #f0f4f8; --card-bg: #ffffff; --text-main: #1a2940; --text-muted: #6b7a8d;
            --border: #e2e8f0; --success: #16a34a; --success-bg: #dcfce7; --danger: #dc2626;
            --danger-bg: #fee2e2; --warning: #d97706; --info: #0891b2;
            --shadow: 0 1px 4px rgba(0,0,0,0.08); --radius: 10px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: var(--body-bg); color: var(--text-main); }
        .layout { display: flex; min-height: 100vh; }
        .main { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; }
        .content { padding: 24px 28px; flex: 1; }
        .card { background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); padding: 20px; }
        .card-title { font-size: 14px; font-weight: 700; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .card-title .ct-icon { font-size: 16px; }
        .mb20 { margin-bottom: 20px; }
        .grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 20px; }
        .statement-card { background: #fff; border-radius: 16px; border: 1px solid #e8edf5; overflow: hidden; }
        .statement-card .card-title { padding: 20px 24px; margin-bottom: 0; }
        .statement-group-header { display: block; background: #eef4ff; color: #475569; font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; padding: 14px 24px; border-bottom: 1px solid #e2e8f0; }
        .statement-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 24px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; }
        .statement-row:last-child { border-bottom: none; }
        .statement-row .statement-label { color: #475569; }
        .statement-row .statement-value { min-width: 160px; text-align: right; font-weight: 700; }
        .statement-row.negative .statement-value { color: var(--danger); }
        .statement-row.positive .statement-value { color: var(--success); }
        .statement-row.neutral .statement-value { color: var(--text-main); }
        .statement-row.total { color: var(--text-main); font-weight: 700; border-top: 1px solid #e2e8f0; padding-top: 16px; }
        .statement-row.final { background: #e0f2fe; color: #1d4ed8; font-weight: 700; }
        .statement-row.final .statement-value { color: #1d4ed8; }
        .chart-box { position: relative; height: 280px; }
        .metric-list { margin-top: 20px; display: grid; gap: 10px; }
        .metric-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: var(--text-muted); }
        .metric-row strong { color: var(--text-main); }
        @media (max-width: 1200px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>

<body>
    <div class="layout">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main">
            <?php include 'includes/navbar.php'; ?>
            <div class="content">
                <div class="page active" id="page-pl">
                    <div class="grid-2">
                        <div class="statement-card card">
                            <div class="card-title"><span class="ct-icon">📊</span> Profit &amp; Loss Statement — <?= htmlspecialchars($latestMonth) ?></div>
                            <span class="statement-group-header">Revenue</span>
                            <div class="statement-row neutral"><span class="statement-label">Gross Revenue (All Orders)</span><span class="statement-value"><?= formatCurrency($grossRev) ?></span></div>
                            <div class="statement-row negative"><span class="statement-label">Less: Returns &amp; Refunds</span><span class="statement-value">-<?= formatCurrency($returns) ?></span></div>
                            <div class="statement-row total positive"><span class="statement-label"><strong>Net Revenue</strong></span><span class="statement-value"><?= formatCurrency($netRev) ?></span></div>
                            <span class="statement-group-header">Cost of Goods Sold</span>
                            <div class="statement-row negative"><span class="statement-label">COGS (Product Cost)</span><span class="statement-value">-<?= formatCurrency($cogs) ?></span></div>
                            <div class="statement-row total positive"><span class="statement-label"><strong>Gross Profit</strong></span><span class="statement-value"><?= formatCurrency($grossProfit) ?></span></div>
                            <span class="statement-group-header">Operating Expenses</span>
                            <div class="statement-row negative"><span class="statement-label">Freight &amp; Delivery Costs</span><span class="statement-value">-<?= formatCurrency($freight) ?></span></div>
                            <div class="statement-row negative"><span class="statement-label">Return Freight Costs</span><span class="statement-value">-<?= formatCurrency($returnFreight) ?></span></div>
                            <div class="statement-row negative"><span class="statement-label">COD Collection Charges</span><span class="statement-value">-<?= formatCurrency($codFees) ?></span></div>
                            <div class="statement-row negative"><span class="statement-label">Packaging &amp; Materials</span><span class="statement-value">-<?= formatCurrency($packaging) ?></span></div>
                            <div class="statement-row negative"><span class="statement-label">Warehouse &amp; Labour</span><span class="statement-value">-<?= formatCurrency($warehouse) ?></span></div>
                            <div class="statement-row negative"><span class="statement-label">Platform &amp; Gateway Fees</span><span class="statement-value">-<?= formatCurrency($platform) ?></span></div>
                            <div class="statement-row total positive"><span class="statement-label"><strong>Operating Profit (EBIT)</strong></span><span class="statement-value"><?= formatCurrency($ebit) ?></span></div>
                            <span class="statement-group-header">Tax</span>
                            <div class="statement-row negative"><span class="statement-label">Net Tax Expense</span><span class="statement-value">-<?= formatCurrency($taxExp) ?></span></div>
                            <div class="statement-row final"><span class="statement-label"><strong>Net Profit / (Loss)</strong></span><span class="statement-value"><?= formatCurrency($netProfit) ?></span></div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📈</span> Margin Waterfall</div>
                            <div class="chart-box"><canvas id="marginChart"></canvas></div>
                            <div class="metric-list">
                                <div class="metric-row"><span>Gross Margin %</span><strong><?= $grossMarginPct ?>%</strong></div>
                                <div class="metric-row"><span>EBIT Margin %</span><strong><?= $ebitMarginPct ?>%</strong></div>
                                <div class="metric-row"><span>Net Margin %</span><strong><?= $netMarginPct ?>%</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script>
        document.getElementById('page-title').textContent = 'P&L Statement';
        document.getElementById('page-bread').textContent = 'Finance / P&L Statement';

        new Chart(document.getElementById('marginChart'), {
            type: 'bar',
            data: {
                labels: ['Net Revenue', 'Gross Profit', 'EBIT', 'Net Profit'],
                datasets: [{
                    data: <?= $chartData ?>,
                    backgroundColor: ['#0ea5e9','#16a34a','#f59e0b','#22c55e'],
                    borderRadius: 8,
                    maxBarThickness: 60
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f1f5f9' }, beginAtZero: true,
                         ticks: { callback: v => 'PKR ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'M' : (v/1000).toFixed(0)+'K') } }
                }
            }
        });
    </script>
</body>
</html>
