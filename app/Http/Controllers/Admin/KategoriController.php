<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::withCount('laporan')->orderBy('id')->get();

        return view('admin.kategori', compact('kategori'));
    }

    public function store(Request $request)
    {
        Kategori::create($this->validasi($request));

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $kategori->update($this->validasi($request));

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->laporan()->exists()) {
            return back()->with('error', 'Kategori masih dipakai oleh laporan, tidak bisa dihapus.');
        }

        $kategori->delete();

        return back()->with('success', 'Kategori dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_kategori' => ['required', 'string', 'max:50'],
            'keterangan'    => ['nullable', 'string', 'max:150'],
        ]);
    }
}
