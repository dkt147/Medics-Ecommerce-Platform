<?php
require_once __DIR__ . '/includes/data_helpers.php';

$returnsRows = getReturnsRows();

// If returns sheet is empty, fall back to filtering orders
if (empty($returnsRows)) {
    $returnsRows = getReturnRows(getOrdersRows());
}

$totalReturns = count($returnsRows);

// Financial loss = sum of COD declared on returned orders (opportunity cost)
$financialLoss = 0.0;
foreach ($returnsRows as $r) {
    $financialLoss += parseCurrencyValue($r['cod_amount'] ?? $r['loss_value'] ?? '');
}

// Condition breakdown
$sellable = 0; $damaged = 0; $lost = 0;
foreach ($returnsRows as $r) {
    $cond = strtolower(trim($r['condition'] ?? $r['item_condition'] ?? ''));
    if (strpos($cond, 'sell') !== false || $cond === 'good') $sellable++;
    elseif (strpos($cond, 'lost') !== false) $lost++;
    else $damaged++;
}

// Return reasons breakdown
$reasons = [];
foreach ($returnsRows as $r) {
    $reason = trim($r['return_reason'] ?? $r['reason'] ?? 'Other');
    if ($reason === '') $reason = 'Other';
    $reasons[$reason] = ($reasons[$reason] ?? 0) + 1;
}
arsort($reasons);

// Return rate by city
$cityCounts    = [];
$allOrdersRows = getOrdersRows();
$totalByCity   = getCityCounts($allOrdersRows);
foreach ($returnsRows as $r) {
    $city = trim($r['city'] ?? '') ?: 'Unknown';
    $cityCounts[$city] = ($cityCounts[$city] ?? 0) + 1;
}
arsort($cityCounts);

// Return rate % per city
$cityRates = [];
foreach ($cityCounts as $city => $cnt) {
    $total = $totalByCity[$city] ?? $cnt;
    $cityRates[$city] = $total > 0 ? round(($cnt / $total) * 100, 1) : 0;
}

$cityChartLabels  = json_encode(array_keys($cityCounts));
$cityChartData    = json_encode(array_values($cityCounts));
$reasonLabels     = json_encode(array_keys($reasons));
$reasonData       = json_encode(array_values($reasons));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns &amp; Refunds</title>
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
        .kpi-meta { font-size: 12px; }
        .kpi-up { color: var(--success); }
        .kpi-down { color: var(--danger); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: #f8fafc; padding: 10px 14px; text-align: left; font-size: 11.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); white-space: nowrap; }
        tbody td { padding: 11px 14px; border-bottom: 1px solid #f1f5f9; color: var(--text-main); font-size: 13px; }
        tbody tr:hover { background: #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .badge-green { background: var(--success-bg); color: var(--success); }
        .badge-red { background: var(--danger-bg); color: var(--danger); }
        .badge-yellow { background: var(--warning-bg); color: var(--warning); }
        .chart-box { position: relative; height: 240px; }
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
                <div class="page active" id="page-returns">

                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Total Returns</div>
                            <div class="kpi-value"><?= number_format($totalReturns) ?></div>
                            <div class="kpi-meta kpi-down"><?= pct($totalReturns, count($allOrdersRows)) ?>% return rate</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Returned — Sellable</div>
                            <div class="kpi-value"><?= number_format($sellable) ?></div>
                            <div class="kpi-meta kpi-up"><?= pct($sellable, $totalReturns) ?>% recoverable</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Returned — Damaged/Lost</div>
                            <div class="kpi-value"><?= number_format($damaged + $lost) ?></div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Financial Loss (Returns)</div>
                            <div class="kpi-value sm"><?= formatCurrency($financialLoss) ?></div>
                        </div>
                    </div>

                    <div class="grid-2 mb20">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📍</span> Returns by City</div>
                            <div class="chart-box"><canvas id="returnCityChart"></canvas></div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📊</span> Return Reason Breakdown</div>
                            <div class="chart-box"><canvas id="returnReasonChart"></canvas></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-title"><span class="ct-icon">🧾</span> Returns Register
                            <span style="margin-left:auto; font-weight:400; font-size:12px; color:var(--text-muted)"><?= count($returnsRows) ?> records</span>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>RETURN TRACKING</th>
                                        <th>ORIGINAL ORDER</th>
                                        <th>PRODUCT</th>
                                        <th>CITY</th>
                                        <th>RETURN REASON</th>
                                        <th>DATE</th>
                                        <th>CONDITION</th>
                                        <th>RETURNED BY</th>
                                        <th>LOSS VALUE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($returnsRows, 0, 100) as $r):
                                        $cond = trim($r['condition'] ?? $r['item_condition'] ?? '');
                                        $condBadge = (strpos(strtolower($cond),'sell') !== false || strtolower($cond)==='good') ? 'badge-green' : (strpos(strtolower($cond),'lost') !== false ? 'badge-red' : 'badge-yellow');
                                    ?>
                                    <tr>
                                        <td><b><?= htmlspecialchars($r['return_tracking'] ?? $r['tracking_no'] ?? '—') ?></b></td>
                                        <td><?= htmlspecialchars($r['order_ref'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($r['product'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($r['city'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($r['return_reason'] ?? $r['reason'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($r['date'] ?? $r['return_date'] ?? '—') ?></td>
                                        <td><span class="badge <?= $condBadge ?>"><?= htmlspecialchars($cond ?: 'Unknown') ?></span></td>
                                        <td><?= htmlspecialchars($r['returned_by'] ?? $r['return_type'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($r['loss_value'] ?? $r['cod_amount'] ?? '—') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($returnsRows)): ?>
                                    <tr><td colspan="9" style="text-align:center; padding:24px; color:var(--text-muted);">No return records found.</td></tr>
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
        document.getElementById('page-title').textContent = 'Returns & Refunds';
        document.getElementById('page-bread').textContent = 'Modules / Returns & Refunds';

        const DANGER = '#dc2626', WARNING = '#d97706', INFO = '#0891b2', GREY = '#94a3b8';
        Chart.defaults.font.family = "'Segoe UI', system-ui, -apple-system, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6b7a8d';

        new Chart(document.getElementById('returnCityChart'), {
            type: 'bar',
            data: {
                labels: <?= $cityChartLabels ?>,
                datasets: [{ label: 'Returns', data: <?= $cityChartData ?>, backgroundColor: '#dc2626', borderRadius: 6, maxBarThickness: 30 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { grid: { color: '#f1f5f9' }, beginAtZero: true } }
            }
        });

        new Chart(document.getElementById('returnReasonChart'), {
            type: 'doughnut',
            data: {
                labels: <?= $reasonLabels ?>,
                datasets: [{ data: <?= $reasonData ?>, backgroundColor: [DANGER, WARNING, INFO, GREY, '#7c3aed', '#f97316', '#22c55e'], borderColor: '#fff', borderWidth: 2 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
                cutout: '55%'
            }
        });
    </script>
</body>
</html>
