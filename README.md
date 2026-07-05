# 🎓 Sistem Absensi Wajah - Universitas Maju Bersama

## 📋 Gambaran Umum

**Sistem absensi mahasiswa dengan teknologi AI pengenalan wajah**, terintegrasi dengan dual-stack architecture:

| Komponen | Stack | Fungsi |
|----------|-------|--------|
| **Backend Utama** | Laravel 9 (PHP) | CRUD data, admin panel, database, API |
| **Sistem AI** | Python + Streamlit | Scan wajah real-time, analisis, prediksi risiko |
| **Model AI** | Teachable Machine | Training model wajah dari Google |

---

## 🚀 QUICK START (3 Langkah)

### Step 1: Siapkan Database

```powershell
# Pastikan MySQL XAMPP sudah berjalan
# Database: absensi_wajah
# User: root
# Password: (kosong)
```

### Step 2: Install Dependencies

```powershell
# Terminal 1 - Laravel
cd "C:\xampp\htdocs\sistem absensi\app-temp"
composer install
php artisan key:generate
php artisan migrate:fresh --seed

# Terminal 2 - Python/Streamlit
cd "C:\xampp\htdocs\sistem absensi"
pip install -r python-ai/requirements.txt
```

### Step 3: Jalankan Aplikasi

**Opsi A: Menggunakan Batch File (PALING MUDAH)**
```
Double-click: startup-local.bat
```

**Opsi B: Manual via Terminal**

Terminal 1:
```powershell
cd "C:\xampp\htdocs\sistem absensi\app-temp"
php artisan serve
# Buka: http://127.0.0.1:8000
```

Terminal 2:
```powershell
cd "C:\xampp\htdocs\sistem absensi"
python -m streamlit run python-ai/app.py
# Buka: http://localhost:8501
```

---

## 📍 Akses Aplikasi

Setelah startup selesai, buka:

| Interface | URL | Fungsi |
|-----------|-----|--------|
| 🌐 **Laravel Admin** | http://127.0.0.1:8000/admin | CRUD data, laporan, statistik |
| 📷 **Streamlit Scan** | http://localhost:8501 | AI scan wajah, analisis, prediksi |
| 🎥 **Halaman Absensi** | http://127.0.0.1:8000 | Interface scan wajah (browser) |

---

## 🔐 Login Admin

| Email | Password |
|-------|----------|
| admin@umb.ac.id | password |

---

## 📁 Struktur Folder

```
sistem absensi/
├── app-temp/                    # Laravel (Backend utama)
│   ├── app/Http/Controllers/    # Controller + API endpoints
│   ├── app/Models/              # Model database
│   ├── database/migrations/     # Schema database
│   ├── resources/views/         # Blade templates
│   └── routes/                  # Web + API routes
│
├── python-ai/                   # Streamlit (Sistem Cerdas)
│   ├── app.py                   # Main page
│   ├── pages/                   # Menu pages (scan, analisis, prediksi)
│   ├── utils/                   # Helper functions
│   └── requirements.txt         # Dependencies
│
├── public/                      # Laravel static files
│   └── SistemAbsensi/           # Model AI files (metadata.json, model.json)
│
├── SistemAbsensi/               # Model files backup
│
├── startup-local.bat            # ⭐ One-click startup
├── stop-local.bat               # Stop services
└── README.md                    # This file
```

---

## 🎯 Fitur Utama

### 🌐 Laravel Admin Panel
- ✅ Dashboard dengan statistik kehadiran
- ✅ CRUD Mahasiswa, Kelas, Dosen, Mata Kuliah, Jadwal
- ✅ Registrasi & manajemen wajah mahasiswa
- ✅ Riwayat absensi dengan filter
- ✅ Laporan kehadiran per periode
- ✅ API endpoint untuk integrasi

### 🤖 Streamlit (Sistem Cerdas)
- ✅ **Scan Wajah AI** → Pengenalan wajah real-time Teachable Machine
- ✅ **Analisis Kehadiran** → Grafik interaktif (Plotly)
- ✅ **Prediksi Risiko** → Deteksi mahasiswa berisiko tidak hadir

