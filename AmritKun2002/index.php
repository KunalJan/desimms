<?php
require_once 'auth.php';

$totalVideos = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
$totalViews  = $pdo->query("SELECT SUM(views) FROM videos")->fetchColumn() ?: 0;
$totalCats   = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalAds    = $pdo->query("SELECT COUNT(*) FROM ads WHERE is_active=1")->fetchColumn();

$recentVideos = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC LIMIT 8")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Dashboard — desimms Admin</title>
<link rel="stylesheet" href="style.css"/>
</head><body>
<div class="admin-wrap">

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main -->
<div class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:10px">
      <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
      <span class="topbar-title">Dashboard</span>
    </div>
    <div class="topbar-actions">
      <a href="../index.php" target="_blank" class="btn btn-ghost btn-sm">🌐 View Site</a>
      <a href="logout.php" class="btn btn-ghost btn-sm">🚪 Logout</a>
    </div>
  </div>

  <div class="content">
    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background:#fef2f2">🎬</div>
        <div><div class="stat-num"><?= number_format((int)$totalVideos) ?></div><div class="stat-label">Total Videos</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff">👁</div>
        <div><div class="stat-num"><?= number_format((int)$totalViews) ?></div><div class="stat-label">Total Views</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4">📂</div>
        <div><div class="stat-num"><?= $totalCats ?></div><div class="stat-label">Categories</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#fefce8">📢</div>
        <div><div class="stat-num"><?= $totalAds ?></div><div class="stat-label">Active Ads</div></div>
      </div>
    </div>

    <!-- Quick actions -->
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:22px">
      <a href="add_video.php" class="btn btn-primary">➕ Add Video</a>
      <a href="manage_ads.php" class="btn btn-blue">📢 Manage Ads</a>
      <a href="categories.php" class="btn btn-green">📂 Categories</a>
      <a href="settings.php" class="btn btn-ghost">⚙️ Settings</a>
    </div>

    <!-- Recent videos -->
    <div class="table-wrap">
      <div class="table-head">
        <h3>Recent Videos</h3>
        <a href="videos.php" class="btn btn-ghost btn-sm">View All →</a>
      </div>
      <div style="overflow-x:auto">
      <table>
        <thead>
          <tr>
            <th>Thumbnail</th><th>Title</th><th>Category</th>
            <th>Views</th><th>Date</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($recentVideos as $v): ?>
          <tr>
            <td><img class="thumb-mini" src="<?= htmlspecialchars($v['thumbnail']?:('https://picsum.photos/seed/v'.$v['id'].'/60/34')) ?>" alt=""/></td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($v['title']) ?></td>
            <td><span class="badge badge-blue"><?= htmlspecialchars($v['category']) ?></span></td>
            <td><?= number_format((int)$v['views']) ?></td>
            <td><?= date('M j, Y', strtotime($v['created_at'])) ?></td>
            <td style="white-space:nowrap">
              <a href="edit_video.php?id=<?= $v['id'] ?>" class="btn btn-ghost btn-sm">✏️ Edit</a>
              <a href="delete_video.php?id=<?= $v['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this video?')">🗑</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</div>
</div>
</body></html>
