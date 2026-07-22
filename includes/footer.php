<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Footer</title>
</head>
<style>
  :root {
    --text-muted: #6b7a8d;
    --border: #e2e8f0;
    --primary: #1a73e8;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  .app-footer {
    padding: 16px 28px;
    border-top: 1px solid var(--border);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    font-size: 12px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
  }

  .app-footer a {
    color: var(--text-muted);
    text-decoration: none;
  }

  .app-footer a:hover {
    color: var(--primary);
  }
</style>

<body>
  <footer class="app-footer">
    <div>© 2026 AgenticSense Automation. All rights reserved.</div>
    <div>E-Com Reconciliation System v1.0&nbsp;|&nbsp;Data Source: Leopard Courier API</div>
  </footer>
</body>
<script>
</script>

</html>