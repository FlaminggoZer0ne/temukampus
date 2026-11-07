<?php

namespace App\Http\Controllers;

use App\Models\Klaim;
use App\Models\Laporan;
use Illuminate\Http\Request;

class KlaimController extends Controller
{
    /** Klaim milik mahasiswa yang login. */
    public function index(Request $request)
    {
        $klaim = $request->user()->klaim()->with(['laporan.lokasi', 'petugas'])->latest()->get();

        return view('klaim.index', compact('klaim'));
    }

    public function create(Laporan $laporan)
    {
        $this->pastikanBisaDiklaim($laporan);

        return view('klaim.create', compact('laporan'));
    }

    public function store(Request $request, Laporan $laporan)
    {
        $this->pastikanBisaDiklaim($laporan);

        $data = $request->validate([
            'bukti_kepemilikan' => ['required', 'string', 'min:15'],
            'foto_bukti'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'bukti_kepemilikan.min' => 'Jelaskan bukti kepemilikan dengan lebih rinci (minimal 15 karakter).',
        ]);

        if ($request->hasFile('foto_bukti')) {
            $data['foto_bukti'] = $request->file('foto_bukti')->store('klaim', 'public');
        }

        $laporan->klaim()->create($data + ['user_id' => $request->user()->id]);
        $laporan->update(['status' => 'diklaim']);

        return redirect()->route('klaim.index')->with('success', 'Klaim terkirim. Petugas akan memeriksa bukti Anda.');
    }

    /**
     * Klaim hanya untuk barang temuan yang sudah aktif,
     * bukan milik pelapor sendiri, dan belum pernah diklaim oleh user yang sama.
     */
    private function pastikanBisaDiklaim(Laporan $laporan): void
    {
        $user = auth()->user();

        abort_unless($laporan->jenis === 'temuan', 403, 'Hanya barang temuan yang bisa diklaim.');
        abort_unless($laporan->status === 'aktif', 403, 'Barang ini sedang tidak dapat diklaim.');
        abort_if($laporan->user_id === $user->id, 403, 'Anda tidak bisa mengklaim laporan sendiri.');
        abort_if(
            Klaim::where('laporan_id', $laporan->id)->where('user_id', $user->id)
                ->whereIn('status', ['menunggu', 'disetujui'])->exists(),
            403, 'Anda sudah mengajukan klaim untuk barang ini.'
        );
    }
}
