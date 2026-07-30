<?php
require_once __DIR__ . '/includes/data_helpers.php';

$excelFilePath = __DIR__ . '/upload/orders_data.xlsx';
$rows = loadOrdersRows($excelFilePath, getDefaultOrdersRows());
$returnRows = getReturnRows($rows);
$metrics = getOrderMetrics($rows);
$cityCounts = getCityCounts($returnRows);
$cityLabels = array_keys($cityCounts);
$cityValues = array_values($cityCounts);
$reasonCounts = [];
foreach ($returnRows as $row) {
    $reason = trim($row['returned_by'] ?? '') ?: 'Customer Refused';
    $reasonCounts[$reason] = ($reasonCounts[$reason] ?? 0) + 1;
}
$reasonLabels = array_keys($reasonCounts);
$reasonValues = array_values($reasonCounts);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns & Refunds</title>
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

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

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
                <div class="page active" id="page-returns">

                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Total Returns</div>
                            <div class="kpi-value"><?php echo count($returnRows); ?></div>
                            <div class="kpi-meta kpi-down"><?php echo $metrics['return_rate']; ?>% return rate</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Returned Orders</div>
                            <div class="kpi-value"><?php echo $metrics['returned_count']; ?></div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Pending Orders</div>
                            <div class="kpi-value"><?php echo $metrics['pending_count']; ?></div>
                            <div class="kpi-meta kpi-up"><?php echo $metrics['in_transit_count']; ?> in transit</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">COD Declared</div>
                            <div class="kpi-value"><?php echo formatCurrency($metrics['cod_declared']); ?></div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📍</span> Return Rate by City</div>
                            <div class="chart-box"><canvas id="returnCityChart"></canvas></div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📊</span> Return Reason Breakdown</div>
                            <div class="chart-box"><canvas id="returnReasonChart"></canvas></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-title"><span class="ct-icon">🧾</span> Returns Register</div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>RETURN TRACKING</th>
                                        <th>ORIGINAL ORDER</th>
                                        <th>RETURN REASON</th>
                                        <th>INITIATED</th>
                                        <th>RECEIVED</th>
                                        <th>CONDITION</th>
                                        <th>COD COLLECTED?</th>
                                        <th>LOSS VALUE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($returnRows as $row): ?>
                                    <tr>
                                        <td><b><?php echo htmlspecialchars($row['tracking_no'] ?? ''); ?></b></td>
                                        <td><a href="orders.php?order_ref=<?php echo urlencode($row['order_ref'] ?? ''); ?>" style="color:var(--primary);text-decoration:none;font-weight:600;"><?php echo htmlspecialchars($row['order_ref'] ?? ''); ?></a></td>
                                        <td><?php echo htmlspecialchars($row['returned_by'] ?: 'Customer Refused'); ?></td>
                                        <td><?php echo htmlspecialchars($row['date'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($row['delivery_date'] ?? ''); ?></td>
                                        <td><span class="badge badge-<?php echo strtolower(trim($row['status'] ?? '')) === 'returned' ? 'green' : 'yellow'; ?>"><?php echo htmlspecialchars(ucfirst($row['status'] ?? 'Returned')); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['cod_amount'] ?: 'No'); ?></td>
                                        <td><?php echo htmlspecialchars($row['cod_amount'] ?: 'PKR 0'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <?php include 'includes/footer.php'; ?>

        </div>
    </div>
</body>
<script>
    document.getElementById('page-title').textContent = 'Returns & Refunds';
    document.getElementById('page-bread').textContent = 'Modules / Returns & Refunds';

    const SUCCESS = '#16a34a';
    const DANGER = '#dc2626';
    const WARNING = '#d97706';
    const INFO = '#0891b2';
    const GREY = '#94a3b8';
    const palette = ['#dc2626', '#ea580c', '#0891b2', '#64748b', '#9333ea', '#f59e0b', '#22c55e'];

    Chart.defaults.font.family = "'Segoe UI', system-ui, -apple-system, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7a8d';

    const returnCityLabels = <?php echo json_encode($cityLabels); ?>;
    const returnCityValues = <?php echo json_encode($cityValues); ?>;
    const returnReasonLabels = <?php echo json_encode($reasonLabels); ?>;
    const returnReasonValues = <?php echo json_encode($reasonValues); ?>;

    new Chart(document.getElementById('returnCityChart'), {
        type: 'bar',
        data: {
            labels: returnCityLabels.length ? returnCityLabels : ['Karachi', 'Lahore', 'Islamabad', 'Faisalabad', 'Rawalpindi', 'Multan'],
            datasets: [{
                label: 'Returns',
                data: returnCityValues.length ? returnCityValues : [24, 13, 11, 14, 12, 17],
                backgroundColor: '#dc2626',
                borderRadius: 6,
                maxBarThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#f1f5f9' }, beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('returnReasonChart'), {
        type: 'doughnut',
        data: {
            labels: returnReasonLabels.length ? returnReasonLabels : ['Customer Refused', 'Wrong Item', 'Not Available', 'Address Incorrect', 'Damaged in Transit', 'Other'],
            datasets: [{
                data: returnReasonValues.length ? returnReasonValues : [32, 18, 14, 12, 10, 14],
                backgroundColor: [DANGER, WARNING, INFO, GREY, '#7c3aed', '#f97316'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, font: { size: 11 } }
                }
            },
            cutout: '55%'
        }
    });
</script>

</html>
