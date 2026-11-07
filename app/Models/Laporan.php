<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Laporan extends Model
{
    protected $table = 'laporan';

    protected $fillable = ['user_id', 'kategori_id', 'lokasi_id', 'jenis',
                           'nama_barang', 'deskripsi', 'foto', 'tanggal_kejadian', 'status'];

    // Cast integer agar perbandingan === (mis. user_id) aman di semua driver/versi PHP.
    protected $casts = [
        'tanggal_kejadian' => 'date',
        'user_id'          => 'integer',
        'kategori_id'      => 'integer',
        'lokasi_id'        => 'integer',
    ];

    protected $appends = ['foto_url'];

    public function user()     { return $this->belongsTo(User::class); }
    public function kategori() { return $this->belongsTo(Kategori::class); }
    public function lokasi()   { return $this->belongsTo(Lokasi::class); }
    public function klaim()    { return $this->hasMany(Klaim::class); }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? Storage::url($this->foto) : null;
    }

    /** Dipakai live search JSON: tanggal sudah diformat untuk tampilan. */
    public function getTanggalLabelAttribute(): string
    {
        return $this->tanggal_kejadian->translatedFormat('j M Y');
    }
}
