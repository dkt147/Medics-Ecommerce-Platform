<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
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

    .card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .report-card {
        background: var(--card-bg);
        border-radius: 18px;
        border: 1px solid #e8edf5;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .report-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .report-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef3ff;
        font-size: 18px;
    }

    .report-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }

    .report-card-desc {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
        margin: 0;
    }

    .report-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid var(--border);
        color: var(--text-main);
    }

    .btn-outline.configure {
        min-width: 110px;
    }

    @media (max-width: 1200px) {
        .card-grid {
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
                <div class="page active" id="page-reports">
                    <div class="card-grid">
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">📅</span>
                                <h3 class="report-card-title">Daily COD Summary</h3>
                            </div>
                            <p class="report-card-desc">COD collected, remitted, and pending — broken down by date. Includes short remittance flags.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">📈</span>
                                <h3 class="report-card-title">Monthly P&L Report</h3>
                            </div>
                            <p class="report-card-desc">Full income statement for the selected calendar month, auto-generated from reconciled data.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">🚚</span>
                                <h3 class="report-card-title">Freight Invoice Reconciliation</h3>
                            </div>
                            <p class="report-card-desc">Billed vs expected charges per Leopard invoice. Highlights weight discrepancies and overcharges.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">↩️</span>
                                <h3 class="report-card-title">Returns Analysis Report</h3>
                            </div>
                            <p class="report-card-desc">Return rate by product, city, and reason. Financial impact of each return including freight cost.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">⏳</span>
                                <h3 class="report-card-title">COD Ageing Report</h3>
                            </div>
                            <p class="report-card-desc">Outstanding COD by age bucket: 0–7, 8–15, 16–30, 30+ days. Identifies overdue remittances.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">🧾</span>
                                <h3 class="report-card-title">Tax Summary Report</h3>
                            </div>
                            <p class="report-card-desc">Monthly output tax, input tax credit, WHT deducted, and net payable to FBR/PRA/SRB.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">📋</span>
                                <h3 class="report-card-title">Expense Ledger Report</h3>
                            </div>
                            <p class="report-card-desc">All operating expenses by category for the selected period. Includes per-order cost breakdown.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">🏛️</span>
                                <h3 class="report-card-title">Vendor Ledger — Leopard</h3>
                            </div>
                            <p class="report-card-desc">Complete running payable account with Leopard: all invoices, payments, and current balance.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">💰</span>
                                <h3 class="report-card-title">SKU-Level Profitability</h3>
                            </div>
                            <p class="report-card-desc">Gross margin and net margin per product/SKU. Identifies top performers and loss-makers.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">🏙️</span>
                                <h3 class="report-card-title">City-wise Delivery Report</h3>
                            </div>
                            <p class="report-card-desc">Orders, deliveries, returns, and COD breakdown by city. Includes delivery rate per region.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">⚡</span>
                                <h3 class="report-card-title">Variance / Exception Report</h3>
                            </div>
                            <p class="report-card-desc">Flags all anomalies: missing COD remittances, overcharged freight, return spikes, weight discrepancies.</p>
                            <div class="report-actions">
                                <button class="btn btn-primary">Excel</button>
                                <button class="btn btn-outline">PDF</button>
                            </div>
                        </div>
                        <div class="report-card">
                            <div class="report-card-header">
                                <span class="report-card-icon">➕</span>
                                <h3 class="report-card-title">Custom Report Builder</h3>
                            </div>
                            <p class="report-card-desc">Build a custom report by selecting dimensions, metrics, and date ranges.</p>
                            <div class="report-actions">
                                <button class="btn btn-outline configure">Configure</button>
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
    document.getElementById('page-title').textContent = 'Reports';
    document.getElementById('page-bread').textContent = 'Output / Reports';
</script>

</html>
