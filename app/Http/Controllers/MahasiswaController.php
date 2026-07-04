<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\FacePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with('kelas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        $mahasiswa = $query->latest()->paginate(10);
        $kelasList = Kelas::orderBy('nama')->get();
        $jurusanList = Mahasiswa::distinct()->pluck('jurusan')->filter();

        return view('admin.mahasiswa.index', compact('mahasiswa', 'kelasList', 'jurusanList'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('nama')->get();

        return view('admin.mahasiswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|unique:mahasiswa,nim',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan' => 'nullable|string|max:255',
            'face_label' => 'nullable|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mahasiswa', 'public');
        }

        Mahasiswa::create($data);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['kelas', 'facePhotos']);

        return view('admin.mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $kelasList = Kelas::orderBy('nama')->get();

        return view('admin.mahasiswa.edit', compact('mahasiswa', 'kelasList'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|unique:mahasiswa,nim,' . $mahasiswa->id,
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan' => 'nullable|string|max:255',
            'face_label' => 'nullable|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($mahasiswa->foto) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('mahasiswa', 'public');
        }

        $mahasiswa->update($data);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        if ($mahasiswa->foto) {
            Storage::disk('public')->delete($mahasiswa->foto);
        }

        foreach ($mahasiswa->facePhotos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $mahasiswa->delete();

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil dihapus.');
    }

    public function registerFace(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load('facePhotos');

        return view('admin.mahasiswa.register-face', compact('mahasiswa'));
    }

    public function storeFacePhoto(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'photo' => 'required|string',
        ]);

        $image = $request->photo;
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $filename = 'face/' . $mahasiswa->id . '_' . time() . '.jpg';
        Storage::disk('public')->put($filename, base64_decode($image));

        FacePhoto::create([
            'mahasiswa_id' => $mahasiswa->id,
            'path' => $filename,
        ]);

        $count = $mahasiswa->facePhotos()->count();
        if ($count >= 10) {
            $mahasiswa->update(['face_registered' => true]);
        }

        return response()->json([
            'success' => true,
            'count' => $count,
            'registered' => $count >= 10,
        ]);
    }

    public function completeFaceRegistration(Mahasiswa $mahasiswa)
    {
        $mahasiswa->update(['face_registered' => true]);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Registrasi wajah berhasil disimpan.');
    }
}
