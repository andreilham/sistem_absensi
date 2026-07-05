<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Sistem Absensi Wajah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af' }
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-cloak></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-primary-800 text-white transform transition-transform duration-300 lg:translate-x-0">
            <div class="flex items-center gap-3 px-6 py-5 border-b border-primary-700">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-primary-700"></i>
                </div>
                <div>
                    <p class="font-bold text-sm leading-tight">Universitas Maju Bersama</p>
                    <p class="text-xs text-primary-200">Sistem Absensi Wajah</p>
                </div>
            </div>
            <nav class="px-3 py-4 space-y-1">
                @php
                    $links = [
                        ['route' => 'admin.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dashboard'],
                        ['route' => 'admin.mahasiswa.index', 'icon' => 'fa-user-graduate', 'label' => 'Mahasiswa'],
                        ['route' => 'admin.mahasiswa.index', 'icon' => 'fa-face-smile', 'label' => 'Data Wajah', 'match' => 'admin.mahasiswa.*'],
                        ['route' => 'admin.jadwal.index', 'icon' => 'fa-calendar', 'label' => 'Jadwal'],
                        ['route' => 'admin.mata-kuliah.index', 'icon' => 'fa-book', 'label' => 'Mata Kuliah'],
                        ['route' => 'admin.kelas.index', 'icon' => 'fa-users', 'label' => 'Kelas'],
                        ['route' => 'admin.dosen.index', 'icon' => 'fa-chalkboard-teacher', 'label' => 'Dosen'],
                        ['route' => 'admin.absensi.index', 'icon' => 'fa-clock-rotate-left', 'label' => 'Riwayat Absensi'],
                        ['route' => 'admin.laporan.index', 'icon' => 'fa-file-lines', 'label' => 'Laporan'],
                    ];
                @endphp
                @foreach($links as $link)
                    @php
                        $active = request()->routeIs($link['match'] ?? $link['route']);
                    @endphp
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition {{ $active ? 'bg-primary-600 text-white' : 'text-primary-100 hover:bg-primary-700' }}">
                        <i class="fas {{ $link['icon'] }} w-5 text-center"></i>
                        {{ $link['label'] }}
                    </a>
                @endforeach
                <a href="{{ route('attendance.scan') }}" target="_blank"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-primary-100 hover:bg-primary-700">
                    <i class="fas fa-camera w-5 text-center"></i> Halaman Absensi
                </a>
                <form action="{{ route('logout') }}" method="POST" class="pt-4 border-t border-primary-700 mt-4">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-red-300 hover:bg-primary-700 w-full">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i> Logout
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="lg:ml-64">
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-lg sm:text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500 hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <div class="w-9 h-9 bg-primary-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-primary-600"></i>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    @stack('scripts')
</body>
</html>
