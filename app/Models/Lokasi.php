<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasi';

    protected $fillable = ['nama_lokasi', 'keterangan'];

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }
}
