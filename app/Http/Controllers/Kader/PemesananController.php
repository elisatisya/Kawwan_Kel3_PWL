<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;

class PemesananController extends Controller
{
    /**
     * Label status pemesanan.
     */
    private const LABEL_STATUS = [
        'menunggu' => 'Menunggu',
        'dikonfirmasi' => 'Dikonfirmasi',
        'sedang_dilayani' => 'Sedang Dilayani',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'tidak_hadir' => 'Tidak Hadir',
    ];

    /**
     * Perubahan status yang boleh dilakukan Kader.
     *
     * Kader bertugas mengonfirmasi kedatangan pemilik.
     * Status pemeriksaan tetap menjadi tanggung jawab Dokter.
     */
    private const TRANSISI = [
        'menunggu' => [
            'dikonfirmasi' => 'Konfirmasi Kehadiran',
            'tidak_hadir' => 'Tidak Hadir',
            'dibatalkan' => 'Batalkan',
        ],

        'dikonfirmasi' => [
            'dibatalkan' => 'Batalkan',
        ],
    ];

    /**
     * Status yang tidak menggunakan kuota jadwal.
     */
    private const STATUS_TIDAK_AKTIF = [
        'dibatalkan',
        'tidak_hadir',
    ];

    /**
     * Menampilkan daftar booking.
     * Mendukung pencarian, filter tanggal, dan filter status.
     */
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal');
        $status = $request->query('status');
        $statusPembayaran = $request->query('status_pembayaran');
        $cari = trim((string) $request->query('cari', ''));

