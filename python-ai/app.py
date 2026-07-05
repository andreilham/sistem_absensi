"""
Sistem Absensi Wajah - Sistem Cerdas (Python + Streamlit)
Universitas Maju Bersama

Jalankan: python -m streamlit run app.py
"""

import streamlit as st

from utils.ui import render_header

st.set_page_config(
    page_title="Sistem Cerdas Absensi",
    page_icon="🎓",
    layout="wide",
    initial_sidebar_state="expanded",
)

render_header()

st.markdown("### Selamat Datang di Sistem Cerdas")
st.markdown("""
Modul **Python + Streamlit** untuk fitur AI absensi mahasiswa.
Terintegrasi dengan **Laravel** sebagai sistem utama, dengan model Teachable Machine yang sama.

**Pilih menu di sidebar kiri:**
- **Scan Wajah** — pengenalan wajah AI (Teachable Machine)
- **Analisis Kehadiran** — grafik statistik kehadiran
- **Prediksi Risiko** — deteksi mahasiswa berisiko tidak hadir
""")

col1, col2 = st.columns(2)
with col1:
    st.info("🌐 **Laravel** (port 8000)\n\nAdmin panel, CRUD data, database, dan halaman absensi")
with col2:
    st.info("🤖 **Streamlit** (port 8501)\n\nAI scan wajah, analisis, dan prediksi risiko")

# Cek koneksi database & Laravel
st.divider()
st.subheader("Status Koneksi")

col_a, col_b = st.columns(2)

with col_a:
    try:
        from utils.database import get_jadwal_aktif
        jadwal = get_jadwal_aktif()
        st.success("✅ Database MySQL terhubung")
        if jadwal:
            st.caption(f"Jadwal aktif: {jadwal['mata_kuliah']}")
    except Exception as e:
        st.error(f"❌ Database: {e}")
        st.caption("Pastikan MySQL XAMPP aktif & file .env benar")

with col_b:
    try:
        import requests
        from utils.config import LARAVEL_URL
        r = requests.get(LARAVEL_URL, timeout=5)
        if r.status_code == 200:
            st.success(f"✅ Laravel terhubung ({LARAVEL_URL})")
        else:
            st.warning(f"⚠️ Laravel status {r.status_code}")
    except Exception as e:
        st.error(f"❌ Laravel: {e}")
        st.caption("Jalankan: php artisan serve di folder app-temp")
