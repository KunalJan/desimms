<?php
require_once __DIR__ . '/../config.php';
requireAdmin();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    db()->prepare("UPDATE seo SET site_name=?,tagline=?,meta_description=?,meta_keywords=?,google_analytics=?,og_image=?,robots_txt=? WHERE id=1")
       ->execute([
           trim($_POST['site_name']        ?? 'desimms'),
           trim($_POST['tagline']          ?? ''),
           trim($_POST['meta_description'] ?? ''),
           trim($_POST['meta_keywords']    ?? ''),
           trim($_POST['google_analytics'] ?? ''),
           trim($_POST['og_image']         ?? ''),
           trim($_POST['robots_txt']       ?? ''),
       ]);
    $msg = '✅ SEO settings saved!';
}

$seo = getSEO();
$activePage = 'seo';
$pageTitle  = 'SEO Settings';
include __DIR__ . '/_layout.php';
?>

<div class="page-header">
  <h1 class="page-title">🔍 SEO Settings</h1>
</div>

<?php if ($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<!-- Guide -->
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:16px;margin-bottom:20px;font-size:13px;line-height:1.7">
  <strong>📋 SEO Tips:</strong><br>
  <b>Meta Description</b> — Shown in Google search results. Keep it under 155 characters.<br>
  <b>Meta Keywords</b> — Less important now but still add relevant words separated by commas.<br>
  <b>Google Analytics</b> — Paste your full GA4 &lt;script&gt; tag. It will be added to every page's &lt;head&gt;.<br>
  <b>OG Image</b> — Image shown when someone shares your site on WhatsApp, Facebook etc. (1200×630px recommended)<br>
  <b>robots.txt</b> — Controls which pages search engines can crawl.
</div>

<form method="post">
  <div class="form-card">
    <h3 style="font-family:var(--font-h);font-size:18px;font-weight:700;margin-bottom:16px">Basic Info</h3>
    <div class="form-grid">
      <div class="form-group">
        <label>Site Name</label>
        <input type="text" name="site_name" value="<?= htmlspecialchars($seo['site_name'] ?? 'desimms') ?>"/>
      </div>
      <div class="form-group">
        <label>Tagline</label>
        <input type="text" name="tagline" value="<?= htmlspecialchars($seo['tagline'] ?? '') ?>" placeholder="Watch Anything, Anywhere"/>
      </div>
      <div class="form-group form-full">
        <label>Meta Description <span style="color:var(--muted);font-weight:400">(shown in Google)</span></label>
        <textarea name="meta_description" rows="3" placeholder="Describe your site in 1-2 sentences (max 155 chars)…"><?= htmlspecialchars($seo['meta_description'] ?? '') ?></textarea>
        <span class="hint" id="descCount">0 / 155 characters</span>
      </div>
      <div class="form-group form-full">
        <label>Meta Keywords</label>
        <input type="text" name="meta_keywords" value="<?= htmlspecialchars($seo['meta_keywords'] ?? '') ?>" placeholder="videos, watch online, free streaming, desimms"/>
      </div>
    </div>

    <hr class="section-divider"/>
    <h3 style="font-family:var(--font-h);font-size:18px;font-weight:700;margin-bottom:16px">Social Sharing</h3>
    <div class="form-group">
      <label>OG / Social Share Image URL</label>
      <input type="url" name="og_image" value="<?= htmlspecialchars($seo['og_image'] ?? '') ?>" placeholder="https://yourdomain.com/og-image.jpg"/>
      <span class="hint">Image shown when sharing on Facebook, WhatsApp, Twitter. 1200×630px recommended.</span>
    </div>

    <hr class="section-divider"/>
    <h3 style="font-family:var(--font-h);font-size:18px;font-weight:700;margin-bottom:16px">Google Analytics / Tracking</h3>
    <div class="form-group">
      <label>Google Analytics Code (or any tracking script)</label>
      <textarea name="google_analytics" rows="6" placeholder="Paste your GA4 <script> tag here, e.g.:&#10;<!-- Google tag (gtag.js) -->&#10;<script async src=&quot;https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX&quot;></script>&#10;<script>..."><?= htmlspecialchars($seo['google_analytics'] ?? '') ?></textarea>
      <span class="hint">This is added to the &lt;head&gt; of every page. Also works for Facebook Pixel, TikTok Pixel, etc.</span>
    </div>

    <hr class="section-divider"/>
    <h3 style="font-family:var(--font-h);font-size:18px;font-weight:700;margin-bottom:16px">robots.txt</h3>
    <div class="form-group">
      <label>robots.txt Content</label>
      <textarea name="robots_txt" rows="5"><?= htmlspecialchars($seo['robots_txt'] ?? "User-agent: *\nAllow: /") ?></textarea>
      <span class="hint">To serve this as /robots.txt, create a robots.php file that reads and outputs this value.</span>
    </div>

  </div>

  <button type="submit" class="btn btn-primary" style="margin-top:16px;width:100%;justify-content:center;padding:13px">💾 Save SEO Settings</button>
</form>

<script>
const desc = document.querySelector('[name=meta_description]');
const cnt  = document.getElementById('descCount');
function updateCount() { cnt.textContent = desc.value.length + ' / 155 characters'; cnt.style.color = desc.value.length > 155 ? '#dc2626' : ''; }
desc.addEventListener('input', updateCount);
updateCount();
</script>

</main></div></body></html>
