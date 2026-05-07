<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kartu_keluarga', function (Blueprint $table) {
            // Cek apakah kolom sudah ada, jika belum tambahkan
            if (!Schema::hasColumn('kartu_keluarga', 'no_kk')) {
                $table->string('no_kk', 16)->unique()->after('id');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'nama_kepala')) {
                $table->string('nama_kepala')->after('no_kk');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'nik_kepala')) {
                $table->string('nik_kepala', 16)->nullable()->after('nama_kepala');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'alamat')) {
                $table->text('alamat')->nullable()->after('nik_kepala');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'rt')) {
                $table->string('rt', 5)->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'rw')) {
                $table->string('rw', 5)->nullable()->after('rt');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'kelurahan')) {
                $table->string('kelurahan')->nullable()->after('rw');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'kecamatan')) {
                $table->string('kecamatan')->nullable()->after('kelurahan');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'kota')) {
                $table->string('kota')->nullable()->after('kecamatan');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'kode_pos')) {
                $table->string('kode_pos', 5)->nullable()->after('kota');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'foto_url')) {
                $table->string('foto_url')->nullable()->after('kode_pos');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'foto_drive_id')) {
                $table->string('foto_drive_id')->nullable()->after('foto_url');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'status_ekonomi')) {
                $table->string('status_ekonomi')->nullable()->after('foto_drive_id');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'catatan')) {
                $table->text('catatan')->nullable()->after('status_ekonomi');
            }
            if (!Schema::hasColumn('kartu_keluarga', 'anggota')) {
                $table->json('anggota')->nullable()->after('catatan');
            }
        });
    }

    public function down()
    {
        // Rollback tidak perlu karena hanya menambah kolom
    }
};