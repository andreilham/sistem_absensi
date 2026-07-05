"""
File konfigurasi sederhana.
Membaca pengaturan dari file .env agar mudah diubah tanpa edit kode.
"""

import os
from pathlib import Path

from dotenv import load_dotenv

# Lokasi folder python-ai
BASE_DIR = Path(__file__).resolve().parent.parent

# Muat file .env jika ada
load_dotenv(BASE_DIR / ".env")

# --- Pengaturan Database ---
DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = int(os.getenv("DB_PORT", "3306"))
DB_USER = os.getenv("DB_USER", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "")
DB_NAME = os.getenv("DB_NAME", "absensi_wajah")

# --- Pengaturan Laravel API ---
LARAVEL_URL = os.getenv("LARAVEL_URL", "http://127.0.0.1:8000")
API_KEY = os.getenv("API_KEY", "absensi-umb-2026")

# --- Pengaturan Model AI ---
MODEL_PATH = Path(os.getenv("MODEL_PATH", BASE_DIR.parent / "app-temp" / "public" / "models"))

# --- Pengaturan Scan Wajah ---
MIN_CONFIDENCE = float(os.getenv("MIN_CONFIDENCE", "70"))
MIN_MARGIN = float(os.getenv("MIN_MARGIN", "15"))
