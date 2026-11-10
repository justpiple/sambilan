<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lowongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('restrict');
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('restrict');
            $table->string('judul', 150);
            $table->enum('tipe', ['magang', 'paruh_waktu']);
            $table->enum('sistem_kerja', ['onsite', 'remote', 'hybrid']);
            $table->string('lokasi', 150);
            $table->unsignedTinyInteger('jam_per_minggu');
            $table->unsignedInteger('kompensasi')->nullable();
            $table->text('deskripsi');
            $table->text('persyaratan');
            $table->string('link_pendaftaran', 255)->nullable();
            $table->string('kontak_pendaftaran', 100)->nullable();
            $table->date('tenggat');
            $table->enum('status', ['buka', 'ditutup'])->default('buka');
            $table->timestamps();

            $table->index(['status', 'tenggat']);
            $table->index('tipe');
            $table->index('sistem_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lowongans');
    }
};
