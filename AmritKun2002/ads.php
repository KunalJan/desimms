<?php
require_once __DIR__ . '/../config.php';
requireAdmin();

$msg = '';

// ── SAVE ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ads = db()->query("SELECT id,position FROM ads")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($ads as $ad) {
        $id  = $ad['id'];
        $pos = $ad['position'];

        $adType    = $_POST["type_$id"]   ?? 'image';
        $adUrl     = trim($_POST["url_$id"]     ?? '');
        $adImage   = trim($_POST["image_$id"]   ?? '');
        $adCode    = trim($_POST["code_$id"]    ?? '');
        $label     = trim($_POST["label_$id"]   ?? $pos);
        $delay     = (int)($_POST["delay_$id"]  ?? 3);
        $isActive  = isset($_POST["active_$id"]) ? 1 : 0;

        db()->prepare("UPDATE ads SET ad_type=?,ad_url=?,ad_image=?,ad_code=?,label=?,popup_delay=?,is_active=? WHERE id=?")
           ->execute([$adType,$adUrl,$adImage,$adCode,$label,$delay,$isActive,$id]);
    }

    // Add new ad slot
    if (!empty($_POST['new_position'])) {
        db()->prepare("INSERT INTO ads (position,label,ad_type,ad_url,ad_image,is_active) VALUES (?,?,?,?,?,1)")
           ->execute([$_POST['new_position'],$_POST['new_label']??'New Ad',$_POST['new_type']??'image',$_POST['new_url']??'',$_POST['new_image']??'']);
    }

    $msg = '✅ Ad settings saved successfully!';
}

