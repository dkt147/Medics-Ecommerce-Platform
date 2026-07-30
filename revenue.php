<?php
require_once __DIR__ . '/includes/data_helpers.php';

$excelFilePath = __DIR__ . '/upload/orders_data.xlsx';
$rows = loadOrdersRows($excelFilePath, getDefaultOrdersRows());
$metrics = getOrderMetrics($rows);
$codDeclared = $metrics['cod_declared'];
$estimatedRevenue = $codDeclared * 1.15;
$returnsValue = max(0, $metrics['returned_count'] * 5000);
$netRevenue = $estimatedRevenue - $returnsValue;
$aov = $metrics['total_orders'] > 0 ? round($estimatedRevenue / $metrics['total_orders']) : 0;
$channelLabels = ['Delivered Orders', 'Returned Orders', 'Pending Orders'];
$channelData = [$metrics['delivered_count'], $metrics['returned_count'], max(1, $metrics['pending_count'])];
$paymentLabels = ['Delivered', 'Returned', 'Pending'];
$paymentData = [$metrics['delivered_count'], $metrics['returned_count'], max(1, $metrics['pending_count'])];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales & Revenue</title>
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

    .kpi-grid .kpi-card .kpi-meta {
        font-size: 12px;
        color: var(--text-muted);
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
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
        height: 260px;
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
                <div class="page active" id="page-revenue">

                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Gross Revenue</div>
                            <div class="kpi-value"><?php echo formatCurrency($estimatedRevenue); ?></div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Less Returns</div>
                            <div class="kpi-value">-<?php echo formatCurrency($returnsValue); ?></div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Net Revenue</div>
                            <div class="kpi-value"><?php echo formatCurrency($netRevenue); ?></div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">AOV (Avg Order Value)</div>
                            <div class="kpi-value"><?php echo formatCurrency($aov); ?></div>
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
                        <div class="card-title"><span class="ct-icon">🧾</span> SKU-Level Revenue (Top Products)</div>
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
                                        <th>GROSS MARGIN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><b>SKU-001</b></td>
                                        <td>Wireless Earbuds Pro</td>
                                        <td>824</td>
                                        <td>PKR 1,648,000</td>
                                        <td>62</td>
                                        <td>PKR 1,524,000</td>
                                        <td><span class="badge badge-green">38.2%</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>SKU-002</b></td>
                                        <td>Phone Case — Premium</td>
                                        <td>1,240</td>
                                        <td>PKR 744,000</td>
                                        <td>44</td>
                                        <td>PKR 717,600</td>
                                        <td><span class="badge badge-green">54.1%</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>SKU-003</b></td>
                                        <td>Smart Watch Band</td>
                                        <td>412</td>
                                        <td>PKR 618,000</td>
                                        <td>71</td>
                                        <td>PKR 511,500</td>
                                        <td><span class="badge badge-yellow">29.8%</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>SKU-004</b></td>
                                        <td>USB-C Fast Charger</td>
                                        <td>608</td>
                                        <td>PKR 486,400</td>
                                        <td>18</td>
                                        <td>PKR 472,000</td>
                                        <td><span class="badge badge-green">41.5%</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>SKU-005</b></td>
                                        <td>Screen Protector Pack</td>
                                        <td>980</td>
                                        <td>PKR 392,000</td>
                                        <td>12</td>
                                        <td>PKR 387,200</td>
                                        <td><span class="badge badge-green">62.3%</span></td>
                                    </tr>
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
    document.getElementById('page-title').textContent = 'Sales & Revenue';
    document.getElementById('page-bread').textContent = 'Modules / Sales & Revenue';

    Chart.defaults.font.family = "'Segoe UI', system-ui, -apple-system, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7a8d';

    const channelLabels = <?php echo json_encode($channelLabels); ?>;
    const channelValues = <?php echo json_encode($channelData); ?>;
    const paymentLabels = <?php echo json_encode($paymentLabels); ?>;
    const paymentValues = <?php echo json_encode($paymentData); ?>;

    new Chart(document.getElementById('channelChart'), {
        type: 'doughnut',
        data: {
            labels: channelLabels,
            datasets: [{
                data: channelValues,
                backgroundColor: ['#1a73e8', '#f97316', '#16a34a'],
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
            cutout: '70%'
        }
    });

    new Chart(document.getElementById('paymentChart'), {
        type: 'doughnut',
        data: {
            labels: paymentLabels,
            datasets: [{
                data: paymentValues,
                backgroundColor: ['#dc2626', '#16a34a', '#f59e0b'],
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
            cutout: '70%'
        }
    });
</script>

</html>
