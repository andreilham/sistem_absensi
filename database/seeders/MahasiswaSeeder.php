<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run()
    {
        $kelas = Kelas::create(['nama' => 'TI-6A', 'jurusan' => 'Teknik Informatika']);
        Kelas::create(['nama' => 'TI-6B', 'jurusan' => 'Teknik Informatika']);

        $dosen = Dosen::create([
            'nama' => 'Dr. Ahmad Wijaya, M.Kom',
            'nip' => '198001012010011001',
            'email' => 'ahmad@umb.ac.id',
        ]);

        $mk = MataKuliah::create([
            'kode' => 'IF601',
            'nama' => 'Kecerdasan Buatan',
            'dosen_id' => $dosen->id,
        ]);

        MataKuliah::create([
            'kode' => 'IF602',
            'nama' => 'Pemrograman Web',
            'dosen_id' => $dosen->id,
        ]);

        $hariMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $hari = $hariMap[now()->englishDayOfWeek] ?? 'Senin';

        Jadwal::create([
            'kelas_id' => $kelas->id,
            'mata_kuliah_id' => $mk->id,
            'hari' => $hari,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '23:59:00',
            'batas_terlambat' => '08:15:00',
        ]);

        $labels = [
            ['nama' => 'Yahya', 'nim' => '2211501001', 'face_label' => 'yahya'],
            ['nama' => 'Timbul', 'nim' => '2211501002', 'face_label' => 'timbul'],
            ['nama' => 'Samhan', 'nim' => '2211501003', 'face_label' => 'samhan'],
            ['nama' => 'Nino Restu', 'nim' => '2211501004', 'face_label' => 'Nino Restu'],
            ['nama' => 'Anggita Ari Nugroho', 'nim' => '2211501005', 'face_label' => 'Anggita Ari Nugroho'],
            ['nama' => 'Alfian Putra', 'nim' => '2211501006', 'face_label' => 'Alfian putra'],
            ['nama' => 'Pradigdya Rafly', 'nim' => '2211501007', 'face_label' => 'Pradigdya Rafly'],
            ['nama' => 'Basri Nur', 'nim' => '2211501008', 'face_label' => 'Basri Nur'],
            ['nama' => 'Oktabian', 'nim' => '2211501009', 'face_label' => 'oktabian'],
        ];

        foreach ($labels as $m) {
            Mahasiswa::create([
                'nama' => $m['nama'],
                'nim' => $m['nim'],
                'kelas_id' => $kelas->id,
                'jurusan' => 'Teknik Informatika',
                'face_label' => $m['face_label'],
                'face_registered' => true,
            ]);
        }
    }
}
