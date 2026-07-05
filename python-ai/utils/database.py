"""
Modul koneksi database MySQL.
Digunakan untuk membaca data kehadiran dan informasi mahasiswa.
"""

import pymysql
import pandas as pd

from utils.config import DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME


def get_connection():
    """Buat koneksi ke database MySQL Laravel."""
    return pymysql.connect(
        host=DB_HOST,
        port=DB_PORT,
        user=DB_USER,
        password=DB_PASSWORD,
        database=DB_NAME,
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
    )


def get_mahasiswa_terdaftar():
    """Ambil daftar mahasiswa yang sudah registrasi wajah."""
    query = """
        SELECT m.id, m.nama, m.nim, m.face_label, k.nama AS kelas
        FROM mahasiswa m
        LEFT JOIN kelas k ON k.id = m.kelas_id
        WHERE m.face_registered = 1 AND m.face_label IS NOT NULL
        ORDER BY m.nama
    """
    with get_connection() as conn:
        return pd.read_sql(query, conn)


def get_mahasiswa_by_label(face_label):
    """Cari mahasiswa berdasarkan label wajah dari model AI."""
    query = """
        SELECT m.*, k.nama AS kelas_nama
        FROM mahasiswa m
        LEFT JOIN kelas k ON k.id = m.kelas_id
        WHERE m.face_label = %s AND m.face_registered = 1
        LIMIT 1
    """
    with get_connection() as conn:
        with conn.cursor() as cursor:
            cursor.execute(query, (face_label,))
            return cursor.fetchone()


def get_jadwal_aktif():
    """Ambil jadwal kuliah yang sedang berlangsung hari ini."""
    hari_map = {
        0: "Senin",
        1: "Selasa",
        2: "Rabu",
        3: "Kamis",
        4: "Jumat",
        5: "Sabtu",
        6: "Minggu",
    }

    from datetime import datetime

    hari = hari_map[datetime.now().weekday()]
    jam_sekarang = datetime.now().strftime("%H:%M:%S")

    query = """
        SELECT j.*, mk.nama AS mata_kuliah, k.nama AS kelas
        FROM jadwal j
        JOIN mata_kuliah mk ON mk.id = j.mata_kuliah_id
        JOIN kelas k ON k.id = j.kelas_id
        WHERE j.hari = %s
          AND j.jam_mulai <= %s
          AND j.jam_selesai >= %s
        LIMIT 1
    """
    with get_connection() as conn:
        with conn.cursor() as cursor:
            cursor.execute(query, (hari, jam_sekarang, jam_sekarang))
            return cursor.fetchone()


def cek_sudah_absen(mahasiswa_id, mata_kuliah_id):
    """Cek apakah mahasiswa sudah absen hari ini."""
    from datetime import date

    query = """
        SELECT * FROM absensi
        WHERE mahasiswa_id = %s
          AND mata_kuliah_id = %s
          AND tanggal = %s
        LIMIT 1
    """
    with get_connection() as conn:
        with conn.cursor() as cursor:
            cursor.execute(query, (mahasiswa_id, mata_kuliah_id, date.today()))
            return cursor.fetchone()


def get_data_absensi(bulan=None, tahun=None):
    """Ambil data absensi untuk analisis (default: bulan ini)."""
    from datetime import datetime

    if bulan is None:
        bulan = datetime.now().month
    if tahun is None:
        tahun = datetime.now().year

    query = """
        SELECT
            a.tanggal,
            a.jam_masuk,
            a.status,
            a.akurasi,
            m.nama,
            m.nim,
            k.nama AS kelas,
            mk.nama AS mata_kuliah
        FROM absensi a
        JOIN mahasiswa m ON m.id = a.mahasiswa_id
        LEFT JOIN kelas k ON k.id = m.kelas_id
        JOIN mata_kuliah mk ON mk.id = a.mata_kuliah_id
        WHERE MONTH(a.tanggal) = %s AND YEAR(a.tanggal) = %s
        ORDER BY a.tanggal DESC, a.jam_masuk DESC
    """
    with get_connection() as conn:
        return pd.read_sql(query, conn, params=(bulan, tahun))


def get_statistik_per_mahasiswa(bulan=None, tahun=None):
    """Hitung jumlah kehadiran per mahasiswa (untuk prediksi risiko)."""
    from datetime import datetime

    if bulan is None:
        bulan = datetime.now().month
    if tahun is None:
        tahun = datetime.now().year

    query = """
        SELECT
            m.id,
            m.nama,
            m.nim,
            k.nama AS kelas,
            SUM(CASE WHEN a.status = 'hadir' THEN 1 ELSE 0 END) AS jumlah_hadir,
            SUM(CASE WHEN a.status = 'terlambat' THEN 1 ELSE 0 END) AS jumlah_terlambat,
            COUNT(a.id) AS total_absensi
        FROM mahasiswa m
        LEFT JOIN kelas k ON k.id = m.kelas_id
        LEFT JOIN absensi a ON a.mahasiswa_id = m.id
            AND MONTH(a.tanggal) = %s AND YEAR(a.tanggal) = %s
        GROUP BY m.id, m.nama, m.nim, k.nama
        ORDER BY total_absensi ASC
    """
    with get_connection() as conn:
        return pd.read_sql(query, conn, params=(bulan, tahun))
