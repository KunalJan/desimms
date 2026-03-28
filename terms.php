<?php require_once 'config.php';
$siteName = getSetting($pdo,'site_name','desimms');
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Terms &amp; Conditions | <?= h($siteName) ?></title>
<meta name="description" content="Terms and Conditions for <?= h($siteName) ?>. Read our terms of use before accessing our platform."/>
<meta name="robots" content="index,follow"/>
<link rel="icon" href="favicon.svg" type="image/svg+xml"/>
<link rel="apple-touch-icon" href="site-preview.svg"/>
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
<h1>Terms &amp; Conditions</h1>
<p class="updated">Last updated: <?= date('F j, Y') ?></p>

<p>Welcome to <strong><?= h($siteName) ?></strong>. By accessing or using our website at <strong><?= h(BASE_URL) ?></strong>, you agree to be bound by these Terms and Conditions. Please read them carefully before using our service.</p>

<h2>1. Acceptance of Terms</h2>
<p>By accessing and using <?= h($siteName) ?>, you accept and agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree to these terms, please do not use our website.</p>

<h2>2. Description of Service</h2>
<p><?= h($siteName) ?> is a free video sharing and streaming platform that aggregates embedded video content from third-party sources. We do not host, store, or upload any video files on our servers unless explicitly stated. All videos are embedded from external platforms such as YouTube and other video hosting services.</p>

<h2>3. User Conduct</h2>
<p>By using <?= h($siteName) ?>, you agree that you will not:</p>
<ul>
  <li>Use the site for any unlawful purpose or in violation of any local, national, or international laws</li>
  <li>Attempt to gain unauthorized access to any part of the site or its related systems</li>
  <li>Upload, post, or transmit any content that is defamatory, obscene, offensive, or harmful</li>
  <li>Engage in any activity that disrupts or interferes with our services</li>
  <li>Use automated tools, bots, or scrapers to access our content without permission</li>
  <li>Reproduce or redistribute our content without written permission</li>
</ul>

<h2>4. Intellectual Property</h2>
<p>All video content displayed on <?= h($siteName) ?> remains the property of the respective copyright owners. <?= h($siteName) ?> does not claim ownership of any embedded video content. Our website design, logo, and original written content are the intellectual property of <?= h($siteName) ?>.</p>
<p>If you believe your copyrighted content has been embedded without permission, please refer to our <a href="dmca.php">DMCA Policy</a> to submit a takedown request.</p>

<h2>5. Disclaimer of Warranties</h2>
<p><?= h($siteName) ?> is provided on an "as is" and "as available" basis without any warranties, express or implied. We do not warrant that the service will be uninterrupted, error-free, or free of viruses or other harmful components. We do not guarantee the accuracy, completeness, or usefulness of any content on the site.</p>

<h2>6. Limitation of Liability</h2>
<p>To the fullest extent permitted by applicable law, <?= h($siteName) ?> shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of, or inability to use, our services. This includes but is not limited to loss of data, loss of profits, or any other damages.</p>

<h2>7. Third-Party Content &amp; Links</h2>
<p>Our website may contain links to third-party websites or embedded content from external platforms. We have no control over the content, privacy policies, or practices of any third-party sites. We encourage you to review the privacy policies and terms of service of any third-party sites you visit.</p>

<h2>8. Advertisements</h2>
<p>Our website displays advertisements from third-party ad networks. These advertisements may use cookies and tracking technologies as described in our Privacy Policy. We are not responsible for the content of advertisements or the products/services they promote.</p>

<h2>9. Content Removal</h2>
<p>We respect intellectual property rights and will remove content upon receiving a valid DMCA takedown notice. To report infringing content, please visit our <a href="dmca.php">DMCA page</a> or contact us at <strong>dmca@<?= strtolower(h($siteName)) ?>.com</strong>.</p>

<h2>10. Privacy Policy</h2>
<p>Your use of <?= h($siteName) ?> is also governed by our <a href="privacy.php">Privacy Policy</a>, which is incorporated into these Terms by reference.</p>

<h2>11. Modifications to Terms</h2>
<p>We reserve the right to modify these Terms and Conditions at any time. We will notify users of significant changes by updating the "Last updated" date at the top of this page. Your continued use of the site after any changes constitutes your acceptance of the new terms.</p>

<h2>12. Governing Law</h2>
<p>These Terms shall be governed by and construed in accordance with the laws of India, without regard to conflict of law provisions. Any disputes arising under these terms shall be subject to the exclusive jurisdiction of the courts of India.</p>

<h2>13. Contact Us</h2>
<p>If you have any questions about these Terms and Conditions, please contact us at:</p>
<ul>
  <li><strong>Email:</strong> legal@<?= strtolower(h($siteName)) ?>.com</li>
  <li><strong>Website:</strong> <a href="contact.php">Contact Form</a></li>
</ul>
</div>
</div>
<footer class="site-footer" style="margin-top:20px">
  <div class="footer-inner">
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> <?= h($siteName) ?>. All rights reserved.</span>
      <span><a href="terms.php">Terms</a> · <a href="privacy.php">Privacy</a> · <a href="dmca.php">DMCA · <a href="2257.php">18 U.S.C. 2257</a></span>
    </div>
  </div>
</footer>
</body></html>
