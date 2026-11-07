<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Klaim;
use App\Models\Laporan;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    /** Halaman verifikasi laporan (status menunggu). */
    public function laporan()
    {
        $laporan = Laporan::with(['kategori', 'lokasi', 'user'])
            ->where('status', 'menunggu')->latest()->get();

        return view('petugas.laporan', compact('laporan'));
    }

    /** Halaman verifikasi klaim: tab menunggu / disetujui / ditolak. */
    public function klaim(Request $request)
    {
        $tab = in_array($request->tab, ['menunggu', 'disetujui', 'ditolak']) ? $request->tab : 'menunggu';

        $klaim = Klaim::with(['laporan.lokasi', 'user', 'petugas'])
            ->where('status', $tab)->latest()->get();

        $hitung = Klaim::selectRaw('status, count(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status');

        return view('petugas.klaim', compact('klaim', 'tab', 'hitung'));
    }

    /** Klaim disetujui yang barangnya belum diserahkan. */
    public function serahTerima()
    {
        $klaim = Klaim::with(['laporan.lokasi', 'user'])
            ->where('status', 'disetujui')
            ->whereHas('laporan', fn ($q) => $q->where('status', 'diklaim'))
            ->latest()->get();

        return view('petugas.serahterima', compact('klaim'));
    }

    /** PATCH /api/laporan/{laporan}/status */
    public function updateStatusLaporan(Request $request, Laporan $laporan)
    {
        $data = $request->validate(['status' => ['required', 'in:aktif,ditolak,dikembalikan']]);

        // Serah-terima hanya sah untuk laporan yang sedang diklaim.
        if ($data['status'] === 'dikembalikan' && $laporan->status !== 'diklaim') {
            return response()->json(['success' => false, 'message' => 'Barang belum punya klaim yang disetujui.'], 422);
        }
        // Verifikasi hanya untuk laporan yang masih menunggu.
        if (in_array($data['status'], ['aktif', 'ditolak']) && $laporan->status !== 'menunggu') {
            return response()->json(['success' => false, 'message' => 'Laporan ini sudah diproses.'], 422);
        }

        $laporan->update(['status' => $data['status']]);

        return response()->json(['success' => true, 'id' => $laporan->id, 'status' => $laporan->status]);
    }

    /** PATCH /api/klaim/{klaim}/status */
    public function updateStatusKlaim(Request $request, Klaim $klaim)
    {
        $data = $request->validate([
            'status'          => ['required', 'in:disetujui,ditolak'],
            'catatan_petugas' => ['nullable', 'string', 'max:500'],
        ]);

        if ($klaim->status !== 'menunggu') {
            return response()->json(['success' => false, 'message' => 'Klaim ini sudah diproses.'], 422);
        }

        $klaim->update($data + ['diverifikasi_oleh' => $request->user()->id]);

        $laporan = $klaim->laporan;
        if ($data['status'] === 'ditolak') {
            // Laporan kembali aktif bila tidak ada klaim lain yang masih berjalan.
            $masihAda = $laporan->klaim()->whereIn('status', ['menunggu', 'disetujui'])->exists();
            if (!$masihAda && $laporan->status === 'diklaim') {
                $laporan->update(['status' => 'aktif']);
            }
        }

        return response()->json([
            'success'        => true,
            'id'             => $klaim->id,
            'status'         => $klaim->status,
            'laporan_status' => $laporan->fresh()->status,
        ]);
    }
}
