<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataKuliah;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Absensi::with(['mahasiswa.kelas', 'mataKuliah']);

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('mahasiswa', fn ($q) => $q->where('kelas_id', $request->kelas_id));
        }

        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->mata_kuliah_id);
        }

        $absensi = $query->latest('tanggal')->latest('jam_masuk')->paginate(15);
        $kelasList = Kelas::orderBy('nama')->get();
        $mataKuliahList = MataKuliah::orderBy('nama')->get();

        return view('admin.absensi.index', compact('absensi', 'kelasList', 'mataKuliahList'));
    }
}
