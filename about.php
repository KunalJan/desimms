<?php require_once 'config.php';
$siteName = getSetting($pdo,'site_name','desimms');
$siteDesc = getSetting($pdo,'site_description','');
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
<title>About Us | <?= h($siteName) ?></title>
<meta name="description" content="Learn about <?= h($siteName) ?> — a free video sharing and streaming platform."/>
<meta name="robots" content="index,follow"/>
<link rel="stylesheet" href="style.css"/>
<link rel="icon" href="favicon.svg" type="image/svg+xml"/>
<link rel="apple-touch-icon" href="site-preview.svg"/>
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
<h1>About <?= h($siteName) ?></h1>
<p class="updated">Your free video sharing platform</p>

<p><?= h($siteName) ?> is a free online video platform dedicated to bringing you the best entertainment, music, sports, education, travel, and lifestyle content — all in one place.</p>

<h2>🎬 What We Do</h2>
<p>We curate and share video content from across the web, making it easy for you to discover new videos, channels, and creators. Whether you're looking for Bollywood music videos, cricket highlights, comedy clips, cooking tutorials, or travel vlogs — <?= h($siteName) ?> has something for everyone.</p>

<h2>🌐 Our Mission</h2>
<p>Our mission is simple: to make great video content accessible to everyone, for free. We believe entertainment should not come with a price tag. We are supported by advertising which allows us to keep the platform free for all users.</p>

<h2>📱 Mobile-First</h2>
<p><?= h($siteName) ?> is built mobile-first. Whether you're on a phone, tablet, or desktop, the experience is fast, clean, and easy to use.</p>

<h2>🔒 Content Policy</h2>
<p>We are committed to hosting only legitimate, family-friendly content. All embedded content is sourced from reputable platforms. If you find content that violates our policies or your copyright, please use our <a href="dmca.php">DMCA page</a> to report it. We respond to all valid requests within 5 business days.</p>

<h2>📧 Contact</h2>
<p>For general inquiries, please visit our <a href="contact.php">Contact page</a>.<br>
For business/advertising: <strong>ads@<?= strtolower(h($siteName)) ?>.com</strong><br>
For DMCA/legal: <strong>legal@<?= strtolower(h($siteName)) ?>.com</strong></p>
</div>
</div>
<footer class="site-footer" style="margin-top:20px">
  <div class="footer-inner">
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> <?= h($siteName) ?>. All rights reserved.</span>
      <span><a href="terms.php">Terms</a> · <a href="privacy.php">Privacy</a> · <a href="dmca.php">DMCA</a> · <a href="2257.php">18 U.S.C. 2257</span>
    </div>
  </div>
</footer>
</body></html>
