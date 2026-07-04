<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceScanController extends Controller
{
    public function index()
    {
        $activeJadwal = $this->getActiveJadwal();

        return view('attendance.scan', [
            'activeJadwal' => $activeJadwal,
            'modelUrl' => asset('models/model.json'),
            'metadataUrl' => asset('models/metadata.json'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'face_label' => 'required|string',
            'akurasi' => 'required|numeric|min:0|max:100',
            'foto_scan' => 'nullable|string',
            'mata_kuliah_id' => 'nullable|exists:mata_kuliah,id',
        ]);

        if ($data['akurasi'] < 70) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak dikenali. Pastikan wajah berada di dalam frame.',
            ], 422);
        }

        $mahasiswa = Mahasiswa::where('face_label', $data['face_label'])
            ->orWhere('nama', $data['face_label'])
            ->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan dalam database.',
            ], 404);
        }

        $today = Carbon::today();
        $now = Carbon::now();
        $activeJadwal = $this->getActiveJadwal();

        $mataKuliahId = $data['mata_kuliah_id']
            ?? ($activeJadwal?->mata_kuliah_id ?? MataKuliah::first()?->id);

        if (!$mataKuliahId) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada mata kuliah aktif saat ini.',
            ], 422);
        }

        $existing = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('mata_kuliah_id', $mataKuliahId)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi hari ini.',
                'data' => $this->formatResponse($mahasiswa, $existing, $activeJadwal),
            ], 409);
        }

        $status = 'hadir';
        if ($activeJadwal && $activeJadwal->batas_terlambat) {
            $batas = Carbon::parse($activeJadwal->batas_terlambat);
            if ($now->format('H:i:s') > $batas->format('H:i:s')) {
                $status = 'terlambat';
            }
        }

        $fotoPath = null;
        if (!empty($data['foto_scan'])) {
            $image = str_replace('data:image/jpeg;base64,', '', $data['foto_scan']);
            $image = str_replace(' ', '+', $image);
            $fotoPath = 'absensi/' . $mahasiswa->id . '_' . time() . '.jpg';
            Storage::disk('public')->put($fotoPath, base64_decode($image));
        }

        $absensi = Absensi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliahId,
            'jadwal_id' => $activeJadwal?->id,
            'tanggal' => $today,
            'jam_masuk' => $now->format('H:i:s'),
            'status' => $status,
            'foto_scan' => $fotoPath,
            'akurasi' => $data['akurasi'],
        ]);

        $absensi->load('mataKuliah');

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil!',
            'data' => $this->formatResponse($mahasiswa, $absensi, $activeJadwal),
        ]);
    }

    private function getActiveJadwal(): ?Jadwal
    {
        $hariMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $hari = $hariMap[Carbon::now()->englishDayOfWeek] ?? Carbon::now()->locale('id')->dayName;
        $now = Carbon::now()->format('H:i:s');

        return Jadwal::with(['mataKuliah', 'kelas'])
            ->where('hari', $hari)
            ->where('jam_mulai', '<=', $now)
            ->where('jam_selesai', '>=', $now)
            ->first();
    }

    private function formatResponse(Mahasiswa $mahasiswa, Absensi $absensi, ?Jadwal $jadwal): array
    {
        $mahasiswa->load('kelas');
        $absensi->load('mataKuliah');

        return [
            'nama' => $mahasiswa->nama,
            'nim' => $mahasiswa->nim,
            'kelas' => $mahasiswa->kelas?->nama ?? '-',
            'mata_kuliah' => $absensi->mataKuliah?->nama ?? $jadwal?->mataKuliah?->nama ?? '-',
            'jam_masuk' => Carbon::parse($absensi->jam_masuk)->format('H:i'),
            'status' => $absensi->status_label,
            'akurasi' => number_format($absensi->akurasi, 1) . '%',
        ];
    }
}
