<?php $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>
<style>
  :root {
    --sidebar-bg: #0d0d0d;
    --sidebar-hover: #1a1a1a;
    --sidebar-active-bg: rgba(255,255,255,0.08);
    --sidebar-border: rgba(255,255,255,0.07);
    --sidebar-text: rgba(255,255,255,0.5);
    --sidebar-width: 260px;
  }
  .sidebar {
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 100;
    overflow-y: auto;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
  }
  .sidebar-logo {
    padding: 22px 22px 18px;
    border-bottom: 1px solid var(--sidebar-border);
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .logo-mark {
    width: 36px; height: 36px;
    background: #fff;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .logo-text .brand {
    font-size: 14px; font-weight: 800; color: #fff;
    letter-spacing: 3px; text-transform: uppercase; line-height: 1;
  }
  .logo-text .sub {
    font-size: 9.5px; color: var(--sidebar-text);
    margin-top: 3px; letter-spacing: 0.5px; text-transform: uppercase;
  }
  .sidebar-section { padding: 20px 0 4px; }
  .sidebar-section-label {
    font-size: 9px; font-weight: 700;
    color: rgba(255,255,255,0.22);
    letter-spacing: 1.5px; text-transform: uppercase;
    padding: 0 22px 8px;
  }
  .nav-item {
    display: flex; align-items: center; gap: 11px;
    padding: 9px 22px;
    color: var(--sidebar-text);
    font-size: 13px; font-weight: 500;
    transition: all 0.15s;
    border-left: 2px solid transparent;
    text-decoration: none;
    letter-spacing: 0.2px;
  }
  .nav-item:hover { background: var(--sidebar-hover); color: #fff; }
  .nav-item.active {
    background: var(--sidebar-active-bg);
    color: #fff;
    border-left-color: #fff;
    font-weight: 600;
  }
  .nav-item .icon { font-size: 14px; width: 20px; text-align: center; flex-shrink: 0; opacity: 0.75; }
  .nav-item.active .icon { opacity: 1; }
  .nav-badge {
    margin-left: auto;
    background: #dc2626; color: #fff;
    font-size: 10px; font-weight: 700;
    padding: 2px 6px; border-radius: 20px;
  }
  .sidebar-footer {
    margin-top: auto; padding: 14px 22px;
    border-top: 1px solid var(--sidebar-border);
    display: flex; align-items: center; gap: 10px;
  }
  .sidebar-footer .avatar {
    width: 32px; height: 32px; background: #fff; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #000; font-weight: 800; font-size: 11px; flex-shrink: 0;
  }
  .sidebar-footer .name { font-size: 13px; color: #fff; font-weight: 600; }
  .sidebar-footer .role { font-size: 10.5px; color: var(--sidebar-text); margin-top: 1px; }
  .sidebar::-webkit-scrollbar { width: 4px; }
  .sidebar::-webkit-scrollbar-track { background: transparent; }
  .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }
</style>

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-mark">
      <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="2" y="2" width="4" height="18" fill="#000"/>
        <rect x="2" y="9" width="18" height="4" fill="#000"/>
        <rect x="16" y="2" width="4" height="18" fill="#000"/>
      </svg>
    </div>
    <div class="logo-text">
      <div class="brand">Hanger</div>
      <div class="sub">Ops &amp; Finance</div>
    </div>
  </div>

  <div class="sidebar-section">
    <div class="sidebar-section-label">Overview</div>
    <a href="dashboard.php" class="nav-item<?= $currentPage === 'dashboard.php' ? ' active' : '' ?>"><span class="icon">📊</span> Dashboard</a>
  </div>

  <div class="sidebar-section">
    <div class="sidebar-section-label">Reconciliation</div>
    <a href="orders.php"  class="nav-item<?= $currentPage === 'orders.php'  ? ' active' : '' ?>"><span class="icon">📦</span> Orders &amp; Shipments</a>
    <a href="cod.php"     class="nav-item<?= $currentPage === 'cod.php'     ? ' active' : '' ?>"><span class="icon">💵</span> COD Ledger</a>
    <a href="charges.php" class="nav-item<?= $currentPage === 'charges.php' ? ' active' : '' ?>"><span class="icon">🚚</span> Courier Charges</a>
    <a href="returns.php" class="nav-item<?= $currentPage === 'returns.php' ? ' active' : '' ?>"><span class="icon">🔄</span> Returns &amp; Refunds</a>
    <a href="revenue.php" class="nav-item<?= $currentPage === 'revenue.php' ? ' active' : '' ?>"><span class="icon">💰</span> Sales &amp; Revenue</a>
  </div>

  <div class="sidebar-section">
    <div class="sidebar-section-label">Finance</div>
    <a href="expenses.php"  class="nav-item<?= $currentPage === 'expenses.php'  ? ' active' : '' ?>"><span class="icon">📋</span> Expense Ledger</a>
    <a href="pl.php"        class="nav-item<?= $currentPage === 'pl.php'        ? ' active' : '' ?>"><span class="icon">📈</span> P&amp;L Statement</a>
    <a href="bs.php"        class="nav-item<?= $currentPage === 'bs.php'        ? ' active' : '' ?>"><span class="icon">🏦</span> Balance Sheet</a>
    <a href="inventory.php" class="nav-item<?= $currentPage === 'inventory.php' ? ' active' : '' ?>"><span class="icon">🗃️</span> Inventory</a>
    <a href="tax.php"       class="nav-item<?= $currentPage === 'tax.php'       ? ' active' : '' ?>"><span class="icon">🧾</span> Tax Ledgers</a>
  </div>

  <div class="sidebar-section">
    <div class="sidebar-section-label">Output</div>
    <a href="disputes.php" class="nav-item<?= $currentPage === 'disputes.php' ? ' active' : '' ?>"><span class="icon">⚠️</span> Dispute Management<span class="nav-badge">8</span></a>
    <a href="reports.php"  class="nav-item<?= $currentPage === 'reports.php'  ? ' active' : '' ?>"><span class="icon">📄</span> Reports</a>
    <a href="settings.php" class="nav-item<?= $currentPage === 'settings.php' ? ' active' : '' ?>"><span class="icon">⚙️</span> Settings</a>
  </div>

  <div class="sidebar-footer">
    <div class="avatar">DK</div>
    <div>
      <div class="name">Daniyal Khan</div>
      <div class="role">Finance Manager</div>
    </div>
  </div>
</aside>
