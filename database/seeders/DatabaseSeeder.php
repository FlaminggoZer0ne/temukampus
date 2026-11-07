<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Klaim;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create(['nama' => 'Admin Kampus', 'email' => 'admin@temukampus.test',
                               'password' => bcrypt('password'), 'role' => 'admin']);
        $petugas = User::create(['nama' => 'Pak Joko', 'nim_nip' => '198001012005', 'email' => 'petugas@temukampus.test',
                                 'password' => bcrypt('password'), 'role' => 'petugas', 'no_hp' => '0811-1111-0001']);
        $mhs = User::create(['nama' => 'Mahasiswa Demo', 'nim_nip' => '1301220001', 'email' => 'mahasiswa@temukampus.test',
                             'password' => bcrypt('password'), 'role' => 'mahasiswa', 'no_hp' => '0812-2222-0001']);
        $andi = User::create(['nama' => 'Andi Pratama', 'nim_nip' => '1301220045', 'email' => 'andi@temukampus.test',
                              'password' => bcrypt('password'), 'role' => 'mahasiswa', 'no_hp' => '0812-3333-4410']);

        $kategori = [];
        foreach (['Elektronik', 'Dokumen', 'Aksesoris', 'Pakaian', 'Lainnya'] as $k) {
            $kategori[$k] = Kategori::create(['nama_kategori' => $k]);
        }
        $lokasi = [];
        foreach (['Gedung A', 'Gedung B', 'Perpustakaan', 'Masjid', 'Kantin', 'Parkiran'] as $l) {
            $lokasi[$l] = Lokasi::create(['nama_lokasi' => $l]);
        }

        // Contoh data laporan: [jenis, nama, kategori, lokasi, hari lalu, status, pelapor]
        $contoh = [
            ['temuan', 'Botol Minum Biru',      'Lainnya',    'Perpustakaan', 1, 'aktif',        $mhs],
            ['hilang', 'Dompet Kulit Coklat',   'Aksesoris',  'Masjid',       2, 'aktif',        $andi],
            ['temuan', 'Kunci Motor Honda',     'Aksesoris',  'Parkiran',     3, 'diklaim',      $mhs],
            ['temuan', 'Charger Laptop Lenovo', 'Elektronik', 'Gedung A',     4, 'aktif',        $petugas],
            ['hilang', 'Jaket Hoodie Abu',      'Pakaian',    'Kantin',       5, 'aktif',        $andi],
            ['temuan', 'Payung Hitam Lipat',    'Lainnya',    'Gedung B',     5, 'aktif',        $mhs],
            ['hilang', 'Earphone Bluetooth',    'Elektronik', 'Gedung A',     6, 'menunggu',     $andi],
            ['temuan', 'Kacamata Minus',        'Aksesoris',  'Perpustakaan', 7, 'aktif',        $mhs],
            ['temuan', 'KTM a.n. Rina A.',      'Dokumen',    'Perpustakaan', 11, 'dikembalikan', $petugas],
        ];

        $dibuat = [];
        foreach ($contoh as [$jenis, $nama, $kat, $lok, $hari, $status, $pelapor]) {
            $dibuat[$nama] = Laporan::create([
                'user_id'          => $pelapor->id,
                'kategori_id'      => $kategori[$kat]->id,
                'lokasi_id'        => $lokasi[$lok]->id,
                'jenis'            => $jenis,
                'nama_barang'      => $nama,
                'deskripsi'        => "Ciri-ciri: {$nama}. Ditemukan/hilang di area {$lok}.",
                'tanggal_kejadian' => now()->subDays($hari)->toDateString(),
                'status'           => $status,
            ]);
        }

        // Satu klaim menunggu pada laporan yang berstatus "diklaim"
        Klaim::create([
            'laporan_id'        => $dibuat['Kunci Motor Honda']->id,
            'user_id'           => $andi->id,
            'bukti_kepemilikan' => 'Gantungan kunci berbentuk bola basket warna oranye, ada 2 kunci (motor & rumah).',
            'status'            => 'menunggu',
        ]);
    }
}
