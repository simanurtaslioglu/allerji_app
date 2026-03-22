<!-- pages/gecmis.php -->
<div class="gecmis-wrap">
  <div class="sayfa-baslik">
    <h2>📋 Tarama Geçmişi</h2>
    <p>Tüm ürün taramalarınızın kayıtları</p>
  </div>

  <div class="filtre-bar">
    <button class="filtre-btn active" onclick="filtrele(this,'hepsi')">Tümü</button>
    <button class="filtre-btn" onclick="filtrele(this,'kirmizi')">🔴 Tehlikeli</button>
    <button class="filtre-btn" onclick="filtrele(this,'sari')">🟡 Dikkatli</button>
    <button class="filtre-btn" onclick="filtrele(this,'yesil')">🟢 Güvenli</button>
  </div>

  <div id="gecmis-liste" class="gecmis-liste">
    <div class="yukluyor">Yükleniyor...</div>
  </div>
</div>

<!-- Detay Modal -->
<div id="detay-modal" class="modal gizli">
  <div class="modal-arka" onclick="modalKapat()"></div>
  <div class="modal-icerik">
    <button class="modal-kapat" onclick="modalKapat()">✕</button>
    <div id="modal-detay"></div>
  </div>
</div>

<script>
let tumVeri = [];
let aktifFiltre = 'hepsi';

document.addEventListener('DOMContentLoaded', gecmisYukle);

async function gecmisYukle() {
  const r = await fetch('api/tara.php?action=gecmis&limit=100');
  const d = await r.json();
  tumVeri = d.data || [];
  renderGecmis();
}

function filtrele(btn, seviye) {
  document.querySelectorAll('.filtre-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  aktifFiltre = seviye;
  renderGecmis();
}

function renderGecmis() {
  const el = document.getElementById('gecmis-liste');
  const veri = aktifFiltre === 'hepsi' ? tumVeri : tumVeri.filter(t => t.uyari_seviyesi === aktifFiltre);

  if (veri.length === 0) {
    el.innerHTML = '<div class="bos-durum">Bu kategoride tarama bulunamadı.</div>';
    return;
  }

  const icons = {kirmizi:'🔴',sari:'🟡',yesil:'🟢'};
  const siniflar = {kirmizi:'gecmis-kart-kirmizi',sari:'gecmis-kart-sari',yesil:'gecmis-kart-yesil'};

  el.innerHTML = veri.map(t => {
    const tarih = new Date(t.tarama_tarihi).toLocaleString('tr-TR');
    const alerjenler = (t.bulunan_alerjenler || []).map(a => `${a.icon} ${a.kategori_adi}`).join(', ') || '—';
    return `<div class="gecmis-kart ${siniflar[t.uyari_seviyesi]}" onclick="detayGoster(${t.id})">
      <div class="gecmis-kart-sol">
        <div class="gecmis-seviye-ikon">${icons[t.uyari_seviyesi]}</div>
        <div>
          <div class="gecmis-urun-adi">${t.urun_adi}</div>
          <div class="gecmis-alerjenler">${alerjenler}</div>
          <div class="gecmis-zaman">${tarih}</div>
        </div>
      </div>
      <div class="gecmis-ok">›</div>
    </div>`;
  }).join('');
}

async function detayGoster(id) {
  const r = await fetch(`api/tara.php?action=gecmis_detay&id=${id}`);
  const d = await r.json();
  if (!d.success) return;

  const t = d.data;
  const tarih = new Date(t.tarama_tarihi).toLocaleString('tr-TR');
  const alerjenler = JSON.parse(t.bulunan_alerjenler || '[]');
  const icons = {kirmizi:'🔴',sari:'🟡',yesil:'🟢'};
  const etiketler = {kirmizi:'Tehlikeli',sari:'Dikkatli',yesil:'Güvenli'};

  document.getElementById('modal-detay').innerHTML = `
    <div class="modal-baslik">
      <span class="modal-seviye">${icons[t.uyari_seviyesi]} ${etiketler[t.uyari_seviyesi]}</span>
      <h3>${t.urun_adi}</h3>
      <div class="modal-tarih">${tarih}</div>
    </div>
    ${alerjenler.length > 0 ? `
    <div class="modal-bolum">
      <h4>Tespit Edilen Alerjenler</h4>
      ${alerjenler.map(a => `<div class="alerjen-satir">
        <span class="alerjen-icon">${a.icon}</span>
        <div>
          <strong>${a.kategori_adi}</strong>
          <span class="alerjen-siddet"> (${a.siddet})</span>
          <div class="alerjen-kelimeler">${a.bulunan_kelimeler.join(', ')}</div>
        </div>
      </div>`).join('')}
    </div>` : '<div class="modal-temiz">✅ Alerjen tespit edilmedi</div>'}
    <div class="modal-bolum">
      <h4>Okunan İçerik</h4>
      <div class="modal-icerik-metin">${t.icerik_metni}</div>
    </div>`;

  document.getElementById('detay-modal').classList.remove('gizli');
}

function modalKapat() {
  document.getElementById('detay-modal').classList.add('gizli');
}
</script>
