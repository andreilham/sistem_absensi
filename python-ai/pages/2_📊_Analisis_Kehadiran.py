"""
Halaman Analisis Kehadiran - Grafik dan statistik
"""

from datetime import datetime

import plotly.express as px
import streamlit as st

from utils.database import get_data_absensi

st.set_page_config(page_title="Analisis Kehadiran", page_icon="📊", layout="wide")

st.title("📊 Analisis Kehadiran")
st.markdown("Visualisasi data absensi mahasiswa dari database Laravel.")

# --- Filter bulan/tahun ---
col1, col2, col3 = st.columns(3)
with col1:
    bulan = st.selectbox(
        "Bulan",
        list(range(1, 13)),
        format_func=lambda x: datetime(2026, x, 1).strftime("%B"),
        index=datetime.now().month - 1,
    )
with col2:
    tahun = st.number_input("Tahun", min_value=2024, max_value=2030, value=datetime.now().year)
with col3:
    st.write("")
    st.write("")
    if st.button("🔄 Muat Ulang Data"):
        st.rerun()

# --- Ambil data ---
try:
    df = get_data_absensi(bulan=bulan, tahun=tahun)
except Exception as e:
    st.error(f"Gagal memuat data: {e}")
    st.stop()

if df.empty:
    st.warning("Tidak ada data absensi untuk periode ini.")
    st.stop()

# --- Statistik ringkas ---
col_a, col_b, col_c, col_d = st.columns(4)
total = len(df)
hadir = len(df[df["status"] == "hadir"])
terlambat = len(df[df["status"] == "terlambat"])

col_a.metric("Total Absensi", total)
col_b.metric("Hadir", hadir)
col_c.metric("Terlambat", terlambat)
col_d.metric("Rata-rata Akurasi AI", f"{df['akurasi'].mean():.1f}%")

st.divider()

# --- Grafik ---
tab1, tab2, tab3 = st.tabs(["Per Hari", "Per Kelas", "Per Mahasiswa"])

with tab1:
    st.subheader("Kehadiran Per Hari")
    harian = df.groupby("tanggal").size().reset_index(name="jumlah")
    harian["tanggal"] = harian["tanggal"].astype(str)
    fig = px.bar(harian, x="tanggal", y="jumlah", title="Jumlah Absensi Harian")
    fig.update_layout(xaxis_title="Tanggal", yaxis_title="Jumlah")
    st.plotly_chart(fig, use_container_width=True)

with tab2:
    st.subheader("Kehadiran Per Kelas")
    if "kelas" in df.columns:
        per_kelas = df.groupby("kelas").size().reset_index(name="jumlah")
        fig2 = px.pie(per_kelas, names="kelas", values="jumlah", title="Distribusi Per Kelas")
        st.plotly_chart(fig2, use_container_width=True)

with tab3:
    st.subheader("Top 10 Mahasiswa Paling Rajin")
    per_mhs = df.groupby(["nama", "nim"]).size().reset_index(name="jumlah")
    top10 = per_mhs.nlargest(10, "jumlah")
    fig3 = px.bar(top10, x="nama", y="jumlah", title="10 Mahasiswa dengan Absensi Terbanyak")
    st.plotly_chart(fig3, use_container_width=True)

# --- Tabel data ---
st.subheader("Data Lengkap")
st.dataframe(df, hide_index=True, use_container_width=True)
