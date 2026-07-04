<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataKuliah;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::with(['kelas', 'mataKuliah'])->orderBy('hari')->paginate(10);
        $kelasList = Kelas::orderBy('nama')->get();
        $mataKuliahList = MataKuliah::orderBy('nama')->get();

        return view('admin.jadwal.index', compact('jadwal', 'kelasList', 'mataKuliahList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'batas_terlambat' => 'nullable',
        ]);

        Jadwal::create($request->only(
            'kelas_id', 'mata_kuliah_id', 'hari',
            'jam_mulai', 'jam_selesai', 'batas_terlambat'
        ));

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'batas_terlambat' => 'nullable',
        ]);

        $jadwal->update($request->only(
            'kelas_id', 'mata_kuliah_id', 'hari',
            'jam_mulai', 'jam_selesai', 'batas_terlambat'
        ));

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
