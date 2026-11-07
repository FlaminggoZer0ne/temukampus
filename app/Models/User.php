<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = ['nama', 'nim_nip', 'email', 'password', 'role', 'no_hp'];

    protected $hidden = ['password', 'remember_token'];

    public const ROLES = ['admin', 'petugas', 'mahasiswa'];

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }

    public function klaim()
    {
        return $this->hasMany(Klaim::class);
    }

    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isPetugas(): bool   { return $this->role === 'petugas'; }
    public function isMahasiswa(): bool { return $this->role === 'mahasiswa'; }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'   => 'Administrator',
            'petugas' => 'Petugas Keamanan',
            default   => 'Mahasiswa',
        };
    }

    /** Inisial untuk avatar (maks. 2 huruf). */
    public function getInisialAttribute(): string
    {
        $kata = preg_split('/\s+/', trim($this->nama));
        $inisial = mb_substr($kata[0] ?? '', 0, 1);
        if (count($kata) > 1) {
            $inisial .= mb_substr(end($kata), 0, 1);
        } else {
            $inisial .= mb_substr($kata[0] ?? '', 1, 1);
        }
        return mb_strtoupper($inisial);
    }
}
