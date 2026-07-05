# 🚀 RAILWAY DEPLOYMENT - STEP BY STEP GUIDE

## 📌 PERSIAPAN

Pastikan:
- ✅ Code sudah di GitHub: https://github.com/andreilham/sistem_absensi
- ✅ File `Procfile` sudah ada di root (untuk Laravel)
- ✅ File `.streamlit/config.toml` sudah update (untuk Streamlit)

---

## STEP 1️⃣: BUAT AKUN RAILWAY

1. Kunjungi: https://railway.app
2. Klik **"Start Now"**
3. Login dengan GitHub
4. Authorize Railway untuk akses repo Anda

---

## STEP 2️⃣: DEPLOY LARAVEL SERVICE

### 2.1 Buat Service Baru

1. Di Railway dashboard, klik **"New Project"**
2. Pilih **"Deploy from GitHub repo"**
3. Cari & pilih: `andreilham/sistem_absensi`
4. Railway akan auto-detect ini PHP project ✅

### 2.2 Configure Deployment

Railway akan auto-provision:
- PHP 8.1+
- Composer
- MySQL driver

### 2.3 Set Environment Variables

Klik service → **"Variables"** tab → Tambahkan:

```
APP_ENV = production
APP_DEBUG = false
APP_KEY = base64:ZjyF5urMWUIBAJAeduAXCBiyUHtmbaBFQdQt5DpmbUc=
APP_URL = https://your-domain.com
STREAMLIT_URL = https://your-domain.com/streamlit
```

(Jangan lupa update database variables di Step 4)

### 2.4 Deploy

Klik **"Deploy"** → Tunggu hingga hijau ✅ (±5 menit)

**Railway akan assign URL:**
- `https://your-laravel-app-xxxx.railway.app`

Catat URL ini untuk nanti!

---

## STEP 3️⃣: DEPLOY STREAMLIT SERVICE

### 3.1 Buat Service Baru

1. Di project yang sama, klik **"+ New Service"**
2. Pilih **"GitHub Repo"**
3. Pilih repo: `andreilham/sistem_absensi` (sama)
4. Railway detect Python project dari `requirements.txt` ✅

### 3.2 Configure Deployment

Railway akan auto-provision Python environment

### 3.3 Set Environment Variables

Klik service → **"Variables"** → Tambahkan:

```
LARAVEL_URL = https://your-laravel-app-xxxx.railway.app
API_KEY = absensi-umb-2026
MODEL_URL = https://your-laravel-app-xxxx.railway.app/SistemAbsensi/model.json
METADATA_URL = https://your-laravel-app-xxxx.railway.app/SistemAbsensi/metadata.json
MIN_CONFIDENCE = 70
MIN_MARGIN = 15
```

(Database variables di Step 4)

### 3.4 Deploy

Klik **"Deploy"** → Tunggu hingga hijau ✅ (±5-10 menit)

**Railway akan assign URL:**
- `https://your-streamlit-app-xxxx.railway.app`

Catat URL ini juga!

---

## STEP 4️⃣: SETUP DATABASE (PlanetScale)

### 4.1 Daftar PlanetScale

1. Kunjungi: https://planetscale.com
2. Klik **"Sign up"** → Login dengan GitHub
3. Authorize PlanetScale

### 4.2 Buat Database

1. Klik **"Create new database"**
2. Nama: `absensi_wajah`
3. Region: Pilih yang terdekat (Asia)
4. Klik **"Create database"** ✅

### 4.3 Get Connection String

1. Database dibuat → Klik nama database
2. Tab **"Branches"** → Klik **"main"**
3. Klik **"Connect"** → Pilih **"MySQL"**
4. Copy connection string (mirip: `mysql://user:password@host/absensi_wajah`)

### 4.4 Setup Database Credentials

Dari connection string, extract:
- **DB_HOST**: `xxxx.connect.psdb.cloud`
- **DB_USERNAME**: `user_name`
- **DB_PASSWORD**: `password_123`
- **DB_DATABASE**: `absensi_wajah`
- **DB_PORT**: `3306`

### 4.5 Add Variables ke Railway

**Laravel Service:**
```
DB_HOST = xxxx.connect.psdb.cloud
DB_PORT = 3306
DB_DATABASE = absensi_wajah
DB_USERNAME = xxxxx
DB_PASSWORD = xxxxx
```

**Streamlit Service:**
```
DB_HOST = xxxx.connect.psdb.cloud
DB_PORT = 3306
DB_DATABASE = absensi_wajah
DB_USERNAME = xxxxx
DB_PASSWORD = xxxxx
```

### 4.6 Run Migrations

Laravel harus jalankan migration pertama kali.

Di Railway, Laravel service akan auto-run Procfile:
```
php artisan migrate --force
```

Ini otomatis buat tables di PlanetScale ✅

---

## STEP 5️⃣: SETUP REVERSE PROXY (OPTIONAL - Untuk Custom Domain)

