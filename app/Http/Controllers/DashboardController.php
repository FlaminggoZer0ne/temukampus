<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Klaim;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin'   => $this->admin(),
            'petugas' => $this->petugas(),
            default   => $this->mahasiswa($user),
        };
    }

    private function admin()
    {
        $terbaru = Laporan::with(['kategori', 'lokasi', 'user'])->latest()->limit(5)->get();

        return view('dashboard.admin', [
            'terbaru' => $terbaru,
            'total'   => [
                'laporan'      => Laporan::count(),
                'menunggu'     => Laporan::where('status', 'menunggu')->count(),
                'dikembalikan' => Laporan::where('status', 'dikembalikan')->count(),
                'user'         => User::count(),
            ],
        ]);
    }

    private function petugas()
    {
        return view('dashboard.petugas', [
            'laporanMenunggu' => Laporan::with(['kategori', 'lokasi', 'user'])
                                    ->where('status', 'menunggu')->latest()->limit(5)->get(),
            'klaimMenunggu'   => Klaim::with(['laporan', 'user'])
                                    ->where('status', 'menunggu')->latest()->limit(5)->get(),
            'total' => [
                'laporan_menunggu' => Laporan::where('status', 'menunggu')->count(),
                'klaim_menunggu'   => Klaim::where('status', 'menunggu')->count(),
                'siap_serah'       => Klaim::where('status', 'disetujui')
                                        ->whereHas('laporan', fn ($q) => $q->where('status', 'diklaim'))->count(),
                'dikembalikan'     => Laporan::where('status', 'dikembalikan')->count(),
            ],
        ]);
    }

    private function mahasiswa(User $user)
    {
        return view('dashboard.mahasiswa', [
            'laporan' => $user->laporan()->with(['kategori', 'lokasi'])->latest()->get(),
            'klaim'   => $user->klaim()->with('laporan')->latest()->get(),
        ]);
    }

    /** GET /api/statistik — data dashboard (admin & petugas). */
    public function statistik()
    {
        // Laporan per bulan: 6 bulan terakhir
        $bulan = [];
        for ($i = 5; $i >= 0; $i--) {
            $awal = now()->startOfMonth()->subMonths($i);
            $bulan[] = [
                'label'  => $awal->translatedFormat('M'),
                'jumlah' => Laporan::whereBetween('created_at', [$awal->copy(), $awal->copy()->endOfMonth()])->count(),
            ];
        }

        $kategori = Kategori::withCount('laporan')->orderByDesc('laporan_count')->get()
            ->map(fn ($k) => ['nama' => $k->nama_kategori, 'jumlah' => $k->laporan_count])->values();

        $status = Laporan::selectRaw('status, count(*) as jumlah')->groupBy('status')
            ->pluck('jumlah', 'status');

        return response()->json([
            'per_bulan'    => $bulan,
            'per_kategori' => $kategori,
            'per_status'   => $status,
        ]);
    }
}
