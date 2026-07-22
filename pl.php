<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P&L Statement</title>
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
        gap: 8px;
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

    .kpi-card.blue::before { background: var(--primary); }
    .kpi-card.green::before { background: var(--success); }
    .kpi-card.orange::before { background: var(--warning); }
    .kpi-card.red::before { background: var(--danger); }

    .kpi-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
    .kpi-value { font-size: 24px; font-weight: 800; color: var(--text-main); }

    .grid-2 {
        display: grid;
        grid-template-columns: 2fr 1fr;
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
        padding: 12px 14px;
        text-align: left;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border);
    }

    tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-main);
    }

    tbody tr:last-child td { border-bottom: none; }

    .statement-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e8edf5;
        overflow: hidden;
    }

    .statement-card .card-title {
        padding: 20px 24px;
        margin-bottom: 0;
    }

    .statement-group-header {
        display: block;
        background: #eef4ff;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        padding: 14px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .statement-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 24px;
        font-size: 13px;
        color: #475569;
        border-bottom: 1px solid #f1f5f9;
    }

    .statement-row:last-child { border-bottom: none; }

    .statement-row span { display: inline-block; }
    .statement-row .statement-label { color: #475569; }
    .statement-row .statement-value { min-width: 140px; text-align: right; font-weight: 700; }

    .statement-row.negative .statement-value { color: var(--danger); }
    .statement-row.positive .statement-value { color: var(--success); }
    .statement-row.neutral .statement-value { color: var(--text-main); }

    .statement-row.total {
        color: var(--text-main);
        font-weight: 700;
        border-top: 1px solid #e2e8f0;
        margin-top: 4px;
        padding-top: 16px;
    }

    .statement-row.final {
        background: #e0f2fe;
        color: #1d4ed8;
        font-weight: 700;
        border-bottom: none;
    }

    .statement-row.final .statement-value { color: #1d4ed8; }

    .chart-box { position: relative; height: 320px; }

    .chart-box { position: relative; height: 320px; }

    .metric-list {
        margin-top: 20px;
        display: grid;
        gap: 10px;
    }

    .metric-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: var(--text-muted);
    }

    .metric-row strong { color: var(--text-main); }

    @media (max-width: 1200px) {
        .grid-2 { grid-template-columns: 1fr; }
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<body>
    <div class="layout">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main">
            <?php include 'includes/navbar.php'; ?>
            <div class="content">
                <div class="page active" id="page-pl">
                    <div class="grid-2">
                        <div class="statement-card card">
                            <div class="card-title"><span class="ct-icon">📊</span> Profit & Loss Statement — July 2026</div>
                            <span class="statement-group-header">Revenue</span>
                            <div class="statement-row neutral"><span class="statement-label">Gross Revenue (Delivered Orders)</span><span class="statement-value">PKR 82,40,000</span></div>
                            <div class="statement-row negative"><span class="statement-label">Less: Returns & Refunds</span><span class="statement-value">-PKR 12,10,000</span></div>
                            <div class="statement-row total positive"><span class="statement-label"><strong>Net Revenue</strong></span><span class="statement-value">PKR 70,30,000</span></div>
                            <span class="statement-group-header">Cost of Goods Sold</span>
                            <div class="statement-row negative"><span class="statement-label">COGS (Product Cost)</span><span class="statement-value">-PKR 42,18,000</span></div>
                            <div class="statement-row total positive"><span class="statement-label"><strong>Gross Profit</strong></span><span class="statement-value">PKR 28,12,000</span></div>
                            <span class="statement-group-header">Operating Expenses</span>
                            <div class="statement-row negative"><span class="statement-label">Freight & Delivery Costs</span><span class="statement-value">-PKR 2,84,000</span></div>
                            <div class="statement-row negative"><span class="statement-label">Return Freight Costs</span><span class="statement-value">-PKR 42,000</span></div>
                            <div class="statement-row negative"><span class="statement-label">COD Collection Charges</span><span class="statement-value">-PKR 97,650</span></div>
                            <div class="statement-row negative"><span class="statement-label">Packaging & Materials</span><span class="statement-value">-PKR 38,400</span></div>
                            <div class="statement-row negative"><span class="statement-label">Warehouse & Labour</span><span class="statement-value">-PKR 55,000</span></div>
                            <div class="statement-row negative"><span class="statement-label">Platform & Gateway Fees</span><span class="statement-value">-PKR 12,200</span></div>
                            <div class="statement-row total positive"><span class="statement-label"><strong>Operating Profit (EBIT)</strong></span><span class="statement-value">PKR 15,18,750</span></div>
                            <span class="statement-group-header">Tax</span>
                            <div class="statement-row negative"><span class="statement-label">Net Tax Expense (Sales Tax)</span><span class="statement-value">-PKR 81,420</span></div>
                            <div class="statement-row final"><span class="statement-label"><strong>Net Profit / (Loss)</strong></span><span class="statement-value">PKR 14,37,330</span></div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📈</span> Margin Waterfall</div>
                            <div class="chart-box"><canvas id="marginChart"></canvas></div>
                            <div class="metric-list">
                                <div class="metric-row"><span>Gross Margin %</span><strong>40.0%</strong></div>
                                <div class="metric-row"><span>EBIT Margin %</span><strong>21.6%</strong></div>
                                <div class="metric-row"><span>Net Margin %</span><strong>20.4%</strong></div>
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
    document.getElementById('page-title').textContent = 'P&L Statement';
    document.getElementById('page-bread').textContent = 'Finance / P&L Statement';

    new Chart(document.getElementById('marginChart'), {
        type: 'bar',
        data: {
            labels: ['Net Revenue', 'Gross Profit', 'EBIT', 'Net Profit'],
            datasets: [{
                data: [7030000, 2812000, 1518750, 1437330],
                backgroundColor: ['#0ea5e9', '#16a34a', '#f59e0b', '#22c55e'],
                borderRadius: 8,
                maxBarThickness: 60
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
</script>

</html>
