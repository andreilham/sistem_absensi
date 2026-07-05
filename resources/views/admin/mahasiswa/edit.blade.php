@extends('layouts.admin')

@section('page-title', 'Edit Mahasiswa')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm p-6">
    <form action="{{ route('admin.mahasiswa.update', $mahasiswa) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
            <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="kelas_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Pilih Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" @selected(old('kelas_id', $mahasiswa->kelas_id) == $k->id)>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <input type="text" name="jurusan" value="{{ old('jurusan', $mahasiswa->jurusan) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Label Wajah</label>
            <input type="text" name="face_label" value="{{ old('face_label', $mahasiswa->face_label) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
            @if($mahasiswa->foto)
                <img src="{{ asset('storage/' . $mahasiswa->foto) }}" class="w-16 h-16 rounded-full object-cover mb-2">
            @endif
            <input type="file" name="foto" accept="image/*"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">Update</button>
            <a href="{{ route('admin.mahasiswa.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">Batal</a>
        </div>
    </form>
</div>
@endsection
