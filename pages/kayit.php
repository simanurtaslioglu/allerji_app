<!-- pages/kayit.php -->
<div class="auth-wrap">
  <div class="auth-hero">
    <div class="auth-hero-inner">
      <div class="hero-shield">🛡️</div>
      <h1 class="hero-title">Alerji<span>Bekçisi</span></h1>
      <p class="hero-sub">3 adımda alerji profilinizi oluşturun</p>
      <div class="steps-preview">
        <div class="step-item active"><span>1</span> Hesap</div>
        <div class="step-arrow">→</div>
        <div class="step-item"><span>2</span> Alerji Profili</div>
        <div class="step-arrow">→</div>
        <div class="step-item"><span>3</span> Tara!</div>
      </div>
    </div>
  </div>

  <div class="auth-form-wrap">
    <div class="auth-card">
      <h2>Hesap Oluştur</h2>
      <p class="auth-sub">Tüm alanları doldurunuz</p>

      <div id="kayit-mesaj" class="mesaj gizli"></div>

      <div class="form-row">
        <div class="form-group">
          <label>Ad</label>
          <input type="text" id="kayit-ad" placeholder="Adınız">
        </div>
        <div class="form-group">
          <label>Soyad</label>
          <input type="text" id="kayit-soyad" placeholder="Soyadınız">
        </div>
      </div>
      <div class="form-group">
        <label>E-posta</label>
        <input type="email" id="kayit-email" placeholder="ornek@mail.com">
      </div>
      <div class="form-group">
        <label>Şifre</label>
        <input type="password" id="kayit-sifre" placeholder="En az 6 karakter">
      </div>
      <div class="form-group">
        <label>Doğum Tarihi <small>(isteğe bağlı)</small></label>
        <input type="date" id="kayit-dogum">
      </div>

      <button class="btn-primary btn-full" onclick="kayitOl()">Kayıt Ol →</button>
      <div class="auth-divider"><span>zaten hesabınız var mı?</span></div>
      <a href="?page=giris" class="btn-outline btn-full">Giriş Yap</a>
    </div>
  </div>
</div>
