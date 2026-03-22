<!-- pages/profil_kurulum.php -->
<div class="kurulum-wrap">
  <div class="kurulum-header">
    <h2>🛡️ Alerji Profilinizi Oluşturun</h2>
    <p>Bilgileriniz güvenle saklanır ve tarama sırasında kullanılır</p>
    <div class="adim-bar">
      <div class="adim active" id="adim-1-dot"><span>1</span></div>
      <div class="adim-cizgi"></div>
      <div class="adim" id="adim-2-dot"><span>2</span></div>
      <div class="adim-cizgi"></div>
      <div class="adim" id="adim-3-dot"><span>3</span></div>
    </div>
  </div>

  <!-- ADIM 1: Tanı -->
  <div class="adim-icerik" id="adim-1">
    <h3>Bir doktor tarafından alerji tanısı konuldu mu?</h3>
    <div class="secim-kartlari">
      <div class="secim-kart" onclick="secimSec(this,'tani','1')">
        <span class="secim-icon">👨‍⚕️</span>
        <span>Evet, tıbbi tanım var</span>
      </div>
      <div class="secim-kart" onclick="secimSec(this,'tani','0')">
        <span class="secim-icon">🤔</span>
        <span>Hayır / Emin değilim</span>
      </div>
    </div>
    <input type="hidden" id="tani_var_mi" value="">

    <h3 style="margin-top:2rem">Reaksiyon türleri? <small>(birden fazla seçebilirsiniz)</small></h3>
    <div class="checkbox-grid">
      <label class="checkbox-item"><input type="checkbox" value="deri"> 🔴 Deri (kaşıntı, döküntü)</label>
      <label class="checkbox-item"><input type="checkbox" value="solunum"> 💨 Solunum yolu</label>
      <label class="checkbox-item"><input type="checkbox" value="sindirim"> 🤢 Sindirim (mide, barsak)</label>
      <label class="checkbox-item"><input type="checkbox" value="anafilaksi"> ⚠️ Anafilaksi (şiddetli)</label>
      <label class="checkbox-item"><input type="checkbox" value="diger"> 🔘 Diğer</label>
    </div>

    <div class="form-group" style="margin-top:1rem">
      <label><input type="checkbox" id="epipen"> 💉 EpiPen / Epinefrin kullanıyorum</label>
    </div>

    <button class="btn-primary" onclick="adimGec(2)">Devam Et →</button>
  </div>

  <!-- ADIM 2: Alerji Seçimi -->
  <div class="adim-icerik gizli" id="adim-2">
    <h3>Hangi maddelere karşı alerjiniz var?</h3>
    <p class="hint">Her alerjen için şiddeti ayarlayabilirsiniz</p>
    <div id="alerji-listesi" class="alerji-grid">
      <div class="yukluyor">Yükleniyor...</div>
    </div>
    <div class="adim-btn-grup">
      <button class="btn-outline" onclick="adimGec(1)">← Geri</button>
      <button class="btn-primary" onclick="adimGec(3)">Devam Et →</button>
    </div>
  </div>

  <!-- ADIM 3: Özet & Kaydet -->
  <div class="adim-icerik gizli" id="adim-3">
    <h3>✅ Profiliniz Hazır!</h3>
    <div id="secilen-ozet" class="ozet-liste"></div>
    <div class="form-group" style="margin-top:1rem">
      <label>Ek notlar <small>(isteğe bağlı)</small></label>
      <textarea id="ek_notlar" rows="3" placeholder="Örn: Çok az miktarda bile reaksiyon oluyor..."></textarea>
    </div>
    <div class="adim-btn-grup">
      <button class="btn-outline" onclick="adimGec(2)">← Geri</button>
      <button class="btn-primary btn-büyük" onclick="profilKaydet()">🛡️ Profili Kaydet</button>
    </div>
  </div>
</div>

<script>
let secilenAlerjiler = {};

async function adimGec(n) {
  if (n === 2 && !document.getElementById('tani_var_mi').value) {
    alert('Lütfen tanı durumunu seçin.');
    return;
  }
  if (n === 3) {
    const secilenler = Object.keys(secilenAlerjiler).filter(k => secilenAlerjiler[k]);
    if (secilenler.length === 0) {
      alert('En az bir alerjen seçin.');
      return;
    }
    renderOzet();
  }
  document.querySelectorAll('.adim-icerik').forEach(el => el.classList.add('gizli'));
  document.getElementById('adim-' + n).classList.remove('gizli');
  document.querySelectorAll('.adim').forEach((el, i) => {
    el.classList.toggle('active', i < n);
    el.classList.toggle('tamamlandi', i < n - 1);
  });
  if (n === 2 && !document.querySelector('.alerji-kart')) loadAlerjiler();
}

