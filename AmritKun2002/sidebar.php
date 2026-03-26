<nav class="sidebar" id="sidebar">
  <div class="sidebar-logo">desi<span>mms</span></div>
  <div class="sidebar-nav">
    <div class="nav-section">Main</div>
    <a href="index.php"    class="nav-item <?= basename($_SERVER['PHP_SELF'])==='index.php'?'active':'' ?>"><span class="icon">📊</span> Dashboard</a>

    <div class="nav-section">Content</div>
    <a href="videos.php"    class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']),['videos.php','add_video.php','edit_video.php'])?'active':'' ?>"><span class="icon">🎬</span> Videos</a>
    <a href="add_video.php" class="nav-item"><span class="icon">➕</span> Add Video</a>
    <a href="categories.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])==='categories.php'?'active':'' ?>"><span class="icon">📂</span> Categories</a>

    <div class="nav-section">Monetisation</div>
    <a href="manage_ads.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])==='manage_ads.php'?'active':'' ?>"><span class="icon">📢</span> Ad Manager</a>

    <div class="nav-section">Site</div>
    <a href="settings.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])==='settings.php'?'active':'' ?>"><span class="icon">⚙️</span> Settings &amp; SEO</a>

    <div class="nav-section">Links</div>
    <a href="../index.php" target="_blank" class="nav-item"><span class="icon">🌐</span> View Site</a>
    <a href="logout.php" class="nav-item"><span class="icon">🚪</span> Logout</a>
  </div>
  <div class="sidebar-footer">
    <a href="../terms.php" target="_blank">Terms</a>
    <a href="../dmca.php"  target="_blank">DMCA</a>
    <span>desimms Admin Panel</span>
  </div>
</nav>
