<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuKeluarga extends Model
{
    protected $table = 'kartu_keluarga';

    protected $fillable = [
        'no_kk', 'nama_kepala', 'nik_kepala', 'alamat', 'rt', 'rw',
        'kelurahan', 'kecamatan', 'kota', 'kode_pos', 'foto_url',
        'foto_drive_id', 'status_ekonomi', 'catatan', 'anggota'
    ];

    protected $casts = [
        'anggota' => 'array',
    ];

    public function getJumlahJiwaAttribute()
    {
        return ($this->anggota ? count($this->anggota) : 0) + 1;
    }
}