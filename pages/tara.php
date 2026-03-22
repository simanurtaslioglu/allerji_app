<!-- pages/tara.php -->
<div class="tara-wrap">
  <div class="tara-header">
    <h2>🔍 Ürün Tarama</h2>
    <p>İçindekiler etiketini kameraya gösterin veya metin girin</p>
  </div>

  <div class="tara-kart">

    <div class="tab-butonlar">
      <button class="tab-btn active" onclick="tabSec(this,'kamera')">📷 Kamera ile Tara</button>
      <button class="tab-btn" onclick="tabSec(this,'metin')">📋 Metin Yapıştır</button>
    </div>

    <!-- KAMERA TAB -->
    <div id="tab-kamera">

      <!-- ADIM 1: Kamera Başlat -->
      <div id="adim-baslat">
        <div class="kamera-davet">
          <div class="kamera-davet-ikon">📷</div>
          <div class="kamera-davet-yazi">Kamerayı başlatın</div>
          <div class="kamera-davet-alt">Ürünün içindekiler etiketini kameraya gösterin, fotoğraf çekin</div>
          <button class="btn-primary" onclick="kameraAc()">📷 Kamerayı Başlat</button>
        </div>
      </div>

      <!-- ADIM 2: Canlı Kamera -->
      <div id="adim-kamera" class="gizli">
        <div class="canli-kamera-wrap">
          <video id="kamera-video" autoplay playsinline muted></video>
          <div class="kamera-rehber">
            <div class="rehber-cerceve">
              <div class="kose k-sol-ust"></div>
              <div class="kose k-sag-ust"></div>
              <div class="kose k-sol-alt"></div>
              <div class="kose k-sag-alt"></div>
            </div>
            <div class="rehber-yazi">İçindekiler metnini çerçeve içine alın</div>
          </div>
        </div>
        <div class="kamera-alt-bar">
          <button class="btn-outline" onclick="kameraKapat()">✕ İptal</button>
          <button class="btn-cek" onclick="fotografCek()">
            <span class="cek-ic"></span>
          </button>
          <div style="width:80px"></div>
        </div>
        <canvas id="cekilen-canvas" style="display:none"></canvas>
      </div>

      <!-- ADIM 3: OCR İşleniyor -->
      <div id="adim-ocr" class="gizli">
        <div class="ocr-islem-wrap">
          <img id="cekilen-fotograf" class="cekilen-on-izleme" alt="Çekilen fotoğraf">
          <div class="ocr-progress-wrap">
            <div class="ocr-spinner"></div>
            <div id="ocr-durum-yazi">Metin okunuyor...</div>
            <div class="ocr-bar-wrap">
              <div class="ocr-bar-ic" id="ocr-bar-ic"></div>
            </div>
            <div id="ocr-yuzde">%0</div>
          </div>
          <button class="btn-outline btn-kucuk-iptal" onclick="ocrIptal()">İptal</button>
        </div>
      </div>

      <!-- ADIM 4: OCR Sonucu + Düzenleme -->
      <div id="adim-sonuc-ocr" class="gizli">
        <div class="ocr-basari">
          <span class="ocr-basari-ikon">✅</span>
          <span>Metin başarıyla okundu! Gerekirse düzenleyin:</span>
        </div>
        <img id="cekilen-fotograf-kucuk" class="cekilen-kucuk" alt="">
        <div class="form-group">
          <label>Okunan İçerik Metni</label>
          <textarea id="ocr-metin-duzenleme" rows="7" placeholder="OCR ile okunan metin buraya gelecek..."></textarea>
        </div>
        <div class="adim-butonlar">
          <button class="btn-outline" onclick="yenidenCek()">🔄 Yeniden Çek</button>
          <button class="btn-primary btn-tara-ocr" onclick="ocrMetniTara()">🔍 Alerjen Kontrol Et</button>
        </div>
      </div>

    </div>

    <!-- METİN TAB -->
    <div id="tab-metin" class="gizli">
      <div class="form-group">
        <label>Ürün Adı <small>(isteğe bağlı)</small></label>
        <input type="text" id="manuel-urun-adi" placeholder="Örn: Çikolatalı Bisküvi">
      </div>
      <div class="form-group">
        <label>İçindekiler Metni</label>
        <textarea id="manuel-metin" rows="8" placeholder="Ürün etiketindeki içindekiler metnini buraya yapıştırın...&#10;&#10;Örn: Buğday unu, şeker, bitkisel yağ, yumurta, tuz..."></textarea>
      </div>
      <button class="btn-primary btn-full" onclick="manuelTara()">🔍 Alerjen Kontrol Et</button>
    </div>

    <div id="tarama-hata" class="mesaj hata gizli"></div>

  </div>

  <!-- SONUÇ ALANI -->
  <div id="sonuc-alani" class="gizli">
    <div id="sonuc-kart" class="sonuc-kart">
      <div id="sonuc-baslik"></div>
      <div id="sonuc-alerjenler"></div>
      <div id="sonuc-temiz" class="gizli sonuc-temiz">
        <div class="temiz-ikon">✅</div>
        <div class="temiz-yazi">Alerjen Tespit Edilmedi!</div>
        <div class="temiz-alt">Kayıtlı alerjenleriniz bu üründe bulunamadı</div>
      </div>
    </div>
    <button class="btn-outline btn-full" onclick="yeniTarama()" style="margin-top:1rem">🔄 Yeni Tarama</button>
  </div>

