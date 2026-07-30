<?php
require_once __DIR__ . '/includes/data_helpers.php';

$excelFilePath = __DIR__ . '/upload/orders_data.xlsx';
$rows = loadOrdersRows($excelFilePath, getDefaultOrdersRows());
$metrics = getOrderMetrics($rows);
$chargeEstimate = $metrics['cod_declared'] * 0.08;
$expectedCharge = $chargeEstimate * 0.95;
$overcharge = max(0, $chargeEstimate - $expectedCharge);
$openDisputes = max(1, $metrics['returned_count']);
$chargeBreakLabels = ['Delivery Charges', 'Return Handling', 'Pending'];
$chargeBreakData = [max(1, $metrics['delivered_count']), max(1, $metrics['returned_count']), max(1, $metrics['pending_count'])];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier Charges</title>
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

    /* ── GRID ── */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
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
                <div class="page active" id="page-charges">

                    <!-- KPI Row -->
                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Total Billed</div>
                            <div class="kpi-value sm"><?php echo formatCurrency($chargeEstimate); ?></div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Expected (Rate Card)</div>
                            <div class="kpi-value sm"><?php echo formatCurrency($expectedCharge); ?></div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Overcharge Detected</div>
                            <div class="kpi-value sm"><?php echo formatCurrency($overcharge); ?></div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Open Disputes</div>
                            <div class="kpi-value"><?php echo $openDisputes; ?></div>
                        </div>
                    </div>

                    <!-- Invoice Validation + Charge Breakdown -->
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📋</span> Leopard Invoice Validation</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Invoice No.</th>
                                            <th>Date</th>
                                            <th>Shipments</th>
                                            <th>Billed</th>
                                            <th>Expected</th>
                                            <th>Variance</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>LC-2607-03</b></td>
                                            <td>Jul 21</td>
                                            <td>312</td>
                                            <td>PKR 2,18,400</td>
                                            <td>PKR 2,12,100</td>
                                            <td><span class="badge badge-red">+PKR 6,300</span></td>
                                            <td><span class="badge badge-red">Disputed</span></td>
                                        </tr>
                                        <tr>
                                            <td><b>LC-2607-02</b></td>
                                            <td>Jul 14</td>
                                            <td>289</td>
                                            <td>PKR 1,94,000</td>
                                            <td>PKR 1,93,600</td>
                                            <td><span class="badge badge-yellow">+PKR 400</span></td>
                                            <td><span class="badge badge-green">Accepted</span></td>
                                        </tr>
                                        <tr>
                                            <td><b>LC-2607-01</b></td>
                                            <td>Jul 7</td>
                                            <td>341</td>
                                            <td>PKR 2,29,600</td>
                                            <td>PKR 2,18,300</td>
                                            <td><span class="badge badge-red">+PKR 11,300</span></td>
                                            <td><span class="badge badge-red">Disputed</span></td>
                                        </tr>
                                        <tr>
                                            <td><b>LC-2606-04</b></td>
                                            <td>Jun 28</td>
                                            <td>298</td>
                                            <td>PKR 2,00,000</td>
                                            <td>PKR 2,00,000</td>
                                            <td><span class="badge badge-green">PKR 0</span></td>
                                            <td><span class="badge badge-green">Reconciled</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">⚖️</span> Charge Breakdown</div>
                            <div class="chart-box"><canvas id="chargeBreakChart"></canvas></div>
                        </div>
                    </div>

                    <!-- Vendor Ledger -->
                    <div class="card">
                        <div class="card-title"><span class="ct-icon">🏦</span> Vendor Ledger — Leopard Courier</div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Jul 01</td>
                                        <td>Opening Balance</td>
                                        <td>—</td>
                                        <td>—</td>
                                        <td><b>PKR 1,22,400</b></td>
                                    </tr>
                                    <tr>
                                        <td>Jul 07</td>
                                        <td>Invoice LC-2607-01</td>
                                        <td>PKR 2,29,600</td>
                                        <td>—</td>
                                        <td>PKR 3,52,000</td>
                                    </tr>
                                    <tr>
                                        <td>Jul 10</td>
                                        <td>Payment — Bank Transfer</td>
                                        <td>—</td>
                                        <td>PKR 2,29,600</td>
                                        <td>PKR 1,22,400</td>
                                    </tr>
                                    <tr>
                                        <td>Jul 14</td>
                                        <td>Invoice LC-2607-02</td>
                                        <td>PKR 1,94,000</td>
                                        <td>—</td>
                                        <td>PKR 3,16,400</td>
                                    </tr>
                                    <tr>
                                        <td>Jul 21</td>
                                        <td>Invoice LC-2607-03</td>
                                        <td>PKR 2,18,400</td>
                                        <td>—</td>
                                        <td>PKR 5,34,800</td>
                                    </tr>
                                    <tr style="font-weight:700; background:#fef3c7">
                                        <td colspan="4">Current Balance Payable</td>
                                        <td>PKR 5,34,800</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div><!-- /page-charges -->
            </div><!-- /content -->

            <?php include 'includes/footer.php'; ?>

        </div><!-- /main -->
    </div><!-- /layout -->
</body>
<script>
    // ── Page title/breadcrumb (navbar shared hai, is liye yahan override kar rahe hain) ──
    document.getElementById('page-title').textContent = 'Courier Charges';
    document.getElementById('page-bread').textContent = 'Modules / Courier Charges';
    // ── CHART (static demo data — API se replace hoga baad me) ──
    Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7a8d';
    const PRIMARY = '#1a73e8';
    const WARNING = '#d97706';
    const INFO = '#0891b2';
    const DANGER = '#dc2626';
    const GREY = '#94a3b8';
    const chargeBreakLabels = <?php echo json_encode($chargeBreakLabels); ?>;
    const chargeBreakData = <?php echo json_encode($chargeBreakData); ?>;

    new Chart(document.getElementById('chargeBreakChart'), {
        type: 'doughnut',
        data: {
            labels: chargeBreakLabels,
            datasets: [{
                data: chargeBreakData,
                backgroundColor: [PRIMARY, WARNING, INFO],
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