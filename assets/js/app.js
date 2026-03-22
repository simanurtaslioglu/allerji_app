// assets/js/app.js

async function girisYap() {
  const email = document.getElementById('giris-email').value.trim();
  const sifre = document.getElementById('giris-sifre').value;
  const mesaj = document.getElementById('giris-mesaj');

  if (!email || !sifre) {
    showMesaj(mesaj, 'E-posta ve şifre giriniz.', 'hata');
    return;
  }

  const fd = new FormData();
  fd.append('action', 'giris');
  fd.append('email', email);
  fd.append('sifre', sifre);

  try {
    const r = await fetch('api/auth.php', { method: 'POST', body: fd });
    const d = await r.json();
    if (d.success) {
      window.location = d.redirect;
    } else {
      showMesaj(mesaj, d.message, 'hata');
    }
  } catch (e) {
    showMesaj(mesaj, 'Bağlantı hatası.', 'hata');
  }
}

async function kayitOl() {
  const ad    = document.getElementById('kayit-ad').value.trim();
  const soyad = document.getElementById('kayit-soyad').value.trim();
  const email = document.getElementById('kayit-email').value.trim();
  const sifre = document.getElementById('kayit-sifre').value;
  const dogum = document.getElementById('kayit-dogum').value;
  const mesaj = document.getElementById('kayit-mesaj');

  if (!ad || !soyad || !email || !sifre) {
    showMesaj(mesaj, 'Lütfen zorunlu alanları doldurunuz.', 'hata');
    return;
  }

  const fd = new FormData();
  fd.append('action', 'kayit');
  fd.append('ad', ad);
  fd.append('soyad', soyad);
  fd.append('email', email);
  fd.append('sifre', sifre);
  fd.append('dogum_tarihi', dogum);

  try {
    const r = await fetch('api/auth.php', { method: 'POST', body: fd });
    const d = await r.json();
    if (d.success) {
      window.location = d.redirect;
    } else {
      showMesaj(mesaj, d.message, 'hata');
    }
  } catch (e) {
    showMesaj(mesaj, 'Bağlantı hatası.', 'hata');
  }
}

async function cikisYap() {
  const fd = new FormData();
  fd.append('action', 'cikis');
  const r = await fetch('api/auth.php', { method: 'POST', body: fd });
  const d = await r.json();
  if (d.success) window.location = d.redirect;
}

function showMesaj(el, text, tip) {
  el.textContent = text;
  el.className = 'mesaj ' + tip;
  el.classList.remove('gizli');
  setTimeout(() => el.classList.add('gizli'), 5000);
}

// Enter key support
document.addEventListener('keydown', e => {
  if (e.key === 'Enter') {
    const btn = document.querySelector('.btn-primary:not([disabled])');
    if (btn && btn.onclick) btn.click();
  }
});
