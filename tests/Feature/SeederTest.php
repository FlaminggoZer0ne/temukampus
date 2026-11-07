<?php

namespace Tests\Feature;

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('SEED_PASSWORD'); // hapus variabel agar tidak bocor ke test lain
        parent::tearDown();
    }

    public function test_seeder_aman_dijalankan_dua_kali()
    {
        $this->seed();
        $users = User::count();
        $laporan = Laporan::count();

        $this->seed(); // tidak boleh error (email unik) dan tidak menggandakan data

        $this->assertSame($users, User::count());
        $this->assertSame($laporan, Laporan::count());
    }

    public function test_password_akun_demo_bisa_diatur_lewat_seed_password()
    {
        putenv('SEED_PASSWORD=RahasiaServer-2026');
        $this->seed();

        foreach (User::all() as $u) {
            $this->assertTrue(Hash::check('RahasiaServer-2026', $u->password), $u->email);
            $this->assertFalse(Hash::check('password', $u->password), $u->email);
        }
    }

    public function test_tanpa_seed_password_memakai_password_bawaan()
    {
        $this->seed();

        $this->assertTrue(Hash::check('password', User::where('email', 'admin@temukampus.test')->value('password')));
    }
}
