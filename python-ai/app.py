"""
Sistem Absensi Wajah - Sistem Cerdas (Python + Streamlit)
Universitas Maju Bersama

Jalankan dengan: streamlit run app.py
"""

import streamlit as st

st.set_page_config(
    page_title="Sistem Cerdas Absensi",
    page_icon="🎓",
    layout="wide",
)

st.title("🎓 Sistem Cerdas Absensi Wajah")
st.markdown("""
Selamat datang di **Sistem Cerdas** untuk absensi mahasiswa.

Aplikasi ini dibangun dengan **Python + Streamlit** dan terintegrasi dengan
**Laravel** sebagai sistem utama manajemen data.

### 📌 Menu yang Tersedia

Gunakan sidebar di kiri untuk membuka halaman:

| Menu | Fungsi |
|------|--------|
| **Scan Wajah** | Pengenalan wajah dengan AI (Teachable Machine) |
| **Analisis Kehadiran** | Grafik dan statistik kehadiran mahasiswa |
| **Prediksi Risiko** | Prediksi mahasiswa berisiko tidak hadir |

### 🏗️ Arsitektur Sistem

```
┌─────────────────┐     API      ┌─────────────────┐
│  Python/Streamlit│ ──────────► │     Laravel      │
│  (Sistem Cerdas) │             │  (Sistem Utama)  │
└─────────────────┘             └─────────────────┘
        │                                  │
        └────────── MySQL Database ────────┘
```

### ⚙️ Cara Menjalankan

```bash
cd python-ai
pip install -r requirements.txt
copy .env.example .env
streamlit run app.py
```

> **Catatan:** Pastikan Laravel sudah berjalan di `http://127.0.0.1:8000`
> dan database MySQL sudah aktif.
""")

st.info("👈 Pilih menu di sidebar untuk memulai.")

# Tampilkan info jadwal aktif jika database tersedia
try:
    from utils.database import get_jadwal_aktif

    jadwal = get_jadwal_aktif()
    if jadwal:
        st.success(
            f"📚 Jadwal aktif: **{jadwal['mata_kuliah']}** "
            f"({jadwal['kelas']}) · "
            f"{str(jadwal['jam_mulai'])[:5]} - {str(jadwal['jam_selesai'])[:5]}"
        )
    else:
        st.warning("Tidak ada jadwal kuliah aktif saat ini.")
except Exception as e:
    st.error(f"Tidak dapat terhubung ke database: {e}")
