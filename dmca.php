<?php require_once 'config.php';
$siteName = getSetting($pdo,'site_name','desimms');
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    // In production, send an email here. For now just show confirmation.
    $msg='<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:14px 16px;margin-bottom:16px;color:#166534">✅ <strong>DMCA notice received.</strong> We will review your request within 5 business days and take appropriate action.</div>';
}
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>DMCA Policy &amp; Content Removal | <?= h($siteName) ?></title>
<meta name="description" content="DMCA takedown policy for <?= h($siteName) ?>. Submit a content removal request here."/>
<link rel="stylesheet" href="style.css"/>
<style>
.dmca-form label{display:block;font-size:13px;font-weight:600;color:var(--text2);margin:12px 0 4px}
.dmca-form input,.dmca-form textarea,.dmca-form select{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:var(--font);font-size:14px;background:var(--input);color:var(--text);outline:none;transition:border-color .2s}
.dmca-form input:focus,.dmca-form textarea:focus{border-color:var(--blue);box-shadow:0 0 0 2px rgba(0,119,204,.1)}
.dmca-form textarea{resize:vertical;min-height:100px}
.dmca-submit{background:var(--accent);color:#fff;border:none;padding:11px 28px;border-radius:50px;font-family:var(--font-h);font-size:15px;font-weight:700;letter-spacing:.04em;margin-top:14px;cursor:pointer;transition:background .2s}
.dmca-submit:hover{background:var(--accent-dark)}
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
<h1>DMCA Policy &amp; Content Removal</h1>
<p class="updated">Last updated: <?= date('F j, Y') ?></p>

<p><?= h($siteName) ?> respects intellectual property rights and expects our users to do the same. In accordance with the <strong>Digital Millennium Copyright Act (DMCA)</strong>, we will respond to valid notices of copyright infringement.</p>

<h2>Our Content Policy</h2>
<p><?= h($siteName) ?> <strong>does not host or store video files</strong> on our servers. All video content displayed on our site is embedded from third-party platforms (such as YouTube). We act as an aggregator and linking service only.</p>
<p>If you own the rights to content embedded on our site and wish to have it removed, please submit a takedown notice using the form below or email us directly.</p>

<h2>How to Submit a DMCA Takedown Notice</h2>
<p>To submit a valid DMCA takedown notice, you must provide:</p>
<ol>
  <li>Your full name, address, telephone number, and email address</li>
  <li>A description of the copyrighted work you claim has been infringed</li>
  <li>The URL of the infringing content on our website</li>
  <li>A statement that you have a good faith belief that the use is not authorized</li>
  <li>A statement that the information in your notice is accurate, and under penalty of perjury, that you are the copyright owner or authorized to act on their behalf</li>
  <li>Your electronic or physical signature</li>
</ol>

<h2>Response Time</h2>
<p>We will process valid DMCA notices within <strong>5 business days</strong>. Upon verification, the content will be removed or disabled promptly.</p>

<h2>Counter-Notice</h2>
<p>If you believe your content was removed in error, you may file a counter-notice. Contact us at the email below with your counter-notice details.</p>

<h2>Contact for DMCA Notices</h2>
<p><strong>Email:</strong> dmca@<?= strtolower(h($siteName)) ?>.com</p>

<h2>Submit Takedown Request</h2>
<?= $msg ?>
<form class="dmca-form" method="POST" action="dmca.php">
  <label>Your Full Name *</label>
  <input type="text" name="name" required placeholder="John Doe"/>

  <label>Your Email Address *</label>
  <input type="email" name="email" required placeholder="you@example.com"/>

  <label>Your Telephone / WhatsApp</label>
  <input type="text" name="phone" placeholder="+91 9876543210"/>

  <label>Copyrighted Work Description *</label>
  <textarea name="work_desc" required placeholder="Describe the original copyrighted content (e.g. 'My original music video titled XYZ published on YouTube on Jan 1, 2024')"></textarea>

  <label>URL of Infringing Page on <?= h($siteName) ?> *</label>
  <input type="url" name="infringing_url" required placeholder="https://<?= strtolower(h($siteName)) ?>.com/video.php?id=..."/>

  <label>Your Relationship to the Content *</label>
  <select name="relationship">
    <option>I am the copyright owner</option>
    <option>I am authorized to act on behalf of the copyright owner</option>
  </select>

  <label>Additional Details</label>
  <textarea name="details" placeholder="Any additional information…"></textarea>

  <p style="font-size:12px;color:var(--muted);margin-top:12px">
    By submitting this form, I declare under penalty of perjury that I am the copyright owner or authorized agent, and that the information in this notice is accurate.
  </p>
  <button type="submit" class="dmca-submit">Submit DMCA Notice</button>
</form>
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
