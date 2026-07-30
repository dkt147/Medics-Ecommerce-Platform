<?php
require_once __DIR__ . '/includes/data_helpers.php';

// Handle Excel export
if (isset($_GET['export']) && $_GET['export'] === '1') {
    exportRowsToExcel(getOrdersRows(), 'orders-export');
}

// Handle Excel import upload
$importedRows = [];
$importError  = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['import_file']['tmp_name'])) {
    $tmpPath = $_FILES['import_file']['tmp_name'];
    $importedRows = importOrdersFromUpload($tmpPath);
    if (empty($importedRows)) $importError = 'Could not parse uploaded file. Ensure it is .xlsx with headers.';
}

$allRows = !empty($importedRows) ? $importedRows : getOrdersRows();

// Filters
$search    = trim($_GET['search'] ?? '');
$statusF   = $_GET['status'] ?? '';
$cityF     = $_GET['city']   ?? '';
$fromDate  = $_GET['from']   ?? '';
$toDate    = $_GET['to']     ?? '';

$rows    = filterOrders($allRows, $search, $statusF, $cityF, $fromDate, $toDate);
$metrics = getOrderMetrics($allRows);
$cities  = getUniqueCities($allRows);
$statuses= getStatusBreakdown($allRows);
$cityCounts = getCityCounts($allRows);

// Chart JSON
$statusLabels = json_encode(array_keys($statuses));
$statusData   = json_encode(array_values($statuses));
$cityLabels   = json_encode(array_keys($cityCounts));
$cityData     = json_encode(array_values($cityCounts));

