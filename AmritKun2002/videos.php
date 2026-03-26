<?php
require_once 'auth.php';

$search = trim($_GET['q']??'');
$page   = max(1,(int)($_GET['page']??1));
$limit  = 20;
$offset = ($page-1)*$limit;

$where  = $search ? "WHERE title LIKE ? OR category LIKE ?" : "WHERE 1=1";
$params = $search ? ["%$search%","%$search%"] : [];

$total = $pdo->prepare("SELECT COUNT(*) FROM videos $where");
$total->execute($params);
$totalCount = (int)$total->fetchColumn();
$totalPages = max(1,ceil($totalCount/$limit));

$stmt = $pdo->prepare("SELECT * FROM videos $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$videos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Videos — desimms Admin</title>
<link rel="stylesheet" href="style.css"/>
</head><body>
<div class="admin-wrap">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:10px">
      <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
      <span class="topbar-title">All Videos (<?= $totalCount ?>)</span>
    </div>
    <div class="topbar-actions">
      <form method="get" style="display:flex;gap:6px">
        <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search…" style="padding:7px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;outline:none"/>
        <button type="submit" class="btn btn-ghost btn-sm">🔍</button>
      </form>
      <a href="add_video.php" class="btn btn-primary btn-sm">➕ Add Video</a>
    </div>
  </div>
  <div class="content">
    <div class="table-wrap">
      <div style="overflow-x:auto">
      <table>
        <thead>
          <tr><th>Thumb</th><th>Title</th><th>Category</th><th>Views</th><th>Featured</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if(empty($videos)): ?>
          <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">No videos found.</td></tr>
        <?php else: foreach($videos as $v): ?>
          <tr>
            <td><img class="thumb-mini" src="<?= htmlspecialchars($v['thumbnail']?:('https://picsum.photos/seed/v'.$v['id'].'/60/34')) ?>" alt=""/></td>
            <td style="max-width:220px"><div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500"><?= htmlspecialchars($v['title']) ?></div>
              <div style="font-size:11px;color:var(--muted)"><?= htmlspecialchars(substr($v['slug'],0,35)) ?></div></td>
            <td><span class="badge badge-blue"><?= htmlspecialchars($v['category']) ?></span></td>
            <td><?= number_format((int)$v['views']) ?></td>
            <td><?= $v['featured']?'<span class="badge badge-gold">⭐ Yes</span>':'<span style="color:var(--muted);font-size:12px">No</span>' ?></td>
            <td style="white-space:nowrap;font-size:12px;color:var(--muted)"><?= date('M j, Y',strtotime($v['created_at'])) ?></td>
            <td style="white-space:nowrap">
              <a href="../video.php?id=<?= $v['id'] ?>" target="_blank" class="btn btn-ghost btn-sm">👁</a>
              <a href="edit_video.php?id=<?= $v['id'] ?>" class="btn btn-blue btn-sm">✏️</a>
              <a href="delete_video.php?id=<?= $v['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">🗑</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
      </div>
    </div>
    <!-- Pagination -->
    <?php if($totalPages>1): ?>
    <div style="display:flex;justify-content:center;gap:6px;padding:16px 0">
      <?php for($i=max(1,$page-3);$i<=min($totalPages,$page+3);$i++): ?>
        <a href="?page=<?=$i?>&q=<?=urlencode($search)?>" class="btn <?=$i===$page?'btn-primary':'btn-ghost'?> btn-sm"><?=$i?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
</div>
</body></html>
