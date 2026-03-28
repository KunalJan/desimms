<?php require_once 'config.php';
$siteName = getSetting($pdo,'site_name','desimms');
?><!DOCTYPE html>
<html lang="en"><head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZL5VP34PWP"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-ZL5VP34PWP');
</script>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Advertise with Us | <?= h($siteName) ?></title>
<meta name="description" content="Advertise on <?= h($siteName) ?> and reach thousands of engaged viewers. Learn about our ad placements."/>
<link rel="stylesheet" href="style.css"/>
<link rel="icon" href="favicon.svg" type="image/svg+xml"/>
<link rel="apple-touch-icon" href="site-preview.svg"/>
<style>
.adspot-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:16px 0}
@media(max-width:500px){.adspot-grid{grid-template-columns:1fr}}
.adspot{background:var(--input);border:1px dashed rgba(233,69,96,.4);border-radius:var(--radius);padding:16px;text-align:center}
.adspot .name{font-family:var(--font-h);font-size:16px;font-weight:700;margin-bottom:4px}
.adspot .size{font-size:13px;color:var(--muted)}
.adspot .pos{font-size:12px;color:var(--accent);font-weight:600;margin-top:6px}
</style>
</head><body>
<header class="site-header">
  <div class="header-inner">
    <a href="index.php" class="logo">desi<span>mms</span></a>
    <form class="search-form" action="index.php" method="get" style="flex:1">
      <input type="search" name="q" placeholder="Search videos…" autocomplete="off"/>
      <button type="submit">🔍</button>
    </form>
  </div>
</header>
<div class="page-wrap" style="margin-top:20px">
<div class="legal-page">
<h1>Advertise with <?= h($siteName) ?></h1>
<p class="updated">Reach engaged viewers across all devices</p>

<p>Looking to promote your brand, product, or service? <?= h($siteName) ?> offers multiple advertising placements to help you reach our growing audience of entertainment and video content lovers.</p>

<h2>📊 Why Advertise with Us?</h2>
<ul>
  <li>Mobile-first platform — majority of our traffic comes from mobile users</li>
  <li>Diverse audience across entertainment, music, sports, tech, and lifestyle categories</li>
  <li>High engagement — visitors watch multiple videos per session</li>
  <li>Clean, fast-loading pages that don't frustrate users</li>
</ul>

<h2>📍 Available Ad Placements</h2>
<div class="adspot-grid">
  <div class="adspot">
    <div class="name">Header Banner</div>
    <div class="size">728×90 — 320×50 (mobile)</div>
    <div class="pos">Top of every page — maximum visibility</div>
  </div>
  <div class="adspot">
    <div class="name">Below Video Title</div>
    <div class="size">468×60 — 320×50 (mobile)</div>
    <div class="pos">Video pages only — high intent audience</div>
  </div>
  <div class="adspot">
    <div class="name">Footer Banner</div>
    <div class="size">728×90 — 320×50 (mobile)</div>
    <div class="pos">Bottom of every page</div>
  </div>
  <div class="adspot">
    <div class="name">Popup / Interstitial</div>
    <div class="size">500×400 (modal)</div>
    <div class="pos">Timed popup on page load</div>
  </div>
</div>

<h2>📧 Get in Touch</h2>
<p>To discuss advertising rates and packages, contact us at:</p>
<ul>
  <li><strong>Email:</strong> ads@<?= strtolower(h($siteName)) ?>.com</li>
  <li><strong>Contact Form:</strong> <a href="contact.php">Click here</a></li>
</ul>
<p>We also support self-serve advertising via Google AdSense and other major ad networks integrated directly into our platform.</p>
</div>
</div>
<footer class="site-footer" style="margin-top:20px">
  <div class="footer-inner">
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> <?= h($siteName) ?>.</span>
      <span><a href="terms.php">Terms</a> · <a href="privacy.php">Privacy</a> · <a href="dmca.php">DMCA</a> · <a href="2257.php">18 U.S.C. 2257</span>
    </div>
  </div>
</footer>
</body></html>
