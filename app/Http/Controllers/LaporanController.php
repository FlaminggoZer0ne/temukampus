<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    /** Beranda publik: barang temuan terbaru. */
    public function beranda()
    {
        $laporan = Laporan::with(['kategori', 'lokasi'])
            ->where('status', 'aktif')->where('jenis', 'temuan')
            ->latest()->limit(8)->get();

        return view('beranda', compact('laporan'));
    }

    /** Daftar Barang (live search + filter). */
    public function index()
    {
        return view('laporan.index', [
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
            'lokasi'   => Lokasi::orderBy('nama_lokasi')->get(),
        ]);
    }

    /** Laporan milik sendiri. */
    public function saya(Request $request)
    {
        $laporan = $request->user()->laporan()->with(['kategori', 'lokasi'])->withCount('klaim')->latest()->get();

        return view('laporan.saya', compact('laporan'));
    }

    public function create()
    {
        return view('laporan.create', $this->opsiForm());
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('laporan', 'public');
        }

        $data['user_id'] = $request->user()->id;
        // Petugas sendiri adalah verifikator, jadi laporannya langsung aktif.
        $data['status'] = $request->user()->isPetugas() ? 'aktif' : 'menunggu';

        Laporan::create($data);

        return redirect()->route('laporan.saya')
            ->with('success', $request->user()->isPetugas()
                ? 'Laporan berhasil dicatat dan langsung tampil di daftar.'
                : 'Laporan terkirim. Menunggu verifikasi petugas.');
    }

    public function show(Laporan $laporan)
    {
        $this->pastikanBolehLihat($laporan);

        $laporan->load(['kategori', 'lokasi', 'user']);
        $user = auth()->user();

        $sudahKlaim = $user->isMahasiswa()
            && $laporan->klaim()->where('user_id', $user->id)->whereIn('status', ['menunggu', 'disetujui'])->exists();

        return view('laporan.show', compact('laporan', 'sudahKlaim'));
    }

    public function edit(Laporan $laporan)
    {
        $this->pastikanBolehUbah($laporan);

        return view('laporan.edit', ['laporan' => $laporan] + $this->opsiForm());
    }

    public function update(Request $request, Laporan $laporan)
    {
        $this->pastikanBolehUbah($laporan);
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            if ($laporan->foto) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $data['foto'] = $request->file('foto')->store('laporan', 'public');
        }

        $laporan->update($data);

        return redirect()->route('laporan.saya')->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Laporan $laporan)
    {
        $this->pastikanBolehUbah($laporan);
        $this->hapus($laporan);

        return redirect()->route('laporan.saya')->with('success', 'Laporan dihapus.');
    }

    /** Admin: hapus laporan apa pun (spam / tidak valid). */
    public function adminDestroy(Laporan $laporan)
    {
        $this->hapus($laporan);

        return back()->with('success', 'Laporan dihapus.');
    }

    /** Admin: tabel semua laporan. */
    public function semua(Request $request)
    {
        $laporan = Laporan::with(['kategori', 'lokasi', 'user'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.laporan', compact('laporan'));
    }

    /** GET /api/laporan/search — live search (JSON). */
    public function search(Request $request)
    {
        $user = $request->user();

        $query = Laporan::with(['kategori', 'lokasi'])
            // Mahasiswa hanya melihat laporan yang sudah diverifikasi.
            ->when($user->isMahasiswa(), fn ($q) => $q->where('status', 'aktif'))
            ->when(!$user->isMahasiswa() && $request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->q, fn ($q, $v) => $q->where('nama_barang', 'like', "%{$v}%"))
            ->when($request->kategori, fn ($q, $v) => $q->where('kategori_id', $v))
            ->when($request->lokasi, fn ($q, $v) => $q->where('lokasi_id', $v))
            ->when($request->jenis, fn ($q, $v) => $q->where('jenis', $v));

        $total = (clone $query)->count();
        $data  = $query->latest()->limit(20)->get()->each->append('tanggal_label');

        return response()->json(['total' => $total, 'data' => $data]);
    }

    // ---------------------------------------------------------------- helper

    private function opsiForm(): array
    {
        return [
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
            'lokasi'   => Lokasi::orderBy('nama_lokasi')->get(),
        ];
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'jenis'            => ['required', 'in:hilang,temuan'],
            'nama_barang'      => ['required', 'string', 'max:100'],
            'kategori_id'      => ['required', 'exists:kategori,id'],
            'lokasi_id'        => ['required', 'exists:lokasi,id'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'deskripsi'        => ['required', 'string'],
            'foto'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    private function hapus(Laporan $laporan): void
    {
        if ($laporan->foto) {
            Storage::disk('public')->delete($laporan->foto);
        }
        $laporan->delete();
    }

    /** Mahasiswa hanya boleh membuka laporan terverifikasi atau miliknya sendiri. */
    private function pastikanBolehLihat(Laporan $laporan): void
    {
        $user = auth()->user();

        if ($user->isMahasiswa() && $laporan->user_id !== $user->id && $laporan->status !== 'aktif') {
            abort(404);
        }
    }

    /** Pemilik laporan saja; mahasiswa hanya selama status "menunggu". */
    private function pastikanBolehUbah(Laporan $laporan): void
    {
        $user = auth()->user();

        abort_unless($laporan->user_id === $user->id, 403, 'Anda hanya bisa mengubah laporan sendiri.');
        abort_if($user->isMahasiswa() && $laporan->status !== 'menunggu', 403,
            'Laporan yang sudah diproses tidak bisa diubah.');
    }
}
