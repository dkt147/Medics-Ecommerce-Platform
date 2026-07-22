<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Sheet</title>
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
        --danger: #dc2626;
        --warning: #d97706;
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
        border: 1px solid #e8edf5;
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
        height: 4px;
    }

    .kpi-card.blue::before { background: var(--primary); }
    .kpi-card.red::before { background: var(--danger); }
    .kpi-card.green::before { background: var(--success); }
    .kpi-card.orange::before { background: var(--warning); }

    .kpi-label { font-size: 12px; color: var(--text-muted); font-weight: 600; }
    .kpi-value { font-size: 24px; font-weight: 800; color: var(--text-main); }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .balance-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .balance-card {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e8edf5;
        background: #f8fafc;
    }

    .balance-card-header {
        padding: 16px 20px;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        border-radius: 16px 16px 0 0;
    }

    .balance-card-header.assets { background: #2563eb; }
    .balance-card-header.liabilities { background: #dc2626; }
    .balance-card-header.equity { background: #16a34a; }

    .balance-body {
        padding: 0;
    }

    .balance-section {
        background: #fff;
        padding: 18px 20px;
    }

    .balance-section + .balance-section {
        border-top: 1px solid #e8edf5;
    }

    .section-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        margin-bottom: 12px;
    }

    .line-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #eef2f7;
        color: #475569;
        font-size: 13px;
    }

    .line-item:last-child { border-bottom: none; }

    .line-item.total {
        font-weight: 700;
        color: var(--text-main);
        border-top: 1px solid #e2e8f0;
        margin-top: 14px;
        padding-top: 16px;
    }

    .balance-total-row {
        background: #eff6ff;
        font-weight: 700;
        color: #1d4ed8;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 0 0 16px 16px;
        margin-top: 16px;
    }

    .line-value { min-width: 120px; text-align: right; }
    .line-value.negative { color: var(--danger); }

    @media (max-width: 1200px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .balance-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 1200px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .balance-grid { grid-template-columns: 1fr; }
    }
</style>

<body>
    <div class="layout">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main">
            <?php include 'includes/navbar.php'; ?>
            <div class="content">
                <div class="page active" id="page-bs">
                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Total Assets</div>
                            <div class="kpi-value">PKR 22.4M</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Total Liabilities</div>
                            <div class="kpi-value">PKR 8.7M</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Net Equity</div>
                            <div class="kpi-value">PKR 13.7M</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Current Ratio</div>
                            <div class="kpi-value">2.57</div>
                        </div>
                    </div>

                    <div class="balance-grid">
                        <div class="balance-card assets">
                            <div class="balance-card-header assets">ASSETS</div>
                            <div class="balance-body">
                                <div class="balance-section">
                                    <div class="section-label">Current Assets</div>
                                    <div class="line-item"><span>Cash & Bank Balances</span><span class="line-value">PKR 4,82,000</span></div>
                                    <div class="line-item"><span>COD Receivable (Leopard)</span><span class="line-value">PKR 11,80,000</span></div>
                                    <div class="line-item"><span>Accounts Receivable (Prepaid)</span><span class="line-value">PKR 1,24,000</span></div>
                                    <div class="line-item"><span>Inventory on Hand (at cost)</span><span class="line-value">PKR 8,40,000</span></div>
                                    <div class="line-item"><span>Inventory in Transit</span><span class="line-value">PKR 2,18,000</span></div>
                                    <div class="line-item"><span>Prepaid Expenses</span><span class="line-value">PKR 45,000</span></div>
                                    <div class="line-item total"><span>Total Current Assets</span><span class="line-value">PKR 28,89,000</span></div>
                                </div>
                                <div class="balance-section">
                                    <div class="section-label">Non-current Assets</div>
                                    <div class="line-item"><span>Fixed Assets (net)</span><span class="line-value">PKR 5,50,000</span></div>
                                    <div class="line-item total"><span>Total Assets</span><span class="line-value">PKR 34,39,000</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="balance-card liabilities">
                            <div class="balance-card-header liabilities">LIABILITIES</div>
                            <div class="balance-body">
                                <div class="balance-section">
                                    <div class="section-label">Current Liabilities</div>
                                    <div class="line-item"><span>Accounts Payable (Leopard)</span><span class="line-value">PKR 5,34,800</span></div>
                                    <div class="line-item"><span>Tax Payable (Net ST)</span><span class="line-value">PKR 81,420</span></div>
                                    <div class="line-item"><span>Customer Refunds Payable</span><span class="line-value">PKR 38,400</span></div>
                                    <div class="line-item"><span>Deferred Revenue</span><span class="line-value">PKR 98,000</span></div>
                                    <div class="line-item total"><span>Total Current Liabilities</span><span class="line-value">PKR 7,52,620</span></div>
                                </div>
                                <div class="balance-section">
                                    <div class="section-label">Long-term Liabilities</div>
                                    <div class="line-item"><span>Long-term Liabilities</span><span class="line-value">PKR 1,20,000</span></div>
                                    <div class="line-item total"><span>Total Liabilities</span><span class="line-value">PKR 8,72,620</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="balance-card equity">
                            <div class="balance-card-header equity">EQUITY</div>
                            <div class="balance-body">
                                <div class="balance-section">
                                    <div class="line-item"><span>Owner's Capital</span><span class="line-value">PKR 12,30,050</span></div>
                                    <div class="line-item"><span>Retained Earnings</span><span class="line-value">PKR 14,37,330</span></div>
                                    <div class="line-item"><span>Less: Drawings</span><span class="line-value negative">-PKR 1,02,000</span></div>
                                    <div class="line-item total"><span>Total Equity</span><span class="line-value">PKR 25,65,380</span></div>
                                </div>
                                <div class="balance-total-row"><span>Total Liabilities + Equity</span><span class="line-value">PKR 34,38,000</span></div>
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
    document.getElementById('page-title').textContent = 'Balance Sheet';
    document.getElementById('page-bread').textContent = 'Finance / Balance Sheet';
</script>

</html>
