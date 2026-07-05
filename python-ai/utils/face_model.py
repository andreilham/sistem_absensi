"""
Modul pengenalan wajah menggunakan model Teachable Machine.
Kode disederhanakan agar mudah dipahami mahasiswa.
"""

import json

import numpy as np
from PIL import Image

from utils.config import MODEL_PATH, MIN_CONFIDENCE, MIN_MARGIN


def load_labels():
    """Baca label nama dari file metadata.json."""
    metadata_file = MODEL_PATH / "metadata.json"
    with open(metadata_file, "r", encoding="utf-8") as f:
        data = json.load(f)
    return data["labels"], data.get("imageSize", 224)


def _load_keras_model():
    """Muat model TensorFlow dari format Teachable Machine."""
    import tensorflowjs as tfjs

    model_file = str(MODEL_PATH / "model.json")
    return tfjs.converters.load_keras_model(model_file)


# Cache model agar tidak dimuat ulang setiap kali
_model = None
_labels = None
_image_size = None


def get_model():
    """Ambil model AI (dimuat sekali saja)."""
    global _model, _labels, _image_size

    if _model is None:
        _labels, _image_size = load_labels()
        _model = _load_keras_model()

    return _model, _labels, _image_size


def preprocess_image(image: Image.Image, image_size: int) -> np.ndarray:
    """
    Ubah foto menjadi format yang dibutuhkan model.
    - Resize ke 224x224 pixel
    - Normalisasi pixel ke rentang 0-1
    """
    image = image.convert("RGB")
    image = image.resize((image_size, image_size))
    arr = np.array(image, dtype=np.float32)
    arr = (arr / 127.5) - 1  # normalisasi Teachable Machine
    return np.expand_dims(arr, axis=0)


def predict_face(image: Image.Image):
    """
    Prediksi wajah dari foto.

    Returns:
        dict dengan keys: success, label, confidence, second_label, second_confidence, message
    """
    try:
        model, labels, image_size = get_model()
    except Exception as e:
        return {
            "success": False,
            "message": f"Gagal memuat model AI: {e}",
        }

    # Siapkan gambar
    input_data = preprocess_image(image, image_size)

    # Jalankan prediksi
    predictions = model.predict(input_data, verbose=0)[0]

    # Urutkan dari confidence tertinggi
    ranked = sorted(
        zip(labels, predictions),
        key=lambda x: x[1],
        reverse=True,
    )

    top_label, top_score = ranked[0]
    second_label, second_score = ranked[1] if len(ranked) > 1 else ("", 0)

    confidence = float(top_score * 100)
    second_confidence = float(second_score * 100)
    margin = confidence - second_confidence

    # Validasi confidence minimum
    if confidence < MIN_CONFIDENCE:
        return {
            "success": False,
            "label": top_label,
            "confidence": confidence,
            "message": f"Akurasi terlalu rendah ({confidence:.1f}%). Minimum {MIN_CONFIDENCE}%.",
        }

    # Validasi margin (beda jauh dengan prediksi ke-2)
    if margin < MIN_MARGIN:
        return {
            "success": False,
            "label": top_label,
            "confidence": confidence,
            "message": "Wajah tidak dapat dipastikan. Hasil terlalu mirip dengan wajah lain.",
        }

    return {
        "success": True,
        "label": top_label,
        "confidence": confidence,
        "second_label": second_label,
        "second_confidence": second_confidence,
        "message": f"Wajah dikenali: {top_label} ({confidence:.1f}%)",
    }
