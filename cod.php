<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COD Reconciliation</title>
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

    .mb16 {
        margin-bottom: 16px;
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

    /* ── TABS ── */
    .tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid var(--border);
        margin-bottom: 20px;
    }

    .tab {
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.15s;
    }

    .tab:hover {
        color: var(--primary);
    }

    .tab.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
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

    .btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.15s;
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
    }
</style>

<body>
    <div class="layout">

        <?php include 'includes/sidebar.php'; ?>

        <div class="main">

            <?php include 'includes/navbar.php'; ?>

            <div class="content">
                <div class="page active" id="page-cod">

                    <!-- KPI Row -->
                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">COD Declared</div>
                            <div class="kpi-value sm">PKR 6.91M</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">COD Collected</div>
                            <div class="kpi-value sm">PKR 6.51M</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Remitted to Bank</div>
                            <div class="kpi-value sm">PKR 5.33M</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Outstanding COD</div>
                            <div class="kpi-value sm">PKR 1.18M</div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="tabs">
                        <div class="tab active" onclick="switchTab(this,'cod-variance')">Variance Report</div>
                        <div class="tab" onclick="switchTab(this,'cod-remittance')">Remittance Tracking</div>
                        <div class="tab" onclick="switchTab(this,'cod-ageing')">COD Ageing</div>
                        <div class="tab" onclick="switchTab(this,'cod-deductions')">Deductions</div>
                    </div>

                    <!-- TAB: Variance Report -->
                    <div class="tab-content active" id="tab-cod-variance">
                        <div class="card mb16">
                            <div class="card-title"><span class="ct-icon">📊</span> Daily COD Declared vs Collected
                            </div>
                            <div class="chart-box"><canvas id="codVarChart"></canvas></div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">⚡</span> Variance Exceptions</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Tracking No.</th>
                                            <th>COD Declared</th>
                                            <th>COD Collected</th>
                                            <th>Variance</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Jul 18</td>
                                            <td>LP0012843190</td>
                                            <td>PKR 3,100</td>
                                            <td>PKR 2,800</td>
                                            <td><span class="badge badge-red">-PKR 300</span></td>
                                            <td><span class="badge badge-yellow">Pending</span></td>
                                            <td><button class="btn btn-outline btn-sm">Raise Dispute</button></td>
                                        </tr>
                                        <tr>
                                            <td>Jul 17</td>
                                            <td>LP0012838900</td>
                                            <td>PKR 5,500</td>
                                            <td>PKR 5,500</td>
                                            <td><span class="badge badge-green">PKR 0</span></td>
                                            <td><span class="badge badge-green">Matched</span></td>
                                            <td>—</td>
                                        </tr>
                                        <tr>
                                            <td>Jul 15</td>
                                            <td>LP0012830110</td>
                                            <td>PKR 7,200</td>
                                            <td>PKR 7,400</td>
                                            <td><span class="badge badge-blue">+PKR 200</span></td>
                                            <td><span class="badge badge-blue">Excess</span></td>
                                            <td><button class="btn btn-outline btn-sm">Review</button></td>
                                        </tr>
                                        <tr>
                                            <td>Jul 14</td>
                                            <td>LP0012826400</td>
                                            <td>PKR 4,400</td>
                                            <td>PKR 3,900</td>
                                            <td><span class="badge badge-red">-PKR 500</span></td>
                                            <td><span class="badge badge-red">Disputed</span></td>
                                            <td><button class="btn btn-outline btn-sm">View Dispute</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: Remittance Tracking -->
                    <div class="tab-content" id="tab-cod-remittance">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">🏦</span> Remittance History</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Remittance Slip ID</th>
                                            <th>Date</th>
                                            <th>COD Batch Period</th>
                                            <th>Collected Amount</th>
                                            <th>Deductions</th>
                                            <th>Net Remitted</th>
                                            <th>Bank Receipt</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>REM-LC-2607-14</b></td>
                                            <td>Jul 22</td>
                                            <td>Jul 12–16</td>
                                            <td>PKR 4,32,000</td>
                                            <td>PKR 28,400</td>
                                            <td>PKR 4,03,600</td>
                                            <td>✅ Matched</td>
                                            <td><span class="badge badge-green">Reconciled</span></td>
                                        </tr>
                                        <tr>
                                            <td><b>REM-LC-2607-11</b></td>
                                            <td>Jul 18</td>
                                            <td>Jul 8–11</td>
                                            <td>PKR 5,18,000</td>
                                            <td>PKR 33,200</td>
                                            <td>PKR 4,84,800</td>
                                            <td>✅ Matched</td>
                                            <td><span class="badge badge-green">Reconciled</span></td>
                                        </tr>
                                        <tr>
                                            <td><b>REM-LC-2607-08</b></td>
                                            <td>—</td>
                                            <td>Jul 5–8</td>
                                            <td>PKR 4,21,000</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td>⏳ Awaiting</td>
                                            <td><span class="badge badge-red">Overdue</span></td>
                                        </tr>
                                        <tr>
                                            <td><b>REM-LC-2606-28</b></td>
                                            <td>Jul 5</td>
                                            <td>Jun 25–28</td>
                                            <td>PKR 3,87,500</td>
                                            <td>PKR 24,100</td>
                                            <td>PKR 3,63,400</td>
                                            <td>✅ Matched</td>
                                            <td><span class="badge badge-green">Reconciled</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: COD Ageing -->
                    <div class="tab-content" id="tab-cod-ageing">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">⏳</span> COD Ageing Report</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Age Bucket</th>
                                            <th>No. of Shipments</th>
                                            <th>COD Amount</th>
                                            <th>% of Outstanding</th>
                                            <th>Risk Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>0–7 Days</td>
                                            <td>94</td>
                                            <td>PKR 3,82,000</td>
                                            <td>32.4%</td>
                                            <td><span class="badge badge-green">Low</span></td>
                                        </tr>
                                        <tr>
                                            <td>8–15 Days</td>
                                            <td>67</td>
                                            <td>PKR 4,21,000</td>
                                            <td>35.7%</td>
                                            <td><span class="badge badge-yellow">Medium</span></td>
                                        </tr>
                                        <tr>
                                            <td>16–30 Days</td>
                                            <td>31</td>
                                            <td>PKR 2,94,000</td>
                                            <td>24.9%</td>
                                            <td><span class="badge badge-red">High</span></td>
                                        </tr>
                                        <tr>
                                            <td>30+ Days</td>
                                            <td>8</td>
                                            <td>PKR 83,000</td>
                                            <td>7.0%</td>
                                            <td><span class="badge badge-red">Critical</span></td>
                                        </tr>
                                        <tr style="font-weight:700">
                                            <td>Total</td>
                                            <td>200</td>
                                            <td>PKR 11,80,000</td>
                                            <td>100%</td>
                                            <td>—</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: Deductions -->
                    <div class="tab-content" id="tab-cod-deductions">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📋</span> COD Deductions Breakdown (July 2026)
                            </div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Deduction Type</th>
                                            <th>Rate / Basis</th>
                                            <th>Amount</th>
                                            <th>% of COD Collected</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Base Freight Charge</td>
                                            <td>Per shipment</td>
                                            <td>PKR 2,41,800</td>
                                            <td>3.71%</td>
                                        </tr>
                                        <tr>
                                            <td>COD Service Fee</td>
                                            <td>1.5% of COD</td>
                                            <td>PKR 97,650</td>
                                            <td>1.50%</td>
                                        </tr>
                                        <tr>
                                            <td>Fuel Surcharge</td>
                                            <td>Per shipment</td>
                                            <td>PKR 48,200</td>
                                            <td>0.74%</td>
                                        </tr>
                                        <tr>
                                            <td>Withholding Income Tax (WHT IT)</td>
                                            <td>2% of COD</td>
                                            <td>PKR 1,30,200</td>
                                            <td>2.00%</td>
                                        </tr>
                                        <tr>
                                            <td>Withholding Sales Tax (WHT ST)</td>
                                            <td>2% of COD</td>
                                            <td>PKR 1,30,200</td>
                                            <td>2.00%</td>
                                        </tr>
                                        <tr style="font-weight:700">
                                            <td>Total Deductions</td>
                                            <td>—</td>
                                            <td>PKR 6,48,050</td>
                                            <td>9.95%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div><!-- /page-cod -->
            </div><!-- /content -->

            <?php include 'includes/footer.php'; ?>

        </div><!-- /main -->
    </div><!-- /layout -->
</body>
<script>
    // ── Page title/breadcrumb (navbar shared hai, is liye yahan override kar rahe hain) ──
    document.getElementById('page-title').textContent = 'COD Reconciliation';
    document.getElementById('page-bread').textContent = 'Modules / COD Reconciliation';
    // ── TABS ────────────────────────────────────────
    function switchTab(tabEl, contentId) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        tabEl.classList.add('active');
        const target = document.getElementById('tab-' + contentId);
        if (target) target.classList.add('active');
    }
    // ── CHART (static demo data — API se replace hoga baad me) ──
    Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7a8d';
    const days = ['Jul 1', '3', '5', '7', '9', '11', '13', '15', '17', '19', '21'];
    new Chart(document.getElementById('codVarChart'), {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                    label: 'COD Declared',
                    data: [310, 420, 380, 440, 510, 480, 390, 450, 530, 490, 560],
                    backgroundColor: 'rgba(26,115,232,0.7)',
                    borderRadius: 4
                },
                {
                    label: 'COD Collected',
                    data: [302, 415, 374, 438, 501, 476, 385, 445, 523, 484, 551],
                    backgroundColor: 'rgba(22,163,74,0.7)',
                    borderRadius: 4
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
</script>

</html>