### Opsi A: Railway Subdomain (FREE - Paling Mudah)

Gunakan Railway subdomain langsung:
- Laravel: `https://your-laravel-app-xxxx.railway.app`
- Streamlit: `https://your-streamlit-app-xxxx.railway.app`

✅ **Ini sudah bisa dipakai sekarang!**

### Opsi B: Custom Domain + Cloudflare (Recommended)

#### 5.1 Setup Cloudflare

1. Kunjungi: https://dash.cloudflare.com
2. Add domain Anda
3. Update nameserver di registrar domain
4. Tunggu propagasi DNS (±24 jam)

#### 5.2 Buat Cloudflare Worker

1. Masuk Cloudflare → **"Workers"**
2. Klik **"Create a Service"**
3. Nama: `sistem-absensi-proxy`
4. Copy code dari: `cloudflare-worker.js`
5. Update LARAVEL_BACKEND dan STREAMLIT_BACKEND ke Railway URLs:
   ```javascript
   const LARAVEL_BACKEND = 'https://your-laravel-app-xxxx.railway.app';
   const STREAMLIT_BACKEND = 'https://your-streamlit-app-xxxx.railway.app';
   ```
6. Klik **"Save and Deploy"** ✅

#### 5.3 Route Domain ke Worker

1. Tab **"Triggers"** → **"Routes"**
2. Add route: `your-domain.com/*`
3. Service: `sistem-absensi-proxy`
4. Klik **"Save"** ✅

Sekarang akses:
- `https://your-domain.com` → Laravel
- `https://your-domain.com/streamlit` → Streamlit
- `https://your-domain.com/scan` → Streamlit Scan Page

---

## ✅ TESTING CHECKLIST

Setelah semua deploy:

1. **Buka Laravel**
   - URL: `https://your-laravel-app-xxxx.railway.app`
   - Harusnya: Admin panel muncul
   - Status: ✅ Database terhubung, ✅ Model files ada

2. **Buka Streamlit**
   - URL: `https://your-streamlit-app-xxxx.railway.app`
   - Harusnya: Halaman Streamlit muncul dengan menu
   - Status: ✅ Database terhubung, ✅ Laravel terhubung

3. **Test Scan Wajah**
   - Buka: `/scan` page di Streamlit
   - Posisikan wajah di kamera
   - Klik scan → harusnya berhasil absen

4. **Lihat di Admin Laravel**
   - Buka Laravel `/admin/absensi`
   - Harusnya: Data absensi dari Streamlit muncul ✅

---

## 🆘 TROUBLESHOOTING

| Error | Penyebab | Solusi |
|-------|----------|--------|
| "502 Bad Gateway" | Service sedang loading | Tunggu 2-3 menit, refresh |
| "Can't connect to MySQL" | Database credentials salah | Cek PlanetScale connection string |
| "Model not found" | Model path salah | Check `MODEL_URL` di Streamlit env |
| "CORS error di browser" | Streamlit config enableCORS false | Update config.toml enableCORS = true |
| "Database migration error" | Procfile belum jalankan | Railway auto-run, tunggu deploy selesai |

---

## 📊 BIAYA

| Service | Free Tier | Catatan |
|---------|-----------|---------|
| Railway | $5 credit/bulan | Cukup untuk testing |
| PlanetScale | 5GB storage | Free tier bagus untuk development |
| Cloudflare | Free | Unlimited Workers |
| **Total** | ~Gratis | Budget-friendly |

---

## 📝 CHEAT SHEET

**Laravel Variables:**
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_HOST=xxxx.psdb.cloud
DB_DATABASE=absensi_wajah
DB_USERNAME=xxxxx
DB_PASSWORD=xxxxx
STREAMLIT_URL=https://your-domain.com/streamlit
```

**Streamlit Variables:**
```
LARAVEL_URL=https://your-laravel-app-xxxx.railway.app
DB_HOST=xxxx.psdb.cloud
DB_DATABASE=absensi_wajah
DB_USERNAME=xxxxx
DB_PASSWORD=xxxxx
MODEL_URL=https://your-laravel-app-xxxx.railway.app/SistemAbsensi/model.json
METADATA_URL=https://your-laravel-app-xxxx.railway.app/SistemAbsensi/metadata.json
```

---

## 🎉 SELESAI!

Sekarang Anda punya:
- ✅ Laravel online di Railway
- ✅ Streamlit online di Railway Cloud
- ✅ Database MySQL online di PlanetScale
- ✅ Unified website dengan Cloudflare routing (optional)

**Estimasi waktu setup: 1-2 jam**

---

## 🔗 LINK PENTING

- Railway: https://railway.app
- PlanetScale: https://planetscale.com
- Cloudflare: https://dash.cloudflare.com
- GitHub Repo: https://github.com/andreilham/sistem_absensi

---

**Ada pertanyaan? Cek `DEPLOYMENT_CLOUD_UNIFIED.md` untuk dokumentasi lengkap.**
