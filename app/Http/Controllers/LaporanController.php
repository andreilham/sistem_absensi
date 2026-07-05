<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $kelasId = $request->get('kelas_id');
        $mataKuliahId = $request->get('mata_kuliah_id');

        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $mahasiswaQuery = Mahasiswa::query();
        if ($kelasId) {
            $mahasiswaQuery->where('kelas_id', $kelasId);
        }
        $totalMahasiswa = $mahasiswaQuery->count();

        $absensiQuery = Absensi::whereBetween('tanggal', [$start, $end]);
        if ($kelasId) {
            $absensiQuery->whereHas('mahasiswa', fn ($q) => $q->where('kelas_id', $kelasId));
        }
        if ($mataKuliahId) {
            $absensiQuery->where('mata_kuliah_id', $mataKuliahId);
        }

        $totalHadir = (clone $absensiQuery)->where('status', 'hadir')->count();
        $totalTerlambat = (clone $absensiQuery)->where('status', 'terlambat')->count();
        $totalAbsensi = $totalHadir + $totalTerlambat;
        $rataRata = $totalMahasiswa > 0
            ? round(($totalAbsensi / max(1, $totalMahasiswa * $start->daysInMonth)) * 100, 1)
            : 0;

        $kelasList = Kelas::orderBy('nama')->get();
        $mataKuliahList = MataKuliah::orderBy('nama')->get();

        return view('admin.laporan.index', compact(
            'bulan', 'tahun', 'kelasId', 'mataKuliahId',
            'totalMahasiswa', 'totalHadir', 'totalTerlambat', 'rataRata',
            'kelasList', 'mataKuliahList'
        ));
    }
}
