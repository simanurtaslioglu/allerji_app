-- =============================================
-- ALERJİ UYARI UYGULAMASI - VERİTABANI ŞEMASI
-- XAMPP / MySQL
-- =============================================

CREATE DATABASE IF NOT EXISTS allerji_db CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE allerji_db;

-- KULLANICILAR
CREATE TABLE IF NOT EXISTS kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(100) NOT NULL,
    soyad VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    dogum_tarihi DATE,
    profil_tamamlandi TINYINT(1) DEFAULT 0,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ALERJİ KATEGORİLERİ (Sabit liste)
CREATE TABLE IF NOT EXISTS alerji_kategorileri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_adi VARCHAR(100) NOT NULL,
    aciklama TEXT,
    renk_kodu VARCHAR(7) DEFAULT '#FF4444',
    icon VARCHAR(50)
) ENGINE=InnoDB;

INSERT INTO alerji_kategorileri (kategori_adi, aciklama, renk_kodu, icon) VALUES
('Gluten / Buğday',     'Buğday, arpa, çavdar, yulaf içeren ürünler',        '#E74C3C', '🌾'),
('Süt / Laktoz',        'Süt ve süt ürünleri (peynir, yoğurt, tereyağı)',     '#3498DB', '🥛'),
('Yumurta',             'Yumurta ve yumurta içeren ürünler',                  '#F39C12', '🥚'),
('Yer Fıstığı',         'Yer fıstığı ve yer fıstığı yağı',                   '#8B4513', '🥜'),
('Ağaç Fıstıkları',     'Badem, ceviz, fındık, kaju, antep fıstığı',         '#27AE60', '🌰'),
('Balık',               'Her türlü balık ve balık ürünleri',                  '#1ABC9C', '🐟'),
('Kabuklu Deniz Ürünleri','İstakoz, karides, yengeç, midye',                 '#E67E22', '🦐'),
('Soya',                'Soya fasulyesi ve soya içeren ürünler',              '#9B59B6', '🫘'),
('Susam',               'Susam tohumu ve susam yağı',                         '#F1C40F', '🌱'),
('Hardal',              'Hardal tohumu ve hardal içeren ürünler',             '#D4AC0D', '🌿'),
('Kereviz',             'Kereviz ve kereviz içeren ürünler',                  '#2ECC71', '🥬'),
('Kükürt Dioksit',      'Şarap, kuru meyve, konserveler (E220-E228)',         '#7F8C8D', '🍷'),
('Lupin',               'Lupin tohumu ve lupin unu içeren ürünler',           '#E91E63', '🌸'),
('Yumuşakça',           'Salyangoz, kalamar, ahtapot, istiridye',             '#00BCD4', '🐚');

-- KULLANICI ALERJİLERİ
CREATE TABLE IF NOT EXISTS kullanici_alerjileri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    kategori_id INT NOT NULL,
    siddet ENUM('hafif','orta','siddetli') DEFAULT 'orta',
    notlar TEXT,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id) ON DELETE CASCADE,
    FOREIGN KEY (kategori_id) REFERENCES alerji_kategorileri(id),
    UNIQUE KEY unique_kullanici_alerji (kullanici_id, kategori_id)
) ENGINE=InnoDB;

-- ÜRÜN TARAMA GEÇMİŞİ
CREATE TABLE IF NOT EXISTS tarama_gecmisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    urun_adi VARCHAR(200),
    icerik_metni LONGTEXT,
    ocr_ham_metin LONGTEXT,
    resim_yolu VARCHAR(500),
    uyari_seviyesi ENUM('yesil','sari','kirmizi') DEFAULT 'yesil',
    bulunan_alerjenler JSON,
    tarama_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ALERJİ ANAHTAR KELİMELERİ (Tarama için)
CREATE TABLE IF NOT EXISTS alerji_anahtar_kelimeler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    kelime VARCHAR(200) NOT NULL,
    FOREIGN KEY (kategori_id) REFERENCES alerji_kategorileri(id)
) ENGINE=InnoDB;

