<?php

namespace Database\Seeders;

use App\Models\KartuKeluarga;
use Illuminate\Database\Seeder;

class KartuKeluargaSeeder extends Seeder
{
    public function run()
    {
        KartuKeluarga::create([
            'no_kk' => '1234567890123456',
            'nama_kepala' => 'Yuvens',
            'nik_kepala' => '1234567890123456',
            'alamat' => 'Jalan Rejosari No. 1',
            'rt' => '01',
            'rw' => '03',
            'kelurahan' => 'Rejosari',
            'status_ekonomi' => 'Sejahtera',
            'catatan' => 'Financial Freedom',
            'anggota' => [
                ['nama' => 'Yuvens', 'nik' => '1234567890123456', 'hubungan' => 'Kepala Keluarga', 'jk' => 'L', 'pekerjaan' => 'Wiraswasta']
            ]
        ]);
        
        KartuKeluarga::create([
            'no_kk' => '1234567890123457',
            'nama_kepala' => 'Siti Aminah',
            'nik_kepala' => '1234567890123457',
            'alamat' => 'Jalan Rejosari No. 2',
            'rt' => '01',
            'rw' => '03',
            'kelurahan' => 'Rejosari',
            'status_ekonomi' => 'Tidak Miskin',
            'anggota' => [
                ['nama' => 'Siti Aminah', 'nik' => '1234567890123457', 'hubungan' => 'Kepala Keluarga', 'jk' => 'P', 'pekerjaan' => 'Pedagang']
            ]
        ]);
    }
}