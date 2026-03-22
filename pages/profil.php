<!-- pages/profil.php -->
<?php $user = currentUser(); ?>
<div class="profil-wrap">
  <div class="sayfa-baslik">
    <h2>👤 Profilim</h2>
  </div>

  <div class="profil-kart">
    <div class="profil-avatar-buyuk"><?= mb_substr($user['ad'],0,1,'UTF-8') ?></div>
    <div class="profil-isim"><?= htmlspecialchars($user['ad'] . ' ' . $user['soyad']) ?></div>
    <div class="profil-email"><?= htmlspecialchars($user['email']) ?></div>
    <?php if ($user['dogum_tarihi']): ?>
    <div class="profil-dogum">🎂 <?= date('d.m.Y', strtotime($user['dogum_tarihi'])) ?></div>
    <?php endif; ?>
  </div>

  <div class="panel-bolum">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <h3>🛡️ Alerji Listesi</h3>
      <a href="?page=profil-kurulum" class="btn-kucuk">Düzenle</a>
    </div>
    <div id="alerji-profil-listesi">Yükleniyor...</div>
  </div>

  <div class="panel-bolum tehlike">
    <h3>⚠️ Hesap</h3>
    <button class="btn-outline-kirmizi" onclick="if(confirm('Çıkış yapmak istiyor musunuz?')) cikisYap()">Çıkış Yap</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const r = await fetch('api/profil.php?action=kullanici_alerjileri');
  const d = await r.json();
  const el = document.getElementById('alerji-profil-listesi');
  if (!d.data || d.data.length === 0) {
    el.innerHTML = '<div class="bos-durum">Henüz alerji eklenmedi. <a href="?page=profil-kurulum">Ekle →</a></div>';
    return;
  }
  const siddetRenk = {hafif:'#F0C040',orta:'#E67E22',siddetli:'#E74C3C'};
  el.innerHTML = d.data.map(a => `
    <div class="profil-alerji-satir" style="border-left: 5px solid ${siddetRenk[a.siddet]}">
      <span class="alerji-icon">${a.icon}</span>
      <div>
        <strong>${a.kategori_adi}</strong>
        <span class="alerjen-siddet" style="color:${siddetRenk[a.siddet]}"> ● ${a.siddet.charAt(0).toUpperCase()+a.siddet.slice(1)}</span>
        ${a.notlar ? `<div class="profil-not">${a.notlar}</div>` : ''}
      </div>
    </div>`).join('');
});
</script>
