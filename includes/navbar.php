<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navbar</title>
</head>
<style>
  :root {
    --primary: #1a73e8;
    --header-bg: #ffffff;
    --text-main: #1a2940;
    --text-muted: #6b7a8d;
    --border: #e2e8f0;
    --danger: #dc2626;
    --primary-light: #e8f0fe;
    --shadow: 0 1px 4px rgba(0,0,0,0.08);
    --header-height: 62px;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }

  .header {
    height: var(--header-height);
    background: var(--header-bg);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    position: sticky;
    top: 0;
    z-index: 50;
    box-shadow: var(--shadow);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
  }
  .header-left { display: flex; flex-direction: column; }
  .header-title { font-size: 17px; font-weight: 700; color: var(--text-main); }
  .breadcrumb { font-size: 12px; color: var(--text-muted); margin-top: 1px; }
  .header-right { display: flex; align-items: center; gap: 12px; }
  .header-btn {
    width: 36px; height: 36px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--header-bg);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    position: relative;
    transition: background 0.15s;
  }
  .header-btn:hover { background: var(--primary-light); }
  .notif-dot {
    position: absolute; top: 6px; right: 6px;
    width: 8px; height: 8px;
    background: var(--danger);
    border-radius: 50%;
    border: 2px solid #fff;
  }
  .period-select {
    padding: 7px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    color: var(--text-main);
    background: #fff;
    cursor: pointer;
    outline: none;
  }
</style>
<body>
  <header class="header">
    <div class="header-left">
      <div class="header-title" id="page-title">Dashboard</div>
      <div class="breadcrumb" id="page-bread">Home</div>
    </div>
    <div class="header-right">
      <select class="period-select" onchange="handlePeriod(this.value)">
        <option>This Month — July 2026</option>
        <option>Last Month — June 2026</option>
        <option>Q2 2026</option>
        <option>Q1 2026</option>
        <option>Custom Range</option>
      </select>
      <div class="header-btn" title="Notifications">🔔<span class="notif-dot"></span></div>
      <div class="header-btn" title="Export">⬇️</div>
      <div class="header-btn" title="Refresh">🔄</div>
    </div>
  </header>
</body>
<script>
  function handlePeriod(val) {
    // Abhi ke liye sirf UI hai. Period change hone par data/API
    // se re-fetch baad me is function ke andar lagega.
  }
</script>
</html>