<?php
require_once 'auth.php';

$msg = '';
$err = '';

function adPositionMeta(string $position): array {
    $map = [
        'header' => ['title' => 'Header Banner', 'hint' => 'Shows at the top of public pages.'],
        'footer' => ['title' => 'Footer Banner', 'hint' => 'Shows at the bottom of public pages.'],
        'below_title' => ['title' => 'Below Title', 'hint' => 'Shows below video title on watch page.'],
        'sidebar' => ['title' => 'Sidebar Ad', 'hint' => 'Shows in watch page sidebar (desktop).'],
        'popup' => ['title' => 'Popup Ad', 'hint' => 'Popup on page load when configured.'],
        'cat_strip' => ['title' => 'Category Strip', 'hint' => 'Small strip under homepage categories.'],
        'grid_middle' => ['title' => 'Grid Middle', 'hint' => 'In-feed ad in homepage video grid.'],
        'above_related' => ['title' => 'Above Related', 'hint' => 'Above related section on watch page.'],
        'between_related' => ['title' => 'Between Related', 'hint' => 'Between related cards on watch page.'],
        'after_description' => ['title' => 'After Description', 'hint' => 'Below description on watch page.'],
    ];

    return $map[$position] ?? [
        'title' => ucwords(str_replace('_', ' ', $position)),
        'hint' => 'Custom ad slot.'
    ];
}

function isValidUrlString(string $value): bool {
    return $value !== '' && filter_var($value, FILTER_VALIDATE_URL) !== false;
}

function buildSimpleLinkAd(string $url, string $label, string $position): string {
    $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $safeLabel = htmlspecialchars($label !== '' ? $label : ucwords(str_replace('_', ' ', $position)), ENT_QUOTES, 'UTF-8');
    $host = parse_url($url, PHP_URL_HOST);
    $safeHost = htmlspecialchars($host ?: $url, ENT_QUOTES, 'UTF-8');

    return '<a href="' . $safeUrl . '" target="_blank" rel="noopener sponsored" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;min-height:90px;padding:14px 16px;border-radius:10px;background:linear-gradient(135deg,#fff0f3 0%,#eef6ff 100%);border:1px solid rgba(233,69,96,.18);color:#111827;text-decoration:none;font-family:Arial,sans-serif">'
        . '<span style="font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#e94560">Sponsored</span>'
        . '<span style="font-size:18px;font-weight:700;line-height:1.15;text-align:center">' . $safeLabel . '</span>'
        . '<span style="font-size:12px;color:#6b7280;text-align:center;word-break:break-word">' . $safeHost . '</span>'
        . '</a>';
}

