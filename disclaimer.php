<?php require_once 'config.php';
$siteName = getSetting($pdo,'site_name','desimms');
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Disclaimer | <?= h($siteName) ?></title>
<meta name="description" content="Disclaimer for <?= h($siteName) ?>. Read our content and liability disclaimer."/>
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
<h1>Disclaimer</h1>
<p class="updated">Last updated: <?= date('F j, Y') ?></p>

<h2>Content Disclaimer</h2>
<p>All video content available on <?= h($siteName) ?> is either embedded from third-party platforms (such as YouTube) or uploaded by us for informational and entertainment purposes. We do not claim ownership of embedded third-party content. All rights to such content remain with their respective copyright owners.</p>

<h2>No Warranty</h2>
<p>The information and content on <?= h($siteName) ?> is provided "as is" without any representations or warranties, express or implied. We make no representations or warranties in relation to the content on this website or the information and materials provided.</p>

<h2>Limitation of Liability</h2>
<p><?= h($siteName) ?> will not be liable to you (whether under law, contract, tort, or otherwise) in relation to the contents of, or use of, or otherwise in connection with, this website for any indirect, special, or consequential loss, or any business losses, loss of revenue, income, profits, or anticipated savings, loss of contracts or business relationships, or loss of reputation or goodwill.</p>

<h2>External Links</h2>
<p>Our website may contain links to external websites. We have no control over the content of those sites and accept no responsibility for them or for any loss or damage that may arise from your use of them.</p>

<h2>Advertisements</h2>
<p>This website contains advertisements. We are not responsible for the content of advertisements displayed on our site. Advertisements are served by third-party ad networks and their content is outside our control. Clicking on advertisements may take you to third-party websites subject to their own terms and privacy policies.</p>

<h2>Contact</h2>
<p>If you have questions about this disclaimer, please <a href="contact.php">contact us</a>.</p>
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