function secimSec(el, alan, deger) {
  document.querySelectorAll('.secim-kart').forEach(k => k.classList.remove('secili'));
  el.classList.add('secili');
  document.getElementById(alan + '_var_mi') && (document.getElementById(alan + '_var_mi').value = deger);
  document.getElementById('tani_var_mi').value = deger;
}

async function loadAlerjiler() {
  const r = await fetch('api/profil.php?action=kategoriler');
  const d = await r.json();
  const grid = document.getElementById('alerji-listesi');
  grid.innerHTML = '';
  d.data.forEach(kat => {
    const kart = document.createElement('div');
    kart.className = 'alerji-kart';
    kart.dataset.id = kat.id;
    kart.innerHTML = `
      <div class="alerji-kart-baslik" onclick="toggleAlerji(${kat.id}, this)">
        <span class="alerji-icon">${kat.icon}</span>
        <span class="alerji-adi">${kat.kategori_adi}</span>
        <span class="kart-check">☐</span>
      </div>
      <div class="siddet-secici gizli" id="siddet-${kat.id}">
        <span>Şiddet:</span>
        <label><input type="radio" name="siddet-${kat.id}" value="hafif"> 🟡 Hafif</label>
        <label><input type="radio" name="siddet-${kat.id}" value="orta" checked> 🟠 Orta</label>
        <label><input type="radio" name="siddet-${kat.id}" value="siddetli"> 🔴 Şiddetli</label>
      </div>`;
    grid.appendChild(kart);
  });
}

function toggleAlerji(id, el) {
  const kart = el.closest('.alerji-kart');
  const siddet = document.getElementById('siddet-' + id);
  const check = el.querySelector('.kart-check');
  secilenAlerjiler[id] = !secilenAlerjiler[id];
  kart.classList.toggle('secili', secilenAlerjiler[id]);
  siddet.classList.toggle('gizli', !secilenAlerjiler[id]);
  check.textContent = secilenAlerjiler[id] ? '✅' : '☐';
}

function renderOzet() {
  const ozet = document.getElementById('secilen-ozet');
  const kartlar = document.querySelectorAll('.alerji-kart.secili');
  ozet.innerHTML = '';
  kartlar.forEach(k => {
    const adi = k.querySelector('.alerji-adi').textContent;
    const icon = k.querySelector('.alerji-icon').textContent;
    const id = k.dataset.id;
    const siddet = document.querySelector(`input[name="siddet-${id}"]:checked`)?.value || 'orta';
    const renkler = {hafif:'#F0C040',orta:'#E67E22',siddetli:'#E74C3C'};
    ozet.innerHTML += `<div class="ozet-satir" style="border-left: 4px solid ${renkler[siddet]}">
      ${icon} <strong>${adi}</strong> — <em>${siddet.charAt(0).toUpperCase()+siddet.slice(1)}</em>
    </div>`;
  });
}

async function profilKaydet() {
  const btn = event.target;
  btn.disabled = true;
  btn.textContent = '⏳ Kaydediliyor...';

  const fd = new FormData();
  fd.append('action', 'kaydet_alerji');
  fd.append('tani_var_mi', document.getElementById('tani_var_mi').value || '0');
  fd.append('ek_notlar', document.getElementById('ek_notlar').value);
  fd.append('epipen_kullaniyor', document.getElementById('epipen').checked ? '1' : '0');

  document.querySelectorAll('input[name^="reaksiyon_"]:checked, .checkbox-grid input:checked').forEach(inp => {
    fd.append('reaksiyon_tipi[]', inp.value);
  });

  document.querySelectorAll('.alerji-kart.secili').forEach(k => {
    const id = k.dataset.id;
    fd.append('kategori_ids[]', id);
    const siddet = document.querySelector(`input[name="siddet-${id}"]:checked`)?.value || 'orta';
    fd.append('siddetler[]', siddet);
    fd.append('notlar[]', '');
  });

  const r = await fetch('api/profil.php', {method:'POST', body:fd});
  const d = await r.json();
  if (d.success) {
    window.location = 'index.php?page=panel';
  } else {
    alert(d.message);
    btn.disabled = false;
    btn.textContent = '🛡️ Profili Kaydet';
  }
}
</script>
