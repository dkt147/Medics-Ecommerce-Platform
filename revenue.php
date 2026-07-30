<?php
require_once __DIR__ . '/includes/data_helpers.php';

$revenueRows  = getRevenueRows();
$ordersRows   = getOrdersRows();
$metrics      = getOrderMetrics($ordersRows);

// Try to get KPIs from revenue sheet first
$grossRevenue = 0.0; $lessReturns = 0.0; $netRevenue = 0.0;
foreach ($revenueRows as $r) {
    $grossRevenue += parseCurrencyValue($r['gross_revenue'] ?? $r['gross'] ?? '');
    $lessReturns  += parseCurrencyValue($r['returns'] ?? $r['refunds'] ?? '');
    $netRevenue   += parseCurrencyValue($r['net_revenue'] ?? $r['net'] ?? '');
}

// Fallback: compute from orders
if ($grossRevenue == 0) {
    // Sum COD amounts on delivered + returned orders (gross)
    foreach ($ordersRows as $o) {
        $grossRevenue += parseCurrencyValue($o['cod_amount'] ?? '');
    }
    foreach ($ordersRows as $o) {
        if (strtolower(trim($o['status'] ?? '')) === 'returned') {
            $lessReturns += parseCurrencyValue($o['cod_amount'] ?? '');
        }
    }
    $netRevenue = $grossRevenue - $lessReturns;
}

$aov = $metrics['total_orders'] > 0 ? round($grossRevenue / $metrics['total_orders']) : 0;

// SKU-level breakdown from orders
$skuBreakdown = [];
foreach ($ordersRows as $o) {
    $sku  = trim($o['sku']  ?? 'UNKNOWN');
    $prod = trim($o['product'] ?? 'Unknown Product');
    $amt  = parseCurrencyValue($o['cod_amount'] ?? '');
    $qty  = (int)($o['qty'] ?? 1);
    $cost = parseCurrencyValue($o['unit_price'] ?? '') * $qty;
    $returned = strtolower(trim($o['status'] ?? '')) === 'returned';
    if (!isset($skuBreakdown[$sku])) {
        $skuBreakdown[$sku] = ['sku' => $sku, 'product' => $prod, 'units_sold' => 0, 'gross_rev' => 0.0, 'returns' => 0, 'net_rev' => 0.0, 'cost' => 0.0];
    }
    if (!$returned) {
        $skuBreakdown[$sku]['units_sold'] += $qty;
        $skuBreakdown[$sku]['gross_rev']  += $amt;
        $skuBreakdown[$sku]['cost']       += $cost;
        $skuBreakdown[$sku]['net_rev']    += $amt;
    } else {
        $skuBreakdown[$sku]['returns']++;
    }
}
uasort($skuBreakdown, fn($a, $b) => $b['gross_rev'] <=> $a['gross_rev']);