// Badge colour map
function statusBadge(string $s): string {
    return match(strtolower(trim($s))) {
        'delivered'  => 'badge-green',
        'in transit' => 'badge-yellow',
        'returned'   => 'badge-red',
        'prepaid'    => 'badge-blue',
        'cancelled'  => 'badge-red',
        default      => 'badge-grey',
    };
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders &amp; Shipments</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        :root {
            --primary: #1a73e8; --primary-light: #e8f0fe; --sidebar-width: 260px;
            --body-bg: #f0f4f8; --card-bg: #ffffff; --text-main: #1a2940; --text-muted: #6b7a8d;
            --border: #e2e8f0; --success: #16a34a; --success-bg: #dcfce7; --danger: #dc2626;
            --danger-bg: #fee2e2; --warning: #d97706; --warning-bg: #fef3c7; --info: #0891b2;
            --info-bg: #e0f2fe; --shadow: 0 1px 4px rgba(0,0,0,0.08); --radius: 10px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: var(--body-bg); color: var(--text-main); }
        .layout { display: flex; min-height: 100vh; }
        .main { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; }
        .content { padding: 24px 28px; flex: 1; }
        .card { background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); padding: 20px; }
        .card-title { font-size: 14px; font-weight: 700; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .card-title .ct-icon { font-size: 16px; }
        .mb20 { margin-bottom: 20px; }
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
        .kpi-card { background: var(--card-bg); border-radius: var(--radius); padding: 18px 20px; box-shadow: var(--shadow); border: 1px solid var(--border); display: flex; flex-direction: column; gap: 6px; position: relative; overflow: hidden; }
        .kpi-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
        .kpi-card.blue::before { background: var(--primary); }
        .kpi-card.green::before { background: var(--success); }
        .kpi-card.red::before { background: var(--danger); }
        .kpi-card.orange::before { background: var(--warning); }
        .kpi-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .kpi-value { font-size: 24px; font-weight: 800; color: var(--text-main); }
        .kpi-meta { font-size: 12px; }
        .kpi-up { color: var(--success); }
        .kpi-down { color: var(--danger); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .filter-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
        .filter-input { padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 13px; outline: none; color: var(--text-main); background: #fff; }
        .filter-input:focus { border-color: var(--primary); }
        .btn { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all 0.15s; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: #1557b0; }
        .btn-outline { background: #fff; color: var(--text-main); border: 1px solid var(--border); }
        .btn-outline:hover { background: #f8fafc; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: #f8fafc; padding: 10px 14px; text-align: left; font-size: 11.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); white-space: nowrap; }
        tbody td { padding: 11px 14px; border-bottom: 1px solid #f1f5f9; color: var(--text-main); font-size: 13px; }
        tbody tr:hover { background: #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .badge-green { background: var(--success-bg); color: var(--success); }
        .badge-red { background: var(--danger-bg); color: var(--danger); }
        .badge-yellow { background: var(--warning-bg); color: var(--warning); }
        .badge-blue { background: var(--primary-light); color: var(--primary); }
        .badge-grey { background: #f1f5f9; color: #64748b; }
        .chart-box { position: relative; height: 240px; }
        .import-box { background: var(--primary-light); border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; font-size: 13px; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c8d4e0; border-radius: 3px; }
        @media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>

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
                            <div class="kpi-value"><?= number_format($metrics['total_orders']) ?></div>
                        </div>
                        <div class="kpi-card green">
                            <div class="kpi-label">Delivered</div>
                            <div class="kpi-value"><?= number_format($metrics['delivered_count']) ?></div>
                            <div class="kpi-meta kpi-up"><?= $metrics['delivery_rate'] ?>% rate</div>
                        </div>
                        <div class="kpi-card red">
                            <div class="kpi-label">Returned</div>
                            <div class="kpi-value"><?= number_format($metrics['returned_count']) ?></div>
                            <div class="kpi-meta kpi-down"><?= $metrics['return_rate'] ?>% rate</div>
                        </div>
                        <div class="kpi-card orange">
                            <div class="kpi-label">In Transit</div>
                            <div class="kpi-value"><?= number_format($metrics['in_transit_count']) ?></div>
                        </div>
                    </div>

                    <!-- Import area -->
                    <?php if (!empty($importError)): ?>
                    <div class="import-box" style="background:#fee2e2; color:#dc2626;"><?= htmlspecialchars($importError) ?></div>
                    <?php elseif (!empty($importedRows)): ?>
                    <div class="import-box">✅ Imported <?= count($importedRows) ?> rows from uploaded file (session only — not saved to database).</div>
                    <?php endif; ?>

                    <!-- Shipment Register -->
                    <div class="card mb20">
                        <div class="card-title"><span class="ct-icon">📦</span> Shipment Register
                            <span style="margin-left:auto; font-weight:400; font-size:12px; color:var(--text-muted)">Showing <?= count($rows) ?> of <?= count($allRows) ?> orders</span>
                        </div>

                        <!-- Filter form -->
                        <form method="get" action="">
                            <div class="filter-bar">
                                <input type="text" name="search" class="filter-input" placeholder="🔍  Search tracking / order / customer..." style="width:260px" value="<?= htmlspecialchars($search) ?>" />
                                <select name="status" class="filter-input">
                                    <option value="">All Status</option>
                                    <?php foreach (['Delivered','In Transit','Returned','Pending','Cancelled','Prepaid'] as $s): ?>
                                    <option <?= $statusF === $s ? 'selected' : '' ?>><?= $s ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="city" class="filter-input">
                                    <option value="">All Cities</option>
                                    <?php foreach ($cities as $c): ?>
                                    <option <?= $cityF === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="date" name="from" class="filter-input" value="<?= htmlspecialchars($fromDate) ?>" />
                                <input type="date" name="to"   class="filter-input" value="<?= htmlspecialchars($toDate) ?>" />
                                <button type="submit" class="btn btn-primary">Apply</button>
                                <a href="?export=1" class="btn btn-outline" style="margin-left:auto; text-decoration:none;">⬇️ Export</a>
                            </div>
                        </form>

                        <!-- Import form -->
                        <form method="post" enctype="multipart/form-data" style="margin-bottom:12px;">
                            <div class="filter-bar">
                                <input type="file" name="import_file" accept=".xlsx" class="filter-input" />
                                <button type="submit" class="btn btn-outline">📤 Import Excel</button>
                            </div>
                        </form>

                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tracking No.</th>
                                        <th>Order Ref</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>City</th>
                                        <th>Product</th>
                                        <th>Courier</th>
                                        <th>COD Amount</th>
                                        <th>Status</th>
                                        <th>Delivery Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($rows, 0, 150) as $r): ?>
                                    <tr>
                                        <td><b><?= htmlspecialchars($r['tracking_no'] ?? '') ?></b></td>
                                        <td><?= htmlspecialchars($r['order_ref'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($r['date'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($r['customer'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($r['city'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($r['product'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($r['courier'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($r['cod_amount'] ?: '—') ?></td>
                                        <td><span class="badge <?= statusBadge($r['status'] ?? '') ?>"><?= htmlspecialchars($r['status'] ?? '') ?></span></td>
                                        <td><?= htmlspecialchars($r['delivery_date'] ?: '—') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($rows)): ?>
                                    <tr><td colspan="10" style="text-align:center; padding:24px; color:var(--text-muted);">No orders match the current filters.</td></tr>
                                    <?php endif; ?>
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

                </div>
            </div>

            <?php include 'includes/footer.php'; ?>

        </div>
    </div>

    <!-- Status update modal -->
<div id="statusModal" style="display:none; position:fixed; top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;z-index:9999;">
    <div style="background:#fff; padding:30px 40px; border-radius:12px; max-width:400px; box-shadow:0 20px 40px rgba(0,0,0,0.3);">
        <h3 style="margin-bottom:16px;">Update Order Status</h3>
        <p><strong>Tracking:</strong> <span id="modalTracking"></span></p>
        <p><strong>Current Status:</strong> <span id="modalCurrentStatus"></span></p>
        <label style="display:block; margin:16px 0 8px;">New Status</label>
        <select id="modalStatusSelect" class="filter-input" style="width:100%;">
            <option value="Delivered">Delivered</option>
            <option value="In Transit">In Transit</option>
            <option value="Returned">Returned</option>
            <option value="Pending">Pending</option>
            <option value="Cancelled">Cancelled</option>
            <option value="Prepaid">Prepaid</option>
        </select>
        <div style="margin-top:20px; display:flex; gap:10px; justify-content:flex-end;">
            <button id="modalCancelBtn" class="btn btn-outline">Cancel</button>
            <button id="modalSaveBtn" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>

    <script>
        document.getElementById('page-title').textContent = 'Orders & Shipments';
        document.getElementById('page-bread').textContent = 'Modules / Orders & Shipments';

        const SUCCESS = '#16a34a', DANGER = '#dc2626', WARNING = '#d97706', INFO = '#0891b2', GREY = '#94a3b8';
        const palette = ['#1a73e8','#16a34a','#d97706','#dc2626','#0891b2','#7c3aed','#db2777','#059669','#ea580c','#9333ea'];

        Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6b7a8d';

        new Chart(document.getElementById('cityChart'), {
            type: 'bar',
            data: {
                labels: <?= $cityLabels ?>,
                datasets: [{ label: 'Orders', data: <?= $cityData ?>, backgroundColor: palette, borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { grid: { color: '#f1f5f9' } } }
            }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: <?= $statusLabels ?>,
                datasets: [{ data: <?= $statusData ?>, backgroundColor: [SUCCESS, INFO, DANGER, WARNING, GREY, '#7c3aed'], borderWidth: 2, borderColor: '#fff' }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
                cutout: '60%'
            }
        });


        let activeTrackingNo = null;

document.querySelector('#page-orders table tbody').addEventListener('dblclick', function(e) {
    // Find the closest row
    const row = e.target.closest('tr');
    if (!row) return;

    // Get the tracking number from the first cell (column 0)
    const td = row.querySelector('td:first-child');
    if (!td) return;

    const trackingNo = td.textContent.trim();
    if (!trackingNo) return;

    // Get current status from the status column (index 8)
    const statusTd = row.querySelectorAll('td')[8];
    const currentStatus = statusTd ? statusTd.textContent.trim() : '';

    // Populate modal
    document.getElementById('modalTracking').textContent = trackingNo;
    document.getElementById('modalCurrentStatus').textContent = currentStatus;
    document.getElementById('modalStatusSelect').value = currentStatus; // pre-select current
    activeTrackingNo = trackingNo;

    // Show modal
    document.getElementById('statusModal').style.display = 'flex';
});

// Modal buttons
document.getElementById('modalCancelBtn').addEventListener('click', function() {
    document.getElementById('statusModal').style.display = 'none';
});

document.getElementById('modalSaveBtn').addEventListener('click', function() {
    const newStatus = document.getElementById('modalStatusSelect').value;
    if (!activeTrackingNo) return;

    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Saving...';

    fetch('update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `tracking_no=${encodeURIComponent(activeTrackingNo)}&status=${encodeURIComponent(newStatus)}`
    })
    .then(response => response.text()) // get raw text
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                alert('Status updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        } catch (e) {
            alert('Invalid JSON response: ' + text.substring(0, 200));
        }
    })
    .catch(err => {
        alert('Network error: ' + err.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Save';
        document.getElementById('statusModal').style.display = 'none';
        activeTrackingNo = null;
    });
});

// Close modal if clicking outside the white box
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
        activeTrackingNo = null;
    }
});
    </script>
</body>
</html>
