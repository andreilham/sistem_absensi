"""
Halaman Prediksi Risiko - Mahasiswa berisiko tidak hadir
"""

from datetime import datetime

import plotly.express as px
import streamlit as st

from utils.database import get_statistik_per_mahasiswa
from utils.predictor import hitung_risiko, prediksi_bulan_depan

st.set_page_config(page_title="Prediksi Risiko", page_icon="🎯", layout="wide")

st.title("🎯 Prediksi Risiko Kehadiran")
st.markdown("""
Modul **Sistem Cerdas** ini menganalisis pola kehadiran mahasiswa
dan memprediksi siapa yang berisiko sering tidak hadir.

> Logika sederhana: persentase kehadiran rendah + sering terlambat = risiko tinggi.
> Cocok untuk pembelajaran dasar machine learning.
""")

# --- Filter ---
col1, col2 = st.columns(2)
with col1:
    bulan = st.selectbox(
        "Bulan",
        list(range(1, 13)),
        format_func=lambda x: datetime(2026, x, 1).strftime("%B"),
        index=datetime.now().month - 1,
    )
with col2:
    tahun = st.number_input("Tahun", min_value=2024, max_value=2030, value=datetime.now().year)

hari_efektif = st.slider("Perkiraan hari kuliah efektif per bulan", 10, 30, 20)

# --- Hitung risiko ---
try:
    df_raw = get_statistik_per_mahasiswa(bulan=bulan, tahun=tahun)
    df_risiko = hitung_risiko(df_raw, hari_efektif=hari_efektif)
except Exception as e:
    st.error(f"Gagal memuat data: {e}")
    st.stop()

if df_risiko.empty:
    st.warning("Tidak ada data mahasiswa.")
    st.stop()

# --- Ringkasan ---
tinggi = len(df_risiko[df_risiko["kategori_risiko"] == "Tinggi"])
sedang = len(df_risiko[df_risiko["kategori_risiko"] == "Sedang"])
rendah = len(df_risiko[df_risiko["kategori_risiko"] == "Rendah"])

c1, c2, c3 = st.columns(3)
c1.metric("🔴 Risiko Tinggi", tinggi)
c2.metric("🟡 Risiko Sedang", sedang)
c3.metric("🟢 Risiko Rendah", rendah)

st.divider()

# --- Grafik ---
fig = px.scatter(
    df_risiko,
    x="persentase_hadir",
    y="skor_risiko",
    color="kategori_risiko",
    hover_data=["nama", "nim", "kelas"],
    title="Scatter Plot: Persentase Hadir vs Skor Risiko",
    color_discrete_map={"Tinggi": "red", "Sedang": "orange", "Rendah": "green"},
)
fig.update_layout(xaxis_title="Persentase Hadir (%)", yaxis_title="Skor Risiko")
st.plotly_chart(fig, use_container_width=True)

# --- Tabel risiko tinggi ---
st.subheader("⚠️ Mahasiswa Berisiko Tinggi")
df_tinggi = df_risiko[df_risiko["kategori_risiko"] == "Tinggi"]

if df_tinggi.empty:
    st.success("Tidak ada mahasiswa dengan risiko tinggi. 👍")
else:
    st.dataframe(
        df_tinggi[["nama", "nim", "kelas", "total_absensi", "persentase_hadir", "skor_risiko"]],
        hide_index=True,
        use_container_width=True,
    )

# --- Prediksi bulan depan (contoh ML) ---
st.subheader("🔮 Prediksi Kehadiran (Linear Regression)")
st.caption("Contoh sederhana machine learning menggunakan scikit-learn.")

try:
    df_pred = prediksi_bulan_depan(df_raw)
    if not df_pred.empty:
        st.dataframe(df_pred, hide_index=True, use_container_width=True)
    else:
        st.info("Data belum cukup untuk prediksi (minimal 3 mahasiswa).")
except Exception as e:
    st.warning(f"Prediksi tidak tersedia: {e}")

# --- Tabel lengkap ---
with st.expander("📋 Lihat Semua Data"):
    st.dataframe(
        df_risiko[
            ["nama", "nim", "kelas", "jumlah_hadir", "jumlah_terlambat",
             "total_absensi", "persentase_hadir", "skor_risiko", "kategori_risiko"]
        ],
        hide_index=True,
        use_container_width=True,
    )
