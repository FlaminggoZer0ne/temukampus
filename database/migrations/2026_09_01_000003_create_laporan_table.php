<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori');
            $table->foreignId('lokasi_id')->constrained('lokasi');
            $table->enum('jenis', ['hilang', 'temuan']);
            $table->string('nama_barang', 100);
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->date('tanggal_kejadian');
            $table->enum('status', ['menunggu', 'aktif', 'diklaim', 'dikembalikan', 'ditolak'])
                  ->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
