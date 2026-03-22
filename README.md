# 🛡️ AlerjiBekçisi - Kurulum Kılavuzu

## Sistem Gereksinimleri
- XAMPP 8.x (PHP 8.0+, MySQL 8.0+, Apache)
- Tarayıcı: Chrome, Firefox, Safari (modern sürüm)

---

## ⚡ Hızlı Kurulum

### 1. Dosyaları Yerleştirin
```
Tüm proje klasörünü buraya kopyalayın:
C:\xampp\htdocs\allerji_app\
```

### 2. XAMPP Başlatın
- Apache ✅ Başlat
- MySQL ✅ Başlat

### 3. Veritabanını Oluşturun
1. Tarayıcıda açın: http://localhost/phpmyadmin
2. Sol menüde "Yeni" ye tıklayın
3. Veritabanı adı: `allerji_db` → Oluştur
4. "SQL" sekmesine gidin
5. `database.sql` dosyasının içeriğini yapıştırın → Çalıştır

### 4. Uygulamayı Açın
```
http://localhost/allerji_app/
```

---

## 📁 Klasör Yapısı

```
allerji_app/
├── index.php              ← Ana router
├── database.sql           ← Veritabanı şeması
├── .htaccess
├── includes/
│   └── config.php         ← DB bağlantısı ve yardımcı fonksiyonlar
├── api/
│   ├── auth.php           ← Giriş/kayıt/çıkış API
│   ├── profil.php         ← Alerji profili API
│   └── tara.php           ← OCR tarama & geçmiş API
├── pages/
│   ├── giris.php          ← Giriş sayfası
│   ├── kayit.php          ← Kayıt sayfası
│   ├── profil_kurulum.php ← Alerji profili kurulum sihirbazı
│   ├── panel.php          ← Dashboard
│   ├── tara.php           ← Ürün tarama (OCR)
│   ├── profil.php         ← Profil görüntüleme
│   └── gecmis.php         ← Tarama geçmişi
├── assets/
│   ├── css/app.css        ← Ana stil dosyası
│   └── js/app.js          ← Ana JavaScript
└── uploads/
    └── ocr/               ← OCR için yüklenen resimler
```

---

## 🔧 Ayarlar

`includes/config.php` dosyasında değiştirebilirsiniz:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');    // MySQL kullanıcı adı
define('DB_PASS', '');        // MySQL şifresi (XAMPP'ta genelde boş)
define('DB_NAME', 'allerji_db');
define('BASE_URL', 'http://localhost/allerji_app');
```

---

## 🌟 Özellikler

| Özellik | Açıklama |
|---------|----------|
| 👤 Kullanıcı Sistemi | Kayıt, giriş, çıkış |
| 🛡️ Alerji Profili | 14 AB standart alerjen + şiddet seviyeleri |
| 📷 OCR Tarama | Tesseract.js ile Türkçe+İngilizce metin okuma |
| 🔴🟡🟢 Uyarı Sistemi | Kırmızı/Sarı/Yeşil seviyeli uyarılar |
| 📋 Geçmiş | Tüm taramaların kaydı ve filtreleme |
| 📊 İstatistik | Dashboard'da tarama özeti |

---

## ⚠️ Önemli Notlar

1. **OCR Kalitesi**: Net, iyi aydınlatılmış fotoğraflar daha iyi sonuç verir
2. **İnternet Gerekli**: Tesseract.js CDN'den yüklenir, ilk kullanımda internet bağlantısı gerekir
3. **uploads/ocr/** klasörünün yazma izni olması gerekir (XAMPP'ta otomatik)
4. Bu uygulama tıbbi tavsiye niteliği taşımaz, sadece bilgi amaçlıdır

---

## 🔒 Güvenlik

- Şifreler bcrypt ile hash'lenir
- PDO prepared statements kullanılır (SQL injection koruması)
- Dosya yükleme sadece resim dosyalarına izin verir
- Session tabanlı kimlik doğrulama
