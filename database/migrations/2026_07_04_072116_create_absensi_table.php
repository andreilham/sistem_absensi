<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwal')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk');
            $table->enum('status', ['hadir', 'terlambat', 'tidak_hadir'])->default('hadir');
            $table->string('foto_scan')->nullable();
            $table->decimal('akurasi', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'mata_kuliah_id', 'tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi');
    }
};