---

## 🔄 Alur Kerja

```
1. Mahasiswa scan wajah di Streamlit atau Halaman Absensi
        ↓
2. Python prediksi wajah menggunakan model Teachable Machine
        ↓
3. Jika akurasi memenuhi threshold → Kirim ke Laravel API
        ↓
4. Laravel simpan data ke MySQL database
        ↓
5. Admin lihat riwayat absensi di panel Laravel
        ↓
6. Streamlit tampilkan analisis & prediksi risiko
```

---

## 🛠️ Teknologi & Tools

- **Backend**: Laravel 9, PHP 8.1+, MySQL 5.7+
- **Frontend**: Tailwind CSS, Alpine.js, Blade Template
- **AI/ML**: Python 3.9+, Streamlit, Teachable Machine, TensorFlow.js
- **Database**: MySQL (XAMPP)
- **Version Control**: Git/GitHub

---

## ⚙️ Konfigurasi

### `.env` Laravel (app-temp/.env)
```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_HOST=127.0.0.1
DB_DATABASE=absensi_wajah
DB_USERNAME=root
DB_PASSWORD=
STREAMLIT_URL=http://127.0.0.1:8501
```

### `.env` Streamlit (python-ai/.env)
```
LARAVEL_URL=http://127.0.0.1:8000
DB_HOST=127.0.0.1
DB_DATABASE=absensi_wajah
DB_USERNAME=root
DB_PASSWORD=
MODEL_URL=http://127.0.0.1:8000/SistemAbsensi/model.json
METADATA_URL=http://127.0.0.1:8000/SistemAbsensi/metadata.json
MIN_CONFIDENCE=70
MIN_MARGIN=15
```

---

## 🆘 Troubleshooting

| Error | Solusi |
|-------|--------|
| "SQLSTATE Connection refused" | MySQL XAMPP belum jalan, start di XAMPP Control Panel |
| "Kamera tidak tersedia" | Izinkan akses kamera di browser settings |
| "Model not found" | Check folder `public/SistemAbsensi/` ada model.json & metadata.json |
| "Python command not found" | Pastikan Python sudah di-install & PATH sudah benar |
| "Port 8000 sudah terpakai" | Laravel akan pakai port lain otomatis (8001, 8002, dst) |

---

## 📝 Catatan Pengembangan

### Data Dummy
Database sudah include seeder untuk:
- Admin user (email: admin@umb.ac.id)
- 9 mahasiswa dengan wajah label (1-9)
- 3 kelas, 3 mata kuliah, 5 dosen
- Sample jadwal kuliah

### Training Model
Model AI (`SistemAbsensi/`) sudah pre-trained dengan 9 wajah mahasiswa.

Jika ingin update/retrain:
1. Gunakan Teachable Machine: https://teachablemachine.withgoogle.com
2. Export model ke folder `SistemAbsensi/`
3. Update metadata.json & model.json

---

## 📚 Dokumentasi Lengkap

- **Laravel Docs**: https://laravel.com/docs
- **Streamlit Docs**: https://docs.streamlit.io
- **Teachable Machine**: https://teachablemachine.withgoogle.com
- **TensorFlow.js**: https://js.tensorflow.org

---

## 🎓 Untuk Mahasiswa

Kode Python sengaja dibuat **sederhana dan berkomentar** agar mudah dipelajari:

- `python-ai/utils/database.py` → Query MySQL dengan pymysql
- `python-ai/utils/face_scanner_html.py` → Load model & prediksi wajah
- `python-ai/utils/predictor.py` → Logika prediksi risiko
- `python-ai/pages/` → Setiap fitur punya file sendiri (multi-page Streamlit)

---

## 📞 Support

Jika ada error atau pertanyaan:
1. Cek folder `storage/logs/laravel.log` untuk Laravel errors
2. Lihat Streamlit console output di terminal
3. Verifikasi `.env` files sudah benar

---

## ✨ Dibuat untuk

**Universitas Maju Bersama** - Sistem Absensi Wajah  
Semester: Genap 2026  
Status: Development

---

**Happy Coding! 🚀**
