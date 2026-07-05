<<<<<<< HEAD
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

### Jalankan dari folder utama
- Laravel: `php artisan serve` dari folder `app-temp`
- Streamlit: `python -m streamlit run python-ai/app.py` dari folder utama proyek

### Cara Mudah (Windows)

Double-click file:
- **`jalankan-laravel.bat`** → http://127.0.0.1:8000
- **`jalankan-streamlit.bat`** → http://127.0.0.1:8501

> Jalankan **keduanya** bersamaan!

### Manual

**Laravel:**
```powershell
cd "C:\xampp\htdocs\sistem absensi\app-temp"
C:\xampp\php\php.exe artisan serve
```

**Streamlit:**
```powershell
cd "C:\xampp\htdocs\sistem absensi\python-ai"
pip install -r requirements.txt
python -m streamlit run app.py
```

> ⚠️ Gunakan `python -m streamlit run app.py` (bukan `streamlit run`) jika perintah streamlit tidak dikenali.

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
=======
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
>>>>>>> a79c454da8ca0d682b15920f1c0ec2b018524f36
