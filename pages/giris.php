<!-- pages/giris.php -->
<div class="auth-wrap">
  <div class="auth-hero">
    <div class="auth-hero-inner">
      <div class="hero-shield">🛡️</div>
      <h1 class="hero-title">Alerji<span>Bekçisi</span></h1>
      <p class="hero-sub">Ürün içeriklerini tara, sağlığını koru</p>
      <div class="hero-badges">
        <span class="badge red">🔴 Tehlikeli</span>
        <span class="badge yellow">🟡 Dikkatli</span>
        <span class="badge green">🟢 Güvenli</span>
      </div>
    </div>
  </div>

  <div class="auth-form-wrap">
    <div class="auth-card">
      <h2>Hoş Geldiniz</h2>
      <p class="auth-sub">Hesabınıza giriş yapın</p>

      <div id="giris-mesaj" class="mesaj gizli"></div>

      <div class="form-group">
        <label>E-posta</label>
        <input type="email" id="giris-email" placeholder="ornek@mail.com" autocomplete="email">
      </div>
      <div class="form-group">
        <label>Şifre</label>
        <input type="password" id="giris-sifre" placeholder="••••••" autocomplete="current-password">
      </div>
      <button class="btn-primary btn-full" onclick="girisYap()">Giriş Yap</button>

      <div class="auth-divider"><span>veya</span></div>
      <a href="?page=kayit" class="btn-outline btn-full">Hesap Oluştur</a>
    </div>
  </div>
</div>
