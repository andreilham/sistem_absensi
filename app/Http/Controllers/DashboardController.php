<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalMahasiswa = Mahasiswa::count();
        $hadirHariIni = Absensi::whereDate('tanggal', $today)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->distinct('mahasiswa_id')
            ->count('mahasiswa_id');
        $terlambatHariIni = Absensi::whereDate('tanggal', $today)
            ->where('status', 'terlambat')
            ->count();
        $belumHadir = max(0, $totalMahasiswa - $hadirHariIni);

        $recentActivity = Absensi::with(['mahasiswa', 'mataKuliah'])
            ->whereDate('tanggal', $today)
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'hadirHariIni',
            'belumHadir',
            'terlambatHariIni',
            'recentActivity'
        ));
    }
}
