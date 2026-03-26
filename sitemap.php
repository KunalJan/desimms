<?php
require_once 'config.php';
header('Content-Type: application/xml; charset=utf-8');

$base    = rtrim(BASE_URL,'/');
$videos  = $pdo->query("SELECT slug,created_at FROM videos ORDER BY created_at DESC")->fetchAll();
$cats    = $pdo->query("SELECT slug FROM categories")->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  <!-- Static pages -->
  <url><loc><?= $base ?>/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
  <url><loc><?= $base ?>/about.php</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
  <url><loc><?= $base ?>/contact.php</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
  <url><loc><?= $base ?>/terms.php</loc><changefreq>yearly</changefreq><priority>0.3</priority></url>
  <url><loc><?= $base ?>/privacy.php</loc><changefreq>yearly</changefreq><priority>0.3</priority></url>
  <url><loc><?= $base ?>/dmca.php</loc><changefreq>yearly</changefreq><priority>0.3</priority></url>

  <!-- Category pages -->
  <?php foreach($cats as $c): ?>
  <url>
    <loc><?= $base ?>/index.php?cat=<?= urlencode($c['slug']) ?></loc>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  <?php endforeach; ?>

  <!-- Video pages -->
  <?php foreach($videos as $v): ?>
  <url>
    <loc><?= $base ?>/video.php?slug=<?= urlencode($v['slug']) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($v['created_at'])) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <?php endforeach; ?>

</urlset>
