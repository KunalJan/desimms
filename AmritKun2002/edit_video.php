<?php
require_once 'auth.php';
$cats = getCategories($pdo);
$msg = ''; $err = '';

$id = (int)($_GET['id'] ?? 0);
if(!$id){ header('Location: videos.php'); exit; }

$v = $pdo->prepare("SELECT * FROM videos WHERE id=?");
$v->execute([$id]);
$vid = $v->fetch();
if(!$vid){ header('Location: videos.php'); exit; }

if($_SERVER['REQUEST_METHOD']==='POST'){
    $title    = trim($_POST['title']??'');
    $desc     = trim($_POST['description']??'');
    $cat      = trim($_POST['category']??'general');
    $tags     = trim($_POST['tags']??'');
    $embed    = trim($_POST['embed_url']??'');
    $thumb    = trim($_POST['thumbnail']??$vid['thumbnail']);
    $featured = isset($_POST['featured'])?1:0;
    $seoT     = trim($_POST['seo_title']??'');
    $seoD     = trim($_POST['seo_desc']??'');
    $seoK     = trim($_POST['seo_keywords']??'');

    if(!$title){ $err='Title is required.'; }
    else {
        // Handle new video file upload
        $videoFile = $vid['video_file'];
        if(!empty($_FILES['video_file']['name'])){
            $allowed=['mp4','webm','ogg','mkv'];
            $ext=strtolower(pathinfo($_FILES['video_file']['name'],PATHINFO_EXTENSION));
            if(!in_array($ext,$allowed)){ $err='Only MP4, WebM, OGG, MKV allowed.'; }
            else {
                $fname=uniqid('v_').'.'.$ext;
                if(move_uploaded_file($_FILES['video_file']['tmp_name'],'../uploads/videos/'.$fname)){
                    // delete old file
                    if($videoFile && file_exists('../uploads/videos/'.$videoFile)) unlink('../uploads/videos/'.$videoFile);
                    $videoFile=$fname;
                } else { $err='File upload failed. Check folder permissions.'; }
            }
        }
        // Handle new thumbnail upload
        if(!empty($_FILES['thumb_file']['name']) && empty($err)){
            $tallow=['jpg','jpeg','png','webp','gif'];
            $text=strtolower(pathinfo($_FILES['thumb_file']['name'],PATHINFO_EXTENSION));
            if(in_array($text,$tallow)){
                $tfname=uniqid('t_').'.'.$text;
                if(move_uploaded_file($_FILES['thumb_file']['tmp_name'],'../uploads/thumbs/'.$tfname)){
                    if($vid['thumbnail'] && str_starts_with($vid['thumbnail'],'uploads/') && file_exists('../'.$vid['thumbnail'])) unlink('../'.$vid['thumbnail']);
                    $thumb='uploads/thumbs/'.$tfname;
                }
            }
        }
        if(!$err){
            $slug = makeSlug($title);
            // ensure unique slug (excluding current)
            $exists = $pdo->prepare("SELECT id FROM videos WHERE slug=? AND id!=?");
            $exists->execute([$slug,$id]);
            if($exists->fetch()) $slug.='-'.time();

            $pdo->prepare("UPDATE videos SET title=?,slug=?,description=?,category=?,tags=?,embed_url=?,video_file=?,thumbnail=?,featured=?,seo_title=?,seo_desc=?,seo_keywords=? WHERE id=?")
                ->execute([$title,$slug,$desc,$cat,$tags,$embed,$videoFile,$thumb,$featured,$seoT,$seoD,$seoK,$id]);
            $msg='✅ Video updated successfully!';
            // Refresh data
            $v->execute([$id]); $vid=$v->fetch();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Edit Video — desimms Admin</title>
<link rel="stylesheet" href="style.css"/>
</head><body>
<div class="admin-wrap">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:10px">
      <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
      <span class="topbar-title">✏️ Edit Video</span>
    </div>
    <div class="topbar-actions">
      <a href="../video.php?id=<?= $id ?>" target="_blank" class="btn btn-ghost btn-sm">👁 Preview</a>
      <a href="videos.php" class="btn btn-ghost btn-sm">← All Videos</a>
    </div>
  </div>
  <div class="content">
    <?php if($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-error">❌ <?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

      <!-- Basic Info -->
      <div class="form-card">
        <h3>📝 Basic Information</h3>
        <div class="form-grid">
          <div class="form-group full">
            <label>Video Title *</label>
            <input type="text" name="title" value="<?= htmlspecialchars($vid['title']) ?>" required/>
          </div>
          <div class="form-group">
            <label>Category</label>
            <select name="category">
              <?php foreach($cats as $c): ?>
                <option value="<?= htmlspecialchars($c['slug']) ?>" <?= $c['slug']===$vid['category']?'selected':'' ?>><?= htmlspecialchars($c['icon'].' '.$c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tags (comma separated)</label>
            <input type="text" name="tags" value="<?= htmlspecialchars($vid['tags']) ?>" placeholder="bollywood, music, 2024"/>
          </div>
          <div class="form-group full">
            <label>Description</label>
            <textarea name="description"><?= htmlspecialchars($vid['description']) ?></textarea>
          </div>
          <div class="form-group" style="flex-direction:row;align-items:center;gap:10px">
            <label style="margin:0">⭐ Featured</label>
            <label class="toggle"><input type="checkbox" name="featured" <?= $vid['featured']?'checked':'' ?>/><span class="toggle-slider"></span></label>
          </div>
        </div>
      </div>

      <!-- Video Source -->
      <div class="form-card">
        <h3>🎬 Video Source</h3>
        <div class="form-grid">
          <div class="form-group full">
            <label>Embed URL</label>
            <input type="url" name="embed_url" value="<?= htmlspecialchars($vid['embed_url']) ?>" placeholder="https://www.youtube.com/embed/VIDEO_ID"/>
            <span class="form-hint">YouTube embed format: https://www.youtube.com/embed/VIDEO_ID</span>
          </div>
          <?php if($vid['video_file']): ?>
          <div class="form-group full">
            <label>Current Uploaded File</label>
            <div style="font-size:13px;background:var(--bg);padding:8px 12px;border-radius:6px;border:1px solid var(--border)">
              📁 <?= htmlspecialchars($vid['video_file']) ?>
            </div>
          </div>
          <?php endif; ?>
          <div class="form-group full">
            <label>Replace Video File (optional)</label>
            <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg"/>
          </div>
        </div>
      </div>

      <!-- Thumbnail -->
      <div class="form-card">
        <h3>🖼️ Thumbnail</h3>
        <div class="form-grid">
          <?php if($vid['thumbnail']): ?>
          <div class="form-group">
            <label>Current Thumbnail</label>
            <img src="<?= htmlspecialchars($vid['thumbnail']) ?>" style="height:70px;border-radius:6px;object-fit:cover;border:1px solid var(--border)" alt=""/>
          </div>
          <?php endif; ?>
          <div class="form-group">
            <label>Thumbnail URL</label>
            <input type="url" name="thumbnail" value="<?= htmlspecialchars($vid['thumbnail']) ?>" placeholder="https://example.com/thumb.jpg"/>
          </div>
          <div class="form-group">
            <label>Upload New Thumbnail (optional)</label>
            <input type="file" name="thumb_file" accept="image/*"/>
          </div>
        </div>
      </div>

      <!-- SEO -->
      <div class="form-card">
        <h3>🔍 SEO Settings</h3>
        <div class="form-grid">
          <div class="form-group full">
            <label>SEO Title</label>
            <input type="text" name="seo_title" value="<?= htmlspecialchars($vid['seo_title']) ?>" maxlength="70" placeholder="Video Title | desimms"/>
            <span class="form-hint">Recommended: 50–60 characters</span>
          </div>
          <div class="form-group full">
            <label>Meta Description</label>
            <textarea name="seo_desc" maxlength="160"><?= htmlspecialchars($vid['seo_desc']) ?></textarea>
            <span class="form-hint">Recommended: 150–160 characters</span>
          </div>
          <div class="form-group full">
            <label>SEO Keywords</label>
            <input type="text" name="seo_keywords" value="<?= htmlspecialchars($vid['seo_keywords']) ?>" placeholder="keyword1, keyword2, keyword3"/>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <button type="submit" class="btn btn-primary">💾 Save Changes</button>
        <a href="videos.php" class="btn btn-ghost">Cancel</a>
        <a href="delete_video.php?id=<?= $id ?>" class="btn btn-danger" onclick="return confirm('Delete this video permanently?')" style="margin-left:auto">🗑️ Delete</a>
      </div>
    </form>
  </div>
</div>
</div>
</body></html>
