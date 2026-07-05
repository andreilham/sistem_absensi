@extends('layouts.admin')

@section('page-title', 'Data Mahasiswa')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 sm:p-6 border-b border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIM..."
                       class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                <select name="kelas_id" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
            </form>
            <a href="{{ route('admin.mahasiswa.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium">
                <i class="fas fa-plus"></i> Tambah Mahasiswa
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Foto</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left hidden sm:table-cell">NIM</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Kelas</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Jurusan</th>
                    <th class="px-4 py-3 text-left">Status Wajah</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($mahasiswa as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if($m->foto)
                                <img src="{{ asset('storage/' . $m->foto) }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-primary-600"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $m->nama }}</td>
                        <td class="px-4 py-3 hidden sm:table-cell text-gray-600">{{ $m->nim }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-gray-600">{{ $m->kelas?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 hidden lg:table-cell text-gray-600">{{ $m->jurusan ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($m->face_registered)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Tersimpan</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Belum Tersimpan</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.mahasiswa.show', $m) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.mahasiswa.edit', $m) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.mahasiswa.register-face', $m) }}" class="p-1.5 text-purple-600 hover:bg-purple-50 rounded" title="Registrasi Wajah">
                                    <i class="fas fa-camera"></i>
                                </a>
                                <form action="{{ route('admin.mahasiswa.destroy', $m) }}" method="POST" onsubmit="return confirm('Hapus mahasiswa ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada data mahasiswa</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($mahasiswa->hasPages())
        <div class="p-4 border-t">{{ $mahasiswa->links() }}</div>
    @endif
</div>
@endsection
