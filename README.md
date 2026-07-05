# Sistem Absensi Wajah - Universitas Maju Bersama

Proyek absensi mahasiswa dengan arsitektur **dual stack**:

| Komponen | Teknologi | Fungsi |
|----------|-----------|--------|
| **Sistem Utama** | Laravel 9 (PHP) | CRUD data, admin panel, database, API |
| **Sistem Cerdas** | Python + Streamlit | AI scan wajah, analisis, prediksi risiko |

## Struktur Proyek

```
sistem absensi/
├── app-temp/              # Laravel — sistem utama
│   ├── app/               # Controller, Model
│   ├── database/          # Migration & Seeder
│   ├── public/models/     # Model AI (Teachable Machine)
│   └── routes/            # Web + API routes
├── python-ai/             # Python — sistem cerdas (Streamlit)
│   ├── app.py             # Halaman utama
│   ├── pages/             # Scan Wajah, Analisis, Prediksi
│   ├── utils/             # Database, AI model, predictor
│   └── requirements.txt
├── model.json             # Model AI (backup)
├── metadata.json
├── weights.bin
└── README.md
```

## Persyaratan

### Laravel
- PHP 8.0+ (XAMPP)
- Composer
- MySQL (database: `absensi_wajah`)

### Python (Sistem Cerdas)
- Python 3.9+
- pip

## Cara Menjalankan

### 1. Laravel (Sistem Utama)

```powershell
cd "C:\xampp\htdocs\sistem absensi\app-temp"
C:\xampp\php\php.exe artisan serve
```

Buka: http://127.0.0.1:8000

### 2. Python/Streamlit (Sistem Cerdas)

```powershell
cd "C:\xampp\htdocs\sistem absensi\python-ai"
pip install -r requirements.txt
copy .env.example .env
streamlit run app.py
```

Buka: http://127.0.0.1:8501

> **Penting:** Jalankan keduanya bersamaan. Streamlit membaca/menulis data via API Laravel.

## Login Admin

| Email | Password |
|-------|----------|
| admin@umb.ac.id | password |

URL: http://127.0.0.1:8000/login

## Fitur

### Laravel (Sistem Utama)
- Dashboard statistik kehadiran
- CRUD Mahasiswa, Kelas, Dosen, Mata Kuliah, Jadwal
- Registrasi wajah mahasiswa
- Riwayat absensi & laporan per periode
- API endpoint untuk Python

### Python/Streamlit (Sistem Cerdas)
- **Scan Wajah AI** — pengenalan wajah Teachable Machine
- **Analisis Kehadiran** — grafik interaktif (Plotly)
- **Prediksi Risiko** — deteksi mahasiswa berisiko tidak hadir (scikit-learn)

## Arsitektur Integrasi

```
Mahasiswa scan wajah di Streamlit
        │
        ▼
Python prediksi wajah (TensorFlow)
        │
        ▼ POST /api/absensi/scan
Laravel validasi & simpan ke MySQL
        │
        ▼
Admin lihat data di panel Laravel
```

## Konfigurasi

### Laravel `.env`
```
STREAMLIT_URL=http://127.0.0.1:8501
PYTHON_API_KEY=absensi-umb-2026
```

### Python `python-ai/.env`
```
DB_HOST=127.0.0.1
DB_NAME=absensi_wajah
LARAVEL_URL=http://127.0.0.1:8000
API_KEY=absensi-umb-2026
```

## Model AI

Model Teachable Machine dengan 9 label wajah. Field `face_label` di database harus **sama persis** dengan label model.

## Catatan untuk Mahasiswa

Kode Python sengaja dibuat **sederhana dan berkomentar** agar mudah dipelajari:
- `utils/database.py` — query MySQL dengan pymysql
- `utils/face_model.py` — load model & prediksi wajah
- `utils/predictor.py` — logika prediksi risiko sederhana
- `pages/` — setiap fitur AI punya file sendiri (multi-page Streamlit)
