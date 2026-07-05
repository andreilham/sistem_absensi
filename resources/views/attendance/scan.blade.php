<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Halaman Absensi - Sistem Absensi Wajah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.7.4/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@0.8.4/dist/teachablemachine-image.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    {{-- Header --}}
    <header class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-gray-800">Universitas Maju Bersama</h1>
                    <p class="text-sm text-gray-500">Sistem Absensi Wajah</p>
                </div>
            </div>
            <div class="text-center sm:text-right">
                <p id="clock" class="text-2xl font-bold text-blue-700 font-mono">--:--:--</p>
                <p id="date" class="text-sm text-gray-500">-</p>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6 sm:py-8">
        @if($activeJadwal)
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800 text-center">
                <i class="fas fa-book mr-1"></i>
                Jadwal aktif: <strong>{{ $activeJadwal->mataKuliah->nama }}</strong>
                ({{ $activeJadwal->kelas->nama }}) · {{ substr($activeJadwal->jam_mulai, 0, 5) }} - {{ substr($activeJadwal->jam_selesai, 0, 5) }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Camera --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="relative aspect-[4/3] bg-gray-900">
                        <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-52 h-64 sm:w-64 sm:h-80 border-4 border-blue-400 rounded-3xl shadow-[0_0_0_9999px_rgba(0,0,0,0.3)]"></div>
                        </div>
                        <div id="scan-status" class="absolute bottom-4 left-0 right-0 text-center">
                            <span class="inline-block px-4 py-2 bg-black/60 text-white text-sm rounded-full">
                                <i class="fas fa-spinner fa-spin mr-1" id="status-icon"></i>
                                <span id="status-text">Memuat model AI...</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-4 text-center text-gray-600 text-sm">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Posisikan wajah Anda di dalam frame
                    </div>
                </div>

                {{-- Tips --}}
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([
                        ['icon' => 'fa-face-smile', 'text' => 'Hadap kamera'],
                        ['icon' => 'fa-sun', 'text' => 'Pencahayaan baik'],
                        ['icon' => 'fa-glasses', 'text' => 'Tanpa aksesoris'],
                        ['icon' => 'fa-ruler', 'text' => 'Jarak ideal'],
                    ] as $tip)
                        <div class="bg-white rounded-lg p-3 text-center shadow-sm">
                            <i class="fas {{ $tip['icon'] }} text-blue-500 text-lg mb-1"></i>
                            <p class="text-xs text-gray-600">{{ $tip['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Result Card --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div id="result-idle" class="text-center py-8">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-gray-400 text-3xl"></i>
                    </div>
                    <p class="text-gray-500">Menunggu scan wajah...</p>
                </div>

                <div id="result-success" class="hidden text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-green-600 text-3xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-green-700 mb-4">Absensi Berhasil!</h2>
                    <div class="space-y-3 text-left text-sm">
                        <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Nama</span><span id="r-nama" class="font-medium">-</span></div>
                        <div class="flex justify-between py-2 border-b"><span class="text-gray-500">NIM</span><span id="r-nim" class="font-medium">-</span></div>
                        <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Kelas</span><span id="r-kelas" class="font-medium">-</span></div>
                        <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Mata Kuliah</span><span id="r-mk" class="font-medium">-</span></div>
                        <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Jam Masuk</span><span id="r-jam" class="font-medium">-</span></div>
                        <div class="flex justify-between py-2"><span class="text-gray-500">Status</span><span id="r-status" class="font-medium text-green-600">-</span></div>
                    </div>
                </div>

                <div id="result-error" class="hidden text-center py-8">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times text-red-600 text-3xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-red-700 mb-2">Gagal</h2>
                    <p id="error-msg" class="text-sm text-gray-600">-</p>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">
                <i class="fas fa-lock mr-1"></i> Login Admin
            </a>
        </div>
    </main>

    <script>
        const MODEL_URL = @json($modelUrl);
        const METADATA_URL = @json($metadataUrl);
        const STORE_URL = @json(route('attendance.store'));
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        const MK_ID = @json($activeJadwal?->mata_kuliah_id);

        let model = null;
        let isProcessing = false;
        let lastScanTime = 0;

        // Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID');
            document.getElementById('date').textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Load model
        async function initModel() {
            try {
                const modelURL = MODEL_URL;
                const metadataURL = METADATA_URL;
                model = await tmImage.load(modelURL, metadataURL);
                document.getElementById('status-text').textContent = 'Scanning...';
                document.getElementById('status-icon').className = 'fas fa-circle text-green-400 mr-1 text-xs';
                startScanning();
            } catch (e) {
                document.getElementById('status-text').textContent = 'Gagal memuat model';
                console.error(e);
            }
        }

        // Webcam
        async function initWebcam() {
            const video = document.getElementById('webcam');
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: 640, height: 480 }
                });
                video.srcObject = stream;
                await video.play();
                initModel();
            } catch (e) {
                document.getElementById('status-text').textContent = 'Kamera tidak tersedia';
            }
        }

        function showResult(type, data) {
            document.getElementById('result-idle').classList.add('hidden');
            document.getElementById('result-success').classList.add('hidden');
            document.getElementById('result-error').classList.add('hidden');

            if (type === 'success') {
                document.getElementById('result-success').classList.remove('hidden');
                document.getElementById('r-nama').textContent = data.nama;
                document.getElementById('r-nim').textContent = data.nim;
                document.getElementById('r-kelas').textContent = data.kelas;
                document.getElementById('r-mk').textContent = data.mata_kuliah;
                document.getElementById('r-jam').textContent = data.jam_masuk;
                document.getElementById('r-status').textContent = data.status;
            } else {
                document.getElementById('result-error').classList.remove('hidden');
                document.getElementById('error-msg').textContent = data.message;
                setTimeout(() => {
                    document.getElementById('result-error').classList.add('hidden');
                    document.getElementById('result-idle').classList.remove('hidden');
                }, 3000);
            }
        }

        async function startScanning() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('canvas');

            setInterval(async () => {
                if (!model || isProcessing || video.readyState !== 4) return;
                if (Date.now() - lastScanTime < 3000) return;

                isProcessing = true;
                try {
                    const prediction = await model.predict(video);
                    prediction.sort((a, b) => b.probability - a.probability);
                    const top = prediction[0];
                    const confidence = top.probability * 100;

                    if (confidence >= 70) {
                        lastScanTime = Date.now();
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0);
                        const foto = canvas.toDataURL('image/jpeg', 0.7);

                        const res = await fetch(STORE_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                face_label: top.className,
                                akurasi: confidence,
                                foto_scan: foto,
                                mata_kuliah_id: MK_ID
                            })
                        });

                        const result = await res.json();
                        if (result.success) {
                            showResult('success', result.data);
                        } else {
                            showResult('error', { message: result.message });
                        }
                    }
                } catch (e) {
                    console.error(e);
                }
                isProcessing = false;
            }, 1000);
        }

        initWebcam();
    </script>
</body>
</html>
