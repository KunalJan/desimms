<?php
require_once 'auth.php';
$cats = getCategories($pdo);
$msg = '';
$err = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $title    = trim($_POST['title']??'');
    $desc     = trim($_POST['description']??'');
    $cat      = trim($_POST['category']??'general');
    $tags     = trim($_POST['tags']??'');
    $embed    = trim($_POST['embed_url']??'');
    $thumb    = trim($_POST['thumbnail']??'');
    $featured = isset($_POST['featured'])?1:0;
    $seoT     = trim($_POST['seo_title']??'');
    $seoD     = trim($_POST['seo_desc']??'');
    $seoK     = trim($_POST['seo_keywords']??'');

    if(!$title){ $err='Title is required.'; }
    else {
        // Handle video file upload
        $videoFile = '';
        if(!empty($_FILES['video_file']['name'])){
            $allowed = ['mp4','webm','ogg','mkv'];
            $ext = strtolower(pathinfo($_FILES['video_file']['name'],PATHINFO_EXTENSION));
            if(!in_array($ext,$allowed)){ $err='Only MP4, WebM, OGG, MKV files allowed.'; }
            else {
                $fname = uniqid('v_').'.'.$ext;
                $dest  = '../uploads/videos/'.$fname;
                if(move_uploaded_file($_FILES['video_file']['tmp_name'],$dest)){
                    $videoFile = $fname;
                } else { $err='File upload failed. Check uploads/videos/ folder permissions.'; }
            }
        }
        // Handle thumbnail upload
        if(!empty($_FILES['thumb_file']['name']) && empty($err)){
            $tallow = ['jpg','jpeg','png','webp','gif'];
            $text   = strtolower(pathinfo($_FILES['thumb_file']['name'],PATHINFO_EXTENSION));
            if(in_array($text,$tallow)){
                $tfname = uniqid('t_').'.'.$text;
                $tdest  = '../uploads/thumbs/'.$tfname;
                if(move_uploaded_file($_FILES['thumb_file']['tmp_name'],$tdest)){
                    $thumb = 'uploads/thumbs/'.$tfname;
                }
            }
        }

        if(!$err){
            $slug = makeSlug($title);
            // ensure unique slug
            $exists = $pdo->prepare("SELECT id FROM videos WHERE slug=?");
            $exists->execute([$slug]);
            if($exists->fetch()) $slug.='-'.time();

            $ins = $pdo->prepare("INSERT INTO videos
                (title,slug,description,category,tags,embed_url,video_file,thumbnail,featured,seo_title,seo_desc,seo_keywords)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
            $ins->execute([$title,$slug,$desc,$cat,$tags,$embed,$videoFile,$thumb,$featured,$seoT,$seoD,$seoK]);
            $newId = $pdo->lastInsertId();
            $msg = "✅ Video added successfully! <a href='../video.php?id=$newId' target='_blank'>View →</a>";
            // Reset fields
            $title=$desc=$cat='general';$tags=$embed=$thumb=$seoT=$seoD=$seoK='';$featured=0;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Add Video — desimms Admin</title>
<link rel="stylesheet" href="style.css"/>
</head><body>
<div class="admin-wrap">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:10px">
      <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
      <span class="topbar-title">Add New Video</span>
    </div>
    <div class="topbar-actions">
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
            <input type="text" name="title" value="<?= htmlspecialchars($title??'') ?>" placeholder="Enter video title" required/>
          </div>
          <div class="form-group">
            <label>Category</label>
            <select name="category">
              <?php foreach($cats as $c): ?>
                <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($cat??'')===$c['slug']?'selected':'' ?>><?= htmlspecialchars($c['icon'].' '.$c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tags (comma separated)</label>
            <input type="text" name="tags" value="<?= htmlspecialchars($tags??'') ?>" placeholder="bollywood, music, 2024"/>
            <span class="form-hint">Used for search and related videos</span>
          </div>
          <div class="form-group full">
            <label>Description</label>
            <textarea name="description" placeholder="Describe this video…"><?= htmlspecialchars($desc??'') ?></textarea>
          </div>
          <div class="form-group" style="flex-direction:row;align-items:center;gap:10px">
            <label style="margin:0">⭐ Featured Video</label>
            <label class="toggle"><input type="checkbox" name="featured" <?= ($featured??0)?'checked':'' ?>/><span class="toggle-slider"></span></label>
          </div>
        </div>
      </div>

      <!-- Video Source -->
      <div class="form-card">
        <h3>🎬 Video Source (choose one)</h3>
        <div class="form-grid">
          <div class="form-group full">
            <label>Embed URL (YouTube, Vimeo, etc.)</label>
            <input type="url" name="embed_url" value="<?= htmlspecialchars($embed??'') ?>" placeholder="https://www.youtube.com/embed/VIDEO_ID"/>
            <span class="form-hint">
              📌 <strong>YouTube:</strong> Go to video → Share → Embed → copy src="…" URL<br>
              📌 <strong>Format:</strong> https://www.youtube.com/embed/dQw4w9WgXcQ
            </span>
          </div>
          <div class="form-group full" style="border-top:1px solid var(--border);padding-top:14px;margin-top:4px">
            <label>— OR — Upload Video File (MP4, WebM, OGG)</label>
            <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg"/>
            <span class="form-hint">Max size depends on your PHP upload_max_filesize setting (check php.ini)</span>
          </div>
        </div>
      </div>

      <!-- Thumbnail -->
      <div class="form-card">
        <h3>🖼️ Thumbnail</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Thumbnail URL</label>
            <input type="url" name="thumbnail" value="<?= htmlspecialchars($thumb??'') ?>" placeholder="https://example.com/thumb.jpg"/>
          </div>
          <div class="form-group">
            <label>— OR — Upload Thumbnail</label>
            <input type="file" name="thumb_file" accept="image/*"/>
          </div>
        </div>
      </div>

      <!-- SEO -->
      <div class="form-card">
        <h3>🔍 SEO Settings</h3>
        <div class="alert alert-info">These fields boost your video in Google search results.</div>
        <div class="form-grid">
          <div class="form-group full">
            <label>SEO Title <span style="color:var(--muted);font-weight:400">(leave blank to auto-generate from video title)</span></label>
            <input type="text" name="seo_title" value="<?= htmlspecialchars($seoT??'') ?>" placeholder="Best Music Video 2024 | desimms" maxlength="70"/>
            <span class="form-hint">Recommended: 50–60 characters</span>
          </div>
          <div class="form-group full">
            <label>Meta Description</label>
            <textarea name="seo_desc" maxlength="160" placeholder="Describe this video in 150–160 characters for search engines…"><?= htmlspecialchars($seoD??'') ?></textarea>
            <span class="form-hint">Recommended: 150–160 characters</span>
          </div>
          <div class="form-group full">
            <label>SEO Keywords</label>
            <input type="text" name="seo_keywords" value="<?= htmlspecialchars($seoK??'') ?>" placeholder="bollywood songs 2024, hindi music, top hits"/>
            <span class="form-hint">Comma-separated keywords for meta tag</span>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary">✅ Publish Video</button>
        <a href="videos.php" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>
</div>
</div>
</body></html>
