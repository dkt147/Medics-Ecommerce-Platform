<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

    .kpi-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 32px;
        opacity: 0.08;
    }

    /* ── GRID LAYOUTS ── */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .grid-2-1 {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    /* ── CHART CONTAINER ── */
    .chart-box {
        position: relative;
        height: 240px;
    }

    .chart-box.sm {
        height: 180px;
    }

    /* ── ALERT ── */
    .alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .alert-icon {
        font-size: 18px;
        flex-shrink: 0;
    }

    .alert-body {
        flex: 1;
    }

    .alert-title {
        font-weight: 700;
        margin-bottom: 2px;
    }

    .alert-sub {
        color: var(--text-muted);
        font-size: 12px;
    }

    .alert-red {
        background: var(--danger-bg);
        border-left: 3px solid var(--danger);
    }

    .alert-red .alert-title {
        color: var(--danger);
    }

    .alert-yellow {
        background: var(--warning-bg);
        border-left: 3px solid var(--warning);
    }

    .alert-yellow .alert-title {
        color: var(--warning);
    }

    .alert-blue {
        background: var(--primary-light);
        border-left: 3px solid var(--primary);
    }

    .alert-blue .alert-title {
        color: var(--primary);
    }

    /* ── STAT ROW ── */
    .stat-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .stat-row:last-child {
        border-bottom: none;
    }

    .stat-label {
        font-size: 13px;
        color: var(--text-muted);
    }

    .stat-value {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
    }

    .stat-value.green {
        color: var(--success);
    }

    hr.divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: 16px 0;
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

        .grid-2-1 {
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
                <div class="page active" id="page-dashboard">

                    <!-- KPI Row 1 -->
                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Total Orders (Month)</div>
                            <div class="kpi-value">5,432</div>
                            <div class="kpi-meta"><span class="kpi-up">▲ 12%</span> vs last month</div>
                            <div class="kpi-icon">📦</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Gross Revenue</div>
                            <div class="kpi-value sm">PKR 8.24M</div>
                            <div class="kpi-meta"><span class="kpi-up">▲ 8.4%</span> vs last month</div>
                            <div class="kpi-icon">💰</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Outstanding COD</div>
                            <div class="kpi-value sm">PKR 1.18M</div>
                            <div class="kpi-meta"><span class="kpi-down">↑ 3 days avg remittance</span></div>
                            <div class="kpi-icon">💵</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Net Profit Margin</div>
                            <div class="kpi-value">18.3%</div>
                            <div class="kpi-meta"><span class="kpi-down">▼ 1.2%</span> vs last month</div>
                            <div class="kpi-icon">📈</div>
                        </div>
                    </div>

                    <!-- KPI Row 2 -->
                    <div class="kpi-grid" style="grid-template-columns: repeat(4,1fr)">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Delivery Rate</div>
                            <div class="kpi-value">78.4%</div>
                            <div class="kpi-meta"><span class="kpi-up">▲ 2.1%</span> vs last month</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Return Rate</div>
                            <div class="kpi-value">14.9%</div>
                            <div class="kpi-meta"><span class="kpi-down">▲ 0.8%</span> vs last month</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">COD Collection Efficiency</div>
                            <div class="kpi-value">94.2%</div>
                            <div class="kpi-meta"><span class="kpi-up">▲ 0.5%</span> vs last month</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Disputed Charges</div>
                            <div class="kpi-value sm">PKR 340K</div>
                            <div class="kpi-meta">8 open disputes</div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📈</span> Net Revenue Trend (Daily)</div>
                            <div class="chart-box"><canvas id="revChart"></canvas></div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📊</span> Delivery vs Return Rate (%)</div>
                            <div class="chart-box"><canvas id="delRetChart"></canvas></div>
                        </div>
                    </div>

                    <!-- Alerts + COD Status -->
                    <div class="grid-2-1">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">⚠️</span> Active Anomalies &amp; Alerts</div>
                            <div class="alert alert-red">
                                <div class="alert-icon">🚨</div>
                                <div class="alert-body">
                                    <div class="alert-title">COD Remittance Overdue — 3 Batches</div>
                                    <div class="alert-sub">Batches from July 8, 11, 14 not remitted. Total: PKR
                                        4,21,000. Age: 8–14 days.</div>
                                </div>
                            </div>
                            <div class="alert alert-yellow">
                                <div class="alert-icon">⚡</div>
                                <div class="alert-body">
                                    <div class="alert-title">Weight Discrepancy — Leopard Invoice #LC-2607</div>
                                    <div class="alert-sub">Billed weight 12% higher than declared on 47 shipments.
                                        Estimated overcharge: PKR 38,400.</div>
                                </div>
                            </div>
                            <div class="alert alert-yellow">
                                <div class="alert-icon">📦</div>
                                <div class="alert-body">
                                    <div class="alert-title">Return Spike — Karachi Region</div>
                                    <div class="alert-sub">Return rate jumped to 24% in Karachi this week (normal: 14%).
                                        68 additional returns logged.</div>
                                </div>
                            </div>
                            <div class="alert alert-blue">
                                <div class="alert-icon">ℹ️</div>
                                <div class="alert-body">
                                    <div class="alert-title">Tax Filing Due — July 31, 2026</div>
                                    <div class="alert-sub">Estimated Sales Tax payable: PKR 1,23,600. WHT deducted: PKR
                                        42,180. Net payable: PKR 81,420.</div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">💵</span> COD Status</div>
                            <div class="chart-box sm"><canvas id="codDonut"></canvas></div>
                            <hr class="divider" />
                            <div class="stat-row"><span class="stat-label">Total COD Declared</span><span
                                    class="stat-value">PKR 6.91M</span></div>
                            <div class="stat-row"><span class="stat-label">Collected by Leopard</span><span
                                    class="stat-value green">PKR 6.51M</span></div>
                            <div class="stat-row"><span class="stat-label">Remitted to Bank</span><span
                                    class="stat-value green">PKR 5.33M</span></div>
                            <div class="stat-row"><span class="stat-label">In Transit</span><span class="stat-value"
                                    style="color:var(--warning)">PKR 1.18M</span></div>
                        </div>
                    </div>

                </div><!-- /page-dashboard -->
            </div><!-- /content -->

            <?php include 'includes/footer.php'; ?>

        </div><!-- /main -->
    </div><!-- /layout -->
</body>
<script>
    // ── CHART DEFAULTS (static demo data — API se replace hoga baad me) ──
    const PRIMARY = '#1a73e8';
    const SUCCESS = '#16a34a';
    const DANGER = '#dc2626';
    const WARNING = '#d97706';
    Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7a8d';

    function renderDashboardCharts() {
        const days = ['Jul 1', '3', '5', '7', '9', '11', '13', '15', '17', '19', '21'];
        new Chart(document.getElementById('revChart'), {
            type: 'line',
            data: {
                labels: days,
                datasets: [{
                    label: 'Net Revenue (PKR 000)',
                    data: [210, 245, 290, 270, 310, 340, 280, 330, 380, 360, 410],
                    borderColor: PRIMARY,
                    backgroundColor: 'rgba(26,115,232,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
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
        new Chart(document.getElementById('delRetChart'), {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3'],
                datasets: [{
                        label: 'Delivery Rate %',
                        data: [76.2, 79.1, 78.4],
                        backgroundColor: SUCCESS,
                        borderRadius: 6
                    },
                    {
                        label: 'Return Rate %',
                        data: [15.3, 14.1, 14.9],
                        backgroundColor: DANGER,
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
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
        new Chart(document.getElementById('codDonut'), {
            type: 'doughnut',
            data: {
                labels: ['Remitted', 'In Transit (COD)', 'Variance/Loss'],
                datasets: [{
                    data: [5330, 1180, 0],
                    backgroundColor: [SUCCESS, WARNING, DANGER],
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
    }
    renderDashboardCharts();
</script>

</html>