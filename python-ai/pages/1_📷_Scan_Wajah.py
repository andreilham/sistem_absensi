"""
Halaman Scan Wajah — AI Teachable Machine (via browser)
Tampilan sama dengan halaman Laravel.
"""

import streamlit as st
import streamlit.components.v1 as components

from utils.config import MODEL_URL, METADATA_URL, LARAVEL_URL
from utils.database import get_jadwal_aktif, get_mahasiswa_terdaftar
from utils.face_scanner_html import get_scanner_html
from utils.ui import render_header, render_jadwal_info

st.set_page_config(page_title="Scan Wajah", page_icon="📷", layout="wide")

render_header()
st.markdown("### 📷 Scan Wajah (AI)")

try:
    jadwal = get_jadwal_aktif()
except Exception:
    jadwal = None
    st.error("Database tidak terhubung. Pastikan MySQL XAMPP aktif.")

render_jadwal_info(jadwal)
mk_id = jadwal["mata_kuliah_id"] if jadwal else None

col_scan, col_info = st.columns([2, 1])

with col_scan:
    st.markdown("#### Kamera Scan")
    st.caption("Posisikan wajah di dalam frame biru. Sistem otomatis mengenali dan absen.")

    html = get_scanner_html(MODEL_URL, METADATA_URL, mk_id)
    components.html(html, height=520, scrolling=False)

with col_info:
    st.markdown("#### Tips Scan")
    st.markdown("""
    1. Hadap kamera lurus
    2. Pencahayaan cukup terang
    3. Tanpa masker / kacamata hitam
    4. Jarak ideal 30–50 cm
    """)

    st.markdown("#### Mahasiswa Terdaftar")
    try:
        df = get_mahasiswa_terdaftar()
        if not df.empty:
            st.dataframe(df[["nama", "nim", "kelas"]], hide_index=True, use_container_width=True)
        else:
            st.caption("Belum ada mahasiswa terdaftar.")
    except Exception:
        st.caption("Database tidak tersedia.")

    st.markdown("---")
    st.link_button("🌐 Buka Halaman Laravel", LARAVEL_URL, use_container_width=True)
