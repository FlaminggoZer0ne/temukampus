<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Klaim;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TemuKampusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function as(string $role)
    {
        return $this->actingAs(User::where('role', $role)->first());
    }

    public function test_public_pages()
    {
        $this->get('/')->assertOk()->assertSee('Barang temuan terbaru')->assertSee('Botol Minum Biru');
        $this->get('/login')->assertOk()->assertSee('Masuk ke akun');
        $this->get('/register')->assertOk();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_login_and_register()
    {
        $this->post('/login', ['email' => 'admin@temukampus.test', 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => 'admin@temukampus.test', 'password' => 'password'])->assertRedirect('/dashboard');
        auth()->logout();
        $this->post('/register', ['nama' => 'Budi Baru', 'nim_nip' => '1301', 'email' => 'budi@x.test',
            'password' => 'rahasia1', 'password_confirmation' => 'rahasia1'])->assertRedirect('/dashboard');
        $this->assertSame('mahasiswa', User::where('email', 'budi@x.test')->value('role'));
    }

    public function test_every_page_renders_per_role()
    {
        $pages = [
            'admin' => ['/dashboard', '/admin/users', '/admin/users?role=petugas', '/admin/kategori', '/admin/lokasi',
                        '/admin/laporan', '/admin/laporan?status=aktif', '/laporan', '/laporan/1', '/profil',
                        '/api/statistik', '/api/laporan/search'],
            'petugas' => ['/dashboard', '/petugas/laporan', '/petugas/klaim', '/petugas/klaim?tab=disetujui',
                          '/petugas/serah-terima', '/laporan', '/laporan/create', '/laporan/saya', '/api/statistik'],
            'mahasiswa' => ['/dashboard', '/laporan', '/laporan/create', '/laporan/saya', '/klaim', '/laporan/1',
                            '/profil', '/api/laporan/search?q=botol&jenis=temuan'],
        ];
        foreach ($pages as $role => $urls) {
            foreach ($urls as $u) {
                $this->as($role)->get($u)->assertOk();
            }
        }
    }

    public function test_role_guards()
    {
        $this->as('mahasiswa')->get('/admin/users')->assertForbidden();
        $this->as('mahasiswa')->get('/petugas/klaim')->assertForbidden();
        $this->as('mahasiswa')->getJson('/api/statistik')->assertForbidden();
        $this->as('petugas')->get('/admin/kategori')->assertForbidden();
        $this->as('petugas')->get('/klaim')->assertForbidden();
        $this->as('admin')->get('/laporan/create')->assertForbidden();
        $this->as('admin')->patchJson('/api/laporan/1/status', ['status' => 'aktif'])->assertForbidden();
    }

    public function test_search_json_and_mahasiswa_sees_only_aktif()
    {
        $r = $this->as('mahasiswa')->getJson('/api/laporan/search')->assertOk()->json();
        $this->assertNotEmpty($r['data']);
        foreach ($r['data'] as $row) {
            $this->assertSame('aktif', $row['status']);
            $this->assertArrayHasKey('tanggal_label', $row);
        }
        $this->as('mahasiswa')->getJson('/api/laporan/search?q=zzzz')->assertJsonPath('total', 0);
        $all = $this->as('petugas')->getJson('/api/laporan/search')->json('total');
        $this->assertGreaterThan($r['total'], $all);
        $this->as('mahasiswa')->get('/laporan/7')->assertNotFound(); // Earphone: menunggu milik orang lain
    }

    public function test_full_claim_flow()
    {
        Storage::fake('public');
        $mhs = User::where('email', 'mahasiswa@temukampus.test')->first();

        $this->actingAs($mhs)->post('/laporan', [
            'jenis' => 'temuan', 'nama_barang' => 'Tas Biru', 'kategori_id' => 5, 'lokasi_id' => 1,
            'tanggal_kejadian' => now()->toDateString(), 'deskripsi' => 'Tas biru ada gantungan',
            'foto' => UploadedFile::fake()->createWithContent('tas.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')),
        ])->assertRedirect('/laporan/saya');
        $lap = Laporan::where('nama_barang', 'Tas Biru')->first();
        $this->assertSame('menunggu', $lap->status);
        Storage::disk('public')->assertExists($lap->foto);

        $this->as('petugas')->patchJson("/api/laporan/{$lap->id}/status", ['status' => 'aktif'])
            ->assertOk()->assertJson(['success' => true, 'status' => 'aktif']);
        $this->as('petugas')->patchJson("/api/laporan/{$lap->id}/status", ['status' => 'aktif'])->assertStatus(422);

        $this->actingAs($mhs)->get("/laporan/{$lap->id}/klaim")->assertForbidden();
        $andi = User::where('email', 'andi@temukampus.test')->first();
        $this->actingAs($andi)->get("/laporan/{$lap->id}/klaim")->assertOk();
        $this->actingAs($andi)->post("/laporan/{$lap->id}/klaim", ['bukti_kepemilikan' => 'pendek'])
            ->assertSessionHasErrors('bukti_kepemilikan');
        $this->actingAs($andi)->post("/laporan/{$lap->id}/klaim", ['bukti_kepemilikan' => 'Ada gantungan beruang di resleting depan'])
            ->assertRedirect('/klaim');
        $klaim = Klaim::where('laporan_id', $lap->id)->first();
        $this->assertSame('diklaim', $lap->fresh()->status);
        $this->actingAs($andi)->get("/laporan/{$lap->id}/klaim")->assertForbidden();

        $this->as('petugas')->patchJson("/api/klaim/{$klaim->id}/status", ['status' => 'ditolak', 'catatan_petugas' => 'tidak cocok'])
            ->assertOk()->assertJson(['laporan_status' => 'aktif']);

        $this->actingAs($andi)->post("/laporan/{$lap->id}/klaim", ['bukti_kepemilikan' => 'Resleting depan ada gantungan beruang'])
            ->assertRedirect('/klaim');
        $k2 = Klaim::where('laporan_id', $lap->id)->latest('id')->first();
        $this->as('petugas')->patchJson("/api/klaim/{$k2->id}/status", ['status' => 'disetujui'])->assertOk();
        $this->as('petugas')->get('/petugas/serah-terima')->assertOk()->assertSee('Tas Biru');
        $this->as('petugas')->patchJson("/api/laporan/{$lap->id}/status", ['status' => 'dikembalikan'])->assertOk();
        $this->assertSame('dikembalikan', $lap->fresh()->status);
        $this->assertNotNull($k2->fresh()->diverifikasi_oleh);
    }

    public function test_edit_delete_rules_and_petugas_autoaktif()
    {
        $mhs = User::where('email', 'mahasiswa@temukampus.test')->first();
        $aktif = Laporan::where('user_id', $mhs->id)->where('status', 'aktif')->first();
        $this->actingAs($mhs)->get("/laporan/{$aktif->id}/edit")->assertForbidden();
        $this->actingAs($mhs)->delete("/laporan/{$aktif->id}")->assertForbidden();

        $this->as('petugas')->post('/laporan', ['jenis' => 'temuan', 'nama_barang' => 'Topi', 'kategori_id' => 4, 'lokasi_id' => 2,
            'tanggal_kejadian' => now()->toDateString(), 'deskripsi' => 'topi hitam'])->assertRedirect();
        $this->assertSame('aktif', Laporan::where('nama_barang', 'Topi')->value('status'));

        $this->as('admin')->delete("/admin/laporan/{$aktif->id}")->assertRedirect();
        $this->assertNull(Laporan::find($aktif->id));
    }

    public function test_admin_crud()
    {
        $this->as('admin')->post('/admin/kategori', ['nama_kategori' => 'Alat tulis', 'keterangan' => 'pena'])->assertRedirect();
        $id = Kategori::where('nama_kategori', 'Alat tulis')->value('id');
        $this->as('admin')->put("/admin/kategori/{$id}", ['nama_kategori' => 'ATK'])->assertRedirect();
        $this->as('admin')->delete("/admin/kategori/{$id}")->assertRedirect();
        $this->assertNull(Kategori::find($id));
        $this->as('admin')->delete('/admin/kategori/1')->assertSessionHas('error');
        $this->assertNotNull(Kategori::find(1));

        $this->as('admin')->post('/admin/lokasi', ['nama_lokasi' => 'Lab 2'])->assertRedirect();
        $this->as('admin')->post('/admin/users', ['nama' => 'Petugas Baru', 'email' => 'p2@x.test', 'role' => 'petugas', 'password' => 'abcdef'])->assertRedirect();
        $u = User::where('email', 'p2@x.test')->first();
        $this->assertTrue(Hash::check('abcdef', $u->password));
        $this->as('admin')->put("/admin/users/{$u->id}", ['nama' => 'P2', 'email' => 'p2@x.test', 'role' => 'mahasiswa'])->assertRedirect();
        $this->assertSame('mahasiswa', $u->fresh()->role);
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertSessionHas('error');
    }

    public function test_statistik_shape()
    {
        $j = $this->as('admin')->getJson('/api/statistik')->assertOk()->json();
        $this->assertCount(6, $j['per_bulan']);
        $this->assertCount(5, $j['per_kategori']);
    }
}
