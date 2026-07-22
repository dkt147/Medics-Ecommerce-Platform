<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
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

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .setting-list {
        margin-top: 14px;
        border-top: 1px solid #eef2f7;
    }

    .setting-row,
    .setting-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .setting-row:last-child,
    .setting-item:last-child {
        border-bottom: none;
    }

    .setting-label {
        color: var(--text-main);
        font-weight: 600;
        font-size: 14px;
    }

    .setting-sub {
        color: var(--text-muted);
        font-size: 13px;
        margin-top: 4px;
    }

    .setting-value {
        color: var(--text-main);
        font-weight: 700;
        text-align: right;
        min-width: 160px;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-connected {
        background: #dcfce7;
        color: #15803d;
    }

    .input-field,
    .select-field {
        width: 100%;
        max-width: 100px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        background: #fff;
        color: var(--text-main);
    }

    .btn {
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
    }

    .btn-secondary {
        background: #fff;
        border: 1px solid var(--border);
        color: var(--text-main);
    }

    .btn-edit {
        padding: 8px 14px;
    }

    .button-group {
        margin-top: 18px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .table-card {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 12px;
    }

    .table-card thead th {
        font-weight: 700;
        color: var(--text-muted);
        padding: 14px 16px;
        text-align: left;
        background: var(--surface);
        border-bottom: 1px solid #e2e8f0;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.02em;
    }

    .table-card tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-main);
        vertical-align: middle;
    }

    .table-card tbody tr:last-child td {
        border-bottom: none;
    }

    .role-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .role-admin {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .role-finance {
        background: #dcfce7;
        color: #15803d;
    }

    .role-ops {
        background: #e2e8f0;
        color: #475569;
    }

    @media (max-width: 1200px) {
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
                <div class="page active" id="page-settings">
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">🔌</span> Leopard API Integration</div>
                            <div class="setting-list">
                                <div class="setting-row">
                                    <div>
                                        <div class="setting-label">API Status</div>
                                        <div class="setting-sub">Connected to Leopard Courier</div>
                                    </div>
                                    <span class="badge-status badge-connected">Connected</span>
                                </div>
                                <div class="setting-row">
                                    <div>
                                        <div class="setting-label">Last Sync</div>
                                    </div>
                                    <div class="setting-value">Jul 22, 2026 — 11:42 AM</div>
                                </div>
                                <div class="setting-row">
                                    <div>
                                        <div class="setting-label">Sync Mode</div>
                                    </div>
                                    <div class="setting-value">Every 2 hours</div>
                                </div>
                                <div class="setting-row">
                                    <div>
                                        <div class="setting-label">API Endpoint</div>
                                    </div>
                                    <div class="setting-value">api.leopardscourier.com/v1</div>
                                </div>
                            </div>
                            <div class="button-group">
                                <button class="btn btn-primary">Sync Now</button>
                                <button class="btn btn-secondary">Configure</button>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">👤</span> User Management</div>
                            <table class="table-card">
                                <thead>
                                    <tr>
                                        <th>USER</th>
                                        <th>ROLE</th>
                                        <th>ACCESS</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Saad Ahmed</td>
                                        <td><span class="role-pill role-admin">Admin</span></td>
                                        <td>Full</td>
                                        <td><button class="btn btn-secondary btn-edit">Edit</button></td>
                                    </tr>
                                    <tr>
                                        <td>Finance Team</td>
                                        <td><span class="role-pill role-finance">Finance</span></td>
                                        <td>Read + Export</td>
                                        <td><button class="btn btn-secondary btn-edit">Edit</button></td>
                                    </tr>
                                    <tr>
                                        <td>Operations</td>
                                        <td><span class="role-pill role-ops">Operations</span></td>
                                        <td>Read Only</td>
                                        <td><button class="btn btn-secondary btn-edit">Edit</button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="button-group">
                                <button class="btn btn-primary">+ Add User</button>
                            </div>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">🔔</span> Alert Configuration</div>
                            <div class="setting-list">
                                <div class="setting-row">
                                    <div>
                                        <div class="setting-label">COD Overdue Alert (days)</div>
                                    </div>
                                    <input class="input-field" value="7" />
                                </div>
                                <div class="setting-row">
                                    <div>
                                        <div class="setting-label">Return Rate Spike Threshold (%)</div>
                                    </div>
                                    <input class="input-field" value="20" />
                                </div>
                                <div class="setting-row">
                                    <div>
                                        <div class="setting-label">Freight Variance Alert (%)</div>
                                    </div>
                                    <input class="input-field" value="5" />
                                </div>
                                <div class="setting-row">
                                    <div>
                                        <div class="setting-label">Email Alerts</div>
                                    </div>
                                    <select class="select-field">
                                        <option>Enabled</option>
                                        <option>Disabled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="button-group">
                                <button class="btn btn-primary">Save Settings</button>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-title"><span class="ct-icon">🏷️</span> Rate Card Configuration</div>
                            <table class="table-card">
                                <thead>
                                    <tr>
                                        <th>ZONE</th>
                                        <th>0.5 KG</th>
                                        <th>1 KG</th>
                                        <th>2 KG</th>
                                        <th>3+ KG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Local (same city)</td>
                                        <td>PKR 120</td>
                                        <td>PKR 150</td>
                                        <td>PKR 190</td>
                                        <td>PKR 230</td>
                                    </tr>
                                    <tr>
                                        <td>Intercity</td>
                                        <td>PKR 180</td>
                                        <td>PKR 220</td>
                                        <td>PKR 280</td>
                                        <td>PKR 340</td>
                                    </tr>
                                    <tr>
                                        <td>Remote / Rural</td>
                                        <td>PKR 240</td>
                                        <td>PKR 290</td>
                                        <td>PKR 360</td>
                                        <td>PKR 440</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="button-group">
                                <button class="btn btn-secondary">Edit Rate Card</button>
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
    document.getElementById('page-title').textContent = 'Settings';
    document.getElementById('page-bread').textContent = 'System / Settings';
</script>

</html>