        $pemesanan = Pemesanan::with([
            'user',
            'hewan',
            'jadwalPelayanan',
        ])
            ->when($tanggal, function ($q) use ($tanggal) {
                $q->whereHas('jadwalPelayanan', function ($j) use ($tanggal) {
                    $j->whereDate('tanggal', $tanggal);
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($statusPembayaran, function ($q) use ($statusPembayaran) {
                if ($statusPembayaran === 'belum_dibayar') {
                    $q->whereDoesntHave('pembayaran');
                } else {
                    $q->whereHas('pembayaran', function ($b) use ($statusPembayaran) {
                        $b->where('status', $statusPembayaran);
                    });
                }
            })
            ->when($cari !== '', function ($q) use ($cari) {
                $keyword = '%'.strtolower($cari).'%';
                $q->where(function ($sub) use ($keyword) {
                    $sub->whereHas('user', function ($u) use ($keyword) {
                        $u->whereRaw('LOWER(name) LIKE ?', [$keyword]);
                    })
                        ->orWhereHas('hewan', function ($h) use ($keyword) {
                            $h->whereRaw('LOWER(jenis) LIKE ?', [$keyword])
                                ->orWhereRaw('LOWER(COALESCE(ras, \'\')) LIKE ?', [$keyword]);
                        });
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // Mengambil pembayaran untuk booking yang ditampilkan.
        $pembayaran = Pembayaran::with('pencatat')
            ->whereIn('pemesanan_id', $pemesanan->pluck('id'))
            ->get()
            ->keyBy('pemesanan_id');

<<<<<<< HEAD
        return view('kader.pemesanan', [
=======
        return view('kader.pemesanan-index', [
>>>>>>> origin/main
            'pemesanan' => $pemesanan,
            'pembayaran' => $pembayaran,
            'tanggal' => $tanggal,
            'status' => $status,
            'statusPembayaran' => $statusPembayaran,
            'cari' => $cari,
            'labelStatus' => self::LABEL_STATUS,
            'transisi' => self::TRANSISI,
        ]);
    }

    /**
     * Menampilkan formulir pendaftaran booking oleh Kader.
     */
    public function create(Request $request)
    {
        $pemilik = User::with('hewan')
            ->whereIn('role', [
                User::ROLE_PEMILIK,
                'pemilik',
            ])
            ->where('status', User::STATUS_AKTIF)
            ->orderBy('name')
            ->get();

        $hewanPerPemilik = $pemilik->mapWithKeys(fn ($u) => [
            $u->id => $u->hewan->map(fn ($h) => [
                'id' => $h->id,
                'jenis' => $h->jenis,
                'ras' => $h->ras,
            ])->values(),
        ]);

        // Menghitung kuota jadwal yang masih terpakai.
        $jadwal = JadwalPelayanan::withCount([
            'pemesanan as terpakai' => fn ($q) => $q->whereNotIn('status', self::STATUS_TIDAK_AKTIF),
        ])
            ->tersedia()
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get()
            ->filter(fn ($j) => $j->kuota === null || $j->terpakai < $j->kuota
            )
            ->values();

<<<<<<< HEAD
        return view('kader.pemesanan', [
=======
        return view('kader.pemesanan-create', [
>>>>>>> origin/main
            'pemilik' => $pemilik,
            'hewanPerPemilik' => $hewanPerPemilik,
            'jadwal' => $jadwal,
            'jadwalDipilih' => $request->query('jadwal'),
        ]);
    }

    /**
     * Menyimpan booking baru yang didaftarkan Kader.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->whereIn('role', [
                        User::ROLE_PEMILIK,
                        'pemilik',
                    ])
                    ->where('status', User::STATUS_AKTIF),
            ],

            'hewan_id' => [
                'required',
                Rule::exists('hewan', 'id')
                    ->where('user_id', $request->input('user_id')),
            ],

            'jadwal_pelayanan_id' => [
                'required',
                'exists:jadwal_pelayanan,id',
            ],
        ], [
            'user_id.required' => 'Pemilik hewan wajib dipilih.',
            'user_id.exists' => 'Pemilik tidak ditemukan atau belum aktif.',
            'hewan_id.required' => 'Hewan wajib dipilih.',
            'hewan_id.exists' => 'Hewan tidak terdaftar atas pemilik tersebut.',
            'jadwal_pelayanan_id.required' => 'Jadwal wajib dipilih.',
            'jadwal_pelayanan_id.exists' => 'Jadwal tidak ditemukan.',
        ]);

        try {
            $pemesanan = DB::transaction(function () use ($data) {

                // Mengunci jadwal selama proses pendaftaran.
                $jadwal = JadwalPelayanan::lockForUpdate()
                    ->findOrFail($data['jadwal_pelayanan_id']);

                if (
                    $jadwal->status !== 'tersedia' ||
                    $jadwal->tanggal->lt(today())
                ) {
                    throw new RuntimeException(
                        'Jadwal ini sudah tidak tersedia.'
                    );
                }

                // Mengambil booking aktif pada jadwal tersebut.
                $aktif = Pemesanan::where(
                    'jadwal_pelayanan_id',
                    $jadwal->id
                )->whereNotIn(
                    'status',
                    self::STATUS_TIDAK_AKTIF
                );

                // Memeriksa ketersediaan kuota.
                if (
                    $jadwal->kuota !== null &&
                    (clone $aktif)->count() >= $jadwal->kuota
                ) {
                    throw new RuntimeException(
                        'Kuota jadwal ini sudah penuh.'
                    );
                }

                // Mencegah hewan yang sama memesan jadwal yang sama.
                if (
                    (clone $aktif)
                        ->where('hewan_id', $data['hewan_id'])
                        ->exists()
                ) {
                    throw new RuntimeException(
                        'Hewan ini sudah terdaftar pada jadwal tersebut.'
                    );
                }

                // Booking baru harus menunggu konfirmasi kedatangan.
                return Pemesanan::create([
                    'user_id' => $data['user_id'],
                    'hewan_id' => $data['hewan_id'],
                    'jadwal_pelayanan_id' => $jadwal->id,
                    'status' => 'menunggu',
                ]);
            });

        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'jadwal_pelayanan_id' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('kader.pemesanan.index')
            ->with(
                'success',
                'Booking berhasil didaftarkan. Menunggu pemilik melakukan pembayaran.'
            );
    }

    /**
     * Memperbarui status booking oleh Kader.
     */
    public function updateStatus(
        Request $request,
        Pemesanan $pemesanan
    ): RedirectResponse {

        // Mengambil status tujuan yang diizinkan.
        $boleh = array_keys(
            self::TRANSISI[$pemesanan->status] ?? []
        );

        $request->validate([
            'status' => [
                'required',
                Rule::in($boleh),
            ],
        ], [
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Perubahan status tersebut tidak diizinkan.',
        ]);

        $pemesanan->update([
            'status' => $request->input('status'),
        ]);

        $pesan = match ($request->input('status')) {
            'dikonfirmasi' => 'Kehadiran pemilik berhasil dikonfirmasi.',

            'tidak_hadir' => 'Pemesanan berhasil ditandai tidak hadir.',

            'dibatalkan' => 'Pemesanan berhasil dibatalkan.',

            default => 'Status pemesanan berhasil diperbarui.',
        };

        return back()->with('success', $pesan);
    }
}

