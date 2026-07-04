@extends('layouts.admin')

@section('page-title', 'Jadwal Kuliah')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">Tambah Jadwal</h3>
        <form method="POST" action="{{ route('admin.jadwal.store') }}" class="space-y-3">
            @csrf
            <select name="kelas_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                <option value="">Pilih Kelas</option>
                @foreach($kelasList as $k)<option value="{{ $k->id }}">{{ $k->nama }}</option>@endforeach
            </select>
            <select name="mata_kuliah_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                <option value="">Pilih MK</option>
                @foreach($mataKuliahList as $mk)<option value="{{ $mk->id }}">{{ $mk->nama }}</option>@endforeach
            </select>
            <select name="hari" required class="w-full px-3 py-2 border rounded-lg text-sm">
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
            <input type="time" name="jam_mulai" required class="w-full px-3 py-2 border rounded-lg text-sm">
            <input type="time" name="jam_selesai" required class="w-full px-3 py-2 border rounded-lg text-sm">
            <input type="time" name="batas_terlambat" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Batas terlambat">
            <button type="submit" class="w-full py-2 bg-primary-600 text-white rounded-lg text-sm">Simpan</button>
        </form>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-3 text-left">Hari</th>
                <th class="px-4 py-3 text-left">Kelas</th>
                <th class="px-4 py-3 text-left">MK</th>
                <th class="px-4 py-3 text-left">Jam</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr></thead>
            <tbody class="divide-y">
                @foreach($jadwal as $j)
                    <tr>
                        <td class="px-4 py-3">{{ $j->hari }}</td>
                        <td class="px-4 py-3">{{ $j->kelas->nama }}</td>
                        <td class="px-4 py-3">{{ $j->mataKuliah->nama }}</td>
                        <td class="px-4 py-3">{{ substr($j->jam_mulai,0,5) }}-{{ substr($j->jam_selesai,0,5) }}</td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.jadwal.destroy', $j) }}" method="POST" onsubmit="return confirm('Hapus?')">
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
