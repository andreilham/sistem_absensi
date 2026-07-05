@extends('layouts.admin')

@section('page-title', 'Detail Mahasiswa')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm p-6">
    <div class="flex flex-col sm:flex-row items-start gap-6">
        @if($mahasiswa->foto)
            <img src="{{ asset('storage/' . $mahasiswa->foto) }}" class="w-24 h-24 rounded-full object-cover">
        @else
            <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-primary-600 text-3xl"></i>
            </div>
        @endif
        <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-800">{{ $mahasiswa->nama }}</h2>
            <p class="text-gray-500">{{ $mahasiswa->nim }}</p>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-500">Kelas:</span> {{ $mahasiswa->kelas?->nama ?? '-' }}</div>
                <div><span class="text-gray-500">Jurusan:</span> {{ $mahasiswa->jurusan ?? '-' }}</div>
                <div><span class="text-gray-500">Label Wajah:</span> {{ $mahasiswa->face_label ?? '-' }}</div>
                <div>
                    <span class="text-gray-500">Status Wajah:</span>
                    @if($mahasiswa->face_registered)
                        <span class="text-green-600 font-medium">Tersimpan</span>
                    @else
                        <span class="text-red-600 font-medium">Belum Tersimpan</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.mahasiswa.register-face', $mahasiswa) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm">
            <i class="fas fa-camera mr-1"></i> Registrasi Wajah
        </a>
        <a href="{{ route('admin.mahasiswa.edit', $mahasiswa) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <a href="{{ route('admin.mahasiswa.index') }}" class="px-4 py-2 bg-gray-100 rounded-lg text-sm">Kembali</a>
    </div>
</div>
@endsection
