<?php
define('ADMIN',true);
require_once '../config.php';
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header('Location: login.php'); exit;
}
// Auto-logout after 2 hours
if(time()-$_SESSION['admin_time']>7200){
    session_destroy();
    header('Location: login.php?timeout=1'); exit;
}
$_SESSION['admin_time']=time();
?>
