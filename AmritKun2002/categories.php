<?php
require_once 'auth.php';
$msg=''; $err='';

// Add
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_cat'])){
    $name = trim($_POST['name']??'');
    $icon = trim($_POST['icon']??'🎬');
    if(!$name){ $err='Category name required.'; }
    else {
        $slug = makeSlug($name);
        try {
            $pdo->prepare("INSERT INTO categories(name,slug,icon) VALUES(?,?,?)")->execute([$name,$slug,$icon]);
            $msg='Category added!';
        } catch(Exception $e){ $err='Slug already exists. Try a different name.'; }
    }
}
// Delete
if(isset($_GET['del'])){
    $did=(int)$_GET['del'];
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$did]);
    header('Location: categories.php?msg=deleted'); exit;
}
if(isset($_GET['msg'])) $msg=['deleted'=>'Category deleted.'][$_GET['msg']]??'';

$cats = getCategories($pdo);
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Categories — desimms Admin</title>
<link rel="stylesheet" href="style.css"/>
</head><body>
<div class="admin-wrap">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:10px">
      <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
      <span class="topbar-title">📂 Categories</span>
    </div>
  </div>
  <div class="content">
    <?php if($msg): ?><div class="alert alert-success">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-error">❌ <?= htmlspecialchars($err) ?></div><?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
      <!-- Add form -->
      <div class="form-card">
        <h3>➕ Add New Category</h3>
        <form method="POST">
          <div class="form-group" style="margin-bottom:12px">
            <label>Category Name *</label>
            <input type="text" name="name" placeholder="e.g. Drama, Documentary" required/>
          </div>
          <div class="form-group" style="margin-bottom:16px">
            <label>Icon (emoji)</label>
            <input type="text" name="icon" value="🎬" placeholder="🎬" maxlength="5"/>
            <span class="form-hint">Paste any emoji — e.g. 🎭 🎵 ⚽ 💻 📺</span>
          </div>
          <button type="submit" name="add_cat" class="btn btn-primary">Add Category</button>
        </form>
      </div>

      <!-- List -->
      <div class="table-wrap" style="align-self:start">
        <div class="table-head"><h3>All Categories (<?= count($cats) ?>)</h3></div>
        <table>
          <thead><tr><th>Icon</th><th>Name</th><th>Slug</th><th>Videos</th><th>Action</th></tr></thead>
          <tbody>
          <?php foreach($cats as $c):
            $count = $pdo->prepare("SELECT COUNT(*) FROM videos WHERE category=?");
            $count->execute([$c['slug']]);
            $cnt = $count->fetchColumn();
          ?>
            <tr>
              <td style="font-size:20px"><?= htmlspecialchars($c['icon']) ?></td>
              <td style="font-weight:600"><?= htmlspecialchars($c['name']) ?></td>
              <td style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($c['slug']) ?></td>
              <td><span class="badge badge-blue"><?= $cnt ?></span></td>
              <td>
                <a href="categories.php?del=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete category \'<?= htmlspecialchars($c['name']) ?>\'? Videos will not be deleted.')">🗑</a>
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
