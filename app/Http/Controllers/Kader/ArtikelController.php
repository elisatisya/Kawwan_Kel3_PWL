<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Services\NewsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtikelController extends Controller
{
    public function index()
    {
        $artikels = Artikel::latest()->paginate(10);

        return view('kader.artikel', compact('artikels'));
    }

    public function searchApi(Request $request, NewsApiService $newsApi)
    {
        $keyword = $request->input('q', 'kesehatan hewan');
        $result = $newsApi->search($keyword);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        $existingLinks = Artikel::whereIn('link', collect($result['data'])->pluck('link'))
            ->pluck('link')
            ->flip();

        $articles = collect($result['data'])->map(function ($item) use ($existingLinks) {
            $item['sudah_diimpor'] = $existingLinks->has($item['link']);
            return $item;
        });

        return response()->json(['data' => $articles]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'judul'     => ['required', 'string', 'max:500'],
            'link'      => ['required', 'url', 'max:500', 'unique:artikel,link'],
            'sumber'    => ['nullable', 'string', 'max:150'],
            'ringkasan' => ['nullable', 'string'],
            'gambar'    => ['nullable', 'url'],
            'kategori'  => ['required', 'string', 'max:100'],
            'status'    => ['required', 'in:draft,published'],
        ]);

        $artikel = Artikel::create([
            ...$validated,
            'user_id'      => Auth::id(),
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        return response()->json([
            'message' => 'Artikel berhasil diimpor.',
            'data'    => $artikel,
        ], 201);
    }

    public function updateStatus(Request $request, Artikel $artikel)
    {
        $request->validate([
            'status'   => ['required', 'in:draft,published'],
            'kategori' => ['sometimes', 'string', 'max:100'],
        ]);

        $artikel->update([
            'status'       => $request->status,
            'kategori'     => $request->input('kategori', $artikel->kategori),
            'published_at' => $request->status === 'published'
                ? ($artikel->published_at ?? now())
                : null,
        ]);

        return back()->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Artikel $artikel)
    {
        $artikel->delete();
        return back()->with('success', 'Artikel berhasil dihapus.');
    }
}