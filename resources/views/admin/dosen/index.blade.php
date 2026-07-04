@extends('layouts.admin')

@section('page-title', 'Data Dosen')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">Tambah Dosen</h3>
        <form method="POST" action="{{ route('admin.dosen.store') }}" class="space-y-3">
            @csrf
            <input type="text" name="nama" placeholder="Nama Dosen" required class="w-full px-3 py-2 border rounded-lg text-sm">
            <input type="text" name="nip" placeholder="NIP" required class="w-full px-3 py-2 border rounded-lg text-sm">
            <input type="email" name="email" placeholder="Email" class="w-full px-3 py-2 border rounded-lg text-sm">
            <button type="submit" class="w-full py-2 bg-primary-600 text-white rounded-lg text-sm">Simpan</button>
        </form>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">NIP</th>
                <th class="px-4 py-3 text-left hidden sm:table-cell">Email</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr></thead>
            <tbody class="divide-y">
                @foreach($dosen as $d)
                    <tr><td class="px-4 py-3 font-medium">{{ $d->nama }}</td>
                        <td class="px-4 py-3">{{ $d->nip }}</td>
                        <td class="px-4 py-3 hidden sm:table-cell">{{ $d->email ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.dosen.destroy', $d) }}" method="POST" onsubmit="return confirm('Hapus?')">
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
