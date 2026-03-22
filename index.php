<?php
// index.php
require_once 'includes/config.php';
$page = $_GET['page'] ?? 'anasayfa';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>AlerjiBekçisi</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<?php if ($user): ?>
<nav class="nav">
  <a href="?page=panel" class="nav-brand">🛡️ AlerjiBekçisi</a>
  <div class="nav-links">
    <a href="?page=tara" class="<?= $page==='tara'?'active':'' ?>">📷 Tara</a>
    <a href="?page=profil" class="<?= $page==='profil'?'active':'' ?>">👤 Profil</a>
    <a href="?page=gecmis" class="<?= $page==='gecmis'?'active':'' ?>">📋 Geçmiş</a>
    <button onclick="cikisYap()" class="btn-cikis">Çıkış</button>
  </div>
</nav>
<?php endif; ?>

<main id="app">
<?php
switch ($page) {
    case 'anasayfa':
    case 'giris':
        include 'pages/giris.php'; break;
    case 'kayit':
        include 'pages/kayit.php'; break;
    case 'profil-kurulum':
        requireLogin(); include 'pages/profil_kurulum.php'; break;
    case 'panel':
        requireLogin(); include 'pages/panel.php'; break;
    case 'tara':
        requireLogin(); include 'pages/tara.php'; break;
    case 'profil':
        requireLogin(); include 'pages/profil.php'; break;
    case 'gecmis':
        requireLogin(); include 'pages/gecmis.php'; break;
    default:
        include 'pages/giris.php';
}
?>
</main>

<!-- ZXing barkod kütüphanesi (tara sayfasında kullanılır) -->
<script src="assets/js/app.js"></script>
</body>
</html>
