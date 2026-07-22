<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispute Management</title>
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
    .kpi-card.orange::before { background: var(--warning); }
    .kpi-card.green::before { background: var(--success); }
    .kpi-card.red::before { background: var(--danger); }

    .kpi-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
    .kpi-value { font-size: 24px; font-weight: 800; color: var(--text-main); }

    .filter-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 16px;
    }

    .filter-input {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: #fff;
        outline: none;
        color: var(--text-main);
    }

    .btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
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
    }

    tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-main);
    }

    tbody tr:hover { background: #f8fafc; }
    tbody tr:last-child td { border-bottom: none; }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-open { background: #fee2e2; color: #b91c1c; }
    .badge-review { background: #fef9c3; color: #92400e; }
    .badge-resolved { background: #dcfce7; color: #15803d; }

    @media (max-width: 1200px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<body>
    <div class="layout">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main">
            <?php include 'includes/navbar.php'; ?>
            <div class="content">
                <div class="page active" id="page-disputes">
                    <div class="kpi-grid">
                        <div class="kpi-card blue">
                            <div class="kpi-label">Open Disputes</div>
                            <div class="kpi-value">8</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">Total Disputed Value</div>
                            <div class="kpi-value">PKR 3.4L</div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Resolved (Month)</div>
                            <div class="kpi-value">12</div>
                        </div>
                        <div class="kpi-card blue">
                            <div class="kpi-label">Credits Received</div>
                            <div class="kpi-value">PKR 1.8L</div>
                        </div>
                    </div>
                    <div class="card mb20">
                        <div class="card-title"><span class="ct-icon">⚠️</span> Active Dispute Log</div>
                        <div class="filter-bar">
                            <select class="filter-input">
                                <option>All Types</option>
                                <option>Freight Overcharge</option>
                                <option>COD Short Remittance</option>
                                <option>Lost Parcel</option>
                            </select>
                            <select class="filter-input">
                                <option>All Status</option>
                                <option>Open</option>
                                <option>Under Review</option>
                                <option>Resolved</option>
                            </select>
                            <button class="btn btn-primary">+ Raise New Dispute</button>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>TYPE</th>
                                        <th>REFERENCE</th>
                                        <th>DESCRIPTION</th>
                                        <th>AMOUNT</th>
                                        <th>RAISED ON</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>DSP-008</td>
                                        <td>Freight Overcharge</td>
                                        <td>LC-2607-03</td>
                                        <td>Weight billed higher on 47 shipments</td>
                                        <td>PKR 38,400</td>
                                        <td>Jul 21</td>
                                        <td><span class="badge badge-open">Open</span></td>
                                        <td><button class="btn btn-outline">View</button></td>
                                    </tr>
                                    <tr>
                                        <td>DSP-007</td>
                                        <td>COD Short Remittance</td>
                                        <td>REM-LC-2607-08</td>
                                        <td>Batch overdue — not remitted 14 days</td>
                                        <td>PKR 4,21,000</td>
                                        <td>Jul 16</td>
                                        <td><span class="badge badge-review">Under Review</span></td>
                                        <td><button class="btn btn-outline">View</button></td>
                                    </tr>
                                    <tr>
                                        <td>DSP-006</td>
                                        <td>COD Short</td>
                                        <td>LP0012843190</td>
                                        <td>COD short by PKR 300 — customer variance</td>
                                        <td>PKR 300</td>
                                        <td>Jul 18</td>
                                        <td><span class="badge badge-open">Open</span></td>
                                        <td><button class="btn btn-outline">View</button></td>
                                    </tr>
                                    <tr>
                                        <td>DSP-005</td>
                                        <td>Lost Parcel</td>
                                        <td>ORD-9740</td>
                                        <td>Return marked received but parcel not found</td>
                                        <td>PKR 5,200</td>
                                        <td>Jul 14</td>
                                        <td><span class="badge badge-review">Under Review</span></td>
                                        <td><button class="btn btn-outline">View</button></td>
                                    </tr>
                                    <tr>
                                        <td>DSP-004</td>
                                        <td>Freight Overcharge</td>
                                        <td>LC-2607-01</td>
                                        <td>Re-attempt fee charged without attempt log</td>
                                        <td>PKR 11,300</td>
                                        <td>Jul 9</td>
                                        <td><span class="badge badge-resolved">Resolved</span></td>
                                        <td><button class="btn btn-outline">View</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
</body>
<script>
    document.getElementById('page-title').textContent = 'Dispute Management';
    document.getElementById('page-bread').textContent = 'Output / Dispute Management';
</script>

</html>
