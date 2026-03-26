<?php
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
if(!$id) { header('Location: index.php'); exit; }

$video = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
$video->execute([$id]);
$v = $video->fetch();

if(!$v) { http_response_code(404); }

// Increment views
if($v) {
    $pdo->prepare("UPDATE videos SET views=views+1 WHERE id=?")->execute([$id]);
    $v['views']++;
}

// Related videos — same category first, fill with others if needed
$related = [];
if($v) {
    // Step 1: same category, excluding current video
    $rel = $pdo->prepare("SELECT * FROM videos WHERE id != ? AND category = ? ORDER BY views DESC LIMIT 6");
    $rel->execute([$id, $v['category']]);
    $related = $rel->fetchAll();

    // Step 2: if fewer than 4, fill up with any other videos
    if(count($related) < 4) {
        $excludeIds   = array_merge([$id], array_column($related, 'id'));
        $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
        $more = $pdo->prepare("SELECT * FROM videos WHERE id NOT IN($placeholders) ORDER BY views DESC LIMIT 6");
        $more->execute($excludeIds);
        $related = array_slice(array_merge($related, $more->fetchAll()), 0, 6);
    }
}

$siteName        = getSetting($pdo,'site_name','desimms');
$categories      = getCategories($pdo);
$adHeader        = getAd($pdo,'header');
$adFooter        = getAd($pdo,'footer');
$adContent       = getAd($pdo,'below_title');
$adPopup         = getAd($pdo,'popup');
$adAboveRelated  = getAd($pdo,'above_related');
$adBetweenRel    = getAd($pdo,'between_related');
$adSidebar       = getAd($pdo,'sidebar');
$adAfterDesc     = getAd($pdo,'after_description');

// SEO
$seoTitle = $v ? ($v['seo_title'] ?: $v['title'].' | '.$siteName) : '404 Not Found | '.$siteName;
$seoDesc  = $v ? ($v['seo_desc']  ?: substr(strip_tags($v['description']??''),0,160)) : '';
$seoKw    = $v ? $v['seo_keywords'] : '';
$tags     = $v ? array_filter(array_map('trim', explode(',', $v['tags']??''))) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title><?= h($seoTitle) ?></title>
  <meta name="description" content="<?= h($seoDesc) ?>"/>
  <?php if($seoKw): ?><meta name="keywords" content="<?= h($seoKw) ?>"/><?php endif; ?>
  <?php if($v): ?>
  <meta property="og:title"       content="<?= h($v['title']) ?>"/>
  <meta property="og:description" content="<?= h($seoDesc) ?>"/>
  <meta property="og:image"       content="<?= h($v['thumbnail']) ?>"/>
  <meta property="og:type"        content="video.other"/>
  <meta name="twitter:card"       content="summary_large_image"/>
  <meta name="robots" content="index,follow"/>
  <link rel="canonical" href="<?= h(BASE_URL.'/video.php?id='.$v['id']) ?>"/>
  <script type="application/ld+json"><?= json_encode([
    '@context'=>'https://schema.org',
    '@type'=>'VideoObject',
    'name'=>$v['title'],
    'description'=>$seoDesc,
    'thumbnailUrl'=>$v['thumbnail'],
    'embedUrl'=>$v['embed_url'],
    'uploadDate'=>$v['created_at'],
    'interactionStatistic'=>['@type'=>'InteractionCounter','interactionType'=>'https://schema.org/WatchAction','userInteractionCount'=>$v['views']]
  ],JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) ?></script>
  <?php endif; ?>
  <link rel="stylesheet" href="style.css"/>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>▶</text></svg>"/>
  <style>
    /* Video page two-column layout on desktop */
    .vpage-wrap{max-width:1100px;margin:0 auto;display:flex;gap:20px;padding:0 14px 30px;align-items:flex-start}
    .vpage-main{flex:1;min-width:0}
    .vpage-sidebar{width:300px;flex-shrink:0;display:none}
    @media(min-width:900px){.vpage-sidebar{display:block}}
    .sidebar-sticky{position:sticky;top:80px;display:flex;flex-direction:column;gap:16px}
    .sidebar-ad-box{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;padding:6px;text-align:center;position:relative}
    .sidebar-ad-box::before{content:'AD';position:absolute;top:2px;left:5px;font-size:9px;color:var(--muted);font-family:var(--font-h)}
    .sidebar-ad-box img{max-width:100%;display:block;margin:0 auto}
    .sidebar-more h4{font-family:var(--font-h);font-size:15px;font-weight:700;margin-bottom:10px;padding:0 2px}
    .sidebar-more .rel-card{flex-direction:column}
    .sidebar-more .rel-thumb{width:100%;min-height:120px}
    .sidebar-more .rel-info{padding:8px 10px}
  </style>
