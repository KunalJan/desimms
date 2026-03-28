<?php
require_once 'config.php';
header('Content-Type: application/xml; charset=utf-8');

$base    = rtrim(BASE_URL,'/');
$videos  = $pdo->query("SELECT id,slug,created_at FROM videos ORDER BY created_at DESC")->fetchAll();
$cats    = $pdo->query("SELECT slug FROM categories")->fetchAll();

function xml(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}
function fileLastMod(string $filePath): string {
    $ts = @filemtime($filePath);
    return $ts ? gmdate('Y-m-d', $ts) : gmdate('Y-m-d');
}

$staticPages = [
    ['url' => '/',               'file' => __DIR__ . '/index.php',      'changefreq' => 'daily',   'priority' => '1.0'],
    ['url' => '/about.php',      'file' => __DIR__ . '/about.php',      'changefreq' => 'monthly', 'priority' => '0.5'],
    ['url' => '/contact.php',    'file' => __DIR__ . '/contact.php',    'changefreq' => 'monthly', 'priority' => '0.5'],
    ['url' => '/advertise.php',  'file' => __DIR__ . '/advertise.php',  'changefreq' => 'monthly', 'priority' => '0.5'],
    ['url' => '/disclaimer.php', 'file' => __DIR__ . '/disclaimer.php', 'changefreq' => 'yearly',  'priority' => '0.3'],
    ['url' => '/terms.php',      'file' => __DIR__ . '/terms.php',      'changefreq' => 'yearly',  'priority' => '0.3'],
    ['url' => '/privacy.php',    'file' => __DIR__ . '/privacy.php',    'changefreq' => 'yearly',  'priority' => '0.3'],
    ['url' => '/dmca.php',       'file' => __DIR__ . '/dmca.php',       'changefreq' => 'yearly',  'priority' => '0.3'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  <!-- Static pages -->
  <?php foreach($staticPages as $page): ?>
  <url>
    <loc><?= xml($base . $page['url']) ?></loc>
    <lastmod><?= fileLastMod($page['file']) ?></lastmod>
    <changefreq><?= $page['changefreq'] ?></changefreq>
    <priority><?= $page['priority'] ?></priority>
  </url>
  <?php endforeach; ?>

  <!-- Category pages -->
  <?php foreach($cats as $c): ?>
  <url>
    <loc><?= xml($base . '/index.php?cat=' . urlencode($c['slug'])) ?></loc>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  <?php endforeach; ?>

  <!-- Video pages -->
  <?php foreach($videos as $v): ?>
  <url>
    <loc><?= xml(videoUrl($v)) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($v['created_at'])) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <?php endforeach; ?>

</urlset>