// Payment split
$codCount     = $metrics['total_orders'] - $metrics['prepaid_count'];
$prepaidCount = $metrics['prepaid_count'];
$codPct       = pct($codCount, $metrics['total_orders']);
$prepaidPct   = pct($prepaidCount, $metrics['total_orders']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales &amp; Revenue</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        :root {
            --primary: #1a73e8; --primary-light: #e8f0fe; --sidebar-width: 260px;
            --body-bg: #f0f4f8; --card-bg: #ffffff; --text-main: #1a2940; --text-muted: #6b7a8d;
            --border: #e2e8f0; --success: #16a34a; --success-bg: #dcfce7; --danger: #dc2626;
            --danger-bg: #fee2e2; --warning: #d97706; --warning-bg: #fef3c7; --info: #0891b2;
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
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
        .kpi-card { background: var(--card-bg); border-radius: var(--radius); padding: 18px 20px; box-shadow: var(--shadow); border: 1px solid var(--border); display: flex; flex-direction: column; gap: 6px; position: relative; overflow: hidden; }
        .kpi-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
        .kpi-card.blue::before { background: var(--primary); }
        .kpi-card.green::before { background: var(--success); }
        .kpi-card.red::before { background: var(--danger); }
        .kpi-card.orange::before { background: var(--warning); }
        .kpi-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .kpi-value { font-size: 24px; font-weight: 800; color: var(--text-main); }
        .kpi-value.sm { font-size: 18px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: #f8fafc; padding: 10px 14px; text-align: left; font-size: 11.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); white-space: nowrap; }
        tbody td { padding: 11px 14px; border-bottom: 1px solid #f1f5f9; color: var(--text-main); font-size: 13px; }
        tbody tr:hover { background: #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .badge-green { background: var(--success-bg); color: var(--success); }
        .badge-yellow { background: var(--warning-bg); color: var(--warning); }
        .badge-red { background: var(--danger-bg); color: var(--danger); }
        .chart-box { position: relative; height: 260px; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c8d4e0; border-radius: 3px; }
        @media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>

<body>
    <div class="layout">

        <?php include 'includes/sidebar.php'; ?>

        <div class="main">

            <?php include 'includes/navbar.php'; ?>

            <div class="content">
                <div class="page active" id="page-revenue">

                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Gross Revenue</div>
                            <div class="kpi-value sm"><?= formatCurrency($grossRevenue) ?></div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Less Returns</div>
                            <div class="kpi-value sm">-<?= formatCurrency($lessReturns) ?></div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Net Revenue</div>
                            <div class="kpi-value sm"><?= formatCurrency($netRevenue) ?></div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">AOV (Avg Order Value)</div>
                            <div class="kpi-value sm"><?= formatCurrency($aov) ?></div>
                        </div>
                    </div>

                    <div class="grid-2 mb20">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📈</span> Revenue by Channel</div>
                            <div class="chart-box"><canvas id="channelChart"></canvas></div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">💰</span> COD vs Prepaid Split</div>
                            <div class="chart-box"><canvas id="paymentChart"></canvas></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-title"><span class="ct-icon">🧾</span> SKU-Level Revenue (Top <?= min(count($skuBreakdown), 20) ?> Products)</div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>PRODUCT</th>
                                        <th>UNITS SOLD</th>
                                        <th>GROSS REVENUE</th>
                                        <th>RETURNS</th>
                                        <th>NET REVENUE</th>
                                        <th>MARGIN %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($skuBreakdown, 0, 20) as $s):
                                        $margin = ($s['gross_rev'] > 0 && $s['cost'] > 0)
                                            ? pct($s['gross_rev'] - $s['cost'], $s['gross_rev'])
                                            : 0.0;
                                        $badgeCls = $margin >= 35 ? 'badge-green' : ($margin >= 20 ? 'badge-yellow' : 'badge-red');
                                    ?>
                                    <tr>
                                        <td><b><?= htmlspecialchars($s['sku']) ?></b></td>
                                        <td><?= htmlspecialchars($s['product']) ?></td>
                                        <td><?= number_format($s['units_sold']) ?></td>
                                        <td><?= formatCurrency($s['gross_rev']) ?></td>
                                        <td><?= number_format($s['returns']) ?></td>
                                        <td><?= formatCurrency($s['net_rev']) ?></td>
                                        <td><span class="badge <?= $badgeCls ?>"><?= $margin > 0 ? $margin . '%' : '—' ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($skuBreakdown)): ?>
                                    <tr><td colspan="7" style="text-align:center; padding:24px; color:var(--text-muted);">No product data available.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <?php include 'includes/footer.php'; ?>

        </div>
    </div>

    <script>
        document.getElementById('page-title').textContent = 'Sales & Revenue';
        document.getElementById('page-bread').textContent = 'Modules / Sales & Revenue';

        Chart.defaults.font.family = "'Segoe UI', system-ui, -apple-system, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6b7a8d';

        new Chart(document.getElementById('channelChart'), {
            type: 'doughnut',
            data: {
                labels: ['Own Website', 'Daraz', 'Other Marketplaces', 'Wholesale'],
                datasets: [{ data: [45, 28, 17, 10], backgroundColor: ['#1a73e8','#f97316','#16a34a','#0ea5e9'], borderColor: '#fff', borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }, cutout: '70%' }
        });

        new Chart(document.getElementById('paymentChart'), {
            type: 'doughnut',
            data: {
                labels: ['COD Orders', 'Prepaid Orders'],
                datasets: [{ data: [<?= $codPct ?>, <?= $prepaidPct ?>], backgroundColor: ['#dc2626','#16a34a'], borderColor: '#fff', borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }, cutout: '70%' }
        });
    </script>
</body>
</html>
