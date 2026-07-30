<style>
  :root {
    --header-bg: #ffffff;
    --text-main: #0d0d0d;
    --text-muted: #6b7280;
    --border: #e5e7eb;
    --shadow: 0 1px 3px rgba(0,0,0,0.06);
    --header-height: 62px;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  .header {
    height: var(--header-height);
    background: var(--header-bg);
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 28px;
    position: sticky; top: 0; z-index: 50;
    box-shadow: var(--shadow);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
  }
  .header-left { display: flex; flex-direction: column; }
  .header-title { font-size: 16px; font-weight: 700; color: var(--text-main); letter-spacing: 0.2px; }
  .breadcrumb { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }
  .header-right { display: flex; align-items: center; gap: 10px; }
  .header-brand {
    font-size: 11px; font-weight: 800; letter-spacing: 3px;
    text-transform: uppercase; color: #0d0d0d;
    border: 1.5px solid #0d0d0d;
    padding: 4px 10px;
    border-radius: 4px;
    margin-right: 6px;
  }
  .header-btn {
    width: 36px; height: 36px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    position: relative;
    transition: background 0.15s;
  }
  .header-btn:hover { background: #f3f4f6; }
  .notif-dot {
    position: absolute; top: 6px; right: 6px;
    width: 7px; height: 7px;
    background: #dc2626;
    border-radius: 50%;
    border: 1.5px solid #fff;
  }
  .period-select {
    padding: 7px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 12.5px;
    color: var(--text-main);
    background: #fff;
    cursor: pointer;
    outline: none;
    font-family: inherit;
  }
  .period-select:focus { border-color: #0d0d0d; }
</style>

<header class="header">
  <div class="header-left">
    <div class="header-title" id="page-title">Dashboard</div>
    <div class="breadcrumb" id="page-bread">Home</div>
  </div>
  <div class="header-right">
    <span class="header-brand">Hanger</span>
    <select class="period-select" onchange="handlePeriod(this.value)">
      <option>This Month — July 2026</option>
      <option>Last Month — June 2026</option>
      <option>Q2 2026</option>
      <option>Q1 2026</option>
      <option>Custom Range</option>
    </select>
    <div class="header-btn" title="Notifications">🔔<span class="notif-dot"></span></div>
    <div class="header-btn" title="Export">⬇️</div>
    <div class="header-btn" title="Refresh" onclick="location.reload()">🔄</div>
  </div>
</header>
<script>
  function handlePeriod(val) { /* period filter wired to data layer later */ }
</script>
