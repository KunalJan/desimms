<?php require_once 'config.php';
$siteName = getSetting($pdo,'site_name','desimms');
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Privacy Policy | <?= h($siteName) ?></title>
<meta name="description" content="Privacy Policy for <?= h($siteName) ?>. Learn how we collect, use, and protect your information."/>
<link rel="stylesheet" href="style.css"/>
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
<h1>Privacy Policy</h1>
<p class="updated">Last updated: <?= date('F j, Y') ?></p>

<p><?= h($siteName) ?> ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>

<h2>1. Information We Collect</h2>
<p>We may collect the following types of information:</p>
<ul>
  <li><strong>Log Data:</strong> IP address, browser type, pages visited, time and date of visit, referring URL</li>
  <li><strong>Cookies:</strong> Small files stored on your device to improve your experience</li>
  <li><strong>Usage Data:</strong> How you interact with our site, which videos you watch, search queries</li>
</ul>
<p>We do <strong>not</strong> collect personal registration information as our site does not require user accounts.</p>

<h2>2. How We Use Your Information</h2>
<ul>
  <li>To operate and improve our website and services</li>
  <li>To analyze usage patterns and improve user experience</li>
  <li>To display relevant advertisements via third-party ad networks</li>
  <li>To detect and prevent fraud or technical issues</li>
  <li>To comply with legal obligations</li>
</ul>

<h2>3. Cookies</h2>
<p>We use cookies to enhance your browsing experience. Types of cookies we use:</p>
<ul>
  <li><strong>Essential cookies:</strong> Required for the site to function properly</li>
  <li><strong>Analytics cookies:</strong> Help us understand how visitors use our site (e.g., Google Analytics)</li>
  <li><strong>Advertising cookies:</strong> Used by our ad partners to show relevant ads</li>
</ul>
<p>You can control cookies through your browser settings. Disabling cookies may affect site functionality.</p>

<h2>4. Third-Party Services</h2>
<p>We use third-party services that may collect information about you:</p>
<ul>
  <li><strong>Google Analytics:</strong> Website traffic analysis</li>
  <li><strong>Google AdSense / Ad Networks:</strong> Advertising services</li>
  <li><strong>YouTube Embeds:</strong> Videos embedded from YouTube are subject to Google's Privacy Policy</li>
</ul>

<h2>5. Data Retention</h2>
<p>We retain log data for a maximum of 90 days. Analytics data may be retained longer in aggregated, anonymized form.</p>

<h2>6. Data Security</h2>
<p>We implement reasonable security measures to protect your information. However, no internet transmission is 100% secure, and we cannot guarantee absolute security.</p>

<h2>7. Children's Privacy</h2>
<p><?= h($siteName) ?> is not directed at children under 13 years of age. We do not knowingly collect personal information from children. If you believe we have inadvertently collected data from a child, please contact us immediately.</p>

<h2>8. Your Rights</h2>
<p>You have the right to:</p>
<ul>
  <li>Request access to your personal data</li>
  <li>Request deletion of your data</li>
  <li>Opt out of analytics tracking using browser settings or ad-opt-out tools</li>
</ul>

<h2>9. Changes to This Policy</h2>
<p>We may update this Privacy Policy periodically. Changes will be reflected by updating the "Last updated" date above. Your continued use of the site constitutes acceptance of any changes.</p>

<h2>10. Contact Us</h2>
<p>For privacy-related concerns: <strong>privacy@<?= strtolower(h($siteName)) ?>.com</strong></p>
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
