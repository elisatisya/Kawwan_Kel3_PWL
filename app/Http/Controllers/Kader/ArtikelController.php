<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ArtikelController extends Controller
{
    // Menampilkan daftar artikel milik kader
    public function index()
    {
        $artikel = Artikel::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('kader.artikel', compact('artikel'));
    }

    // Menampilkan form tambah artikel
    public function create()
    {
        return view('kader.artikel-create');
    }

    // Menyimpan artikel baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori' => 'required|string|max:100',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = $this->generateSlug($validated['judul']);
        $validated['status'] = 'draft';

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')
                ->store('artikel', 'public');
        }

        Artikel::create($validated);

        return redirect()
            ->route('kader.artikel.index')
            ->with('success', 'Artikel berhasil disimpan sebagai draft.');
    }

    // Menampilkan form edit artikel
    public function edit(Artikel $artikel)
    {
        $artikel = $this->artikelMilikKader($artikel);

        return view('kader.artikel-edit', compact('artikel'));
    }

    // Memperbarui artikel
    public function update(Request $request, Artikel $artikel)
    {
        $artikel = $this->artikelMilikKader($artikel);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori' => 'required|string|max:100',
        ]);

        if ($artikel->judul !== $validated['judul']) {
            $validated['slug'] = $this->generateSlug($validated['judul']);
        }

        if ($request->hasFile('gambar')) {
            if ($artikel->gambar) {
                Storage::disk('public')->delete($artikel->gambar);
            }

            $validated['gambar'] = $request->file('gambar')
                ->store('artikel', 'public');
        }

        $artikel->update($validated);

        return redirect()
            ->route('kader.artikel.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    // Menghapus artikel
    public function destroy(Artikel $artikel)
    {
        $artikel = $this->artikelMilikKader($artikel);

        if ($artikel->gambar) {
            Storage::disk('public')->delete($artikel->gambar);
        }

        $artikel->delete();

        return redirect()
            ->route('kader.artikel.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    // Mengajukan artikel untuk publikasi
    public function ajukan(Artikel $artikel)
    {
        $artikel = $this->artikelMilikKader($artikel);

        if ($artikel->status !== 'draft') {
            return back()->with('error', 'Hanya artikel draft yang dapat diajukan.');
        }

        $artikel->update([
            'status' => 'diajukan',
        ]);

        return redirect()
            ->route('kader.artikel.index')
            ->with('success', 'Artikel berhasil diajukan untuk publikasi.');
    }

    // Memastikan artikel merupakan milik kader yang sedang login
    private function artikelMilikKader(Artikel $artikel)
    {
        abort_unless(
            $artikel->user_id === auth()->id(),
            403,
            'Kamu tidak memiliki akses ke artikel ini.'
        );

        return $artikel;
    }

    // Membuat slug yang unik
    private function generateSlug(string $judul): string
    {
        $slug = Str::slug($judul);
        $originalSlug = $slug;
        $counter = 1;

        while (Artikel::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

