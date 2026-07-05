# 🚀 RENDER.COM DEPLOYMENT - STEP BY STEP GUIDE

## 📌 KENAPA RENDER?

✅ **Spending Limit JELAS** → Set max $7, tidak bisa lebih  
✅ **Auto-Pause Protection** → Aplikasi pause, bukan di-charge  
✅ **$7/bulan free credit** (lebih banyak dari Railway)  
✅ **Simple UI** → Mudah dimengerti beginner  
✅ **Support Laravel + Python** → Bisa deploy keduanya  

---

## PERSIAPAN

Pastikan:
- ✅ Code sudah di GitHub: https://github.com/andreilham/sistem_absensi
- ✅ File `Procfile` sudah ada (kami sudah buat untuk Laravel)

---

## STEP 1️⃣: DAFTAR RENDER

1. Kunjungi: https://render.com
2. Klik **"Get Started"** atau **"Sign Up"**
3. Pilih **"Sign up with GitHub"**
4. Authorize Render untuk akses GitHub Anda
5. Selesai! ✅

---

## STEP 2️⃣: DEPLOY LARAVEL SERVICE

### 2.1 Buat Service Baru

1. Di Render dashboard, klik **"+ New +"**
2. Pilih **"Web Service"**
3. Pilih **"Build and deploy from a Git repository"**
4. Authorize jika diminta

### 2.2 Connect Repository

1. Cari repo: `andreilham/sistem_absensi`
2. Klik **"Connect"** ✅
3. Render akan scan dan auto-detect PHP project

### 2.3 Configure Service

Di form yang muncul, isi:

| Field | Value |
|-------|-------|
| Name | `sistem-absensi-laravel` |
| Runtime | `PHP` (auto-selected) |
| Build Command | `composer install` |
| Start Command | `cd app-temp && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT` |
| Branch | `main` |

### 2.4 Environment Variables

Scroll ke bagian **"Environment"** → Tambahkan:

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:ZjyF5urMWUIBAJAeduAXCBiyUHtmbaBFQdQt5DpmbUc=
APP_URL=https://your-render-app.onrender.com
STREAMLIT_URL=https://your-render-streamlit.onrender.com
```

(Database variables ditambah di Step 4)

### 2.5 Plan

- Pilih **"Free"** (tapi ini akan auto-sleep, scroll ke "Paid Plans")
- Pilih **"Starter" $7/month** ← INI YANG AMAN (tidak auto-sleep, punya spending limit)
- Render akan charge $7/bulan tapi Anda sudah dapat $7 free credit

### 2.6 Deploy

Klik **"Create Web Service"** → Tunggu deployment (±5-10 menit)

**Render akan assign URL:**
- `https://your-app-name-xxxx.onrender.com`

Catat URL ini! ✅

---

## STEP 3️⃣: DEPLOY STREAMLIT SERVICE

### 3.1 Buat Service Baru

1. Klik **"+ New +"** → **"Web Service"**
2. Pilih repo: `andreilham/sistem_absensi`
3. Klik **"Connect"**

### 3.2 Configure Streamlit Service

| Field | Value |
|-------|-------|
| Name | `sistem-absensi-streamlit` |
| Runtime | `Python 3` |
| Build Command | `pip install -r python-ai/requirements.txt` |
| Start Command | `cd python-ai && streamlit run app.py --server.port=$PORT --server.address=0.0.0.0` |
| Branch | `main` |

### 3.3 Environment Variables

```
LARAVEL_URL=https://your-laravel-app-xxxx.onrender.com
API_KEY=absensi-umb-2026
MODEL_URL=https://your-laravel-app-xxxx.onrender.com/SistemAbsensi/model.json
METADATA_URL=https://your-laravel-app-xxxx.onrender.com/SistemAbsensi/metadata.json
MIN_CONFIDENCE=70
MIN_MARGIN=15
```

(Database variables ditambah di Step 4)

### 3.4 Plan

- Pilih **"Starter" $7/month** (same as Laravel)

### 3.5 Deploy

Klik **"Create Web Service"** → Tunggu deployment

**Render akan assign URL:**
- `https://your-streamlit-app-xxxx.onrender.com`

---

## STEP 4️⃣: SETUP DATABASE (PlanetScale)

### 4.1 Daftar PlanetScale

1. Kunjungi: https://planetscale.com
2. Klik **"Sign Up"** → Login dengan GitHub
3. Authorize

### 4.2 Buat Database

1. Klik **"Create Database"**
2. Nama: `absensi_wajah`
3. Region: **Asia** (atau terdekat)
4. Klik **"Create"** ✅

### 4.3 Get Connection Details

1. Database dibuat → Klik nama database
2. Tab **"Branches"** → Klik **"main"**
3. Klik **"Connect"** → Pilih **"MySQL"**
4. Copy credentials

Extract dari connection string:
- **Host**: `xxxx.connect.psdb.cloud`
- **Username**: `xxxxx`
- **Password**: `xxxxx`
- **Database**: `absensi_wajah`
- **Port**: `3306`

### 4.4 Add Database Variables ke Render

**Di Laravel service**, klik **"Environment"** → Tambahkan:

```
DB_HOST=xxxx.connect.psdb.cloud
DB_PORT=3306
DB_DATABASE=absensi_wajah
DB_USERNAME=xxxxx
DB_PASSWORD=xxxxx
```

