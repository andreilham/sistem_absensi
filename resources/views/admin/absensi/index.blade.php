@extends('layouts.admin')

@section('page-title', 'Riwayat Absensi')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 sm:p-6 border-b">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                   class="px-3 py-2 border rounded-lg text-sm">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                   class="px-3 py-2 border rounded-lg text-sm">
            <select name="kelas_id" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
            <select name="mata_kuliah_id" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">Semua MK</option>
                @foreach($mataKuliahList as $mk)
                    <option value="{{ $mk->id }}" @selected(request('mata_kuliah_id') == $mk->id)>{{ $mk->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm">Filter</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Mata Kuliah</th>
                    <th class="px-4 py-3 text-left hidden sm:table-cell">Tanggal</th>
                    <th class="px-4 py-3 text-left">Jam</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Foto</th>
                    <th class="px-4 py-3 text-left">Akurasi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($absensi as $i => $a)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $absensi->firstItem() + $i }}</td>
                        <td class="px-4 py-3 font-medium">{{ $a->mahasiswa->nama }}</td>
                        <td class="px-4 py-3 hidden md:table-cell">{{ $a->mataKuliah->nama }}</td>
                        <td class="px-4 py-3 hidden sm:table-cell">{{ $a->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ substr($a->jam_masuk, 0, 5) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $a->status === 'hadir' ? 'bg-green-100 text-green-700' : ($a->status === 'terlambat' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $a->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($a->foto_scan)
                                <img src="{{ asset('storage/' . $a->foto_scan) }}" class="w-10 h-10 rounded object-cover">
                            @else - @endif
                        </td>
                        <td class="px-4 py-3">{{ $a->akurasi ? number_format($a->akurasi, 1) . '%' : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada data absensi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($absensi->hasPages())<div class="p-4">{{ $absensi->links() }}</div>@endif
</div>
@endsection
