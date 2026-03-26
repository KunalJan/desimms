<?php
require_once 'auth.php';
$msg=''; $err='';

if($_SERVER['REQUEST_METHOD']==='POST'){

    // Change password
    if(isset($_POST['change_pass'])){
        $cur  = $_POST['current_pass']??'';
        $new  = $_POST['new_pass']??'';
        $conf = $_POST['confirm_pass']??'';
        $hash = getSetting($pdo,'admin_password','');
        if(!password_verify($cur,$hash)){ $err='Current password is incorrect.'; }
        elseif(strlen($new)<6){ $err='New password must be at least 6 characters.'; }
        elseif($new!==$conf){ $err='Passwords do not match.'; }
        else {
            $newHash = password_hash($new, PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE settings SET `value`=? WHERE `key`='admin_password'")->execute([$newHash]);
            $msg='Password changed successfully!';
        }
    }

    // Save settings
    if(isset($_POST['save_settings'])){
        $fields = ['site_name','site_tagline','site_description','site_keywords','videos_per_page','maintenance_mode','footer_text'];
        foreach($fields as $f){
            $val = trim($_POST[$f]??'');
            $pdo->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=?")->execute([$f,$val,$val]);
        }
        $msg='Settings saved!';
    }
}

// Load current settings
$s = [];
$rows = $pdo->query("SELECT `key`,`value` FROM settings")->fetchAll();
foreach($rows as $r) $s[$r['key']] = $r['value'];
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Settings — desimms Admin</title>
<link rel="stylesheet" href="style.css"/>
</head><body>
<div class="admin-wrap">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:10px">
      <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
      <span class="topbar-title">⚙️ Settings &amp; SEO</span>
    </div>
  </div>
  <div class="content">
    <?php if($msg): ?><div class="alert alert-success">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-error">❌ <?= htmlspecialchars($err) ?></div><?php endif; ?>

    <!-- Site Settings -->
    <form method="POST">
      <div class="form-card">
        <h3>🌐 Site Settings</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Site Name</label>
            <input type="text" name="site_name" value="<?= htmlspecialchars($s['site_name']??'desimms') ?>"/>
          </div>
          <div class="form-group">
            <label>Site Tagline</label>
            <input type="text" name="site_tagline" value="<?= htmlspecialchars($s['site_tagline']??'') ?>" placeholder="Watch Anything, Anywhere"/>
          </div>
          <div class="form-group full">
            <label>Footer Text</label>
            <input type="text" name="footer_text" value="<?= htmlspecialchars($s['footer_text']??'') ?>" placeholder="Short description for footer"/>
          </div>
          <div class="form-group">
            <label>Videos Per Page</label>
            <input type="number" name="videos_per_page" value="<?= (int)($s['videos_per_page']??12) ?>" min="4" max="60"/>
          </div>
          <div class="form-group" style="flex-direction:row;align-items:center;gap:12px">
            <label style="margin:0">🔧 Maintenance Mode</label>
            <label class="toggle">
              <input type="checkbox" name="maintenance_mode" value="1" <?= ($s['maintenance_mode']??'0')==='1'?'checked':'' ?>/>
              <span class="toggle-slider"></span>
            </label>
            <span style="font-size:12px;color:var(--muted)">Visitors see a maintenance page</span>
          </div>
        </div>
      </div>

      <div class="form-card">
        <h3>🔍 Global SEO (Homepage defaults)</h3>
        <div class="alert alert-info">These appear in search results for your homepage. Each video has its own SEO settings too.</div>
        <div class="form-grid">
          <div class="form-group full">
            <label>Homepage Meta Description</label>
            <textarea name="site_description" maxlength="160"><?= htmlspecialchars($s['site_description']??'') ?></textarea>
            <span class="form-hint">Recommended: 150–160 characters. Appears in Google search results.</span>
          </div>
          <div class="form-group full">
            <label>Homepage Meta Keywords</label>
            <input type="text" name="site_keywords" value="<?= htmlspecialchars($s['site_keywords']??'') ?>" placeholder="desimms, free videos, watch online, entertainment"/>
            <span class="form-hint">Comma-separated. Note: Google doesn't heavily use meta keywords, but other engines do.</span>
          </div>
        </div>
      </div>

      <div class="form-card" style="background:#f0fdf4;border-color:#86efac">
        <h3 style="color:#166534">📈 SEO Tips</h3>
        <div style="font-size:13px;line-height:1.9;color:#166534">
          ✅ <strong>Every video</strong> should have a unique SEO title, meta description, and keywords (set when adding/editing videos)<br>
          ✅ Use descriptive <strong>video titles</strong> — include year, topic, and relevant keywords<br>
          ✅ Add <strong>tags</strong> to videos — they power the #tag links and internal search<br>
          ✅ <strong>Thumbnails</strong> improve click-through rate from search results<br>
          ✅ This site auto-generates <strong>JSON-LD schema</strong> for every video page — Google can index them as video results<br>
          ✅ Add your site to <strong>Google Search Console</strong> and submit your sitemap
        </div>
      </div>

      <button type="submit" name="save_settings" class="btn btn-primary">💾 Save Settings</button>
    </form>

    <!-- Change Password -->
    <form method="POST" style="margin-top:24px">
      <div class="form-card">
        <h3>🔒 Change Admin Password</h3>
        <div class="form-grid">
          <div class="form-group full">
            <label>Current Password</label>
            <input type="password" name="current_pass" required/>
          </div>
          <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_pass" minlength="6" required/>
            <span class="form-hint">Minimum 6 characters</span>
          </div>
          <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_pass" required/>
          </div>
        </div>
        <button type="submit" name="change_pass" class="btn btn-blue" style="margin-top:14px">🔒 Update Password</button>
      </div>
    </form>

  </div>
</div>
</div>
</body></html>
