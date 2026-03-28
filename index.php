<?php
require_once 'config.php';

$siteName    = getSetting($pdo,'site_name','desimms');
$siteTagline = getSetting($pdo,'site_tagline','Watch Anything, Anywhere');
$siteDesc    = getSetting($pdo,'site_description','');
$siteKw      = getSetting($pdo,'site_keywords','');
$perPage     = (int)getSetting($pdo,'videos_per_page','12');

// ── Filters ──────────────────────────────────
$cat    = isset($_GET['cat'])  ? trim($_GET['cat'])  : '';
$search = isset($_GET['q'])    ? trim($_GET['q'])    : '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

// ── Build query ───────────────────────────────
$where = ['1=1'];
$params = [];
if ($cat)    { $where[] = 'category = ?'; $params[] = $cat; }
if ($search) { $where[] = '(title LIKE ? OR description LIKE ? OR tags LIKE ?)';
               $params = array_merge($params, ["%$search%","%$search%","%$search%"]); }
$whereSQL = implode(' AND ', $where);

$total = $pdo->prepare("SELECT COUNT(*) FROM videos WHERE $whereSQL");
$total->execute($params);
$totalCount = (int)$total->fetchColumn();
$totalPages = max(1, ceil($totalCount / $perPage));

$stmt = $pdo->prepare("SELECT * FROM videos WHERE $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$videos = $stmt->fetchAll();

$categories = getCategories($pdo);

// Active category label
$catLabel = '🎬 All Videos';
foreach ($categories as $c) {
    if ($c['slug'] === $cat) { $catLabel = $c['icon'].' '.$c['name']; break; }
}
if ($search) $catLabel = '🔍 Results for "'.$search.'"';

// Ads
$adHeader     = getAd($pdo,'header');
$adFooter     = getAd($pdo,'footer');
$adPopup      = getAd($pdo,'popup');
$adGridMiddle = getAd($pdo,'grid_middle');
$adCatStrip   = getAd($pdo,'cat_strip');

// Page title & meta
$pageTitle = $search ? "Search: $search | $siteName" : ($cat ? ucfirst($cat)." Videos | $siteName" : "$siteName — $siteTagline");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title><?= h($pageTitle) ?></title>
  <meta name="description" content="<?= h($siteDesc) ?>"/>
  <meta name="keywords"    content="<?= h($siteKw) ?>"/>
  <meta property="og:title"       content="<?= h($pageTitle) ?>"/>
  <meta property="og:description" content="<?= h($siteDesc) ?>"/>
  <meta property="og:image"       content="<?= h(BASE_URL.'/site-preview.svg') ?>"/>
  <meta property="og:type"        content="website"/>
  <meta name="twitter:card"       content="summary_large_image"/>
  <meta name="twitter:image"      content="<?= h(BASE_URL.'/site-preview.svg') ?>"/>
  <meta name="robots" content="index,follow"/>
  <link rel="canonical" href="<?= h(BASE_URL.'/index.php'.($cat?'?cat='.urlencode($cat):'')) ?>"/>
  <link rel="stylesheet" href="style.css"/>
  <link rel="icon" href="favicon.svg" type="image/svg+xml"/>
  <link rel="apple-touch-icon" href="site-preview.svg"/>
</head>
<body class="home-page">

<?php if($adPopup && !empty(trim($adPopup['popup_url'])) && !empty(trim($adPopup['ad_code']))): ?>

<!-- ── Popup Ad ── -->
<div class="popup-overlay" id="popupAd">
  <div class="popup-box">
    <button class="popup-close" onclick="document.getElementById('popupAd').classList.remove('show')">✕</button>
    <a href="<?= h($adPopup['popup_url']) ?>" target="_blank" rel="noopener">
      <?= $adPopup['ad_code'] ?>
    </a>
  </div>
</div>
<script>
setTimeout(function(){
  document.getElementById('popupAd').classList.add('show');
},<?= (int)$adPopup['popup_delay'] ?> * 1000);
</script>
<?php endif; ?>

<!-- ── Sidebar Overlay ── -->
<div class="cat-overlay" id="catOverlay" onclick="closeCatMenu()"></div>

<!-- ── Category Sidebar ── -->
<nav class="cat-sidebar" id="catSidebar" aria-label="Categories">
  <div class="cat-sidebar-head">
    <span class="cat-sidebar-title">📂 Categories</span>
    <button class="cat-sidebar-close" onclick="closeCatMenu()" aria-label="Close">✕</button>
  </div>
  <div class="cat-sidebar-body">
    <a href="index.php" class="cat-nav-item <?= !$cat?'active':'' ?>" onclick="closeCatMenu()">
      <span class="cni-icon">⚡</span> All Videos
    </a>
    <?php foreach($categories as $c): ?>
      <a href="index.php?cat=<?= urlencode($c['slug']) ?>"
         class="cat-nav-item <?= $c['slug']===$cat?'active':'' ?>"
         onclick="closeCatMenu()">
        <span class="cni-icon"><?= h($c['icon']) ?></span> <?= h($c['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>

<!-- ── Header ── -->
<header class="site-header">
  <?php if($adHeader): ?>
  <div class="ad-wrap ad-header"><?= $adHeader['ad_code'] ?></div>
  <?php endif; ?>
  <div class="header-inner">
    <!-- Hamburger button top-left -->
    <button class="hamburger" id="hamburger" onclick="openCatMenu()" aria-label="Open categories" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
    <a href="index.php" class="logo">desi<span>mms</span></a>
    <form class="search-form" action="index.php" method="get" role="search">
      <?php if($cat): ?><input type="hidden" name="cat" value="<?= h($cat) ?>"/><?php endif; ?>
      <input type="search" name="q" placeholder="Search videos…" value="<?= h($search) ?>" autocomplete="off" aria-label="Search"/>
      <button type="submit" aria-label="Search">🔍</button>
    </form>
  </div>
  <!-- Active category indicator bar -->
  <?php if($cat): ?>
  <div class="active-cat-bar">
    <span>Browsing:</span>
    <?php foreach($categories as $c): if($c['slug']===$cat): ?>
      <strong><?= h($c['icon'].' '.$c['name']) ?></strong>
    <?php endif; endforeach; ?>
    <a href="index.php" class="clear-cat">✕ Clear</a>
  </div>
  <?php endif; ?>
</header>

<!-- ★ AD — Category Strip 320x50 ★ -->
<?php if($adCatStrip): ?>
<div class="ad-wrap ad-cat-strip">
  <?= $adCatStrip['ad_code'] ?>
</div>
<?php endif; ?>

<style>
/* ── Hamburger button ── */
.hamburger{
  background:none;border:none;padding:6px 8px;cursor:pointer;
  display:flex;flex-direction:column;gap:5px;flex-shrink:0;border-radius:var(--radius-sm);
  transition:background .15s
}
.hamburger:hover{background:var(--hover)}
.hamburger span{
  display:block;width:22px;height:2px;background:var(--text);
  border-radius:2px;transition:all .25s
}
.hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.hamburger.open span:nth-child(2){opacity:0;transform:scaleX(0)}
.hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}

/* ── Sidebar overlay ── */
.cat-overlay{
  display:none;position:fixed;inset:0;
  background:rgba(0,0,0,.45);z-index:200;
  backdrop-filter:blur(2px)
}
.cat-overlay.show{display:block}

/* ── Category Sidebar ── */
.cat-sidebar{
  position:fixed;top:0;left:-280px;
  width:265px;height:100%;
  background:var(--card);
  border-right:1px solid var(--border);
  box-shadow:4px 0 24px rgba(0,0,0,.12);
  z-index:201;
  display:flex;flex-direction:column;
  transition:left .27s cubic-bezier(.4,0,.2,1);
  overflow:hidden
}
.cat-sidebar.open{left:0}

.cat-sidebar-head{
  display:flex;align-items:center;
  justify-content:space-between;
  padding:16px 18px 14px;
  border-bottom:1px solid var(--border);
  background:var(--card);
  position:sticky;top:0;z-index:1
}
.cat-sidebar-title{
  font-family:var(--font-h);font-size:16px;
  font-weight:700;letter-spacing:.04em;
  color:var(--accent);text-transform:uppercase
}
.cat-sidebar-close{
  background:none;border:none;
  color:var(--muted);font-size:18px;
  cursor:pointer;padding:4px 7px;
  border-radius:6px;transition:all .15s;
  line-height:1
}
.cat-sidebar-close:hover{background:var(--hover);color:var(--text)}

.cat-sidebar-body{
  overflow-y:auto;flex:1;padding:8px 10px 20px
}
.cat-nav-item{
  display:flex;align-items:center;gap:10px;
  padding:11px 12px;border-radius:8px;
  font-size:14px;font-weight:500;color:var(--muted);
  transition:all .15s;text-decoration:none;
  border-left:3px solid transparent;margin-bottom:2px
}
.cat-nav-item:hover{background:var(--hover);color:var(--text)}
.cat-nav-item.active{
  background:rgba(233,69,96,.08);
  color:var(--accent);font-weight:600;
  border-left-color:var(--accent)
}
.cni-icon{font-size:18px;width:24px;text-align:center;flex-shrink:0}

/* ── Active category bar ── */
.active-cat-bar{
  display:flex;align-items:center;gap:8px;
  padding:5px 14px;background:rgba(233,69,96,.12);
  border-top:1px solid rgba(233,69,96,.15);
  font-size:12px;color:var(--muted)
}
.active-cat-bar strong{color:var(--accent)}
.clear-cat{
  margin-left:auto;color:var(--muted);
  font-size:11px;font-weight:600;
  background:var(--hover);
  padding:2px 8px;border-radius:50px;
  transition:all .15s
}
.clear-cat:hover{background:var(--accent);color:#fff}

/* Small ad strip below categories */
.ad-cat-strip{
  padding:4px 10px;
  background:var(--card);
  border-top:0;
  border-left:0;
  border-right:0;
  border-bottom:1px solid var(--border)
}
.ad-cat-strip::before{top:1px}
.ad-cat-strip img,.ad-cat-strip iframe{max-height:50px}
</style>

<script>
function openCatMenu(){
  document.getElementById('catSidebar').classList.add('open');
  document.getElementById('catOverlay').classList.add('show');
  document.getElementById('hamburger').classList.add('open');
  document.getElementById('hamburger').setAttribute('aria-expanded','true');
  document.body.style.overflow='hidden';
}
function closeCatMenu(){
  document.getElementById('catSidebar').classList.remove('open');
  document.getElementById('catOverlay').classList.remove('show');
  document.getElementById('hamburger').classList.remove('open');
  document.getElementById('hamburger').setAttribute('aria-expanded','false');
  document.body.style.overflow='';
}
document.addEventListener('keydown',function(e){
  if(e.key==='Escape') closeCatMenu();
});
</script>

<!-- ── Main ── -->
<div class="page-wrap">
  <div class="sec-head">
    <h2><?= h($catLabel) ?></h2>
    <?php if($totalCount>0): ?><span class="pill-count"><?= $totalCount ?> videos</span><?php endif; ?>
  </div>

  <div class="video-grid">
    <?php if(empty($videos)): ?>
      <div class="empty">
        <div class="icon">🎬</div>
        <p>No videos found<?= $search?' for "'.h($search).'"':'' ?>.</p>
      </div>
    <?php else:
      $cardIndex = 0;
      foreach($videos as $v):
        $cardIndex++;
    ?>
      <a class="v-card" href="video.php?id=<?= (int)$v['id'] ?>">
        <div class="v-thumb">
          <img src="<?= h($v['thumbnail']?:('https://picsum.photos/seed/v'.$v['id'].'/400/225')) ?>"
               alt="<?= h($v['title']) ?>" loading="lazy" width="400" height="225"/>
          <div class="v-play"><div class="v-play-btn">▶</div></div>
        </div>
        <div class="v-info">
          <p class="v-title"><?= h($v['title']) ?></p>
          <div class="v-meta">
            <span class="v-channel"><?= h($v['category']) ?></span> ·
            <?= fmtViews((int)$v['views']) ?> views · <?= timeAgo($v['created_at']) ?>
          </div>
        </div>
      </a>

      <?php /* ★ AD — In-feed ads after card 6 and 12 ★ */
      if(in_array($cardIndex,[6,12],true) && $adGridMiddle): ?>
        </div><!-- close grid -->
        <div class="ad-wrap ad-content" style="margin:14px 0">
          <?= $adGridMiddle['ad_code'] ?>
        </div>
        <div class="video-grid"><!-- reopen grid -->
      <?php endif; ?>

    <?php endforeach; endif; ?>
  </div>

  <!-- Pagination -->
  <?php if($totalPages > 1): ?>
  <div class="pagination">
    <?php if($page>1): ?>
      <a class="page-btn" href="?<?= http_build_query(array_merge($_GET,['page'=>$page-1])) ?>">← Prev</a>
    <?php endif; ?>
    <?php for($i=max(1,$page-2);$i<=min($totalPages,$page+2);$i++): ?>
      <a class="page-btn <?= $i===$page?'active':'' ?>" href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if($page<$totalPages): ?>
      <a class="page-btn" href="?<?= http_build_query(array_merge($_GET,['page'=>$page+1])) ?>">Next →</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ── Footer ── -->
<footer class="site-footer">
  <?php if($adFooter): ?>
  <div class="ad-wrap ad-footer"><?= $adFooter['ad_code'] ?></div>
  <?php endif; ?>
  <div class="footer-inner">
    <div class="footer-cols">
      <div class="footer-col">
        <div class="footer-logo">desi<span>mms</span></div>
        <p style="font-size:12px;color:var(--muted);margin-top:8px"><?= h(getSetting($pdo,'footer_text','')) ?></p>
      </div>
      <div class="footer-col">
        <h4>Browse</h4>
        <a href="index.php">Home</a>
        <?php foreach(array_slice($categories,0,5) as $c): ?>
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
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> <?= h($siteName) ?>. All rights reserved.</span>
      <span>For entertainment purposes only. All content belongs to respective owners.</span>
    </div>
  </div>
</footer>

<button class="scroll-top" id="scrollTop" aria-label="Top">↑</button>
<script>
const st=document.getElementById('scrollTop');
function syncEdgeAds(){
  const scrollTop = window.scrollY || window.pageYOffset;
  const viewportBottom = scrollTop + window.innerHeight;
  const pageBottom = document.documentElement.scrollHeight;
  document.body.classList.toggle('hide-header-ad', scrollTop > 5);
  document.body.classList.toggle('hide-footer-ad', viewportBottom < pageBottom - 5);
}
window.addEventListener('scroll',()=>{
  st.classList.toggle('show',scrollY>300);
  syncEdgeAds();
},{passive:true});
window.addEventListener('resize',syncEdgeAds);
syncEdgeAds();
st.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}));
</script>
</body>
</html>
