<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;
use App\Models\Layanan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\PemesananLayanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class PemesananController extends Controller
{
    private const STATUS_TIDAK_AKTIF = [
        'dibatalkan',
        'tidak_hadir',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();

        $daftarHewan = $user->hewan()
            ->orderBy('nama')
            ->get();

        $jadwalTersedia = JadwalPelayanan::with([
            'dokter',
            'dokter.layananDokter',
        ])
            ->withCount([
                'pemesanan as terpakai' => fn ($q) => $q->whereNotIn('status', self::STATUS_TIDAK_AKTIF),
            ])
            ->tersedia()
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get()
            ->filter(fn ($j) => $j->kuota === null || $j->terpakai < $j->kuota)
            ->values();

        $daftarLayanan = Layanan::with('dokter')
            ->where('aktif', true)
            ->orderBy('nama')
            ->get();

        $daftarPemesanan = Pemesanan::with([
            'hewan',
            'jadwalPelayanan.dokter',
            'pemesananLayanan.layanan',
            'pembayaran',
            'antrean.dokter',
        ])
            ->where('user_id', $user->id)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $jadwalDipilih = $request->query('jadwal');

        return view('pemilik.pemesanan', compact(
            'daftarHewan',
            'jadwalTersedia',
            'daftarLayanan',
            'daftarPemesanan',
            'jadwalDipilih'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'hewan_id' => [
                'required',
                Rule::exists('hewan', 'id')->where('user_id', $user->id),
            ],
            'jadwal_pelayanan_id' => [
                'required',
                'exists:jadwal_pelayanan,id',
            ],
            'layanan_ids' => ['nullable', 'array'],
            'layanan_ids.*' => [
                'integer',
                Rule::exists('layanan', 'id')->where('aktif', true),
            ],
        ], [
            'hewan_id.required' => 'Pilih hewan peliharaan terlebih dahulu.',
            'hewan_id.exists' => 'Hewan tidak valid.',
            'jadwal_pelayanan_id.required' => 'Pilih jadwal pelayanan terlebih dahulu.',
            'jadwal_pelayanan_id.exists' => 'Jadwal pelayanan tidak ditemukan.',
        ]);

        try {
            DB::transaction(function () use ($user, $data) {
                $jadwal = JadwalPelayanan::with('dokter')
                    ->lockForUpdate()
                    ->findOrFail($data['jadwal_pelayanan_id']);

                if ($jadwal->status !== 'tersedia' || $jadwal->tanggal->lt(today())) {
                    throw new RuntimeException('Jadwal pelayanan ini sudah tidak tersedia.');
                }

                $aktif = Pemesanan::where('jadwal_pelayanan_id', $jadwal->id)
                    ->whereNotIn('status', self::STATUS_TIDAK_AKTIF);

                if ($jadwal->kuota !== null && (clone $aktif)->count() >= $jadwal->kuota) {
                    throw new RuntimeException('Kuota pada jadwal pelayanan ini sudah penuh.');
                }

                if ((clone $aktif)->where('hewan_id', $data['hewan_id'])->exists()) {
                    throw new RuntimeException('Hewan ini sudah terdaftar pada jadwal pelayanan tersebut.');
                }

                $pemesanan = Pemesanan::create([
                    'user_id' => $user->id,
                    'hewan_id' => $data['hewan_id'],
                    'jadwal_pelayanan_id' => $jadwal->id,
                    'status' => 'menunggu',
                ]);

                $layananIds = array_unique($data['layanan_ids'] ?? []);
                if (! empty($layananIds)) {
                    $layananList = Layanan::with('dokter')
                        ->whereIn('id', $layananIds)
                        ->get();

                    foreach ($layananList as $layanan) {
                        $dokterId = $jadwal->dokter_id
                            ?? $layanan->dokter->first()?->id;

                        if ($dokterId) {
                            PemesananLayanan::create([
                                'pemesanan_id' => $pemesanan->id,
                                'layanan_id' => $layanan->id,
                                'dokter_id' => $dokterId,
                                'biaya' => $layanan->biaya,
                            ]);
                        }
                    }
                }
            });
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors(['jadwal_pelayanan_id' => $e->getMessage()]);
        }

        return redirect()
            ->route('pemilik.pemesanan.index')
            ->with('success', 'Pemesanan antrean berhasil dibuat.');
    }

    public function bayar(Request $request, Pemesanan $pemesanan): RedirectResponse
    {
        abort_unless($pemesanan->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'metode' => ['required', Rule::in(['transfer', 'qris', 'tunai'])],
            'jumlah' => ['required', 'integer', 'min:0'],
            'bukti_pembayaran' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'metode.required' => 'Pilih metode pembayaran.',
            'jumlah.required' => 'Nominal pembayaran wajib diisi.',
        ]);

        $pembayaranLama = $pemesanan->pembayaran;
        $pathBukti = $pembayaranLama?->bukti_pembayaran;

        if ($request->hasFile('bukti_pembayaran')) {
            if ($pathBukti) {
                Storage::disk('public')->delete($pathBukti);
            }
            $pathBukti = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
        }

        Pembayaran::updateOrCreate(
            ['pemesanan_id' => $pemesanan->id],
            [
                'jumlah' => $data['jumlah'],
                'metode' => $data['metode'],
                'bukti_pembayaran' => $pathBukti,
                'status' => 'menunggu_verifikasi',
                'catatan' => null,
            ]
        );

        return redirect()
            ->route('pemilik.pemesanan.index')
            ->with('success', 'Data pembayaran berhasil dikirim dan menunggu verifikasi kader.');
    }

    public function batal(Request $request, Pemesanan $pemesanan): RedirectResponse
    {
        abort_unless($pemesanan->user_id === $request->user()->id, 403);

        if ($pemesanan->status !== 'menunggu') {
            return back()->withErrors([
                'pemesanan' => 'Hanya pemesanan dengan status menunggu yang dapat dibatalkan.',
            ]);
        }

        $pemesanan->update(['status' => 'dibatalkan']);

        return redirect()
            ->route('pemilik.pemesanan.index')
            ->with('success', 'Pemesanan antrean berhasil dibatalkan.');
    }
}
