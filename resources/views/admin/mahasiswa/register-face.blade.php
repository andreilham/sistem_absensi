@extends('layouts.admin')

@section('page-title', 'Registrasi Wajah')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Info Mahasiswa</h3>
        <div class="space-y-2 text-sm">
            <p><span class="text-gray-500">Nama:</span> <strong>{{ $mahasiswa->nama }}</strong></p>
            <p><span class="text-gray-500">NIM:</span> {{ $mahasiswa->nim }}</p>
            <p><span class="text-gray-500">Kelas:</span> {{ $mahasiswa->kelas?->nama ?? '-' }}</p>
            <p><span class="text-gray-500">Label AI:</span> {{ $mahasiswa->face_label ?? '-' }}</p>
        </div>
        <div class="mt-4">
            <div class="flex justify-between text-sm mb-1">
                <span>Progress Foto</span>
                <span id="photo-count">{{ $mahasiswa->facePhotos->count() }}/15</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-primary-600 h-2 rounded-full transition-all"
                     style="width: {{ min(100, ($mahasiswa->facePhotos->count() / 15) * 100) }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Ambil foto dari berbagai sudut (min. 10 foto)</p>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <div class="relative aspect-video bg-gray-900 rounded-xl overflow-hidden mb-4">
            <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
            <canvas id="canvas" class="hidden"></canvas>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-48 h-60 border-2 border-primary-400 rounded-2xl opacity-70"></div>
            </div>
        </div>

        <div id="photo-grid" class="grid grid-cols-5 sm:grid-cols-8 gap-2 mb-4 min-h-[60px]">
            @foreach($mahasiswa->facePhotos as $photo)
                <img src="{{ asset('storage/' . $photo->path) }}" class="w-full aspect-square object-cover rounded-lg">
            @endforeach
        </div>

        <div class="flex flex-wrap gap-3">
            <button id="btn-capture" type="button"
                    class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                <i class="fas fa-camera mr-1"></i> Ambil Foto
            </button>
            <form action="{{ route('admin.mahasiswa.complete-face', $mahasiswa) }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    Lanjutkan
                </button>
            </form>
            <a href="{{ route('admin.mahasiswa.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg">Batal</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const video = document.getElementById('webcam');
const canvas = document.getElementById('canvas');
const photoGrid = document.getElementById('photo-grid');
const photoCount = document.getElementById('photo-count');
const progressBar = document.getElementById('progress-bar');
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const storeUrl = @json(route('admin.mahasiswa.store-face-photo', $mahasiswa));

navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } })
    .then(stream => { video.srcObject = stream; })
    .catch(err => alert('Tidak dapat mengakses kamera: ' + err.message));

document.getElementById('btn-capture').addEventListener('click', async () => {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const photo = canvas.toDataURL('image/jpeg', 0.8);

    const res = await fetch(storeUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ photo })
    });
    const data = await res.json();
    if (data.success) {
        const img = document.createElement('img');
        img.src = photo;
        img.className = 'w-full aspect-square object-cover rounded-lg';
        photoGrid.appendChild(img);
        photoCount.textContent = data.count + '/15';
        progressBar.style.width = Math.min(100, (data.count / 15) * 100) + '%';
    }
});
</script>
@endpush
