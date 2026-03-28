<?php
// ============================================
//  desimms — config.php
//  Change DB_USER / DB_PASS if needed
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'temp_desimms_user');
define('DB_PASS', 'Desi2024');
define('DB_NAME', 'temp_desimms');
define('BASE_URL', 'https://desimms.de');

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:30px;background:#fff1f0;border:1px solid #fca5a5;border-radius:8px;margin:20px;color:#991b1b">
        <strong>Database Error:</strong> '.$e->getMessage().'<br><br>
        Make sure you have:<br>1. Run <code>desimms.sql</code> in phpMyAdmin<br>
        2. Set correct DB_USER and DB_PASS in config.php</div>');
}

function getSetting(PDO $db, string $key, string $default=''): string {
    $s=$db->prepare("SELECT `value` FROM settings WHERE `key`=?");
    $s->execute([$key]);
    $r=$s->fetchColumn();
    return $r!==false?$r:$default;
}
function getAd(PDO $db, string $pos): ?array {
    $s=$db->prepare("SELECT * FROM ads WHERE position=? AND is_active=1 LIMIT 1");
    $s->execute([$pos]);
    return $s->fetch()?:null;
}
function fmtViews(int $n): string {
    if($n>=1000000) return round($n/1000000,1).'M';
    if($n>=1000)    return round($n/1000,1).'K';
    return (string)$n;
}
function timeAgo(string $dt): string {
    $d=time()-strtotime($dt);
    if($d<60)      return 'just now';
    if($d<3600)    return floor($d/60).' min ago';
    if($d<86400)   return floor($d/3600).' hr ago';
    if($d<604800)  return floor($d/86400).' days ago';
    if($d<2592000) return floor($d/604800).' weeks ago';
    return date('M j, Y',strtotime($dt));
}
function makeSlug(string $t): string {
    $t=strtolower(trim($t));
    $t=preg_replace('/[^a-z0-9\s-]/','',$t);
    return trim(preg_replace('/[\s-]+/','-',$t),'-');
}
function h(string $s): string { return htmlspecialchars($s,ENT_QUOTES,'UTF-8'); }
function getCategories(PDO $db): array {
    return $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
}
function videoPath(array $video): string {
    if (!empty($video['slug'])) {
        return 'video.php?slug=' . rawurlencode($video['slug']);
    }
    return 'video.php?id=' . (int)($video['id'] ?? 0);
}
function videoUrl(array $video): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim(videoPath($video), '/');
}
if(!defined('ADMIN') && getSetting($pdo,'maintenance_mode')==='1'){
    http_response_code(503);
    die('<!DOCTYPE html><html><body style="font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;background:#f0f2f5;margin:0"><div style="text-align:center;background:#fff;padding:40px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1)"><div style="font-size:48px">🔧</div><h2>Under Maintenance</h2><p style="color:#666">We\'ll be back soon!</p></div></body></html>');
}

