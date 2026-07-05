"""
Scanner wajah via TensorFlow.js (browser).
Digunakan di Streamlit karena tensorflowjs Python tidak kompatibel dengan Python 3.13.
Logika AI tetap Teachable Machine — sama persis dengan Laravel.
"""

from utils.config import LARAVEL_URL, API_KEY, MIN_CONFIDENCE


def get_scanner_html(model_url: str, metadata_url: str, mata_kuliah_id=None) -> str:
    """Generate HTML scanner wajah (sama seperti halaman Laravel)."""
    mk_id = mata_kuliah_id if mata_kuliah_id else "null"
    api_url = f"{LARAVEL_URL}/api/absensi/scan"
    min_conf = MIN_CONFIDENCE

    return f"""
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.7.4/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@0.8.4/dist/teachablemachine-image.min.js"></script>
    <style>
        body {{ margin:0; font-family: sans-serif; background:#111; }}
        .wrap {{ position:relative; width:100%; max-width:640px; margin:0 auto; }}
        video {{ width:100%; border-radius:12px; }}
        .frame {{
            position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
            width:180px; height:220px; border:3px solid #60a5fa; border-radius:20px;
            box-shadow:0 0 0 9999px rgba(0,0,0,0.35); pointer-events:none;
        }}
        .status {{
            position:absolute; bottom:12px; left:0; right:0; text-align:center;
        }}
        .status span {{
            background:rgba(0,0,0,0.65); color:white; padding:8px 16px;
            border-radius:20px; font-size:13px;
        }}
        .result {{
            margin-top:12px; padding:16px; border-radius:12px; text-align:center;
            font-size:14px; display:none;
        }}
        .result.ok {{ background:#dcfce7; color:#166534; display:block; }}
        .result.err {{ background:#fee2e2; color:#991b1b; display:block; }}
    </style>
</head>
<body>
    <div class="wrap">
        <video id="webcam" autoplay playsinline muted></video>
        <div class="frame"></div>
        <div class="status"><span id="status-text">Memuat model AI...</span></div>
    </div>
    <div id="result" class="result"></div>

    <script>
        const MODEL_URL = "{model_url}";
        const METADATA_URL = "{metadata_url}";
        const API_URL = "{api_url}";
        const API_KEY = "{API_KEY}";
        const MK_ID = {mk_id};
        const MIN_CONF = {min_conf};

        let model = null;
        let isProcessing = false;
        let lastScan = 0;

        async function init() {{
            const video = document.getElementById('webcam');
            try {{
                const stream = await navigator.mediaDevices.getUserMedia({{
                    video: {{ facingMode:'user', width:640, height:480 }}
                }});
                video.srcObject = stream;
                await video.play();
                model = await tmImage.load(MODEL_URL, METADATA_URL);
                document.getElementById('status-text').textContent = 'Scanning...';
                setInterval(scan, 1000);
            }} catch(e) {{
                document.getElementById('status-text').textContent = 'Kamera/model error: ' + e.message;
            }}
        }}

        function showResult(ok, msg) {{
            const el = document.getElementById('result');
            el.className = 'result ' + (ok ? 'ok' : 'err');
            el.textContent = msg;
            el.style.display = 'block';
            if (!ok) setTimeout(() => {{ el.style.display='none'; }}, 4000);
        }}

        async function scan() {{
            const video = document.getElementById('webcam');
            if (!model || isProcessing || video.readyState !== 4) return;
            if (Date.now() - lastScan < 3000) return;
            isProcessing = true;
            try {{
                const pred = await model.predict(video);
                pred.sort((a,b) => b.probability - a.probability);
                const top = pred[0];
                const second = pred[1] || {{ probability: 0 }};
                const conf = top.probability * 100;
                const conf2 = second.probability * 100;

                if (conf >= MIN_CONF && (conf - conf2) >= 15) {{
                    lastScan = Date.now();
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    const foto = canvas.toDataURL('image/jpeg', 0.7);

                    const body = {{
                        face_label: top.className,
                        akurasi: conf,
                        second_akurasi: conf2,
                        foto_scan: foto,
                    }};
                    if (MK_ID) body.mata_kuliah_id = MK_ID;

                    const res = await fetch(API_URL, {{
                        method: 'POST',
                        headers: {{
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-API-Key': API_KEY,
                        }},
                        body: JSON.stringify(body),
                    }});
                    const data = await res.json();
                    if (data.success) {{
                        const d = data.data;
                        showResult(true, '✅ ' + d.nama + ' (' + d.nim + ') — ' + d.status + ' · ' + d.jam_masuk);
                    }} else {{
                        showResult(false, '❌ ' + data.message);
                    }}
                }}
            }} catch(e) {{ console.error(e); }}
            isProcessing = false;
        }}

        init();
    </script>
</body>
</html>
"""
