"""
Halaman Prediksi Risiko — Machine Learning sederhana
"""

from datetime import datetime

import plotly.express as px
import streamlit as st

from utils.database import get_statistik_per_mahasiswa
from utils.predictor import hitung_risiko, prediksi_bulan_depan
from utils.ui import render_header

st.set_page_config(page_title="Prediksi Risiko", page_icon="🎯", layout="wide")

render_header()
st.markdown("### 🎯 Prediksi Risiko Kehadiran")
st.caption("Analisis pola kehadiran mahasiswa menggunakan logika sederhana + scikit-learn.")

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

hari_efektif = st.slider("Hari kuliah efektif per bulan", 10, 30, 20)

try:
    df_raw = get_statistik_per_mahasiswa(bulan=bulan, tahun=tahun)
    df_risiko = hitung_risiko(df_raw, hari_efektif=hari_efektif)
except Exception as e:
    st.error(f"Gagal memuat data: {e}")
    st.stop()

if df_risiko.empty:
    st.warning("Tidak ada data mahasiswa.")
    st.stop()

c1, c2, c3 = st.columns(3)
c1.metric("🔴 Risiko Tinggi", len(df_risiko[df_risiko["kategori_risiko"] == "Tinggi"]))
c2.metric("🟡 Risiko Sedang", len(df_risiko[df_risiko["kategori_risiko"] == "Sedang"]))
c3.metric("🟢 Risiko Rendah", len(df_risiko[df_risiko["kategori_risiko"] == "Rendah"]))

st.divider()

fig = px.scatter(
    df_risiko, x="persentase_hadir", y="skor_risiko",
    color="kategori_risiko", hover_data=["nama", "nim", "kelas"],
    title="Persentase Hadir vs Skor Risiko",
    color_discrete_map={"Tinggi": "#ef4444", "Sedang": "#f59e0b", "Rendah": "#22c55e"},
)
st.plotly_chart(fig, use_container_width=True)

st.subheader("⚠️ Mahasiswa Berisiko Tinggi")
df_tinggi = df_risiko[df_risiko["kategori_risiko"] == "Tinggi"]
if df_tinggi.empty:
    st.success("Tidak ada mahasiswa berisiko tinggi.")
else:
    st.dataframe(
        df_tinggi[["nama", "nim", "kelas", "total_absensi", "persentase_hadir", "skor_risiko"]],
        hide_index=True, use_container_width=True,
    )

st.subheader("🔮 Prediksi Kehadiran (Linear Regression)")
try:
    df_pred = prediksi_bulan_depan(df_raw)
    if not df_pred.empty:
        st.dataframe(df_pred, hide_index=True, use_container_width=True)
    else:
        st.info("Data belum cukup (minimal 3 mahasiswa).")
except Exception as e:
    st.warning(f"Prediksi tidak tersedia: {e}")