function normalizeAdPayload(array $ad, array $input): array {
    $position = (string)$ad['position'];

    $label = trim($input['label'] ?? (string)($ad['label'] ?? ''));
    $quickLink = trim($input['quick_link'] ?? '');
    $customCode = trim($input['ad_code'] ?? '');
    $popupUrl = trim($input['popup_url'] ?? '');
    $popupDelay = max(0, (int)($input['popup_delay'] ?? ($ad['popup_delay'] ?? 3)));

    // Non-popup: if custom code empty and quick link is URL, auto-generate ad HTML.
    if ($position !== 'popup' && $customCode === '' && isValidUrlString($quickLink)) {
        $customCode = buildSimpleLinkAd($quickLink, $label, $position);
    }

    // Popup: allow quick setup by link only.
    if ($position === 'popup') {
        if ($popupUrl === '' && isValidUrlString($quickLink)) {
            $popupUrl = $quickLink;
        }
        if ($customCode === '' && isValidUrlString($popupUrl)) {
            $customCode = buildSimpleLinkAd($popupUrl, ($label !== '' ? $label : 'Popup Sponsor'), $position);
        }
    }

    $isActive = 0;
    if ($position === 'popup') {
        $isActive = ($popupUrl !== '' && $customCode !== '') ? 1 : 0;
    } else {
        $isActive = ($customCode !== '') ? 1 : 0;
    }

    return [
        'label' => $label,
        'ad_code' => $customCode,
        'popup_url' => ($position === 'popup') ? $popupUrl : '',
        'popup_delay' => ($position === 'popup') ? $popupDelay : 0,
        'is_active' => $isActive
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ad'])) {
    $id = (int)($_POST['ad_id'] ?? 0);

    if ($id > 0) {
        $st = $pdo->prepare("SELECT * FROM ads WHERE id = ? LIMIT 1");
        $st->execute([$id]);
        $ad = $st->fetch(PDO::FETCH_ASSOC);

        if ($ad) {
            try {
                $payload = normalizeAdPayload($ad, $_POST);

                $up = $pdo->prepare("UPDATE ads SET ad_code=?, popup_url=?, popup_delay=?, is_active=?, label=? WHERE id=?");
                $up->execute([
                    $payload['ad_code'],
                    $payload['popup_url'],
                    $payload['popup_delay'],
                    $payload['is_active'],
                    $payload['label'],
                    $id
                ]);

                // Keep popup setting in sync for older codebases that still read popup_enabled.
                if ((string)$ad['position'] === 'popup') {
                    $val = $payload['is_active'] ? '1' : '0';
                    $pdo->prepare("UPDATE settings SET `value`=? WHERE `key`='popup_enabled'")->execute([$val]);
                }

                $msg = 'Ad slot saved successfully.';
            } catch (Throwable $e) {
                $err = 'Could not save this ad slot. Please check your values and try again.';
            }
        } else {
            $err = 'Ad slot not found.';
        }
    } else {
        $err = 'Invalid ad slot.';
    }
}

$ads = $pdo->query("SELECT * FROM ads ORDER BY FIELD(position,'header','cat_strip','grid_middle','below_title','after_description','above_related','between_related','sidebar','footer','popup'), id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Ad Manager - desimms Admin</title>
<link rel="stylesheet" href="style.css"/>
<style>
.ads-wrap{display:flex;flex-direction:column;gap:18px}
.slot-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:0;overflow:hidden}
.slot-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:18px 20px;border-bottom:1px solid var(--border);background:#fbfcfe}
.slot-title{font-size:18px;font-weight:700;color:var(--text)}
.slot-hint{font-size:12px;color:var(--muted);margin-top:4px}
.slot-body{padding:20px}
.slot-preview{margin-top:14px;padding:12px;border:1px dashed var(--border);border-radius:10px;background:#f8fafc}
.slot-preview-title{font-size:11px;font-weight:700;color:var(--muted);letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px}
.slot-actions{display:flex;justify-content:flex-end;margin-top:14px}
.quick-note{font-size:12px;color:var(--muted);line-height:1.6}
.badge-live{background:#dcfce7;color:#166534}
.badge-empty{background:#fee2e2;color:#991b1b}
@media(max-width:768px){
  .slot-head{flex-direction:column;align-items:flex-start}
}
</style>
</head>
<body>
<div class="admin-wrap">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:10px">
      <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
      <span class="topbar-title">Ad Manager</span>
    </div>
  </div>

  <div class="content">
    <?php if($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>

    <div class="form-card" style="margin-bottom:18px">
      <h3 style="margin-bottom:10px">How this works now</h3>
      <div class="quick-note">
        No manual enable/disable button is needed. Save with content and the slot becomes live automatically.
        Clear content and save to hide that slot.
      </div>
    </div>

    <div class="ads-wrap">
      <?php foreach($ads as $ad): ?>
        <?php $meta = adPositionMeta((string)$ad['position']); ?>
        <div class="slot-card">
          <div class="slot-head">
            <div>
              <div class="slot-title"><?php echo htmlspecialchars($meta['title']); ?></div>
              <div class="slot-hint"><?php echo htmlspecialchars($meta['hint']); ?> Position key: <strong><?php echo htmlspecialchars((string)$ad['position']); ?></strong></div>
            </div>
            <span class="badge <?php echo ((int)$ad['is_active'] === 1) ? 'badge-live' : 'badge-empty'; ?>">
              <?php echo ((int)$ad['is_active'] === 1) ? 'Live' : 'Empty'; ?>
            </span>
          </div>

          <div class="slot-body">
            <form method="POST">
              <input type="hidden" name="ad_id" value="<?php echo (int)$ad['id']; ?>"/>
              <input type="hidden" name="save_ad" value="1"/>

              <div class="form-grid">
                <div class="form-group">
                  <label>Slot Label</label>
                  <input type="text" name="label" value="<?php echo htmlspecialchars((string)($ad['label'] ?? '')); ?>" placeholder="Example: Header Sponsor"/>
                </div>

                <?php if((string)$ad['position'] === 'popup'): ?>
                  <div class="form-group">
                    <label>Popup Link</label>
                    <input type="url" name="popup_url" value="<?php echo htmlspecialchars((string)($ad['popup_url'] ?? '')); ?>" placeholder="https://advertiser.com"/>
                    <span class="form-hint">If custom code is empty, a default popup banner will be generated from this link.</span>
                  </div>

                  <div class="form-group">
                    <label>Popup Delay (seconds)</label>
                    <input type="number" name="popup_delay" min="0" max="60" value="<?php echo (int)($ad['popup_delay'] ?? 3); ?>"/>
                  </div>
                <?php else: ?>
                  <div class="form-group">
                    <label>Quick Link (optional)</label>
                    <input type="url" name="quick_link" value="" placeholder="https://advertiser.com"/>
                    <span class="form-hint">If custom code is empty, this link becomes a simple sponsored banner automatically.</span>
                  </div>
                <?php endif; ?>

                <div class="form-group full">
                  <label>Custom Ad HTML / Script</label>
                  <textarea name="ad_code" rows="6" placeholder="Paste AdSense, script, iframe, or full HTML"><?php echo htmlspecialchars((string)($ad['ad_code'] ?? '')); ?></textarea>
                  <span class="form-hint">Advanced mode. This renders exactly as provided.</span>
                </div>
              </div>

              <div class="slot-preview">
                <div class="slot-preview-title">Current Output Preview</div>
                <?php if(trim((string)$ad['ad_code']) !== ''): ?>
                  <?php echo (string)$ad['ad_code']; ?>
                <?php else: ?>
                  <div class="quick-note">No ad content saved yet for this slot.</div>
                <?php endif; ?>
              </div>

              <div class="slot-actions">
                <button type="submit" class="btn btn-primary">Save Slot</button>
              </div>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
</div>
</body>
</html>
