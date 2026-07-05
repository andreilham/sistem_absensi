# 🚀 Deployment Streamlit Cloud — Panduan Lengkap

## Prasyarat
1. ✅ GitHub repository sudah aktif: https://github.com/andreilham/sistem_absensi
2. ✅ Kode sudah di-push ke GitHub (main branch)
3. ⬜ Akun Streamlit Cloud (gratis)
4. ⬜ Database MySQL online (atau di-expose ke public)
5. ⬜ Laravel API online

---

## Step 1: Buat Akun Streamlit Cloud

1. Kunjungi: https://streamlit.io/cloud
2. Klik **"Sign up"** → Pilih **"Sign up with GitHub"**
3. Authorize Streamlit untuk akses repository GitHub Anda
4. Pilih: andreilham/sistem_absensi
5. Klik **"Continue"**

---

## Step 2: Deploy Repository

Di halaman "Deploy an app":

| Field | Isi |
|-------|-----|
| **Repository** | andreilham/sistem_absensi |
| **Branch** | main |
| **Main file path** | python-ai/app.py |
| **App URL** | (auto-generated) |

Klik **"Deploy"** → Tunggu deployment selesai (±5 menit)

---

## Step 3: Konfigurasi Database Secrets

Deployment berhasil tapi aplikasi error? **Database belum terhubung.**

### Cara Setup Secrets:

1. Di halaman aplikasi Streamlit Cloud, klik **⚙️ Settings**
2. Pilih tab **"Secrets"**
3. Paste konfigurasi ini ke text area:

```toml
# Database Configuration
DB_HOST = "your-mysql-host.com"
DB_PORT = 3306
DB_USER = "username_database"
DB_PASSWORD = "password_database"
DB_NAME = "absensi_wajah"

# Laravel API Configuration
LARAVEL_URL = "https://your-laravel-domain.com"
API_KEY = "absensi-umb-2026"

# Model AI URLs
MODEL_URL = "https://your-laravel-domain.com/SistemAbsensi/model.json"
METADATA_URL = "https://your-laravel-domain.com/SistemAbsensi/metadata.json"

# Face Scanning
MIN_CONFIDENCE = 70
MIN_MARGIN = 15
```

4. Klik **"Save"** → Streamlit otomatis restart

---

## Step 4: Konfigurasi Database

### Opsi A: MySQL Online (Database.com, PlanetScale, dll)

Gunakan provider hosting database:
- Database.com
- PlanetScale (MySQL compatible)
- AWS RDS
- DigitalOcean Managed Database

Set `DB_HOST` ke alamat public database Anda.

### Opsi B: MySQL Lokal (XAMPP) — Jika ingin test dulu

```sql
-- Expose MySQL XAMPP ke public (pakai service seperti ngrok):
ngrok tcp 3306
```

---

## Step 5: Konfigurasi Laravel API Online

Jika Laravel masih lokal (127.0.0.1:8000), Streamlit Cloud **tidak bisa** mengaksesnya.

**Solusi:**
1. Deploy Laravel juga ke hosting (Heroku, Railway, Replit, dll)
2. Set `LARAVEL_URL` ke domain Laravel yang sudah online

Contoh:
```toml
LARAVEL_URL = "https://sistem-absensi-laravel.railway.app"
```

---

## Step 6: Test Koneksi

Buka URL aplikasi Streamlit Cloud Anda (misal: https://sistem-absensi.streamlit.app)

Cek halaman utama — apakah ada status koneksi:
- ✅ Database MySQL terhubung
- ✅ Laravel terhubung

Jika masih error, lihat **"Logs"** di Streamlit Cloud untuk debug.

---

## 🎯 Ringkas: Untuk Streamlit Cloud Jalan

| Komponen | Status | Action |
|----------|--------|--------|
| GitHub Code | ✅ Sudah | - |
| Streamlit Cloud App | ⬜ Buat | Deploy sekarang |
| Database MySQL | ⬜ Online | Gunakan provider online |
| Laravel API | ⬜ Online | Deploy Laravel juga |
| Secrets Config | ⬜ Setup | Copy ke Streamlit Cloud |

---

## 📝 Catatan Penting

1. **Jangan push `secrets.toml` ke GitHub!** 
   - File sudah ada di `.gitignore`
   - Secrets diatur langsung di Streamlit Cloud dashboard

2. **Model AI files** (metadata.json, model.json):
   - Sudah ada di `/public/SistemAbsensi/`
   - Diakses via Laravel public folder
   - **Pastikan Laravel online** agar Streamlit bisa download

3. **Database harus online/public**:
   - Streamlit Cloud tidak bisa akses MySQL lokal XAMPP
   - Gunakan hosting database managed (lebih aman)

4. **Keep API Key aman**:
   - Simpan di Secrets, jangan di code
   - Config sudah setup dengan benar ✅

---

## 🆘 Troubleshooting

| Error | Solusi |
|-------|--------|
| "Failed to connect to database" | Database belum online, setup di `secrets` |
| "Failed to load model.json" | Laravel belum online atau `MODEL_URL` salah |
| "Cannot POST /api/absensi/scan" | Laravel URL di `secrets` salah atau API error |
| App crash di Streamlit Cloud | Cek **"Logs"** tab untuk error detail |

---

## 🚀 Link Penting

- Streamlit Cloud: https://share.streamlit.io
- GitHub Repo: https://github.com/andreilham/sistem_absensi
- Dokumentasi Secrets: https://docs.streamlit.io/deploy/streamlit-cloud/deploy-your-app#secrets-management

