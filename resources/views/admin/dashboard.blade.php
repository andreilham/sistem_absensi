@extends('layouts.admin')

@section('page-title', 'Dashboard Admin')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-primary-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Mahasiswa</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalMahasiswa }}</p>
            </div>
            <div class="w-12 h-12 bg-primary-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-graduate text-primary-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Hadir Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $hadirHariIni }}</p>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Belum Hadir</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $belumHadir }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Terlambat</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $terlambatHariIni }}</p>
            </div>
            <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Hari Ini</h2>
        <div class="space-y-4">
            @php $persen = $totalMahasiswa > 0 ? round(($hadirHariIni / $totalMahasiswa) * 100) : 0; @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Kehadiran Keseluruhan</span>
                    <span class="font-medium text-primary-600">{{ $persen }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-primary-600 h-3 rounded-full transition-all" style="width: {{ $persen }}%"></div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center pt-2">
                <div class="p-3 bg-green-50 rounded-lg">
                    <p class="text-lg font-bold text-green-700">{{ $hadirHariIni - $terlambatHariIni }}</p>
                    <p class="text-xs text-green-600">Tepat Waktu</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg">
                    <p class="text-lg font-bold text-yellow-700">{{ $terlambatHariIni }}</p>
                    <p class="text-xs text-yellow-600">Terlambat</p>
                </div>
                <div class="p-3 bg-red-50 rounded-lg">
                    <p class="text-lg font-bold text-red-700">{{ $belumHadir }}</p>
                    <p class="text-xs text-red-600">Belum Hadir</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h2>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse($recentActivity as $item)
                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-check text-primary-600 text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $item->mahasiswa->nama }}</p>
                        <p class="text-xs text-gray-500">{{ $item->mataKuliah->nama ?? '-' }} · {{ $item->jam_masuk }}</p>
                        <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full {{ $item->status === 'hadir' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $item->status_label }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-8">Belum ada absensi hari ini</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