**Di Streamlit service**, tambahkan juga:

```
DB_HOST=xxxx.connect.psdb.cloud
DB_PORT=3306
DB_DATABASE=absensi_wajah
DB_USERNAME=xxxxx
DB_PASSWORD=xxxxx
```

### 4.5 Klik "Save" di kedua service

Render akan auto-redeploy dengan environment variables baru ✅

---

## STEP 5️⃣: SET SPENDING LIMIT (PALING AMAN!)

### 5.1 Buka Account Settings

1. Render dashboard → Klik profile (kanan atas)
2. Pilih **"Account"** atau **"Settings"**

### 5.2 Cari Billing

1. Pilih tab **"Billing"**
2. Cari **"Account Balance"** atau **"Payment Method"**

### 5.3 Set Spending Cap

Di section **"Spending Cap"**:
- Input: **$7** (limit amanmu)
- Klik **"Update"** ✅

**Apa yang terjadi:**
- Jika pakai $7 → Normal, gratis
- Jika melebihi $7 → Aplikasi auto-pause (tidak bisa kena charge lebih)

---

## ✅ TESTING CHECKLIST

Setelah semua deploy:

### 1. Test Laravel

```
Buka: https://your-laravel-app-xxxx.onrender.com/admin
Expected: Admin panel muncul
Check: ✅ Database terhubung
       ✅ Model files tersedia
```

### 2. Test Streamlit

```
Buka: https://your-streamlit-app-xxxx.onrender.com
Expected: Halaman Streamlit muncul
Check: ✅ Database terhubung
       ✅ Laravel API terhubung
       ✅ Model loaded
```

### 3. Test Scan Wajah

```
Buka Streamlit → Scan Wajah page
Expected: Kamera loading
Action: Posisikan wajah → Scan
Result: ✅ Absensi berhasil di Laravel
```

### 4. Verifikasi di Admin

```
Buka Laravel /admin/absensi
Expected: Data absensi dari Streamlit muncul ✅
```

---

## 🆘 TROUBLESHOOTING

| Error | Penyebab | Solusi |
|-------|----------|--------|
| "Build failed" | Dependency error | Cek `requirements.txt` atau `composer.json` |
| "502 Bad Gateway" | Service loading | Tunggu 2-3 menit, refresh browser |
| "Can't connect to MySQL" | Database credentials salah | Verifikasi PlanetScale connection string |
| "Model not found" | URL salah | Check `MODEL_URL` di Streamlit env |
| "Service stopped" | Spending limit tercapai | Upgrade plan atau tunggu bulan depan |
| "SSL certificate error" | Deploy belum selesai | Tunggu 5-10 menit |

---

## 📊 BIAYA BREAKDOWN

| Service | Plan | Harga/bulan | Credit |
|---------|------|------------|---------|
| Laravel | Starter | $7 | -$7 |
| Streamlit | Starter | $7 | -$7 |
| PlanetScale | Free | $0 | $0 |
| **Total** | | **$14** | **$14 free** |
| **Anda bayar** | | | **$0** ✅ |

---

## 🛡️ KEAMANAN

✅ **Spending Limit Set** → Maksimal $7, auto-pause  
✅ **Auto-redeploy** → Push ke GitHub, otomatis deploy  
✅ **HTTPS built-in** → SSL gratis di Render  
✅ **Database aman** → PlanetScale + SSL  
✅ **Environment variables aman** → Tidak expose di kode  

---

## 📝 CHEAT SHEET

**Laravel Environment:**
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-laravel-app-xxxx.onrender.com
DB_HOST=xxxx.connect.psdb.cloud
DB_DATABASE=absensi_wajah
DB_USERNAME=xxxxx
DB_PASSWORD=xxxxx
STREAMLIT_URL=https://your-streamlit-app-xxxx.onrender.com
```

**Streamlit Environment:**
```
LARAVEL_URL=https://your-laravel-app-xxxx.onrender.com
DB_HOST=xxxx.connect.psdb.cloud
DB_DATABASE=absensi_wajah
DB_USERNAME=xxxxx
DB_PASSWORD=xxxxx
MODEL_URL=https://your-laravel-app-xxxx.onrender.com/SistemAbsensi/model.json
METADATA_URL=https://your-laravel-app-xxxx.onrender.com/SistemAbsensi/metadata.json
```

---

## 🎉 SELESAI!

Anda sekarang punya:
- ✅ Laravel online di Render
- ✅ Streamlit online di Render
- ✅ Database online di PlanetScale
- ✅ Spending limit $7 (AMAN! 🛡️)
- ✅ Auto-pause jika melebihi limit

**Estimasi waktu: 1-2 jam**

---

## 🔗 LINK PENTING

- Render: https://render.com
- PlanetScale: https://planetscale.com
- GitHub Repo: https://github.com/andreilham/sistem_absensi

---

## 💡 BONUS: Custom Domain (Optional)

Jika ingin domain sendiri (https://your-domain.com):

1. Beli domain di Namecheap/GoDaddy
2. Di Render service → **"Settings"** → **"Custom Domain"**
3. Ikuti instruksi Render untuk DNS setup
4. Tunggu DNS propagate (±24 jam)

---

**Ready? Mari mulai dari STEP 1! 🚀**
