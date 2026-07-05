"""
Halaman Scan Wajah - Pengenalan wajah dengan AI
"""

import streamlit as st
from PIL import Image

from utils.database import get_jadwal_aktif, get_mahasiswa_terdaftar
from utils.face_model import predict_face
from utils.api_client import kirim_absensi_ke_laravel

st.set_page_config(page_title="Scan Wajah", page_icon="📷", layout="wide")

st.title("📷 Scan Wajah (AI)")
st.markdown("Ambil foto wajah Anda, sistem AI akan mengenali dan mencatat absensi.")

# --- Info jadwal ---
jadwal = get_jadwal_aktif()
if jadwal:
    st.info(
        f"Jadwal aktif: **{jadwal['mata_kuliah']}** · "
        f"{str(jadwal['jam_mulai'])[:5]} - {str(jadwal['jam_selesai'])[:5]}"
    )
    mk_id = jadwal["mata_kuliah_id"]
else:
    st.warning("Tidak ada jadwal aktif. Absensi tetap bisa dicatat ke mata kuliah default.")
    mk_id = None

col_kamera, col_info = st.columns([2, 1])

with col_kamera:
    st.subheader("Ambil Foto")
    foto = st.camera_input("Hadapkan wajah ke kamera")

    if foto is not None:
        image = Image.open(foto)

        # Tampilkan preview
        st.image(image, caption="Foto yang diambil", width=300)

        if st.button("🔍 Scan & Absen", type="primary", use_container_width=True):
            with st.spinner("Menganalisis wajah..."):
                hasil = predict_face(image)

            if not hasil["success"]:
                st.error(hasil["message"])
            else:
                st.success(hasil["message"])

                # Kirim ke Laravel
                with st.spinner("Menyimpan absensi..."):
                    response = kirim_absensi_ke_laravel(
                        face_label=hasil["label"],
                        akurasi=hasil["confidence"],
                        second_akurasi=hasil.get("second_confidence", 0),
                        image=image,
                        mata_kuliah_id=mk_id,
                    )

                if response.get("success"):
                    data = response.get("data", {})
                    st.balloons()
                    st.markdown("### ✅ Absensi Berhasil!")
                    st.markdown(f"""
                    | | |
                    |---|---|
                    | **Nama** | {data.get('nama', '-')} |
                    | **NIM** | {data.get('nim', '-')} |
                    | **Kelas** | {data.get('kelas', '-')} |
                    | **Mata Kuliah** | {data.get('mata_kuliah', '-')} |
                    | **Jam Masuk** | {data.get('jam_masuk', '-')} |
                    | **Status** | {data.get('status', '-')} |
                    | **Akurasi AI** | {data.get('akurasi', '-')} |
                    """)
                else:
                    st.error(response.get("message", "Gagal menyimpan absensi."))

with col_info:
    st.subheader("Tips Scan")
    st.markdown("""
    1. **Hadap kamera** lurus
    2. **Pencahayaan** cukup terang
    3. **Jangan pakai** masker/kacamata hitam
    4. **Jarak ideal** 30-50 cm
    """)

    st.subheader("Mahasiswa Terdaftar")
    try:
        df = get_mahasiswa_terdaftar()
        if not df.empty:
            st.dataframe(
                df[["nama", "nim", "kelas"]],
                hide_index=True,
                use_container_width=True,
            )
        else:
            st.caption("Belum ada mahasiswa terdaftar wajah.")
    except Exception:
        st.caption("Database tidak tersedia.")
