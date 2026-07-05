# Sistem Cerdas — Python + Streamlit

Modul AI untuk Sistem Absensi Wajah UMB.  
Kode disusun sederhana agar mudah dipahami mahasiswa.

## Instalasi

```bash
# 1. Masuk ke folder
cd python-ai

# 2. Install dependensi
pip install -r requirements.txt

# 3. Buat file konfigurasi
copy .env.example .env   # Windows
# cp .env.example .env   # Linux/Mac

# 4. Sesuaikan .env (database & URL Laravel)

# 5. Jalankan
python -m streamlit run app.py
```

Buka browser: http://127.0.0.1:8501

## Struktur File

```
python-ai/
├── app.py                          # Halaman beranda
├── pages/
│   ├── 1_📷_Scan_Wajah.py          # Scan wajah + absensi
│   ├── 2_📊_Analisis_Kehadiran.py  # Grafik kehadiran
│   └── 3_🎯_Prediksi_Risiko.py     # Prediksi ML
└── utils/
    ├── config.py       # Baca setting dari .env
    ├── database.py     # Koneksi MySQL
    ├── face_model.py   # Model AI Teachable Machine
    ├── predictor.py    # Hitung risiko kehadiran
    └── api_client.py   # Kirim absensi ke Laravel
```

## Alur Scan Wajah

1. Mahasiswa ambil foto via `st.camera_input`
2. Python jalankan prediksi (`face_model.py`)
3. Jika akurasi cukup, kirim ke Laravel API
4. Laravel simpan ke database MySQL

## Deploy ke Streamlit Cloud

1. Push repo ke GitHub
2. Buka https://share.streamlit.io
3. Connect repo, set main file: `python-ai/app.py`
4. Tambahkan secrets (DB_HOST, DB_NAME, LARAVEL_URL, API_KEY, dll)

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Gagal koneksi database | Pastikan MySQL XAMPP aktif & `.env` benar |
| Gagal kirim absensi | Pastikan Laravel jalan di port 8000 |
| Model AI error | Cek path `MODEL_URL` dan `METADATA_URL` mengarah ke `SistemAbsensi` di folder publik Laravel |
