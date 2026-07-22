<!DOCTYPE html>
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
                            <div class="kpi-value">5,432</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Delivered</div>
                            <div class="kpi-value">4,261</div>
                            <div class="kpi-meta kpi-up">78.4% rate</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Returned</div>
                            <div class="kpi-value">808</div>
                            <div class="kpi-meta kpi-down">14.9% rate</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">In Transit</div>
                            <div class="kpi-value">363</div>
                        </div>
                    </div>

                    <!-- Shipment Register -->
                    <div class="card mb20">
                        <div class="card-title"><span class="ct-icon">📦</span> Shipment Register</div>
                        <div class="filter-bar">
                            <input type="text" class="filter-input" placeholder="🔍  Search tracking / order ID..."
                                style="width:240px" />
                            <select class="filter-input">
                                <option>All Status</option>
                                <option>Delivered</option>
                                <option>In Transit</option>
                                <option>Returned</option>
                                <option>Pending</option>
                                <option>Cancelled</option>
                            </select>
                            <select class="filter-input">
                                <option>All Cities</option>
                                <option>Karachi</option>
                                <option>Lahore</option>
                                <option>Islamabad</option>
                                <option>Faisalabad</option>
                                <option>Rawalpindi</option>
                            </select>
                            <input type="date" class="filter-input" value="2026-07-01" />
                            <input type="date" class="filter-input" value="2026-07-22" />
                            <button class="btn btn-primary">Apply</button>
                            <button class="btn btn-outline" style="margin-left:auto">⬇️ Export</button>
                        </div>
                        <div class="table-wrap">
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
                                        <th>Delivery Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><b>LP0012847263</b></td>
                                        <td>ORD-9812</td>
                                        <td>Jul 20</td>
                                        <td>Ahmed Raza</td>
                                        <td>Karachi</td>
                                        <td>1.2 kg</td>
                                        <td>PKR 2,500</td>
                                        <td><span class="badge badge-green">Delivered</span></td>
                                        <td>Jul 22</td>
                                    </tr>
                                    <tr>
                                        <td><b>LP0012846901</b></td>
                                        <td>ORD-9811</td>
                                        <td>Jul 20</td>
                                        <td>Fatima Malik</td>
                                        <td>Lahore</td>
                                        <td>0.8 kg</td>
                                        <td>PKR 1,800</td>
                                        <td><span class="badge badge-green">Delivered</span></td>
                                        <td>Jul 21</td>
                                    </tr>
                                    <tr>
                                        <td><b>LP0012845234</b></td>
                                        <td>ORD-9808</td>
                                        <td>Jul 19</td>
                                        <td>Usman Khan</td>
                                        <td>Islamabad</td>
                                        <td>2.5 kg</td>
                                        <td>PKR 5,200</td>
                                        <td><span class="badge badge-yellow">In Transit</span></td>
                                        <td>—</td>
                                    </tr>
                                    <tr>
                                        <td><b>LP0012843190</b></td>
                                        <td>ORD-9803</td>
                                        <td>Jul 18</td>
                                        <td>Aisha Siddiqui</td>
                                        <td>Rawalpindi</td>
                                        <td>1.0 kg</td>
                                        <td>PKR 3,100</td>
                                        <td><span class="badge badge-red">Returned</span></td>
                                        <td>—</td>
                                    </tr>
                                    <tr>
                                        <td><b>LP0012841000</b></td>
                                        <td>ORD-9799</td>
                                        <td>Jul 18</td>
                                        <td>Bilal Hussain</td>
                                        <td>Faisalabad</td>
                                        <td>3.1 kg</td>
                                        <td>PKR 7,800</td>
                                        <td><span class="badge badge-green">Delivered</span></td>
                                        <td>Jul 20</td>
                                    </tr>
                                    <tr>
                                        <td><b>LP0012839400</b></td>
                                        <td>ORD-9795</td>
                                        <td>Jul 17</td>
                                        <td>Sara Iqbal</td>
                                        <td>Multan</td>
                                        <td>0.5 kg</td>
                                        <td>—</td>
                                        <td><span class="badge badge-blue">Prepaid</span></td>
                                        <td>Jul 19</td>
                                    </tr>
                                    <tr>
                                        <td><b>LP0012837100</b></td>
                                        <td>ORD-9790</td>
                                        <td>Jul 17</td>
                                        <td>Hassan Butt</td>
                                        <td>Karachi</td>
                                        <td>1.8 kg</td>
                                        <td>PKR 4,400</td>
                                        <td><span class="badge badge-grey">Pending</span></td>
                                        <td>—</td>
                                    </tr>
                                    <tr>
                                        <td><b>LP0012834200</b></td>
                                        <td>ORD-9785</td>
                                        <td>Jul 16</td>
                                        <td>Zainab Noor</td>
                                        <td>Lahore</td>
                                        <td>0.9 kg</td>
                                        <td>PKR 2,200</td>
                                        <td><span class="badge badge-red">Returned</span></td>
                                        <td>—</td>
                                    </tr>
                                </tbody>
                            </table>
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
    // ── CHART DEFAULTS (static demo data — API se replace hoga baad me) ──
    const SUCCESS = '#16a34a';
    const DANGER = '#dc2626';
    const WARNING = '#d97706';
    const INFO = '#0891b2';
    const GREY = '#94a3b8';
    const palette = ['#1a73e8', '#16a34a', '#d97706', '#dc2626', '#0891b2', '#7c3aed', '#db2777'];
    Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7a8d';
    new Chart(document.getElementById('cityChart'), {
        type: 'bar',
        data: {
            labels: ['Karachi', 'Lahore', 'Islamabad', 'Faisalabad', 'Rawalpindi', 'Multan', 'Others'],
            datasets: [{
                label: 'Orders',
                data: [1840, 1220, 680, 540, 380, 290, 482],
                backgroundColor: palette,
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
            labels: ['Delivered', 'In Transit', 'Returned', 'Pending', 'Cancelled'],
            datasets: [{
                data: [4261, 363, 808, 120, 80],
                backgroundColor: [SUCCESS, INFO, DANGER, WARNING, GREY],
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