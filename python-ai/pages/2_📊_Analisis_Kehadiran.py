"""
Halaman Analisis Kehadiran — Grafik statistik
"""

from datetime import datetime

import plotly.express as px
import streamlit as st

from utils.database import get_data_absensi
from utils.ui import render_header

st.set_page_config(page_title="Analisis Kehadiran", page_icon="📊", layout="wide")

render_header()
st.markdown("### 📊 Analisis Kehadiran")

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

try:
    df = get_data_absensi(bulan=bulan, tahun=tahun)
except Exception as e:
    st.error(f"Gagal memuat data: {e}")
    st.stop()

if df.empty:
    st.warning("Tidak ada data absensi untuk periode ini.")
    st.stop()

c1, c2, c3, c4 = st.columns(4)
c1.metric("Total Absensi", len(df))
c2.metric("Hadir", len(df[df["status"] == "hadir"]))
c3.metric("Terlambat", len(df[df["status"] == "terlambat"]))
c4.metric("Rata-rata Akurasi AI", f"{df['akurasi'].mean():.1f}%")

st.divider()

tab1, tab2, tab3 = st.tabs(["Per Hari", "Per Kelas", "Top Mahasiswa"])

with tab1:
    harian = df.groupby("tanggal").size().reset_index(name="jumlah")
    harian["tanggal"] = harian["tanggal"].astype(str)
    fig = px.bar(harian, x="tanggal", y="jumlah", title="Absensi Harian",
                 color_discrete_sequence=["#2563eb"])
    st.plotly_chart(fig, use_container_width=True)

with tab2:
    if "kelas" in df.columns:
        per_kelas = df.groupby("kelas").size().reset_index(name="jumlah")
        fig2 = px.pie(per_kelas, names="kelas", values="jumlah", title="Distribusi Per Kelas")
        st.plotly_chart(fig2, use_container_width=True)

with tab3:
    per_mhs = df.groupby(["nama", "nim"]).size().reset_index(name="jumlah")
    top10 = per_mhs.nlargest(10, "jumlah")
    fig3 = px.bar(top10, x="nama", y="jumlah", title="10 Mahasiswa Paling Rajin",
                  color_discrete_sequence=["#2563eb"])
    st.plotly_chart(fig3, use_container_width=True)

st.subheader("Data Lengkap")
st.dataframe(df, hide_index=True, use_container_width=True)
