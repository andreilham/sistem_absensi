@extends('layouts.admin')

@section('page-title', 'Mata Kuliah')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">Tambah Mata Kuliah</h3>
        <form method="POST" action="{{ route('admin.mata-kuliah.store') }}" class="space-y-3">
            @csrf
            <input type="text" name="kode" placeholder="Kode MK" required class="w-full px-3 py-2 border rounded-lg text-sm">
            <input type="text" name="nama" placeholder="Nama MK" required class="w-full px-3 py-2 border rounded-lg text-sm">
            <select name="dosen_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                <option value="">Pilih Dosen</option>
                @foreach($dosenList as $d)
                    <option value="{{ $d->id }}">{{ $d->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="w-full py-2 bg-primary-600 text-white rounded-lg text-sm">Simpan</button>
        </form>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left hidden sm:table-cell">Dosen</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr></thead>
            <tbody class="divide-y">
                @foreach($mataKuliah as $mk)
                    <tr><td class="px-4 py-3">{{ $mk->kode }}</td>
                        <td class="px-4 py-3 font-medium">{{ $mk->nama }}</td>
                        <td class="px-4 py-3 hidden sm:table-cell">{{ $mk->dosen?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.mata-kuliah.destroy', $mk) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
