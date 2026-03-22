<!-- pages/panel.php -->
<?php $user = currentUser(); ?>
<div class="panel-wrap">
  <div class="panel-hosgeldin">
    <div class="hosgeldin-sol">
      <div class="hosgeldin-avatar"><?= mb_substr($user['ad'],0,1,'UTF-8') ?></div>
      <div>
        <div class="hosgeldin-isim">Merhaba, <?= htmlspecialchars($user['ad']) ?>! 👋</div>
        <div class="hosgeldin-sub">Alerji profiliniz aktif ve koruma altındasınız</div>
      </div>
    </div>
    <a href="?page=tara" class="btn-primary btn-hizli-tara">📷 Hızlı Tara</a>
  </div>

  <div id="istatistik-grid" class="istatistik-grid">
    <div class="istat-kart yukluyor">Yükleniyor...</div>
  </div>

  <div class="panel-bolum">
    <h3>🛡️ Alerji Profilim</h3>
    <div id="profil-alerjiler" class="alerji-cips">Yükleniyor...</div>
    <a href="?page=profil" class="link-kucuk">Profili düzenle →</a>
  </div>

  <div class="panel-bolum">
    <h3>📋 Son Taramalar</h3>
    <div id="son-taramalar">Yükleniyor...</div>
    <a href="?page=gecmis" class="link-kucuk">Tümünü gör →</a>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  // İstatistikler
  const ri = await fetch('api/tara.php?action=istatistik');
  const di = await ri.json();
  if (di.success) {
    const ist = di.istatistik;
    document.getElementById('istatistik-grid').innerHTML = `
      <div class="istat-kart toplam"><div class="istat-sayi">${ist.toplam_tarama}</div><div class="istat-ad">Toplam Tarama</div></div>
      <div class="istat-kart kirmizi"><div class="istat-sayi">${ist.kirmizi_uyari||0}</div><div class="istat-ad">🔴 Tehlikeli</div></div>
      <div class="istat-kart sari"><div class="istat-sayi">${ist.sari_uyari||0}</div><div class="istat-ad">🟡 Dikkatli</div></div>
      <div class="istat-kart yesil"><div class="istat-sayi">${ist.yesil_uyari||0}</div><div class="istat-ad">🟢 Güvenli</div></div>`;
  }

  // Profil
  const rp = await fetch('api/profil.php?action=profil_ozet');
  const dp = await rp.json();
  if (dp.success) {
    const el = document.getElementById('profil-alerjiler');
    if (dp.alerjiler.length === 0) {
      el.innerHTML = '<span class="hint">Henüz alerji eklenmedi</span>';
    } else {
      el.innerHTML = dp.alerjiler.map(a => {
        const renk = {hafif:'#F0C040',orta:'#E67E22',siddetli:'#E74C3C'}[a.siddet];
        return `<span class="cip" style="border-color:${renk}">${a.icon} ${a.kategori_adi}</span>`;
      }).join('');
    }
  }

  // Son taramalar
  const rg = await fetch('api/tara.php?action=gecmis&limit=5');
  const dg = await rg.json();
  const el2 = document.getElementById('son-taramalar');
  if (!dg.data || dg.data.length === 0) {
    el2.innerHTML = '<div class="bos-durum">Henüz tarama yapılmadı. <a href="?page=tara">İlk taramanı yap →</a></div>';
  } else {
    el2.innerHTML = dg.data.map(t => {
      const icons = {kirmizi:'🔴',sari:'🟡',yesil:'🟢'};
      const tarih = new Date(t.tarama_tarihi).toLocaleDateString('tr-TR');
      return `<div class="gecmis-satir" onclick="window.location='?page=gecmis'">
        <span class="gecmis-icon">${icons[t.uyari_seviyesi]}</span>
        <div class="gecmis-bilgi">
          <div class="gecmis-urun">${t.urun_adi}</div>
          <div class="gecmis-tarih">${tarih}</div>
        </div>
        <span class="gecmis-alerjen-sayisi">${(t.bulunan_alerjenler||[]).length} alerjen</span>
      </div>`;
    }).join('');
  }
});
</script>
