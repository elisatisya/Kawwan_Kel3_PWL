<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Mail\AccountStatusMail;
use Illuminate\Support\Facades\Mail;
use Throwable;

class VerifikasiController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan verifikasi pengguna.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'semua');
        $search = trim((string) $request->query('search', ''));
        $roleFilter = $request->query('role', 'semua');

        $query = User::with(['verifiedBy', 'hewan'])
            ->withCount('hewan')
            ->latest('created_at');

        // Filter status
        if ($statusFilter !== 'semua' && in_array($statusFilter, [
            User::STATUS_MENUNGGU,
            User::STATUS_AKTIF,
            User::STATUS_DITOLAK,
            User::STATUS_NONAKTIF,
        ], true)) {
            $query->where('status', $statusFilter);
        }

        // Filter role
        if ($roleFilter !== 'semua') {
            if ($roleFilter === 'pemilik') {
                $query->whereIn('role', [User::ROLE_PEMILIK, 'pemilik']);
            } elseif ($roleFilter === 'dokter') {
                $query->whereIn('role', [User::ROLE_DOKTER, 'dokter']);
            } elseif ($roleFilter === 'kader') {
                $query->where('role', User::ROLE_KADER);
            }
        }

        // Pencarian berdasarkan nama, NIK, email, atau nomor telepon
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nomor_telepon', 'like', "%{$search}%");
            });
        }

        $penggunaList = $query->paginate(10)->withQueryString();

        // Counter untuk badge tab filter
        $counter = [
            'total' => User::count(),
            'menunggu' => User::where('status', User::STATUS_MENUNGGU)->count(),
            'aktif' => User::where('status', User::STATUS_AKTIF)->count(),
            'ditolak' => User::where('status', User::STATUS_DITOLAK)->count(),
            'nonaktif' => User::where('status', User::STATUS_NONAKTIF)->count(),
        ];

        return view('admin.verifikasi.index', compact('penggunaList', 'counter', 'statusFilter', 'search', 'roleFilter'));
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $catatan = $request->filled('catatan')
            ? trim((string) $request->input('catatan'))
            : 'Verifikasi disetujui oleh Administrator.';

        $user->update([
            'status' => User::STATUS_AKTIF,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'catatan_verifikasi' => $catatan,
        ]);

        if (empty($user->email)) {
            return back()->with(
                'warning',
                "Akun {$user->name} berhasil disetujui, tetapi tidak memiliki alamat email."
            );
        }

        try {
            Mail::to($user->email)->send(
                new AccountStatusMail($user, 'approved')
            );

            return back()->with(
                'success',
                "Akun {$user->name} berhasil disetujui dan email pemberitahuan telah dikirim."
            );
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'warning',
                "Akun {$user->name} berhasil disetujui, tetapi email gagal dikirim."
            );
        }
    }
    /**
     * Tolak verifikasi akun pengguna.
     */
    public function reject(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'catatan' => ['required', 'string', 'max:500'],
        ], [
            'catatan.required' => 'Alasan penolakan verifikasi wajib diisi agar pengguna mengetahui kendala identitasnya.',
            'catatan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $user->update([
            'status' => User::STATUS_DITOLAK,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'catatan_verifikasi' => trim((string) $request->input('catatan')),
        ]);

        return back()->with('warning', "Verifikasi akun {$user->name} ditolak. Alasan penolakan telah disimpan.");
    }

    /**
     * Toggle status aktif / nonaktif akun pengguna.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun administrator Anda sendiri.');
        }

        if ($user->status === User::STATUS_AKTIF) {
            $user->update([
                'status' => User::STATUS_NONAKTIF,
                'catatan_verifikasi' => $request->input('catatan', 'Dinonaktifkan oleh Administrator.'),
            ]);

            return back()->with('info', "Akun {$user->name} berhasil dinonaktifkan.");
        }

        $user->update([
            'status' => User::STATUS_AKTIF,
            'catatan_verifikasi' => 'Diaktifkan kembali oleh Administrator.',
        ]);

        return back()->with('success', "Akun {$user->name} berhasil diaktifkan kembali.");
    }

    /**
     * Verifikasi beberapa pengguna sekaligus (Batch Approval).
     */
    public function batchApprove(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ], [
            'user_ids.required' => 'Pilih setidaknya satu pengguna untuk diverifikasi massal.',
            'user_ids.min' => 'Pilih setidaknya satu pengguna.',
        ]);

        $users = User::whereIn('id', $request->input('user_ids'))
            ->where('status', User::STATUS_MENUNGGU)
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($users as $user) {
            $user->update([
                'status' => User::STATUS_AKTIF,
                'verified_at' => now(),
                'verified_by' => Auth::id(),
                'catatan_verifikasi' => 'Verifikasi massal disetujui oleh Administrator.',
            ]);

            if (empty($user->email)) {
                $failedCount++;
                continue;
            }

            try {
                Mail::to($user->email)->send(
                    new AccountStatusMail($user, 'approved')
                );

                $sentCount++;
            } catch (Throwable $e) {
                report($e);
                $failedCount++;
            }
        }

        return back()->with(
            'success',
            "Sebanyak {$users->count()} akun diproses. Email berhasil dikirim ke {$sentCount} pengguna dan gagal dikirim ke {$failedCount} pengguna."
        );
    }
    /**
     * Ambil data detail pengguna (untuk modal popup detail).
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['verifiedBy', 'hewan']);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email ?? '-',
            'nik' => $user->nik ?? '-',
            'nomor_telepon' => $user->nomor_telepon ?? '-',
            'alamat' => $user->alamat ?? '-',
            'role' => $user->role,
            'role_label' => $user->role_label,
            'status' => $user->status,
            'status_label' => $user->status_label,
            'status_badge_class' => $user->status_badge_class,
            'created_at_formatted' => $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') : '-',
            'verified_at_formatted' => $user->verified_at ? $user->verified_at->translatedFormat('d F Y, H:i') : null,
            'verified_by_name' => $user->verifiedBy?->name,
            'catatan_verifikasi' => $user->catatan_verifikasi,
            'hewan' => $user->hewan->map(fn ($h) => [
                'id' => $h->id,
                'nama' => $h->nama,
                'jenis' => $h->jenis,
                'ras' => $h->ras ?: '-',
                'jenis_kelamin' => $h->jenis_kelamin,
                'umur' => $h->umur ?: '-',
            ]),
        ]);
    }
}
