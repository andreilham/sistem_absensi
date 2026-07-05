@extends('layouts.admin')

@section('page-title', 'Laporan Absensi')

@section('content')
<div class="mb-6">
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3">
        <select name="bulan" class="px-3 py-2 border rounded-lg text-sm">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" @selected($bulan == $m)>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
            @endfor
        </select>
        <select name="tahun" class="px-3 py-2 border rounded-lg text-sm">
            @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
            @endfor
        </select>
        <select name="kelas_id" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
                <option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>
        <select name="mata_kuliah_id" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">Semua MK</option>
            @foreach($mataKuliahList as $mk)
                <option value="{{ $mk->id }}" @selected($mataKuliahId == $mk->id)>{{ $mk->nama }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm">Tampilkan</button>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-primary-600">{{ $totalMahasiswa }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Mahasiswa</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $totalHadir }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Hadir</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-yellow-600">{{ $totalTerlambat }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Terlambat</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-blue-600">{{ $rataRata }}%</p>
        <p class="text-sm text-gray-500 mt-1">Rata-rata Kehadiran</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="font-semibold text-gray-800 mb-4">Ringkasan Kehadiran</h3>
    @php $total = max(1, $totalHadir + $totalTerlambat); @endphp
    <div class="space-y-4">
        <div>
            <div class="flex justify-between text-sm mb-1"><span>Hadir</span><span>{{ round($totalHadir/$total*100) }}%</span></div>
            <div class="w-full bg-gray-200 rounded-full h-3"><div class="bg-green-500 h-3 rounded-full" style="width:{{ round($totalHadir/$total*100) }}%"></div></div>
        </div>
        <div>
            <div class="flex justify-between text-sm mb-1"><span>Terlambat</span><span>{{ round($totalTerlambat/$total*100) }}%</span></div>
            <div class="w-full bg-gray-200 rounded-full h-3"><div class="bg-yellow-500 h-3 rounded-full" style="width:{{ round($totalTerlambat/$total*100) }}%"></div></div>
        </div>
    </div>
</div>
@endsection