$ads = db()->query("SELECT * FROM ads ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

$activePage = 'ads';
$pageTitle  = 'Manage Ads';
include __DIR__ . '/_layout.php';
?>

<div class="page-header">
  <h1 class="page-title">💰 Manage Ads</h1>
</div>

<?php if ($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<!-- AD SLOTS GUIDE -->
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px;margin-bottom:20px;font-size:13px;line-height:1.7">
  <strong>📍 Ad Slot Positions:</strong><br>
  <b>header</b> — Banner shown at very top of every page (728×90 recommended)<br>
  <b>footer</b> — Banner at very bottom of every page (728×90 recommended)<br>
  <b>below_title</b> — Banner shown on video page, just below the title (468×60 recommended)<br>
  <b>popup</b> — Popup ad that appears after N seconds. Enable/disable and set delay.<br><br>
  <strong>💡 Ad Types:</strong><br>
  <b>image</b> — Paste an image URL + destination link URL<br>
  <b>code</b> — Paste raw HTML (e.g. Google AdSense &lt;script&gt; tags, pop-under scripts, any ad network code)<br><br>
  <strong>🔗 Popup / Pop-under links:</strong> Use <b>ad_type = code</b> and paste your popup script (e.g. PropellerAds, Hilltopads, etc.) into the Code field.
</div>

<form method="post">
  <?php foreach ($ads as $ad): ?>
  <?php $id = $ad['id']; ?>
  <div class="form-card" style="margin-bottom:18px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
      <div>
        <span style="font-family:var(--font-h);font-size:20px;font-weight:700"><?= strtoupper(str_replace('_',' ',$ad['position'])) ?></span>
        <span class="badge <?= $ad['is_active']?'badge-green':'badge-red' ?>" style="margin-left:8px"><?= $ad['is_active']?'Active':'Inactive' ?></span>
      </div>
      <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px;font-weight:600">
        <input type="checkbox" name="active_<?= $id ?>" <?= $ad['is_active']?'checked':'' ?> style="width:auto;accent-color:var(--accent)"/> Enable
      </label>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label>Label / Name</label>
        <input type="text" name="label_<?= $id ?>" value="<?= htmlspecialchars($ad['label'] ?? '') ?>" placeholder="e.g. Header Banner"/>
      </div>

      <div class="form-group">
        <label>Ad Type</label>
        <select name="type_<?= $id ?>" onchange="toggleAdType(<?= $id ?>,this.value)">
          <option value="image" <?= $ad['ad_type']==='image'?'selected':'' ?>>🖼 Image + Link</option>
          <option value="code"  <?= $ad['ad_type']==='code' ?'selected':'' ?>>📋 Raw HTML / Ad Code (AdSense, scripts)</option>
        </select>
      </div>

      <!-- Image type fields -->
      <div class="form-group img-fields-<?= $id ?>" <?= $ad['ad_type']==='code'?'style="display:none"':'' ?>>
        <label>Ad Image URL</label>
        <input type="url" name="image_<?= $id ?>" value="<?= htmlspecialchars($ad['ad_image'] ?? '') ?>" placeholder="https://example.com/banner.jpg"/>
        <span class="hint">Direct URL to your banner image</span>
      </div>

      <div class="form-group img-fields-<?= $id ?>" <?= $ad['ad_type']==='code'?'style="display:none"':'' ?>>
        <label>Destination URL (click link)</label>
        <input type="url" name="url_<?= $id ?>" value="<?= htmlspecialchars($ad['ad_url'] ?? '') ?>" placeholder="https://advertiser-site.com"/>
      </div>

      <!-- Code type field -->
      <div class="form-group form-full code-fields-<?= $id ?>" <?= $ad['ad_type']!=='code'?'style="display:none"':'' ?>>
        <label>Raw Ad Code (HTML / JavaScript)</label>
        <textarea name="code_<?= $id ?>" rows="6" placeholder="Paste your AdSense code, PropellerAds script, pop-under code, or any ad network HTML here…"><?= htmlspecialchars($ad['ad_code'] ?? '') ?></textarea>
        <span class="hint">💡 Paste AdSense &lt;script&gt; blocks, native ad widgets, or any network's HTML code here. It will be output as-is.</span>
      </div>

      <?php if ($ad['position'] === 'popup'): ?>
      <div class="form-group">
        <label>Popup Delay (seconds)</label>
        <input type="number" name="delay_<?= $id ?>" min="0" max="60" value="<?= (int)($ad['popup_delay'] ?? 3) ?>"/>
        <span class="hint">How many seconds after page load before the popup shows</span>
      </div>
      <?php endif; ?>

    </div>

    <!-- Preview -->
    <?php if ($ad['ad_image'] && $ad['ad_type'] === 'image'): ?>
    <div style="margin-top:10px">
      <span style="font-size:11px;color:var(--muted)">Current preview:</span><br>
      <img src="<?= htmlspecialchars($ad['ad_image']) ?>" style="max-height:90px;border-radius:4px;margin-top:4px"/>
    </div>
    <?php endif; ?>

  </div>
  <?php endforeach; ?>

  <hr class="section-divider"/>
  <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px">💾 Save All Ad Settings</button>
</form>

<!-- ADD NEW AD SLOT -->
<div class="form-card" style="margin-top:24px">
  <h2 style="font-family:var(--font-h);font-size:20px;font-weight:700;margin-bottom:14px">➕ Add New Ad Slot</h2>
  <form method="post">
    <div class="form-grid">
      <div class="form-group">
        <label>Position Name</label>
        <input type="text" name="new_position" placeholder="e.g. sidebar, between_videos"/>
      </div>
      <div class="form-group">
        <label>Label</label>
        <input type="text" name="new_label" placeholder="Friendly name"/>
      </div>
      <div class="form-group">
        <label>Type</label>
        <select name="new_type">
          <option value="image">Image + Link</option>
          <option value="code">Raw HTML Code</option>
        </select>
      </div>
      <div class="form-group">
        <label>Image URL</label>
        <input type="url" name="new_image" placeholder="https://..."/>
      </div>
      <div class="form-group form-full">
        <label>Destination URL</label>
        <input type="url" name="new_url" placeholder="https://..."/>
      </div>
    </div>
    <button type="submit" class="btn btn-secondary" style="margin-top:12px">➕ Add Slot</button>
  </form>
</div>

<script>
function toggleAdType(id, val) {
  document.querySelectorAll('.img-fields-'+id).forEach(el => el.style.display = val==='code'?'none':'');
  document.querySelectorAll('.code-fields-'+id).forEach(el => el.style.display = val==='code'?'':'none');
}
</script>

</main></div></body></html>
