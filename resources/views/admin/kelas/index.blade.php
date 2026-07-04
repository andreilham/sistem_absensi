@extends('layouts.admin')

@section('page-title', 'Data Kelas')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">Tambah Kelas</h3>
        <form method="POST" action="{{ route('admin.kelas.store') }}" class="space-y-3">
            @csrf
            <input type="text" name="nama" placeholder="Nama Kelas" required class="w-full px-3 py-2 border rounded-lg text-sm">
            <input type="text" name="jurusan" placeholder="Jurusan" required class="w-full px-3 py-2 border rounded-lg text-sm">
            <button type="submit" class="w-full py-2 bg-primary-600 text-white rounded-lg text-sm">Simpan</button>
        </form>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Jurusan</th>
                <th class="px-4 py-3 text-left">Mahasiswa</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr></thead>
            <tbody class="divide-y">
                @foreach($kelas as $k)
                    <tr><td class="px-4 py-3 font-medium">{{ $k->nama }}</td>
                        <td class="px-4 py-3">{{ $k->jurusan }}</td>
                        <td class="px-4 py-3">{{ $k->mahasiswa_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($kelas->hasPages())<div class="p-4">{{ $kelas->links() }}</div>@endif
    </div>
</div>
@endsection