</div>

<!-- Tesseract OCR -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

<script>
let aktifTab = 'kamera';
let kameraStream = null;
let ocrWorker = null;
let ocrIptalEdildi = false;

// ── TAB ──────────────────────────────────────────────────────
function tabSec(btn, tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-kamera').classList.toggle('gizli', tab !== 'kamera');
  document.getElementById('tab-metin').classList.toggle('gizli', tab !== 'metin');
  if (tab !== 'kamera') kameraKapat();
  aktifTab = tab;
}

// ── KAMERA AÇ ────────────────────────────────────────────────
async function kameraAc() {
  try {
    kameraStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }
    });
    document.getElementById('kamera-video').srcObject = kameraStream;
    goster('adim-baslat', false);
    goster('adim-kamera', true);
  } catch (e) {
    hataGoster('Kamera açılamadı: ' + (e.message || e) + '. Tarayıcı iznini kontrol edin.');
  }
}

function kameraKapat() {
  if (kameraStream) {
    kameraStream.getTracks().forEach(t => t.stop());
    kameraStream = null;
  }
  goster('adim-kamera', false);
  goster('adim-baslat', true);
  goster('adim-ocr', false);
  goster('adim-sonuc-ocr', false);
}

// ── FOTOĞRAF ÇEK ─────────────────────────────────────────────
function fotografCek() {
  const video = document.getElementById('kamera-video');
  const canvas = document.getElementById('cekilen-canvas');
  canvas.width  = video.videoWidth  || 1280;
  canvas.height = video.videoHeight || 720;
  canvas.getContext('2d').drawImage(video, 0, 0);

  const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
  document.getElementById('cekilen-fotograf').src = dataUrl;
  document.getElementById('cekilen-fotograf-kucuk').src = dataUrl;

  kameraStream.getTracks().forEach(t => t.stop());
  kameraStream = null;

  goster('adim-kamera', false);
  goster('adim-ocr', true);
  ocrBaslat(canvas);
}

// ── OCR ──────────────────────────────────────────────────────
async function ocrBaslat(canvas) {
  ocrIptalEdildi = false;
  document.getElementById('ocr-bar-ic').style.width = '0%';
  document.getElementById('ocr-yuzde').textContent = '%0';
  document.getElementById('ocr-durum-yazi').textContent = 'Metin okunuyor...';

  try {
    ocrWorker = await Tesseract.createWorker('tur+eng', 1, {
      logger: m => {
        if (ocrIptalEdildi) return;
        if (m.status === 'recognizing text') {
          const pct = Math.round(m.progress * 100);
          document.getElementById('ocr-bar-ic').style.width = pct + '%';
          document.getElementById('ocr-yuzde').textContent = '%' + pct;
          document.getElementById('ocr-durum-yazi').textContent = 'Metin analiz ediliyor... %' + pct;
        } else if (m.status === 'loading language traineddata') {
          document.getElementById('ocr-durum-yazi').textContent = 'Dil paketi yükleniyor...';
        } else if (m.status === 'initializing api') {
          document.getElementById('ocr-durum-yazi').textContent = 'OCR motoru başlatılıyor...';
        }
      }
    });

    if (ocrIptalEdildi) return;

    const { data } = await ocrWorker.recognize(canvas);
    await ocrWorker.terminate();
    ocrWorker = null;

    if (ocrIptalEdildi) return;

    const metin = data.text.trim();
    document.getElementById('ocr-metin-duzenleme').value = metin;

    goster('adim-ocr', false);
    goster('adim-sonuc-ocr', true);

    if (!metin || metin.length < 5) {
      hataGoster('Metin okunamadı. Fotoğrafı daha net ve yakın çekmeyi deneyin veya "Metin Yapıştır" sekmesini kullanın.');
    }

  } catch (e) {
    if (!ocrIptalEdildi) {
      goster('adim-ocr', false);
      goster('adim-baslat', true);
      hataGoster('OCR hatası: ' + e.message);
    }
  }
}

function ocrIptal() {
  ocrIptalEdildi = true;
  if (ocrWorker) { try { ocrWorker.terminate(); } catch(e){} ocrWorker = null; }
  goster('adim-ocr', false);
  goster('adim-baslat', true);
}

function yenidenCek() {
  goster('adim-sonuc-ocr', false);
  document.getElementById('ocr-metin-duzenleme').value = '';
  kameraAc();
}

// ── OCR METNİNİ TARA ─────────────────────────────────────────
async function ocrMetniTara() {
  const metin = document.getElementById('ocr-metin-duzenleme').value.trim();
  if (!metin) {
    hataGoster('Metin alanı boş. Fotoğrafı yeniden çekin veya metni düzenleyin.');
    return;
  }
  await apiTara(metin, 'Fotoğraftan Taranan Ürün');
}

