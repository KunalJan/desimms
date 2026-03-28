<?php require_once 'config.php';
$siteName = getSetting($pdo,'site_name','desimms');
$msg=''; $err='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $name    = trim($_POST['name']??'');
    $email   = trim($_POST['email']??'');
    $subject = trim($_POST['subject']??'');
    $body    = trim($_POST['message']??'');

    if(!$name||!$email||!$subject||!$body){ $err='Please fill in all fields.'; }
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){ $err='Please enter a valid email address.'; }
    else {
        // In production: replace with mail() or PHPMailer
        // mail("admin@$siteName.com","[$siteName Contact] $subject","From: $name <$email>\n\n$body");
        $msg='✅ Thank you! Your message has been received. We will reply within 2–3 business days.';
    }
}
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Contact Us | <?= h($siteName) ?></title>
<meta name="description" content="Contact <?= h($siteName) ?> — Get in touch with us for support, advertising, or general inquiries."/>
<meta name="robots" content="index,follow"/>
<link rel="icon" href="favicon.svg" type="image/svg+xml"/>
<link rel="apple-touch-icon" href="site-preview.svg"/>
<link rel="stylesheet" href="style.css"/>
<style>
.contact-grid{display:grid;grid-template-columns:1fr;gap:20px}
@media(min-width:600px){.contact-grid{grid-template-columns:1fr 1fr}}
.contact-form label{display:block;font-size:13px;font-weight:600;color:var(--text2);margin:12px 0 4px}
.contact-form input,.contact-form textarea,.contact-form select{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:var(--font);font-size:14px;color:var(--text);background:var(--input);outline:none;transition:border-color .2s}
.contact-form input:focus,.contact-form textarea:focus{border-color:var(--blue);box-shadow:0 0 0 2px rgba(0,119,204,.1)}
.contact-form textarea{resize:vertical;min-height:120px}
.contact-submit{background:var(--accent);color:#fff;border:none;padding:11px 28px;border-radius:50px;font-family:var(--font-h);font-size:15px;font-weight:700;letter-spacing:.04em;margin-top:14px;cursor:pointer;transition:background .2s}
.contact-submit:hover{background:var(--accent-dark)}
.info-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:18px 20px}
.info-card h3{font-family:var(--font-h);font-size:16px;font-weight:700;margin-bottom:12px}
.info-item{display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;font-size:13px;color:var(--text2)}
.info-icon{font-size:18px;flex-shrink:0;margin-top:1px}
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
  <div style="max-width:800px;margin:0 auto">
    <h1 style="font-family:var(--font-h);font-size:28px;font-weight:900;margin-bottom:6px">Contact Us</h1>
    <p style="color:var(--muted);margin-bottom:20px">We'd love to hear from you. Fill in the form or use the contact details below.</p>

    <?php if($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-error">❌ <?= htmlspecialchars($err) ?></div><?php endif; ?>

    <div class="contact-grid">
      <!-- Form -->
      <div class="legal-page" style="padding:20px 22px">
        <form method="POST" class="contact-form">
          <label>Your Name *</label>
          <input type="text" name="name" required placeholder="John Doe"/>

          <label>Email Address *</label>
          <input type="email" name="email" required placeholder="you@example.com"/>

          <label>Subject *</label>
          <select name="subject">
            <option>General Inquiry</option>
            <option>Advertise with Us</option>
            <option>Content Removal / DMCA</option>
            <option>Technical Issue</option>
            <option>Partnership</option>
            <option>Other</option>
          </select>

          <label>Message *</label>
          <textarea name="message" required placeholder="Write your message here…"></textarea>

          <button type="submit" class="contact-submit">Send Message →</button>
        </form>
      </div>

      <!-- Contact info -->
      <div style="display:flex;flex-direction:column;gap:14px">
        <div class="info-card">
          <h3>📬 Get in Touch</h3>
          <div class="info-item"><span class="info-icon">📧</span><div><strong>General:</strong><br>hello@<?= strtolower(h($siteName)) ?>.com</div></div>
          <div class="info-item"><span class="info-icon">📢</span><div><strong>Advertising:</strong><br>ads@<?= strtolower(h($siteName)) ?>.com</div></div>
          <div class="info-item"><span class="info-icon">⚖️</span><div><strong>Legal / DMCA:</strong><br>legal@<?= strtolower(h($siteName)) ?>.com</div></div>
          <div class="info-item"><span class="info-icon">⏱</span><div>We typically respond within <strong>2–3 business days</strong>.</div></div>
        </div>
        <div class="info-card">
          <h3>🔗 Quick Links</h3>
          <a href="dmca.php" style="display:block;color:var(--blue);font-size:13px;padding:4px 0">→ DMCA / Content Removal</a>
          <a href="advertise.php" style="display:block;color:var(--blue);font-size:13px;padding:4px 0">→ Advertise with Us</a>
          <a href="terms.php" style="display:block;color:var(--blue);font-size:13px;padding:4px 0">→ Terms &amp; Conditions</a>
          <a href="privacy.php" style="display:block;color:var(--blue);font-size:13px;padding:4px 0">→ Privacy Policy</a>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="site-footer" style="margin-top:24px">
  <div class="footer-inner">
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> <?= h($siteName) ?>. All rights reserved.</span>
      <span><a href="terms.php">Terms</a> · <a href="privacy.php">Privacy</a> · <a href="dmca.php">DMCA</a> · <a href="2257.php">18 U.S.C. 2257</span>
    </div>
  </div>
</footer>
</body></html>