</head>
<body>

<?php if($adPopup && !empty(trim($adPopup['popup_url'])) && !empty(trim($adPopup['ad_code']))): ?>


<div class="popup-overlay" id="popupAd">
  <div class="popup-box">
    <button class="popup-close" onclick="document.getElementById('popupAd').classList.remove('show')">✕</button>
    <a href="<?= h($adPopup['popup_url']) ?>" target="_blank" rel="noopener"><?= $adPopup['ad_code'] ?></a>
  </div>
</div>
<script>setTimeout(()=>document.getElementById('popupAd').classList.add('show'),<?= (int)$adPopup['popup_delay'] ?>000);</script>
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
    <a href="index.php" class="cat-nav-item">
      <span class="cni-icon">⚡</span> All Videos
    </a>
    <?php foreach($categories as $c): ?>
      <a href="index.php?cat=<?= urlencode($c['slug']) ?>"
         class="cat-nav-item <?= $v&&$c['slug']===$v['category']?'active':'' ?>">
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
    <button class="hamburger" id="hamburger" onclick="openCatMenu()" aria-label="Open categories" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
    <a href="index.php" class="logo">desi<span>mms</span></a>
    <form class="search-form" action="index.php" method="get" role="search" style="flex:1">
      <input type="search" name="q" placeholder="Search videos…" autocomplete="off" aria-label="Search"/>
      <button type="submit" aria-label="Search">🔍</button>
    </form>
  </div>
</header>

