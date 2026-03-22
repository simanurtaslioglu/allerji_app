<?php
// api/auth.php
require_once '../includes/config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'kayit':
        $ad      = trim($_POST['ad'] ?? '');
        $soyad   = trim($_POST['soyad'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $sifre   = $_POST['sifre'] ?? '';
        $dogum   = $_POST['dogum_tarihi'] ?? null;

        if (!$ad || !$soyad || !$email || !$sifre) {
            jsonResponse(['success' => false, 'message' => 'Tüm alanlar zorunludur.']);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Geçerli bir e-posta adresi giriniz.']);
        }
        if (strlen($sifre) < 6) {
            jsonResponse(['success' => false, 'message' => 'Şifre en az 6 karakter olmalıdır.']);
        }

        $stmt = db()->prepare("SELECT id FROM kullanicilar WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Bu e-posta zaten kayıtlı.']);
        }

        $hash = password_hash($sifre, PASSWORD_DEFAULT);
        $stmt = db()->prepare("INSERT INTO kullanicilar (ad, soyad, email, sifre, dogum_tarihi) VALUES (?,?,?,?,?)");
        $stmt->execute([$ad, $soyad, $email, $hash, $dogum ?: null]);
        $id = db()->lastInsertId();

        $_SESSION['kullanici_id'] = $id;
        $_SESSION['kullanici_ad'] = $ad;
        jsonResponse(['success' => true, 'message' => 'Kayıt başarılı!', 'redirect' => BASE_URL . '/index.php?page=profil-kurulum']);
        break;

    case 'giris':
        $email = trim($_POST['email'] ?? '');
        $sifre = $_POST['sifre'] ?? '';

        $stmt = db()->prepare("SELECT * FROM kullanicilar WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($sifre, $user['sifre'])) {
            jsonResponse(['success' => false, 'message' => 'E-posta veya şifre hatalı.']);
        }

        $_SESSION['kullanici_id'] = $user['id'];
        $_SESSION['kullanici_ad'] = $user['ad'];

        $redirect = $user['profil_tamamlandi']
            ? BASE_URL . '/index.php?page=panel'
            : BASE_URL . '/index.php?page=profil-kurulum';

        jsonResponse(['success' => true, 'redirect' => $redirect]);
        break;

    case 'cikis':
        session_destroy();
        jsonResponse(['success' => true, 'redirect' => BASE_URL . '/index.php']);
        break;
}
