"""
Helper untuk mengirim data absensi ke Laravel via API.
"""

import base64
import io

import requests
from PIL import Image

from utils.config import LARAVEL_URL, API_KEY


def kirim_absensi_ke_laravel(face_label, akurasi, second_akurasi, image=None, mata_kuliah_id=None):
    """
    Kirim hasil scan wajah ke API Laravel.

    Returns:
        dict response dari Laravel
    """
    payload = {
        "face_label": face_label,
        "akurasi": round(akurasi, 2),
        "second_akurasi": round(second_akurasi, 2),
    }

    if mata_kuliah_id:
        payload["mata_kuliah_id"] = mata_kuliah_id

    # Konversi foto ke base64 jika ada
    if image is not None:
        buffer = io.BytesIO()
        image.save(buffer, format="JPEG", quality=70)
        foto_base64 = base64.b64encode(buffer.getvalue()).decode("utf-8")
        payload["foto_scan"] = f"data:image/jpeg;base64,{foto_base64}"

    url = f"{LARAVEL_URL}/api/absensi/scan"
    headers = {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-API-Key": API_KEY,
    }

    try:
        response = requests.post(url, json=payload, headers=headers, timeout=10)
        return response.json()
    except requests.RequestException as e:
        return {
            "success": False,
            "message": f"Gagal terhubung ke Laravel: {e}",
        }
