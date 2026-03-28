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
<title>18 U.S.C. 2257 Compliance Statement | <?= h($siteName) ?></title>
<meta name="description" content="18 U.S.C. 2257 Record-Keeping Requirements Compliance Statement for <?= h($siteName) ?>."/>
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

<h1>18 U.S.C. § 2257 Record-Keeping Requirements Compliance Statement</h1>
<p class="updated">Last updated: <?= date('F j, Y') ?></p>

<p>
  <strong><?= h($siteName) ?></strong> ("the Website") is not a producer (primary or secondary) of any content found on this website as defined in 18 U.S.C. § 2257 and 28 C.F.R. 75.
</p>

<h2>Platform Nature</h2>
<p>
  <?= h($siteName) ?> is a video aggregation and sharing platform. All video content accessible through this website is either:
</p>
<ul>
  <li>Embedded from third-party video hosting platforms (such as YouTube, Vimeo, and other licensed hosting services), or</li>
  <li>Uploaded by users or administrators of this platform</li>
</ul>
<p>
  For content embedded from third-party platforms, those platforms are solely responsible for compliance with 18 U.S.C. § 2257 and all applicable record-keeping requirements. <?= h($siteName) ?> exercises no editorial control over such third-party content.
</p>

<h2>Content Policy</h2>
<p>
  <?= h($siteName) ?> is a <strong>general entertainment platform</strong>. We do not knowingly host, display, or distribute any sexually explicit material as defined under 18 U.S.C. § 2257. Our content policy strictly prohibits:
</p>
<ul>
  <li>Sexually explicit content of any kind</li>
  <li>Content involving minors in any inappropriate manner</li>
  <li>Content that violates any local, national, or international laws</li>
</ul>
<p>
  Any content found to violate these policies will be removed immediately upon discovery or upon receipt of a valid complaint.
</p>

<h2>Custodian of Records</h2>
<p>
  To the extent that any content on this website is subject to the requirements of 18 U.S.C. § 2257 and 28 C.F.R. 75, the records required by that statute and regulation are kept by the original content producer or the third-party platform from which the content is sourced.
</p>
<p>
  For any content produced directly by <?= h($siteName) ?>, records are maintained by:
</p>
<ul>
  <li><strong>Website:</strong> <?= h($siteName) ?></li>
  <li><strong>Email:</strong> legal@<?= strtolower(h($siteName)) ?>.com</li>
  <li><strong>Contact:</strong> <a href="contact.php">Contact Form</a></li>
</ul>

<h2>Reporting Violations</h2>
<p>
  If you believe any content on <?= h($siteName) ?> violates 18 U.S.C. § 2257 or our content policies, please report it immediately:
</p>
<ul>
  <li><strong>Email:</strong> legal@<?= strtolower(h($siteName)) ?>.com</li>
  <li><strong>DMCA / Content Removal:</strong> <a href="dmca.php">Submit a takedown request</a></li>
  <li><strong>Contact Form:</strong> <a href="contact.php">Click here</a></li>
</ul>
<p>We take all reports seriously and will act within <strong>24–48 hours</strong> of receiving a valid complaint.</p>

<h2>Related Laws &amp; Resources</h2>
<ul>
  <li><a href="https://www.law.cornell.edu/uscode/text/18/2257" target="_blank" rel="noopener">18 U.S.C. § 2257 — Full Text (Cornell Law)</a></li>
  <li><a href="https://www.ecpat.org" target="_blank" rel="noopener">ECPAT — End Child Prostitution and Trafficking</a></li>
  <li><a href="https://www.missingkids.org/gethelpnow/cybertipline" target="_blank" rel="noopener">NCMEC CyberTipline — Report Child Exploitation</a></li>
</ul>

<div style="background:#fef9c3;border:1px solid #fde68a;border-radius:8px;padding:16px;margin-top:20px">
  <strong>⚠️ Notice to Parents:</strong> <?= h($siteName) ?> is committed to a safe internet. Parents can use parental control software and internet filters to restrict access to video sharing sites. This website is rated for general audiences. For more information on protecting children online, visit <a href="https://www.connectsafely.org" target="_blank" rel="noopener">ConnectSafely.org</a>.
</div>

</div>
</div>

<footer class="site-footer" style="margin-top:20px">
  <div class="footer-inner">
    <div class="footer-cols">
      <div class="footer-col">
        <div class="footer-logo">desi<span>mms</span></div>
        <p style="font-size:12px;color:var(--muted);margin-top:8px"><?= h(getSetting($pdo,'footer_text','')) ?></p>
      </div>
      <div class="footer-col">
        <h4>Browse</h4>
        <a href="index.php">Home</a>
        <?php foreach(array_slice(getCategories($pdo),0,5) as $c): ?>
          <a href="index.php?cat=<?= urlencode($c['slug']) ?>"><?= h($c['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="footer-col">
        <h4>Company</h4>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
        <a href="advertise.php">Advertise</a>
        <a href="dmca.php">DMCA</a>
      </div>
      <div class="footer-col">
        <h4>Legal</h4>
        <a href="terms.php">Terms &amp; Conditions</a>
        <a href="privacy.php">Privacy Policy</a>
        <a href="dmca.php">DMCA / Content Removal</a>
        <a href="disclaimer.php">Disclaimer</a>
        <a href="2257.php">18 U.S.C. 2257</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> <?= h($siteName) ?>. All rights reserved.</span>
      <span>
        <a href="terms.php">Terms</a> ·
        <a href="privacy.php">Privacy</a> ·
        <a href="dmca.php">DMCA</a> ·
        <a href="2257.php">18 U.S.C. 2257</a>
      </span>
    </div>
  </div>
</footer>
</body></html>
