"""
Modul prediksi risiko kehadiran mahasiswa.
Menggunakan aturan sederhana + scikit-learn (mudah dipahami mahasiswa).
"""

import pandas as pd


def hitung_risiko(df_statistik: pd.DataFrame, hari_efektif: int = 20) -> pd.DataFrame:
    """
    Hitung skor risiko ketidakhadiran per mahasiswa.

    Logika sederhana:
    - Persentase kehadiran rendah = risiko tinggi
    - Banyak terlambat = risiko sedang

    Args:
        df_statistik: DataFrame dari get_statistik_per_mahasiswa()
        hari_efektif: Perkiraan jumlah hari kuliah dalam sebulan

    Returns:
        DataFrame dengan kolom tambahan: persentase_hadir, skor_risiko, kategori_risiko
    """
    if df_statistik.empty:
        return df_statistik

    df = df_statistik.copy()
    df["total_absensi"] = df["total_absensi"].fillna(0).astype(int)
    df["jumlah_hadir"] = df["jumlah_hadir"].fillna(0).astype(int)
    df["jumlah_terlambat"] = df["jumlah_terlambat"].fillna(0).astype(int)

    # Persentase kehadiran (0-100)
    df["persentase_hadir"] = (df["total_absensi"] / hari_efektif * 100).clip(0, 100).round(1)

    # Skor risiko: semakin tinggi = semakin berisiko tidak hadir
    # Formula sederhana untuk pembelajaran
    df["skor_risiko"] = (100 - df["persentase_hadir"] + df["jumlah_terlambat"] * 2).round(1)

    # Kategorikan risiko
    def kategori(skor):
        if skor >= 60:
            return "Tinggi"
        if skor >= 35:
            return "Sedang"
        return "Rendah"

    df["kategori_risiko"] = df["skor_risiko"].apply(kategori)

    return df.sort_values("skor_risiko", ascending=False)


def prediksi_bulan_depan(df_statistik: pd.DataFrame) -> pd.DataFrame:
    """
    Prediksi sederhana kehadiran bulan depan menggunakan Linear Regression.
    Hanya contoh ML dasar untuk mahasiswa.
    """
    from sklearn.linear_model import LinearRegression
    import numpy as np

    if len(df_statistik) < 3:
        return pd.DataFrame()

    df = df_statistik.copy()
    X = df[["total_absensi", "jumlah_terlambat"]].fillna(0).values
    y = df["total_absensi"].fillna(0).values

    model = LinearRegression()
    model.fit(X, y)

    # Prediksi (contoh: asumsi pola sama)
    prediksi = model.predict(X)
    df["prediksi_kehadiran"] = np.clip(prediksi, 0, 25).round(1)

    return df[["nama", "nim", "kelas", "total_absensi", "prediksi_kehadiran"]]
