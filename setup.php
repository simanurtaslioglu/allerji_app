<?php
// setup.php — Railway'de ilk çalıştırmada veritabanını kurar
// Kullanım: https://senin-uygulaman.railway.app/setup.php
// Kurulum bittikten sonra bu dosyayı silebilirsiniz (opsiyonel)

require_once 'includes/config.php';

header('Content-Type: text/html; charset=utf-8');

$sql = file_get_contents(__DIR__ . '/database.sql');

// CREATE DATABASE ve USE satırlarını çıkar (Railway zaten DB veriyor)
$sql = preg_replace('/CREATE DATABASE.*?;\s*/is', '', $sql);
$sql = preg_replace('/USE .*?;\s*/is', '', $sql);

$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    fn($s) => strlen($s) > 5
);

$basarili = 0;
$hatalar  = [];

foreach ($statements as $stmt) {
    try {
        db()->exec($stmt);
        $basarili++;
    } catch (PDOException $e) {
        // Tablo zaten varsa sessizce geç
        if (!str_contains($e->getMessage(), 'already exists')) {
            $hatalar[] = $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>AlerjiBekçisi — Kurulum</title>
<style>
  body { font-family: sans-serif; background:#0F1117; color:#E8ECF4; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
  .kart { background:#161A24; border:1px solid #2A3045; border-radius:16px; padding:2rem; max-width:500px; width:90%; }
  h2 { margin-bottom:1rem; }
  .ok  { color:#2ECC71; }
  .err { color:#E74C3C; font-size:0.82rem; margin-top:0.5rem; }
  a { display:inline-block; margin-top:1.5rem; background:#4F8EF7; color:#fff; padding:0.8rem 1.5rem; border-radius:10px; text-decoration:none; font-weight:600; }
</style>
</head>
<body>
<div class="kart">
  <h2>🛡️ AlerjiBekçisi Kurulum</h2>
  <?php if (empty($hatalar)): ?>
    <p class="ok">✅ Veritabanı başarıyla kuruldu!</p>
    <p style="color:#8892A4;font-size:0.88rem;margin-top:0.5rem"><?= $basarili ?> sorgu çalıştırıldı.</p>
    <a href="index.php">🚀 Uygulamayı Aç</a>
  <?php else: ?>
    <p class="ok">⚠️ Kısmen tamamlandı (<?= $basarili ?> sorgu başarılı)</p>
    <?php foreach ($hatalar as $h): ?>
      <div class="err">❌ <?= htmlspecialchars($h) ?></div>
    <?php endforeach; ?>
    <a href="index.php">Yine de Uygulamayı Dene →</a>
  <?php endif; ?>
</div>
</body>
</html>
