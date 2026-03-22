<?php
// api/profil.php
require_once '../includes/config.php';
requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'kategoriler':
        $stmt = db()->query("SELECT * FROM alerji_kategorileri ORDER BY id");
        jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'kullanici_alerjileri':
        $stmt = db()->prepare("
            SELECT ua.*, ak.kategori_adi, ak.icon, ak.renk_kodu
            FROM kullanici_alerjileri ua
            JOIN alerji_kategorileri ak ON ua.kategori_id = ak.id
            WHERE ua.kullanici_id = ?
        ");
        $stmt->execute([$_SESSION['kullanici_id']]);
        jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'kaydet_alerji':
        $kategori_ids = $_POST['kategori_ids'] ?? [];
        $siddetler    = $_POST['siddetler'] ?? [];
        $notlar       = $_POST['notlar'] ?? [];

        // Mevcut alerjileri sil, yeniden ekle
        $stmt = db()->prepare("DELETE FROM kullanici_alerjileri WHERE kullanici_id = ?");
        $stmt->execute([$_SESSION['kullanici_id']]);

        $ins = db()->prepare("INSERT INTO kullanici_alerjileri (kullanici_id, kategori_id, siddet, notlar) VALUES (?,?,?,?)");
        foreach ($kategori_ids as $i => $kid) {
            $ins->execute([
                $_SESSION['kullanici_id'],
                $kid,
                $siddetler[$i] ?? 'orta',
                $notlar[$i] ?? ''
            ]);
        }

        // Anketi kaydet
        $tani       = $_POST['tani_var_mi'] ?? 0;
        $reaksiyon  = implode(',', (array)($_POST['reaksiyon_tipi'] ?? []));
        $epipen     = $_POST['epipen_kullaniyor'] ?? 0;
        $ek_notlar  = $_POST['ek_notlar'] ?? '';

        $stmt = db()->prepare("
            INSERT INTO profil_anketi (kullanici_id, tani_var_mi, reaksiyon_tipi, epipen_kullaniyor, ek_notlar)
            VALUES (?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
                tani_var_mi=VALUES(tani_var_mi),
                reaksiyon_tipi=VALUES(reaksiyon_tipi),
                epipen_kullaniyor=VALUES(epipen_kullaniyor),
                ek_notlar=VALUES(ek_notlar)
        ");
        $stmt->execute([$_SESSION['kullanici_id'], $tani, $reaksiyon, $epipen, $ek_notlar]);

        // Profili tamamlandı olarak işaretle
        $stmt = db()->prepare("UPDATE kullanicilar SET profil_tamamlandi = 1 WHERE id = ?");
        $stmt->execute([$_SESSION['kullanici_id']]);

        jsonResponse(['success' => true, 'message' => 'Alerji profili kaydedildi.']);
        break;

    case 'profil_ozet':
        $user = currentUser();
        $stmt = db()->prepare("
            SELECT ua.siddet, ak.kategori_adi, ak.icon, ak.renk_kodu
            FROM kullanici_alerjileri ua
            JOIN alerji_kategorileri ak ON ua.kategori_id = ak.id
            WHERE ua.kullanici_id = ?
            ORDER BY FIELD(ua.siddet,'siddetli','orta','hafif')
        ");
        $stmt->execute([$_SESSION['kullanici_id']]);
        $alerjiler = $stmt->fetchAll();

        $stmt2 = db()->prepare("SELECT * FROM profil_anketi WHERE kullanici_id = ?");
        $stmt2->execute([$_SESSION['kullanici_id']]);
        $anket = $stmt2->fetch();

        jsonResponse([
            'success'   => true,
            'kullanici' => $user,
            'alerjiler' => $alerjiler,
            'anket'     => $anket
        ]);
        break;
}
