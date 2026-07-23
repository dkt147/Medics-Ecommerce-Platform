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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    document.getElementById('page-title').textContent = 'Reports';
    document.getElementById('page-bread').textContent = 'Output / Reports';

    const reportData = {
        'Daily COD Summary': [
            { date: '2026-07-01', collected: 125000, remitted: 118000, pending: 7000, note: 'Short remittance' },
            { date: '2026-07-02', collected: 132500, remitted: 132500, pending: 0, note: 'Cleared' },
            { date: '2026-07-03', collected: 98000, remitted: 90000, pending: 8000, note: 'Pending review' }
        ],
        'Monthly P&L Report': [
            { month: '2026-07', revenue: 2450000, expense: 1750000, profit: 700000, margin: '28.6%' },
            { month: '2026-06', revenue: 2280000, expense: 1680000, profit: 600000, margin: '26.3%' },
            { month: '2026-05', revenue: 2140000, expense: 1600000, profit: 540000, margin: '25.2%' }
        ],
        'Freight Invoice Reconciliation': [
            { invoice: 'LF-1001', billed: 1860, expected: 1740, variance: 120, status: 'Overcharged' },
            { invoice: 'LF-1002', billed: 1580, expected: 1580, variance: 0, status: 'Matched' },
            { invoice: 'LF-1003', billed: 2210, expected: 2060, variance: 150, status: 'Review needed' }
        ],
        'Returns Analysis Report': [
            { sku: 'SKU-101', city: 'Lahore', returns: 18, rate: '9.2%', freight_cost: 5400, impact: 12000 },
            { sku: 'SKU-205', city: 'Karachi', returns: 12, rate: '6.8%', freight_cost: 3600, impact: 9500 },
            { sku: 'SKU-310', city: 'Islamabad', returns: 9, rate: '5.1%', freight_cost: 2900, impact: 7600 }
        ],
        'COD Ageing Report': [
            { bucket: '0-7 Days', outstanding: 180000, count: 22, overdue: 'No' },
            { bucket: '8-15 Days', outstanding: 96000, count: 14, overdue: 'No' },
            { bucket: '16-30 Days', outstanding: 135000, count: 17, overdue: 'Yes' },
            { bucket: '30+ Days', outstanding: 72000, count: 9, overdue: 'Yes' }
        ],
        'Tax Summary Report': [
            { month: '2026-07', output_tax: 410000, input_tax: 296000, wht: 24000, net_payable: 90000 },
            { month: '2026-06', output_tax: 388000, input_tax: 280000, wht: 22000, net_payable: 86000 },
            { month: '2026-05', output_tax: 372000, input_tax: 271000, wht: 21000, net_payable: 80000 }
        ],
        'Expense Ledger Report': [
            { category: 'Freight', amount: 184000, orders: 120, avg_cost: 1533 },
            { category: 'Packaging', amount: 76000, orders: 120, avg_cost: 633 },
            { category: 'Marketing', amount: 95000, orders: 120, avg_cost: 792 }
        ],
        'Vendor Ledger — Leopard': [
            { invoice: 'INV-2401', date: '2026-07-05', debit: 118000, payment: 90000, balance: 28000 },
            { invoice: 'INV-2402', date: '2026-07-10', debit: 76000, payment: 76000, balance: 0 },
            { invoice: 'INV-2403', date: '2026-07-15', debit: 93000, payment: 50000, balance: 43000 }
        ],
        'SKU-Level Profitability': [
            { sku: 'SKU-001', units: 420, revenue: 1680000, cogs: 1210000, gross_margin: '28.0%' },
            { sku: 'SKU-002', units: 310, revenue: 1240000, cogs: 930000, gross_margin: '25.0%' },
            { sku: 'SKU-003', units: 250, revenue: 1000000, cogs: 680000, gross_margin: '32.0%' }
        ],
        'City-wise Delivery Report': [
            { city: 'Lahore', orders: 420, delivered: 402, returns: 18, cod: 185000 },
            { city: 'Karachi', orders: 360, delivered: 348, returns: 12, cod: 162000 },
            { city: 'Islamabad', orders: 280, delivered: 273, returns: 9, cod: 124000 }
        ],
        'Variance / Exception Report': [
            { issue: 'Missing COD remittance', count: 5, impact: 42000, severity: 'High' },
            { issue: 'Weight discrepancy', count: 3, impact: 15000, severity: 'Medium' },
            { issue: 'Return spike', count: 2, impact: 22000, severity: 'High' }
        ],
        'Custom Report Builder': [
            { dimension: 'Date', metric: 'Orders', value: 1520, note: 'Sample build' },
            { dimension: 'City', metric: 'COD', value: 471000, note: 'Sample build' }
        ]
    };

    function slugify(value) {
        return String(value)
            .toLowerCase()
            .replace(/&/g, 'and')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    }

    function escapeCsv(value) {
        const text = String(value ?? '');
        return /[",\n]/.test(text) ? `"${text.replace(/"/g, '""')}"` : text;
    }

    function exportToExcel(reportTitle, rows) {
        if (!rows.length) {
            rows = [{ report: reportTitle, note: 'No dummy data available' }];
        }

        const headers = Object.keys(rows[0]);
        const csvRows = [headers.join(',')];
        rows.forEach((row) => {
            csvRows.push(headers.map((key) => escapeCsv(row[key])).join(','));
        });

        const blob = new Blob([csvRows.join('\n')], { type: 'application/vnd.ms-excel;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `${slugify(reportTitle)}.xls`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }

    function exportToPdf(reportTitle, rows) {
        if (!rows.length) {
            rows = [{ report: reportTitle, note: 'No dummy data available' }];
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const headers = Object.keys(rows[0]);
        let y = 18;

        doc.setFontSize(16);
        doc.text(reportTitle, 14, y);
        y += 8;
        doc.setFontSize(10);
        doc.text(headers.join(' | '), 14, y);
        y += 6;

        rows.forEach((row, index) => {
            const line = headers.map((key) => String(row[key] ?? '')).join(' | ');
            if (y > 280) {
                doc.addPage();
                y = 18;
            }
            doc.text(`${index + 1}. ${line}`, 14, y);
            y += 7;
        });

        doc.save(`${slugify(reportTitle)}.pdf`);
    }

    document.querySelectorAll('.report-actions button').forEach((button) => {
        button.addEventListener('click', function () {
            const card = this.closest('.report-card');
            const titleEl = card ? card.querySelector('.report-card-title') : null;
            const reportTitle = titleEl ? titleEl.textContent.trim() : '';
            const exportType = this.textContent.trim().toLowerCase();
            const rows = reportData[reportTitle] || [{ report: reportTitle, note: 'Dummy data placeholder' }];

            if (exportType === 'excel') {
                exportToExcel(reportTitle, rows);
            }
            if (exportType === 'pdf') {
                exportToPdf(reportTitle, rows);
            }
        });
    });
</script>

</html>
