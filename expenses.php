<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Ledger</title>
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
        font-size: 24x;
        font-weight: 800;
        color: var(--text-main);
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

    .badge-yellow {
        background: var(--warning-bg);
        color: var(--warning);
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
                <div class="page active" id="page-expenses">
                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Total Operating Expenses</div>
                            <div class="kpi-value">PKR 5.28M</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Freight & Delivery</div>
                            <div class="kpi-value">PKR 2.84M</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Returns Freight</div>
                            <div class="kpi-value">PKR 0.42M</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Cost per Delivered Order</div>
                            <div class="kpi-value">PKR 1,238</div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📊</span> Expense Categories</div>
                            <div class="chart-box"><canvas id="expenseChart"></canvas></div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">🧾</span> Expense Ledger Detail</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>CATEGORY</th>
                                            <th>AMOUNT</th>
                                            <th>% OF TOTAL</th>
                                            <th>VS LAST MONTH</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Freight & Delivery</td>
                                            <td>PKR 2,84,000</td>
                                            <td>53.8%</td>
                                            <td><span class="badge badge-green">▲ 6%</span></td>
                                        </tr>
                                        <tr>
                                            <td>Return Freight</td>
                                            <td>PKR 42,000</td>
                                            <td>7.9%</td>
                                            <td><span class="badge badge-red">▲ 12%</span></td>
                                        </tr>
                                        <tr>
                                            <td>COD Collection Fee</td>
                                            <td>PKR 97,650</td>
                                            <td>18.5%</td>
                                            <td><span class="badge badge-green">▲ 8%</span></td>
                                        </tr>
                                        <tr>
                                            <td>Packaging & Materials</td>
                                            <td>PKR 38,400</td>
                                            <td>7.3%</td>
                                            <td><span class="badge badge-green">▼ 2%</span></td>
                                        </tr>
                                        <tr>
                                            <td>Warehouse / Storage</td>
                                            <td>PKR 25,000</td>
                                            <td>4.7%</td>
                                            <td><span class="badge badge-blue">No change</span></td>
                                        </tr>
                                        <tr>
                                            <td>Labour / Fulfilment</td>
                                            <td>PKR 30,000</td>
                                            <td>5.7%</td>
                                            <td><span class="badge badge-blue">No change</span></td>
                                        </tr>
                                        <tr>
                                            <td>Platform Fees (Shopify)</td>
                                            <td>PKR 9,800</td>
                                            <td>1.8%</td>
                                            <td><span class="badge badge-blue">No change</span></td>
                                        </tr>
                                        <tr>
                                            <td>Payment Gateway Fees</td>
                                            <td>PKR 2,400</td>
                                            <td>0.4%</td>
                                            <td><span class="badge badge-green">▲ 3%</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total</strong></td>
                                            <td><strong>PKR 5,28,250</strong></td>
                                            <td><strong>100%</strong></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
</body>
<script>
    document.getElementById('page-title').textContent = 'Expense Ledger';
    document.getElementById('page-bread').textContent = 'Finance / Expense Ledger';

    new Chart(document.getElementById('expenseChart'), {
        type: 'doughnut',
        data: {
            labels: ['Freight & Delivery', 'Return Freight', 'COD Fee', 'Packaging', 'Warehouse', 'Labour', 'Platform'],
            datasets: [{
                data: [53.8, 7.9, 18.5, 7.3, 4.7, 5.7, 1.8],
                backgroundColor: ['#1a73e8', '#dc2626', '#f97316', '#ef4444', '#0ea5e9', '#7c3aed', '#ec4899'],
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
            cutout: '68%'
        }
    });
</script>

</html>