<style>
.hamburger{background:none;border:none;padding:6px 8px;cursor:pointer;display:flex;flex-direction:column;gap:5px;flex-shrink:0;border-radius:var(--radius-sm);transition:background .15s}
.hamburger:hover{background:var(--hover)}
.hamburger span{display:block;width:22px;height:2px;background:var(--text);border-radius:2px;transition:all .25s}
.hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.hamburger.open span:nth-child(2){opacity:0;transform:scaleX(0)}
.hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
.cat-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;backdrop-filter:blur(2px)}
.cat-overlay.show{display:block}
.cat-sidebar{position:fixed;top:0;left:-280px;width:265px;height:100%;background:#fff;border-right:1px solid var(--border);box-shadow:4px 0 24px rgba(0,0,0,.12);z-index:201;display:flex;flex-direction:column;transition:left .27s cubic-bezier(.4,0,.2,1);overflow:hidden}
.cat-sidebar.open{left:0}
.cat-sidebar-head{display:flex;align-items:center;justify-content:space-between;padding:16px 18px 14px;border-bottom:1px solid var(--border);background:#fff;position:sticky;top:0;z-index:1}
.cat-sidebar-title{font-family:var(--font-h);font-size:16px;font-weight:700;letter-spacing:.04em;color:var(--accent);text-transform:uppercase}
.cat-sidebar-close{background:none;border:none;color:var(--muted);font-size:18px;cursor:pointer;padding:4px 7px;border-radius:6px;transition:all .15s;line-height:1}
.cat-sidebar-close:hover{background:var(--hover);color:var(--text)}
.cat-sidebar-body{overflow-y:auto;flex:1;padding:8px 10px 20px}
.cat-nav-item{display:flex;align-items:center;gap:10px;padding:11px 12px;border-radius:8px;font-size:14px;font-weight:500;color:var(--muted);transition:all .15s;text-decoration:none;border-left:3px solid transparent;margin-bottom:2px}
.cat-nav-item:hover{background:var(--hover);color:var(--text)}
.cat-nav-item.active{background:rgba(233,69,96,.08);color:var(--accent);font-weight:600;border-left-color:var(--accent)}
.cni-icon{font-size:18px;width:24px;text-align:center;flex-shrink:0}
</style>

<a href="javascript:history.back()" class="back-link">← Back</a>

<?php if(!$v): ?>
<!-- 404 -->
<div style="text-align:center;padding:60px 20px;color:var(--muted)">
  <div style="font-size:64px;font-family:var(--font-h);font-weight:900;color:var(--accent)">404</div>
  <h2 style="margin:10px 0 12px">Video Not Found</h2>
  <p>The video you're looking for doesn't exist or has been removed.</p>
  <a href="index.php" style="display:inline-block;margin-top:16px;background:var(--accent);color:#fff;padding:10px 24px;border-radius:50px;font-family:var(--font-h);font-weight:700">Go Home</a>
</div>

<?php else: ?>
<!-- ── Two-column layout ── -->
<div class="vpage-wrap">

  <!-- ── LEFT: Main content ── -->
  <div class="vpage-main">

    <!-- Video Player -->
    <div class="v-player-wrap">
      <?php if($v['embed_url']): ?>
        <iframe src="<?= h($v['embed_url']) ?>" title="<?= h($v['title']) ?>"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen loading="lazy"></iframe>
      <?php elseif($v['video_file']): ?>
        <video controls preload="metadata" poster="<?= h($v['thumbnail']) ?>">
          <source src="<?= h('uploads/videos/'.$v['video_file']) ?>">
          Your browser does not support the video tag.
        </video>
      <?php endif; ?>
    </div>

    <div class="video-body">
      <h1 class="video-big-title"><?= h($v['title']) ?></h1>

      <div class="video-stats-row">
        <span class="stat-badge blue">👁 <?= fmtViews((int)$v['views']) ?> views</span>
        <span class="stat-badge"><?= timeAgo($v['created_at']) ?></span>
        <span class="stat-badge gold">📂 <?= ucfirst(h($v['category'])) ?></span>
      </div>

      <!-- ★ AD: Below Title ★ -->
      <?php if($adContent): ?>
      <div class="ad-wrap ad-content"><?= $adContent['ad_code'] ?></div>
      <?php endif; ?>

      <div class="chan-row">
        <div class="chan-av"><?= strtoupper(substr($v['category'],0,1)) ?></div>
        <div>
          <div class="chan-name"><?= ucfirst(h($v['category'])) ?></div>
          <div class="chan-date"><?= timeAgo($v['created_at']) ?></div>
        </div>
        <button class="sub-btn" onclick="this.textContent=this.dataset.sub?(this.style.background='',this.textContent='SUBSCRIBE',delete this.dataset.sub,''):(this.style.background='#2a8c5a',this.dataset.sub='1','✔ SUBSCRIBED')">SUBSCRIBE</button>
      </div>

      <?php if($v['description']): ?>
      <div class="video-desc-box"><?= nl2br(h($v['description'])) ?></div>
      <?php endif; ?>

      <!-- ★ AD: After Description ★ -->
      <?php if($adAfterDesc): ?>
      <div class="ad-wrap ad-content"><?= $adAfterDesc['ad_code'] ?></div>
      <?php endif; ?>

      <?php if(!empty($tags)): ?>
      <div class="video-tags">
        <?php foreach($tags as $tag): ?>
          <a href="index.php?q=<?= urlencode($tag) ?>" class="tag">#<?= h(trim($tag)) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="action-bar">
        <button class="act-btn act-like" onclick="toggleAct(this,'👍 Like','👍 Liked!')">👍 Like</button>
        <button class="act-btn" onclick="toggleAct(this,'👎 Dislike','👎 Noted')">👎 Dislike</button>
        <button class="act-btn" onclick="shareVideo()">🔗 Share</button>
        <button class="act-btn" onclick="toggleAct(this,'🔖 Save','🔖 Saved!')">🔖 Save</button>
      </div>
    </div>

    <!-- ★ AD: Above Related Videos ★ -->
    <?php if($adAboveRelated): ?>
    <div class="ad-wrap ad-content" style="margin:0 14px 14px"><?= $adAboveRelated['ad_code'] ?></div>
    <?php endif; ?>

    <?php if(!empty($related)): ?>
    <section class="related-section">
      <h3>🎬 Related Videos</h3>
      <div class="related-list">
        <?php foreach($related as $ri => $r): ?>

          <a class="rel-card" href="video.php?id=<?= (int)$r['id'] ?>">
            <div class="rel-thumb">
              <img src="<?= h($r['thumbnail']?:('https://picsum.photos/seed/v'.$r['id'].'/400/225')) ?>"
                   alt="<?= h($r['title']) ?>" loading="lazy" width="130" height="73"/>
              <span class="v-dur"><?php
                // show category as badge
              ?></span>
            </div>
            <div class="rel-info">
              <p class="rel-title"><?= h($r['title']) ?></p>
              <div class="rel-meta">
                <span style="color:var(--blue);font-weight:600"><?= ucfirst(h($r['category'])) ?></span>
                · <?= fmtViews((int)$r['views']) ?> views · <?= timeAgo($r['created_at']) ?>
              </div>
            </div>
          </a>

          <!-- ★ AD: Between Related (after every 2nd item) ★ -->
          <?php if($adBetweenRel && ($ri + 1) % 2 === 0 && $ri < count($related) - 1): ?>
          <div class="ad-wrap" style="border-radius:var(--radius);border:1px dashed rgba(233,69,96,.3);padding:8px">
            <?= $adBetweenRel['ad_code'] ?>
          </div>
          <?php endif; ?>

        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

  </div><!-- end vpage-main -->

  <!-- ── RIGHT: Sidebar (desktop only) ── -->
  <div class="vpage-sidebar">
    <div class="sidebar-sticky">

      <!-- ★ AD: Sidebar 300x600 ★ -->
      <?php if($adSidebar): ?>
      <div class="sidebar-ad-box"><?= $adSidebar['ad_code'] ?></div>
      <?php endif; ?>

      <!-- More videos in sidebar -->
      <?php if(!empty($related)): ?>
      <div class="sidebar-more">
        <h4>🎬 More Videos</h4>
        <div class="related-list">
          <?php foreach(array_slice($related,0,4) as $r): ?>
            <a class="rel-card" href="video.php?id=<?= (int)$r['id'] ?>" style="flex-direction:column">
              <div class="rel-thumb" style="width:100%;min-height:120px">
                <img src="<?= h($r['thumbnail']?:('https://picsum.photos/seed/v'.$r['id'].'/300/170')) ?>"
                     alt="<?= h($r['title']) ?>" loading="lazy" style="width:100%;height:120px;object-fit:cover"/>
              </div>
              <div class="rel-info" style="padding:8px 10px">
                <p class="rel-title"><?= h($r['title']) ?></p>
                <div class="rel-meta"><?= ucfirst(h($r['category'])) ?> · <?= fmtViews((int)$r['views']) ?> views</div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div><!-- end sidebar -->

</div><!-- end vpage-wrap -->
<?php endif; ?>

<!-- ── Footer ── -->
<footer class="site-footer" style="margin-top:24px">
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
      <span>All content belongs to respective copyright owners.</span>
    </div>
  </div>
</footer>

<button class="scroll-top" id="scrollTop" aria-label="Top">↑</button>
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
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeCatMenu();});
function toggleAct(btn,off,on){
  const a=btn.dataset.a;
  btn.textContent=a?off:on;
  btn.style.color=a?'':'var(--accent)';
  btn.style.borderColor=a?'':'var(--accent)';
  a?delete btn.dataset.a:(btn.dataset.a='1');
}
function shareVideo(){
  if(navigator.share) navigator.share({title:document.title,url:location.href}).catch(()=>{});
  else{ navigator.clipboard.writeText(location.href).then(()=>alert('Link copied!')); }
}
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
