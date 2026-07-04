<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index()
    {
        $dosen = Dosen::withCount('mataKuliah')->orderBy('nama')->paginate(10);

        return view('admin.dosen.index', compact('dosen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|unique:dosen,nip',
            'email' => 'nullable|email',
        ]);

        Dosen::create($request->only('nama', 'nip', 'email'));

        return back()->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|unique:dosen,nip,' . $dosen->id,
            'email' => 'nullable|email',
        ]);

        $dosen->update($request->only('nama', 'nip', 'email'));

        return back()->with('success', 'Dosen berhasil diperbarui.');
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->delete();

        return back()->with('success', 'Dosen berhasil dihapus.');
    }
}
