<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::withCount('laporan')->orderBy('id')->get();

        return view('admin.lokasi', compact('lokasi'));
    }

    public function store(Request $request)
    {
        Lokasi::create($this->validasi($request));

        return back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $lokasi->update($this->validasi($request));

        return back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Lokasi $lokasi)
    {
        if ($lokasi->laporan()->exists()) {
            return back()->with('error', 'Lokasi masih dipakai oleh laporan, tidak bisa dihapus.');
        }

        $lokasi->delete();

        return back()->with('success', 'Lokasi dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_lokasi' => ['required', 'string', 'max:100'],
            'keterangan'  => ['nullable', 'string', 'max:150'],
        ]);
    }
}