// ── MANUEL METİN TARA ────────────────────────────────────────
async function manuelTara() {
  const metin = document.getElementById('manuel-metin').value.trim();
  const urunAdi = document.getElementById('manuel-urun-adi').value.trim() || 'Bilinmeyen Ürün';
  if (!metin) {
    hataGoster('Lütfen içerik metnini girin.');
    return;
  }
  await apiTara(metin, urunAdi);
}

// ── API TARA ─────────────────────────────────────────────────
async function apiTara(metin, urunAdi) {
  document.getElementById('tarama-hata').classList.add('gizli');
  document.getElementById('sonuc-alani').classList.add('gizli');

  // Buton bul ve disable et
  const btns = document.querySelectorAll('.btn-tara-ocr, .btn-primary[onclick*="manuelTara"]');
  btns.forEach(b => { b.disabled = true; b.textContent = '⏳ Kontrol ediliyor...'; });

  try {
    const fd = new FormData();
    fd.append('action', 'tara_metin');
    fd.append('metin', metin);
    fd.append('ocr_ham', metin);
    fd.append('urun_adi', urunAdi);
    fd.append('resim_yolu', '');

    const r = await fetch('api/tara.php', { method: 'POST', body: fd });
    const d = await r.json();

    if (!d.success) {
      hataGoster(d.message);
    } else {
      renderSonuc(d);
    }
  } catch (e) {
    hataGoster('Sunucu bağlantısı kurulamadı: ' + e.message);
  } finally {
    btns.forEach(b => { b.disabled = false; b.textContent = '🔍 Alerjen Kontrol Et'; });
  }
}

// ── SONUÇ RENDER ─────────────────────────────────────────────
function renderSonuc(d) {
  const alan    = document.getElementById('sonuc-alani');
  const kart    = document.getElementById('sonuc-kart');
  const baslik  = document.getElementById('sonuc-baslik');
  const alerjen = document.getElementById('sonuc-alerjenler');
  const temiz   = document.getElementById('sonuc-temiz');

  kart.className = 'sonuc-kart seviye-' + d.uyari_seviyesi;

  const sv = {
    kirmizi: { icon:'🔴', text:'TEHLİKELİ! Alerjen İçeriyor',  acik:'Bu üründe şiddetli alerjenik madde tespit edildi!' },
    sari:    { icon:'🟡', text:'DİKKAT! Alerjen Var',           acik:'Bu üründe alerjeniniz bulunuyor.' },
    yesil:   { icon:'🟢', text:'GÜVENLİ',                       acik:'Kayıtlı alerjenleriniz bu üründe bulunamadı.' }
  }[d.uyari_seviyesi];

  baslik.innerHTML = `
    <div class="sonuc-seviye-ikon">${sv.icon}</div>
    <div class="sonuc-seviye-text">${sv.text}</div>
    <div class="sonuc-urun">${d.urun_adi}</div>
    <div class="sonuc-acik">${sv.acik}</div>`;

  if (d.eslesmeler && d.eslesmeler.length > 0) {
    temiz.classList.add('gizli');
    alerjen.innerHTML = d.eslesmeler.map(e => {
      const renk = {hafif:'#F0C040', orta:'#E67E22', siddetli:'#E74C3C'}[e.siddet] || '#E74C3C';
      return `<div class="alerjen-satir" style="border-left:5px solid ${renk}">
        <span class="alerjen-icon">${e.icon}</span>
        <div class="alerjen-bilgi">
          <strong>${e.kategori_adi}</strong>
          <span class="alerjen-siddet" style="color:${renk}">● ${e.siddet.charAt(0).toUpperCase()+e.siddet.slice(1)}</span>
          <div class="alerjen-kelimeler">${e.bulunan_kelimeler.join(', ')}</div>
        </div>
      </div>`;
    }).join('');
  } else {
    temiz.classList.remove('gizli');
    alerjen.innerHTML = '';
  }

  alan.classList.remove('gizli');
  alan.scrollIntoView({ behavior: 'smooth' });
}

// ── YENİ TARAMA ──────────────────────────────────────────────
function yeniTarama() {
  document.getElementById('sonuc-alani').classList.add('gizli');
  document.getElementById('tarama-hata').classList.add('gizli');
  document.getElementById('ocr-metin-duzenleme').value = '';
  document.getElementById('manuel-metin').value = '';
  document.getElementById('manuel-urun-adi').value = '';
  goster('adim-sonuc-ocr', false);
  goster('adim-baslat', true);
  kameraKapat();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── YARDIMCILAR ──────────────────────────────────────────────
function goster(id, goster) {
  const el = document.getElementById(id);
  if (el) el.classList.toggle('gizli', !goster);
}

function hataGoster(mesaj) {
  const el = document.getElementById('tarama-hata');
  el.textContent = mesaj;
  el.classList.remove('gizli');
}
</script>
