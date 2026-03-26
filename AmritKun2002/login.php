<?php
define('ADMIN',true);
require_once '../config.php';
session_start();

if(isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php'); exit;
}

$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $pass = $_POST['password'] ?? '';
    $hash = getSetting($pdo,'admin_password','');
    if(password_verify($pass, $hash)){
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_time']      = time();
        header('Location: index.php'); exit;
    } else {
        $error = 'Incorrect password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Admin Login — desimms</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#f0f2f6;display:flex;align-items:center;justify-content:center;min-height:100vh}
.box{background:#fff;border-radius:12px;padding:36px 32px;width:100%;max-width:360px;box-shadow:0 4px 24px rgba(0,0,0,.10)}
.logo{font-size:28px;font-weight:900;text-align:center;margin-bottom:6px;letter-spacing:-.5px}
.logo span{color:#e94560}
.sub{text-align:center;font-size:13px;color:#6b7280;margin-bottom:24px}
label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px}
input[type=password]{width:100%;padding:11px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;transition:border-color .2s}
input[type=password]:focus{border-color:#0077cc;box-shadow:0 0 0 3px rgba(0,119,204,.1)}
.btn{width:100%;background:#e94560;color:#fff;border:none;padding:12px;border-radius:8px;font-size:15px;font-weight:700;margin-top:16px;cursor:pointer;transition:background .2s}
.btn:hover{background:#c73650}
.err{background:#fff1f0;border:1px solid #fca5a5;border-radius:6px;padding:10px 12px;font-size:13px;color:#991b1b;margin-bottom:14px}
.hint{font-size:12px;color:#9ca3af;text-align:center;margin-top:14px}
</style>
</head><body>
<div class="box">
  <div class="logo">desi<span>mms</span></div>
  <div class="sub">Admin Panel</div>
  <?php if($error): ?><div class="err">❌ <?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="POST">
    <label for="password">Admin Password</label>
    <input type="password" id="password" name="password" placeholder="Enter password" autofocus required/>
    <button type="submit" class="btn">Login →</button>
  </form>
  <p class="hint">Default password: <code>password</code><br>Change it in Admin → Settings</p>
</div>
</body></html>
