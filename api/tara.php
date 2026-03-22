<?php
// api/tara.php
require_once '../includes/config.php';
requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'tara_metin':
        // Manuel metin veya OCR sonucu gelir
        $metin     = $_POST['metin'] ?? '';
        $urun_adi  = $_POST['urun_adi'] ?? 'Bilinmeyen Ürün';
        $ocr_ham   = $_POST['ocr_ham'] ?? $metin;
        $resim_yolu = $_POST['resim_yolu'] ?? '';

        if (!$metin) {
            jsonResponse(['success' => false, 'message' => 'Taranacak metin bulunamadı.']);
        }

        // Kullanıcının alerjenlerini getir
        $stmt = db()->prepare("
            SELECT ua.siddet, ak.id as kategori_id, ak.kategori_adi, ak.icon, ak.renk_kodu
            FROM kullanici_alerjileri ua
            JOIN alerji_kategorileri ak ON ua.kategori_id = ak.id
            WHERE ua.kullanici_id = ?
        ");
        $stmt->execute([$_SESSION['kullanici_id']]);
        $kullanici_alerjileri = $stmt->fetchAll();

        if (empty($kullanici_alerjileri)) {
            jsonResponse(['success' => false, 'message' => 'Önce alerji profilinizi oluşturunuz.']);
        }

        // Anahtar kelimeleri getir
        $kategori_ids = array_column($kullanici_alerjileri, 'kategori_id');
        $placeholders = implode(',', array_fill(0, count($kategori_ids), '?'));
        $stmt = db()->prepare("SELECT * FROM alerji_anahtar_kelimeler WHERE kategori_id IN ($placeholders)");
        $stmt->execute($kategori_ids);
        $anahtar_kelimeler = $stmt->fetchAll();

        // Metni normalize et
        $metin_lower = mb_strtolower($metin, 'UTF-8');
        $metin_lower = str_replace(['İ','Ş','Ğ','Ü','Ö','Ç'], ['i','ş','ğ','ü','ö','ç'], $metin_lower);

        // Eşleşmeleri bul
        $eslesmeler = [];
        foreach ($anahtar_kelimeler as $kw) {
            $kelime = mb_strtolower($kw['kelime'], 'UTF-8');
            if (mb_strpos($metin_lower, $kelime) !== false) {
                $kid = $kw['kategori_id'];
                if (!isset($eslesmeler[$kid])) {
                    // Kullanıcının bu alerji için bilgilerini bul
                    foreach ($kullanici_alerjileri as $al) {
                        if ($al['kategori_id'] == $kid) {
                            $eslesmeler[$kid] = [
                                'kategori_id'   => $kid,
                                'kategori_adi'  => $al['kategori_adi'],
                                'icon'          => $al['icon'],
                                'renk_kodu'     => $al['renk_kodu'],
                                'siddet'        => $al['siddet'],
                                'bulunan_kelimeler' => []
                            ];
                            break;
                        }
                    }
                }
                if (isset($eslesmeler[$kid])) {
                    $eslesmeler[$kid]['bulunan_kelimeler'][] = $kw['kelime'];
                }
            }
        }

        // Uyarı seviyesini hesapla
        $uyari_seviyesi = 'yesil';
        $siddet_puanlari = ['hafif' => 1, 'orta' => 2, 'siddetli' => 3];
        $max_puan = 0;

        foreach ($eslesmeler as &$eslesme) {
            $eslesme['bulunan_kelimeler'] = array_unique($eslesme['bulunan_kelimeler']);
            $puan = $siddet_puanlari[$eslesme['siddet']] ?? 1;
            if ($puan > $max_puan) $max_puan = $puan;
        }

        if ($max_puan === 3) $uyari_seviyesi = 'kirmizi';
        elseif ($max_puan === 2) $uyari_seviyesi = 'sari';
        elseif ($max_puan === 1) $uyari_seviyesi = 'sari';

        if (empty($eslesmeler)) $uyari_seviyesi = 'yesil';

        // Veritabanına kaydet
        $stmt = db()->prepare("
            INSERT INTO tarama_gecmisi (kullanici_id, urun_adi, icerik_metni, ocr_ham_metin, resim_yolu, uyari_seviyesi, bulunan_alerjenler)
            VALUES (?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $_SESSION['kullanici_id'],
            $urun_adi,
            $metin,
            $ocr_ham,
            $resim_yolu,
            $uyari_seviyesi,
            json_encode(array_values($eslesmeler), JSON_UNESCAPED_UNICODE)
        ]);

        jsonResponse([
            'success'         => true,
            'uyari_seviyesi'  => $uyari_seviyesi,
            'eslesmeler'      => array_values($eslesmeler),
            'toplam_eslesme'  => count($eslesmeler),
            'urun_adi'        => $urun_adi
        ]);
        break;

    case 'ocr_yukle':
        // Resim yükle, base64'e çevir (Tesseract.js frontend'de çalışacak)
        if (!isset($_FILES['resim']) || $_FILES['resim']['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(['success' => false, 'message' => 'Dosya yükleme hatası.']);
        }

        $izin_tipleri = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $tip = $_FILES['resim']['type'];
        if (!in_array($tip, $izin_tipleri)) {
            jsonResponse(['success' => false, 'message' => 'Sadece JPEG/PNG/WEBP dosyaları kabul edilir.']);
        }

        $uzanti = pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION);
        $dosya_adi = 'ocr_' . $_SESSION['kullanici_id'] . '_' . time() . '.' . $uzanti;
        $hedef = __DIR__ . '/../uploads/ocr/' . $dosya_adi;

        if (move_uploaded_file($_FILES['resim']['tmp_name'], $hedef)) {
            jsonResponse([
                'success'    => true,
                'dosya_yolu' => BASE_URL . '/uploads/ocr/' . $dosya_adi,
                'dosya_adi'  => $dosya_adi
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Dosya kaydedilemedi.']);
        }
        break;

    case 'gecmis':
        $limit = intval($_GET['limit'] ?? 20);
        $limit = max(1, min(100, $limit));

        $stmt = db()->prepare("
            SELECT id, urun_adi, uyari_seviyesi, bulunan_alerjenler, tarama_tarihi
            FROM tarama_gecmisi
            WHERE kullanici_id = ?
            ORDER BY tarama_tarihi DESC
            LIMIT ?
        ");
        $stmt->execute([$_SESSION['kullanici_id'], $limit]);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $row['bulunan_alerjenler'] = json_decode($row['bulunan_alerjenler'], true);
        }

        jsonResponse(['success' => true, 'data' => $rows]);
        break;

    case 'gecmis_detay':
        $id = intval($_GET['id'] ?? 0);
        $stmt = db()->prepare("SELECT * FROM tarama_gecmisi WHERE id = ? AND kullanici_id = ?");
        $stmt->execute([$id, $_SESSION['kullanici_id']]);
        $row = $stmt->fetch();
        if (!$row) jsonResponse(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        $row['bulunan_alerjenler'] = json_decode($row['bulunan_alerjenler'], true);
        jsonResponse(['success' => true, 'data' => $row]);
        break;

    case 'istatistik':
        $stmt = db()->prepare("
            SELECT
                COUNT(*) as toplam_tarama,
                SUM(uyari_seviyesi = 'kirmizi') as kirmizi_uyari,
                SUM(uyari_seviyesi = 'sari')    as sari_uyari,
                SUM(uyari_seviyesi = 'yesil')   as yesil_uyari
            FROM tarama_gecmisi WHERE kullanici_id = ?
        ");
        $stmt->execute([$_SESSION['kullanici_id']]);
        $istat = $stmt->fetch();

        // Son 7 günlük tarama
        $stmt2 = db()->prepare("
            SELECT DATE(tarama_tarihi) as tarih, COUNT(*) as adet, uyari_seviyesi
            FROM tarama_gecmisi
            WHERE kullanici_id = ? AND tarama_tarihi >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(tarama_tarihi), uyari_seviyesi
            ORDER BY tarih DESC
        ");
        $stmt2->execute([$_SESSION['kullanici_id']]);
        $haftalik = $stmt2->fetchAll();

        jsonResponse(['success' => true, 'istatistik' => $istat, 'haftalik' => $haftalik]);
        break;
}
