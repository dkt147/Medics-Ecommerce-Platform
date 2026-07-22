<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Ledgers</title>
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
        border: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 8px;
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

    .tab-bar {
        display: flex;
        gap: 0;
        border-bottom: 1px solid var(--border);
        margin-bottom: 20px;
        overflow-x: auto;
    }

    .tab {
        padding: 14px 20px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.15s;
        white-space: nowrap;
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

    .info-note {
        margin-top: 14px;
        padding: 14px 16px;
        border-radius: 10px;
        background: #fef9c3;
        color: #92400e;
        font-size: 13px;
        line-height: 1.5;
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
                <div class="page active" id="page-tax">
                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Output Sales Tax</div>
                            <div class="kpi-value">PKR 1,23,600</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Service Tax (Courier)</div>
                            <div class="kpi-value">PKR 18,480</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">WHT Income Tax</div>
                            <div class="kpi-value">PKR 1,30,200</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Net Tax Payable</div>
                            <div class="kpi-value">PKR 81,420</div>
                        </div>
                    </div>

                    <div class="tab-bar">
                        <div class="tab active" onclick="switchTaxTab(event, 'tab-output-sales')">Output Sales Tax</div>
                        <div class="tab" onclick="switchTaxTab(event, 'tab-service-tax')">Service Tax</div>
                        <div class="tab" onclick="switchTaxTab(event, 'tab-wht-income')">WHT Income Tax</div>
                        <div class="tab" onclick="switchTaxTab(event, 'tab-wht-sales')">WHT Sales Tax</div>
                    </div>

                    <div class="tab-content active" id="tab-output-sales">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📄</span> Output Sales Tax Ledger — July 2026</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>DATE</th>
                                            <th>ORDER REF</th>
                                            <th>TAXABLE REVENUE</th>
                                            <th>TAX RATE</th>
                                            <th>ST AMOUNT</th>
                                            <th>JURISDICTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Jul 22</td>
                                            <td>ORD-9812</td>
                                            <td>PKR 2,500</td>
                                            <td>17%</td>
                                            <td>PKR 425</td>
                                            <td>SRB (Sindh)</td>
                                        </tr>
                                        <tr>
                                            <td>Jul 22</td>
                                            <td>ORD-9811</td>
                                            <td>PKR 1,800</td>
                                            <td>16%</td>
                                            <td>PKR 288</td>
                                            <td>PRA (Punjab)</td>
                                        </tr>
                                        <tr>
                                            <td>Jul 20</td>
                                            <td>ORD-9805</td>
                                            <td>PKR 5,200</td>
                                            <td>15%</td>
                                            <td>PKR 780</td>
                                            <td>FBR (Federal)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="margin-top: 16px; display: flex; justify-content: flex-end; font-weight: 700; color: var(--text-main);">
                                Monthly Total&nbsp;&nbsp;PKR 1,23,600
                            </div>
                            <div class="info-note">Filing Summary: Output ST: PKR 1,23,600 | Input ST Credit: PKR 42,180 | <strong>Net ST Payable to FBR/PRA/SRB: PKR 81,420</strong></div>
                        </div>
                    </div>

                    <div class="tab-content" id="tab-service-tax">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">🚚</span> Service Tax on Courier Charges</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>INVOICE</th>
                                            <th>COURIER CHARGES</th>
                                            <th>ST RATE</th>
                                            <th>SERVICE TAX</th>
                                            <th>AUTHORITY</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>LC-2607-03</td>
                                            <td>PKR 2,18,400</td>
                                            <td>8%</td>
                                            <td>PKR 17,472</td>
                                            <td>PRA</td>
                                        </tr>
                                        <tr>
                                            <td>LC-2607-02</td>
                                            <td>PKR 1,94,000</td>
                                            <td>8%</td>
                                            <td>PKR 15,520</td>
                                            <td>PRA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="margin-top: 16px; display: flex; justify-content: flex-end; font-weight: 700; color: var(--text-main);">
                                Monthly Total&nbsp;&nbsp;PKR 18,480
                            </div>
                        </div>
                    </div>

                    <div class="tab-content" id="tab-wht-income">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">💼</span> Withholding Income Tax (2% on COD)</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>REMITTANCE SLIP</th>
                                            <th>COD COLLECTED</th>
                                            <th>WHT IT RATE</th>
                                            <th>WHT IT AMOUNT</th>
                                            <th>DEDUCTED BY</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>REM-LC-2607-14</td>
                                            <td>PKR 4,32,000</td>
                                            <td>2%</td>
                                            <td>PKR 8,640</td>
                                            <td>Leopard Courier</td>
                                        </tr>
                                        <tr>
                                            <td>REM-LC-2607-11</td>
                                            <td>PKR 5,18,000</td>
                                            <td>2%</td>
                                            <td>PKR 10,360</td>
                                            <td>Leopard Courier</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="margin-top: 16px; display: flex; justify-content: flex-end; font-weight: 700; color: var(--text-main);">
                                Monthly Total (est.)&nbsp;&nbsp;PKR 1,30,200
                            </div>
                            <div class="info-note">WHT IT deducted by Leopard is claimable as a tax credit against income tax liability. Retain all remittance slips for filing.</div>
                        </div>
                    </div>

                    <div class="tab-content" id="tab-wht-sales">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">🧾</span> Withholding Sales Tax (2% on COD)</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>REMITTANCE SLIP</th>
                                            <th>COD COLLECTED</th>
                                            <th>WHT ST RATE</th>
                                            <th>WHT ST AMOUNT</th>
                                            <th>DEDUCTED BY</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>REM-LC-2607-14</td>
                                            <td>PKR 4,32,000</td>
                                            <td>2%</td>
                                            <td>PKR 8,640</td>
                                            <td>Leopard Courier</td>
                                        </tr>
                                        <tr>
                                            <td>REM-LC-2607-11</td>
                                            <td>PKR 5,18,000</td>
                                            <td>2%</td>
                                            <td>PKR 10,360</td>
                                            <td>Leopard Courier</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="margin-top: 16px; display: flex; justify-content: flex-end; font-weight: 700; color: var(--text-main);">
                                Monthly Total (est.)&nbsp;&nbsp;PKR 1,30,200
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
    document.getElementById('page-title').textContent = 'Tax Ledgers';
    document.getElementById('page-bread').textContent = 'Finance / Tax Ledgers';

    function switchTaxTab(event, tabId) {
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        event.currentTarget.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }
</script>

</html>
