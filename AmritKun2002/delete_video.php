<?php
require_once 'auth.php';
$id = (int)($_GET['id']??0);
if(!$id){ header('Location: videos.php'); exit; }

$v = $pdo->prepare("SELECT * FROM videos WHERE id=?");
$v->execute([$id]);
$vid = $v->fetch();
if(!$vid){ header('Location: videos.php'); exit; }

// Delete uploaded files
if($vid['video_file'] && file_exists('../uploads/videos/'.$vid['video_file'])){
    unlink('../uploads/videos/'.$vid['video_file']);
}
if($vid['thumbnail'] && str_starts_with($vid['thumbnail'],'uploads/') && file_exists('../'.$vid['thumbnail'])){
    unlink('../'.$vid['thumbnail']);
}

$pdo->prepare("DELETE FROM videos WHERE id=?")->execute([$id]);
header('Location: videos.php?msg=deleted');
exit;