INSERT INTO alerji_anahtar_kelimeler (kategori_id, kelime) VALUES
-- Gluten
(1,'gluten'),(1,'buğday'),(1,'wheat'),(1,'arpa'),(1,'barley'),(1,'çavdar'),(1,'rye'),
(1,'yulaf'),(1,'oat'),(1,'un'),(1,'flour'),(1,'nişasta'),(1,'starch'),(1,'ekmek'),
(1,'makarna'),(1,'irmik'),(1,'semolina'),(1,'tritikale'),(1,'kamut'),(1,'spelt'),
-- Süt
(2,'süt'),(2,'milk'),(2,'laktoz'),(2,'lactose'),(2,'peynir'),(2,'cheese'),(2,'yoğurt'),
(2,'yogurt'),(2,'tereyağ'),(2,'butter'),(2,'krem'),(2,'cream'),(2,'kazein'),(2,'casein'),
(2,'whey'),(2,'peynir altı suyu'),(2,'süt tozu'),(2,'skimmed milk'),
-- Yumurta
(3,'yumurta'),(3,'egg'),(3,'albumin'),(3,'albümin'),(3,'livettin'),(3,'ovalbumin'),
(3,'ovomucin'),(3,'yolk'),(3,'sarısı'),(3,'beyazı'),
-- Yer Fıstığı
(4,'yer fıstığı'),(4,'peanut'),(4,'groundnut'),(4,'arachis'),
-- Ağaç Fıstıkları
(5,'badem'),(5,'almond'),(5,'ceviz'),(5,'walnut'),(5,'fındık'),(5,'hazelnut'),
(5,'kaju'),(5,'cashew'),(5,'antep fıstığı'),(5,'pistachio'),(5,'pekan'),(5,'pecan'),
(5,'macadamia'),(5,'brazil nut'),(5,'brezilya cevizi'),
-- Balık
(6,'balık'),(6,'fish'),(6,'somon'),(6,'salmon'),(6,'ton'),(6,'tuna'),(6,'hamsi'),
(6,'anchovy'),(6,'morina'),(6,'cod'),(6,'levrek'),(6,'bass'),(6,'çipura'),
-- Kabuklu Deniz Ürünleri
(7,'karides'),(7,'shrimp'),(7,'prawn'),(7,'istakoz'),(7,'lobster'),(7,'yengeç'),
(7,'crab'),(7,'midye'),(7,'mussel'),
-- Soya
(8,'soya'),(8,'soy'),(8,'soybeans'),(8,'tofu'),(8,'miso'),(8,'tempeh'),(8,'edamame'),
-- Susam
(9,'susam'),(9,'sesame'),(9,'tahini'),(9,'tahin'),
-- Hardal
(10,'hardal'),(10,'mustard'),(10,'sinapis'),
-- Kereviz
(11,'kereviz'),(11,'celery'),
-- Kükürt Dioksit
(12,'e220'),(12,'e221'),(12,'e222'),(12,'e223'),(12,'e224'),(12,'e225'),(12,'e226'),
(12,'e227'),(12,'e228'),(12,'sülfür dioksit'),(12,'sulphur dioxide'),(12,'sulfites'),
-- Lupin
(13,'lupin'),(13,'lupine'),
-- Yumuşakça
(14,'salyangoz'),(14,'snail'),(14,'kalamar'),(14,'squid'),(14,'ahtapot'),(14,'octopus'),
(14,'istiridye'),(14,'oyster');

-- KULLANICI SORU CEVAPLARI (Profil oluşturma anketi)
CREATE TABLE IF NOT EXISTS profil_anketi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL UNIQUE,
    tani_var_mi TINYINT(1) DEFAULT 0,
    reaksiyon_tipi SET('deri','solunum','sindirim','anafilaksi','diger') DEFAULT '',
    epipen_kullaniyor TINYINT(1) DEFAULT 0,
    ek_notlar TEXT,
    tamamlanma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id) ON DELETE CASCADE
) ENGINE=InnoDB;
