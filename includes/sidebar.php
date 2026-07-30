<?php $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar</title>
</head>
<style>
  :root {
    --primary: #1a73e8;
    --sidebar-bg: #1e3a5f;
    --sidebar-hover: #2a4f7c;
    --sidebar-active: #1a73e8;
    --sidebar-text: #c8d8eb;
    --sidebar-text-active: #ffffff;
    --sidebar-width: 260px;
    --danger: #dc2626;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  .sidebar {
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 100;
    overflow-y: auto;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
  }

  .sidebar-logo {
    padding: 20px 20px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .sidebar-logo .brand {
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.3px;
  }

  .sidebar-logo .sub {
    font-size: 11px;
    color: var(--sidebar-text);
    margin-top: 2px;
  }

  .sidebar-logo .badge {
    display: inline-block;
    background: var(--primary);
    color: #fff;
    font-size: 10px;
    padding: 2px 7px;
    border-radius: 20px;
    margin-left: 8px;
  }

  .sidebar-section {
    padding: 20px 0 4px;
  }

  .sidebar-section-label {
    font-size: 10px;
    font-weight: 600;
    color: rgba(200, 216, 235, 0.45);
    letter-spacing: 1.2px;
    text-transform: uppercase;
    padding: 0 20px 8px;
  }

  .nav-item {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 10px 20px;
    cursor: pointer;
    color: var(--sidebar-text);
    font-size: 13.5px;
    font-weight: 500;
    transition: all 0.18s;
    border-left: 3px solid transparent;
    text-decoration: none;
    user-select: none;
  }

  .nav-item:hover {
    background: var(--sidebar-hover);
    color: #fff;
  }

  .nav-item.active {
    background: rgba(26, 115, 232, 0.18);
    color: #fff;
    border-left-color: var(--primary);
    font-weight: 600;
  }

  .nav-item .icon {
    font-size: 16px;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
  }

  .sidebar-footer {
    margin-top: auto;
    padding: 16px 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
  }

  .sidebar-footer .avatar {
    width: 34px;
    height: 34px;
    background: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 13px;
  }

  .sidebar-footer .name {
    font-size: 13px;
    color: #fff;
    font-weight: 600;
  }

  .sidebar-footer .role {
    font-size: 11px;
    color: var(--sidebar-text);
  }

  ::-webkit-scrollbar {
    width: 6px;
  }

  ::-webkit-scrollbar-track {
    background: transparent;
  }

  ::-webkit-scrollbar-thumb {
    background: #c8d4e0;
    border-radius: 3px;
  }
</style>

<body>
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="brand">AgenticSense <span class="badge">LIVE</span></div>
      <div class="sub">E-Com Reconciliation System</div>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Overview</div>
      <a href="dashboard.php" class="nav-item<?php echo $currentPage === 'dashboard.php' ? ' active' : ''; ?>">
        <span class="icon">📊</span> Dashboard
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Reconciliation Modules</div>
      <a href="orders.php" class="nav-item<?php echo $currentPage === 'orders.php' ? ' active' : ''; ?>">
        <span class="icon">📦</span> Orders &amp; Shipments
      </a>
      <a href="cod.php" class="nav-item<?php echo $currentPage === 'cod.php' ? ' active' : ''; ?>">
        <span class="icon">💵</span> COD Ledger
      </a>
      <a href="charges.php" class="nav-item<?php echo $currentPage === 'charges.php' ? ' active' : ''; ?>">
        <span class="icon">🚚</span> Courier Charges
      </a>
      <a href="returns.php" class="nav-item<?php echo $currentPage === 'returns.php' ? ' active' : ''; ?>">
        <span class="icon">🔄</span> Returns &amp; Refunds
      </a>
      <a href="revenue.php" class="nav-item<?php echo $currentPage === 'revenue.php' ? ' active' : ''; ?>">
        <span class="icon">💰</span> Sales &amp; Revenue
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Financial Modules</div>
      <a href="tax.php" class="nav-item<?php echo $currentPage === 'tax.php' ? ' active' : ''; ?>">
        <span class="icon">🧾</span> Tax Ledgers
      </a>
      <a href="expenses.php" class="nav-item<?php echo $currentPage === 'expenses.php' ? ' active' : ''; ?>">
        <span class="icon">📋</span> Expense Ledger
      </a>
      <a href="pl.php" class="nav-item<?php echo $currentPage === 'pl.php' ? ' active' : ''; ?>">
        <span class="icon">📈</span> P&amp;L Statement
      </a>
      <a href="bs.php" class="nav-item<?php echo $currentPage === 'bs.php' ? ' active' : ''; ?>">
        <span class="icon">🏦</span> Balance Sheet
      </a>
      <a href="inventory.php" class="nav-item<?php echo $currentPage === 'inventory.php' ? ' active' : ''; ?>">
        <span class="icon">🗃️</span> Inventory
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Output</div>
      <a href="reports.php" class="nav-item<?php echo $currentPage === 'reports.php' ? ' active' : ''; ?>">
        <span class="icon">📄</span> Reports
      </a>
      <a href="disputes.php" class="nav-item<?php echo $currentPage === 'disputes.php' ? ' active' : ''; ?>">
        <span class="icon">⚠️</span> Dispute Management
        <span class="nav-badge">8</span>
      </a>
      <a href="settings.php" class="nav-item<?php echo $currentPage === 'settings.php' ? ' active' : ''; ?>">
        <span class="icon">⚙️</span> Settings
      </a>
    </div>

    <div class="sidebar-footer">
      <div class="avatar">DK</div>
      <div>
        <div class="name">Daniyal Khan</div>
        <div class="role">Finance Manager</div>
      </div>
    </div>
  </aside>
</body>

</html>