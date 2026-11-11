<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $roleFilter = $request->query('role', 'semua');
        $statusFilter = $request->query('status', 'semua');

        $query = User::withCount('hewan')->latest('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('nomor_telepon', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== 'semua') {
            if ($roleFilter === 'pemilik') {
                $query->whereIn('role', [User::ROLE_PEMILIK, 'pemilik']);
            } elseif ($roleFilter === 'dokter') {
                $query->whereIn('role', [User::ROLE_DOKTER, 'dokter']);
            } elseif ($roleFilter === 'kader') {
                $query->where('role', User::ROLE_KADER);
            } elseif ($roleFilter === 'admin') {
                $query->where('role', User::ROLE_ADMIN);
            }
        }

        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        $users = $query->paginate(12)->withQueryString();

        $roleCounts = [
            'total' => User::count(),
            'pemilik' => User::whereIn('role', [User::ROLE_PEMILIK, 'pemilik'])->count(),
            'dokter' => User::whereIn('role', [User::ROLE_DOKTER, 'dokter'])->count(),
            'kader' => User::where('role', User::ROLE_KADER)->count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
        ];

        return view('admin.pengguna', compact('users', 'search', 'roleFilter', 'statusFilter', 'roleCounts'));
    }

    /**
     * Simpan pembuatan akun Kader atau Dokter baru oleh Admin.
     * (Sesuai arahan PDF: akun peran khusus dibuat secara terkontrol melalui Admin, bukan registrasi publik).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nik' => ['nullable', 'string', 'max:20', 'unique:users,nik'],
            'nomor_telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'role' => ['required', Rule::in([User::ROLE_DOKTER, User::ROLE_KADER, User::ROLE_ADMIN])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'nik.unique' => 'NIK sudah digunakan akun lain.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'role.required' => 'Role petugas wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'nik' => ! empty($validated['nik']) ? trim($validated['nik']) : null,
            'nomor_telepon' => trim($validated['nomor_telepon']),
            'alamat' => ! empty($validated['alamat']) ? trim($validated['alamat']) : null,
            'role' => $validated['role'],
            'status' => User::STATUS_AKTIF, // Akun yang dibuat oleh admin langsung berstatus aktif & terverifikasi
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'catatan_verifikasi' => 'Akun peran khusus dibuat oleh Administrator.',
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', "Akun {$user->role_label} baru atas nama {$user->name} berhasil dibuat.");
    }

    /**
     * Perbarui data pengguna (Update profil / kredensial).
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('users', 'nik')->ignore($user->id)],
            'nomor_telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'role' => ['required', Rule::in([
                User::ROLE_PEMILIK,
                User::ROLE_DOKTER,
                User::ROLE_KADER,
                User::ROLE_ADMIN,
                'pemilik',
                'dokter',
            ])],
            'status' => ['required', Rule::in([
                User::STATUS_AKTIF,
                User::STATUS_MENUNGGU,
                User::STATUS_DITOLAK,
                User::STATUS_NONAKTIF,
            ])],
            'password' => ['nullable', 'string', 'min:8'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'nik.unique' => 'NIK sudah digunakan oleh akun lain.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'role.required' => 'Peran pengguna wajib dipilih.',
            'status.required' => 'Status pengguna wajib dipilih.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $updateData = [
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'nik' => ! empty($validated['nik']) ? trim($validated['nik']) : null,
            'nomor_telepon' => trim($validated['nomor_telepon']),
            'alamat' => ! empty($validated['alamat']) ? trim($validated['alamat']) : null,
            'status' => $validated['status'],
        ];

        // Jangan izinkan admin menghapus role admin dari dirinya sendiri
        if ($user->id !== Auth::id()) {
            $updateData['role'] = $validated['role'];
        }

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Perbarui role pengguna.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun administrator Anda sendiri.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in([
                User::ROLE_PEMILIK,
                User::ROLE_DOKTER,
                User::ROLE_KADER,
                User::ROLE_ADMIN,
            ])],
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('success', "Role untuk pengguna {$user->name} berhasil diubah menjadi {$user->role_label}.");
    }

    /**
     * Hapus akun pengguna secara permanen jika diizinkan sistem.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $nama = $user->name;
        $user->delete();

        return back()->with('info', "Akun {$nama} telah berhasil dihapus dari sistem.");
    }
}
