<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Reconciliation</title>
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

    .kpi-card.red::before {
        background: var(--danger);
    }

    .kpi-card.green::before {
        background: var(--success);
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
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-yellow {
        background: #fef9c3;
        color: #92400e;
    }

    .badge-red {
        background: #fee2e2;
        color: #b91c1c;
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
                <div class="page active" id="page-inventory">
                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Opening Stock</div>
                            <div class="kpi-value">6,240</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Units Dispatched</div>
                            <div class="kpi-value">5,432</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Units Returned (Good)</div>
                            <div class="kpi-value">601</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Closing Stock</div>
                            <div class="kpi-value">1,409</div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">📦</span> Inventory Movement Summary</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>MOVEMENT TYPE</th>
                                            <th>UNITS</th>
                                            <th>COST VALUE (PKR)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Opening Stock</td>
                                            <td>6,240</td>
                                            <td>PKR 93,60,000</td>
                                        </tr>
                                        <tr>
                                            <td>Purchases / Received</td>
                                            <td>+600</td>
                                            <td>+PKR 9,00,000</td>
                                        </tr>
                                        <tr>
                                            <td>Dispatched (Orders)</td>
                                            <td>-5,432</td>
                                            <td>-PKR 81,48,000</td>
                                        </tr>
                                        <tr>
                                            <td>Returned — Good Condition</td>
                                            <td>+601</td>
                                            <td>+PKR 9,01,500</td>
                                        </tr>
                                        <tr>
                                            <td>Returned — Damaged</td>
                                            <td>+150</td>
                                            <td>+PKR 0 (written off)</td>
                                        </tr>
                                        <tr>
                                            <td>Returned — Lost</td>
                                            <td>+57</td>
                                            <td>+PKR 0 (provisioned)</td>
                                        </tr>
                                        <tr>
                                            <td>Shrinkage / Adjustment</td>
                                            <td>-4</td>
                                            <td>-PKR 6,000</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Closing Stock</strong></td>
                                            <td><strong>1,409</strong></td>
                                            <td><strong>PKR 21,13,500</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">⚠️</span> Slow-Moving & Dead Stock</div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>PRODUCT</th>
                                            <th>UNITS</th>
                                            <th>DAYS IN STOCK</th>
                                            <th>VALUE</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>SKU-008</td>
                                            <td>Bluetooth Speaker Mini</td>
                                            <td>84</td>
                                            <td>65 days</td>
                                            <td>PKR 1,26,000</td>
                                            <td><span class="badge badge-yellow">Slow-Moving</span></td>
                                        </tr>
                                        <tr>
                                            <td>SKU-011</td>
                                            <td>Wired Headphones</td>
                                            <td>42</td>
                                            <td>92 days</td>
                                            <td>PKR 42,000</td>
                                            <td><span class="badge badge-red">Dead Stock</span></td>
                                        </tr>
                                        <tr>
                                            <td>SKU-015</td>
                                            <td>Type-A Hub Adapter</td>
                                            <td>120</td>
                                            <td>48 days</td>
                                            <td>PKR 84,000</td>
                                            <td><span class="badge badge-yellow">Slow-Moving</span></td>
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
    document.getElementById('page-title').textContent = 'Inventory Reconciliation';
    document.getElementById('page-bread').textContent = 'Finance / Inventory Reconciliation';
</script>

</html>
