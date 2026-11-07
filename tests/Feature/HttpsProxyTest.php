<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Perilaku HTTPS di belakang proxy (Railway, dsb.): proxy mengakhiri TLS dan
 * meneruskan request ke aplikasi lewat HTTP dengan header X-Forwarded-*.
 */
class HttpsProxyTest extends TestCase
{
    use RefreshDatabase;

    private const HOST = 'temukampus.example.com';

    private function proxy(): array
    {
        return [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_HOST'  => self::HOST,
            'HTTP_X_FORWARDED_PORT'  => '443',
        ];
    }

    public function test_halaman_dibuat_dengan_url_https_di_belakang_proxy()
    {
        $html = $this->withServerVariables($this->proxy())->get('/login')->assertOk()->getContent();

        $this->assertStringContainsString('href="https://'.self::HOST.'/css/style.css"', $html);
        $this->assertStringContainsString('https://'.self::HOST.'/img/logo-mark.svg', $html);
        $this->assertStringContainsString('action="https://'.self::HOST.'/login"', $html);
        // Tidak boleh ada sumber daya http:// (mixed content). Namespace XML w3.org dikecualikan.
        $this->assertDoesNotMatchRegularExpression('~(?:src|href|action)="http://(?!www\.w3\.org)~', $html);
        $this->assertStringNotContainsString('http://localhost', $html);
    }

    public function test_redirect_setelah_login_memakai_https()
    {
        User::create(['nama' => 'Admin', 'email' => 'a@x.test', 'password' => bcrypt('password'), 'role' => 'admin']);

        $this->withServerVariables($this->proxy())
            ->post('/login', ['email' => 'a@x.test', 'password' => 'password'])
            ->assertRedirect('https://'.self::HOST.'/dashboard');
    }

    public function test_url_foto_mengikuti_skema_request_bukan_app_url()
    {
        $u = User::create(['nama' => 'M', 'email' => 'm@x.test', 'password' => bcrypt('password'), 'role' => 'mahasiswa']);
        $k = Kategori::create(['nama_kategori' => 'Lainnya']);
        $l = Lokasi::create(['nama_lokasi' => 'Masjid']);
        Laporan::create(['user_id' => $u->id, 'kategori_id' => $k->id, 'lokasi_id' => $l->id, 'jenis' => 'temuan',
            'nama_barang' => 'Botol', 'deskripsi' => 'biru', 'foto' => 'laporan/botol.jpg',
            'tanggal_kejadian' => now()->toDateString(), 'status' => 'aktif']);

        $json = $this->actingAs($u)->withServerVariables($this->proxy())
            ->getJson('/api/laporan/search')->assertOk()->json();

        $this->assertSame('https://'.self::HOST.'/storage/laporan/botol.jpg', $json['data'][0]['foto_url']);
    }

    public function test_mode_production_memaksa_https_tanpa_header_proxy()
    {
        $this->app['env'] = 'production';
        (new AppServiceProvider($this->app))->boot();

        $this->assertStringStartsWith('https://', asset('css/style.css'));
        $this->assertStringStartsWith('https://', route('login'));

        URL::forceHttps(false);
    }

    public function test_mode_selain_production_tidak_memaksa_https()
    {
        $this->assertStringStartsWith('http://', asset('css/style.css'));
    }
}
