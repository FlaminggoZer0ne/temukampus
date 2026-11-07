<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klaim extends Model
{
    protected $table = 'klaim';

    protected $fillable = ['laporan_id', 'user_id', 'bukti_kepemilikan',
                           'foto_bukti', 'status', 'diverifikasi_oleh', 'catatan_petugas'];

    protected $casts = [
        'laporan_id'        => 'integer',
        'user_id'           => 'integer',
        'diverifikasi_oleh' => 'integer',
    ];

    public function laporan()  { return $this->belongsTo(Laporan::class); }
    public function user()     { return $this->belongsTo(User::class); }
    public function petugas()  { return $this->belongsTo(User::class, 'diverifikasi_oleh'); }

    public function getFotoBuktiUrlAttribute(): ?string
    {
        return $this->foto_bukti ? asset('storage/'.$this->foto_bukti) : null;
    }
}
