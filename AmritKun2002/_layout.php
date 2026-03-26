<?php
// Shared admin layout — included at top of each admin page
// Usage: include __DIR__.'/_layout.php';  (AFTER requireAdmin())
$activePage = $activePage ?? '';
$totalVideos = db()->query("SELECT COUNT(*) FROM videos WHERE is_active=1")->fetchColumn();
$totalAds    = db()->query("SELECT COUNT(*) FROM ads WHERE is_active=1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= $pageTitle ?? 'Admin' ?> — desimms</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;900&family=Barlow:wght@400;500;600&display=swap');
    *{box-sizing:border-box;margin:0;padding:0}
    :root{
      --bg:#f0f2f5;--card:#fff;--accent:#e94560;--accent2:#0077cc;
      --text:#111827;--muted:#6b7280;--border:rgba(0,0,0,.09);
      --sidebar:#1e2535;--sidebar-hover:#2a3347;--sidebar-active:#e94560;
      --font-h:'Barlow Condensed',sans-serif;--font:'Barlow',sans-serif;
      --radius:10px;
    }
    html,body{height:100%}
    body{font-family:var(--font);background:var(--bg);color:var(--text);display:flex;flex-direction:column;min-height:100vh}
    a{text-decoration:none;color:inherit}

    /* Top bar */
    .topbar{background:var(--sidebar);color:#fff;padding:0 20px;height:52px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50}
    .topbar-logo{font-family:var(--font-h);font-size:24px;font-weight:900;letter-spacing:-.3px}
    .topbar-logo span{color:var(--accent)}
    .topbar-right{display:flex;align-items:center;gap:14px;font-size:13px;color:rgba(255,255,255,.7)}
    .topbar-right a{color:rgba(255,255,255,.7);transition:color .15s}
    .topbar-right a:hover{color:#fff}
    .view-site-btn{background:var(--accent);color:#fff!important;padding:5px 13px;border-radius:50px;font-size:12px;font-weight:600}

    /* Layout */
    .admin-wrap{display:flex;flex:1}
    .sidebar{width:220px;background:var(--sidebar);padding:16px 10px;flex-shrink:0}
    .sidebar-section{font-size:10px;font-weight:700;letter-spacing:.1em;color:rgba(255,255,255,.35);padding:14px 12px 6px;text-transform:uppercase}
    .nav-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;font-size:14px;color:rgba(255,255,255,.7);transition:all .15s;margin-bottom:2px}
    .nav-link:hover{background:var(--sidebar-hover);color:#fff}
    .nav-link.active{background:rgba(233,69,96,.2);color:var(--accent);font-weight:600}
    .nav-icon{font-size:16px;width:20px;text-align:center}

    /* Main */
    .admin-main{flex:1;padding:24px;overflow-x:hidden}
    .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
    .page-title{font-family:var(--font-h);font-size:26px;font-weight:700;letter-spacing:.01em}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;font-family:var(--font);font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all .18s}
    .btn-primary{background:var(--accent);color:#fff}
    .btn-primary:hover{background:#c73650}
    .btn-secondary{background:var(--bg);color:var(--text);border:1px solid var(--border)}
    .btn-secondary:hover{background:#e4e8ef}
    .btn-danger{background:#ef4444;color:#fff}
    .btn-danger:hover{background:#dc2626}
    .btn-sm{padding:5px 12px;font-size:12px}

    /* Cards */
    .stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:24px}
    .stat-card{background:var(--card);border-radius:var(--radius);padding:18px;border:1px solid var(--border)}
    .stat-card .num{font-family:var(--font-h);font-size:36px;font-weight:900;color:var(--accent);line-height:1}
    .stat-card .label{font-size:13px;color:var(--muted);margin-top:4px}

    /* Table */
    .table-card{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);overflow:hidden}
    table{width:100%;border-collapse:collapse}
    th{background:#f8f9fb;padding:11px 14px;text-align:left;font-size:12px;font-weight:700;color:var(--muted);letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid var(--border)}
    td{padding:11px 14px;font-size:13px;border-bottom:1px solid rgba(0,0,0,.05)}
    tr:last-child td{border-bottom:none}
    tr:hover td{background:#fafbfc}
    .badge{display:inline-block;padding:2px 8px;border-radius:50px;font-size:11px;font-weight:700}
    .badge-green{background:#dcfce7;color:#16a34a}
    .badge-red{background:#fee2e2;color:#dc2626}
    .badge-blue{background:#dbeafe;color:#1d4ed8}
    .badge-yellow{background:#fef9c3;color:#a16207}

    /* Form */
    .form-card{background:var(--card);border-radius:var(--radius);padding:24px;border:1px solid var(--border)}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .form-full{grid-column:1/-1}
    .form-group{display:flex;flex-direction:column;gap:5px}
    .form-group label{font-size:13px;font-weight:600;color:var(--text)}
    .form-group .hint{font-size:11px;color:var(--muted)}
    input[type=text],input[type=url],input[type=number],input[type=password],select,textarea{
      border:1px solid var(--border);border-radius:8px;padding:9px 13px;font-size:14px;
      font-family:var(--font);outline:none;transition:border-color .2s;width:100%;
      background:#fff;color:var(--text)
    }
    input:focus,select:focus,textarea:focus{border-color:var(--accent2);box-shadow:0 0 0 2px rgba(0,119,204,.1)}
    textarea{resize:vertical;min-height:90px}
    .section-divider{border:none;border-top:1px solid var(--border);margin:22px 0}

    /* Alert */
    .alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
    .alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d}
    .alert-error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c}

    /* Responsive */
    @media(max-width:768px){
      .sidebar{display:none}
      .form-grid{grid-template-columns:1fr}
      .admin-main{padding:14px}
    }
  </style>
</head>
<body>

<div class="topbar">
  <div class="topbar-logo">desi<span>mms</span> <span style="font-size:13px;font-weight:400;opacity:.5;margin-left:6px">Admin</span></div>
  <div class="topbar-right">
    <a href="<?= SITE_URL ?>" target="_blank" class="view-site-btn">🌐 View Site</a>
    <a href="<?= SITE_URL ?>/admin/logout.php">Logout</a>
  </div>
</div>

<div class="admin-wrap">
  <nav class="sidebar">
    <div class="sidebar-section">Main</div>
    <a href="<?= SITE_URL ?>/admin/" class="nav-link <?= $activePage==='dashboard'?'active':'' ?>"><span class="nav-icon">📊</span> Dashboard</a>

    <div class="sidebar-section">Content</div>
    <a href="<?= SITE_URL ?>/admin/videos.php" class="nav-link <?= $activePage==='videos'?'active':'' ?>"><span class="nav-icon">🎬</span> Videos</a>
    <a href="<?= SITE_URL ?>/admin/videos.php?action=add" class="nav-link <?= $activePage==='addvideo'?'active':'' ?>"><span class="nav-icon">➕</span> Add Video</a>

    <div class="sidebar-section">Monetize</div>
    <a href="<?= SITE_URL ?>/admin/ads.php" class="nav-link <?= $activePage==='ads'?'active':'' ?>"><span class="nav-icon">💰</span> Manage Ads</a>

    <div class="sidebar-section">Settings</div>
    <a href="<?= SITE_URL ?>/admin/seo.php" class="nav-link <?= $activePage==='seo'?'active':'' ?>"><span class="nav-icon">🔍</span> SEO Settings</a>
    <a href="<?= SITE_URL ?>/admin/settings.php" class="nav-link <?= $activePage==='settings'?'active':'' ?>"><span class="nav-icon">⚙️</span> Site Settings</a>
  </nav>

  <main class="admin-main">
