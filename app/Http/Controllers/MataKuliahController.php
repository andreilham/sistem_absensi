<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index()
    {
        $mataKuliah = MataKuliah::with('dosen')->orderBy('nama')->paginate(10);
        $dosenList = Dosen::orderBy('nama')->get();

        return view('admin.mata-kuliah.index', compact('mataKuliah', 'dosenList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:mata_kuliah,kode',
            'nama' => 'required|string|max:255',
            'dosen_id' => 'nullable|exists:dosen,id',
        ]);

        MataKuliah::create($request->only('kode', 'nama', 'dosen_id'));

        return back()->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'kode' => 'required|string|unique:mata_kuliah,kode,' . $mataKuliah->id,
            'nama' => 'required|string|max:255',
            'dosen_id' => 'nullable|exists:dosen,id',
        ]);

        $mataKuliah->update($request->only('kode', 'nama', 'dosen_id'));

        return back()->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        $mataKuliah->delete();

        return back()->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
