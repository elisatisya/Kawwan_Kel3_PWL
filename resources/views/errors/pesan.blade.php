@php
    $status = $exception->getStatusCode();

    $daftar = [
        403 => ['Akses Ditolak', 'Anda tidak memiliki izin untuk membuka halaman ini.'],
        404 => ['Halaman Tidak Ditemukan', 'Halaman yang Anda cari tidak ada atau sudah dipindahkan.'],
        419 => ['Sesi Berakhir', 'Sesi Anda sudah habis. Silakan muat ulang halaman atau masuk kembali.'],
        422 => ['Data Tidak Dapat Diproses', 'Permintaan Anda tidak dapat diproses. Silakan periksa kembali data yang dikirim.'],
        429 => ['Terlalu Banyak Permintaan', 'Mohon tunggu sebentar, lalu coba lagi.'],
        500 => ['Terjadi Kesalahan pada Server', 'Maaf, terjadi masalah pada sistem. Silakan coba lagi beberapa saat lagi.'],
        503 => ['Sistem Sedang Pemeliharaan', 'Layanan sedang dalam pemeliharaan. Silakan kembali beberapa saat lagi.'],
    ];

    [$judul, $pesan] = $daftar[$status] ?? ['Terjadi Kesalahan', 'Maaf, terjadi kesalahan. Silakan coba lagi.'];

    // Pesan khusus dari abort() hanya ditampilkan untuk 403 dan 422
    if (in_array($status, [403, 422], true) && trim($exception->getMessage()) !== '') {
        $pesan = $exception->getMessage();
    }

    $beranda = url('/');
    if (auth()->check()) {
        $beranda = match (auth()->user()->role) {
            \App\Models\User::ROLE_ADMIN => route('admin.dashboard'),
            \App\Models\User::ROLE_DOKTER, 'dokter' => route('dokter.dashboard'),
            \App\Models\User::ROLE_KADER => route('kader.dashboard'),
            default => route('pemilik.dashboard'),
        };
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status }} — {{ $judul }} | KAWWAN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: "Inter", Arial, sans-serif;
            background: #F8FAFC;
            color: #181310;
        }
        .err-card {
            width: 100%;
            max-width: 480px;
            padding: 36px 32px;
            text-align: center;
            background: #fff;
            border: 1px solid #F3DCC9;
            border-radius: 20px;
            box-shadow: 0 6px 24px rgba(24, 19, 16, 0.05);
        }
        .err-logo { height: 44px; margin-bottom: 18px; }
        .err-code { margin: 0; font-size: 64px; font-weight: 800; line-height: 1; color: #FF6B00; }
        .err-title { margin: 12px 0 8px; font-size: 20px; font-weight: 800; }
        .err-text { margin: 0 0 24px; font-size: 14px; line-height: 1.6; color: #81766E; }
        .err-actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .err-btn {
            padding: 10px 20px;
            border-radius: 999px;
            border: 1.5px solid #181310;
            font: 600 13px "Inter", Arial, sans-serif;
            text-decoration: none;
            cursor: pointer;
            background: transparent;
            color: #181310;
        }
        .err-btn-primary { background: #FF6B00; border-color: #FF6B00; color: #fff; }
    </style>
</head>
<body>
    <div class="err-card">
        <img class="err-logo" src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
        <p class="err-code">{{ $status }}</p>
        <h1 class="err-title">{{ $judul }}</h1>
        <p class="err-text">{{ $pesan }}</p>

        <div class="err-actions">
            <button type="button" class="err-btn" onclick="history.back()">Kembali</button>
            <a href="{{ $beranda }}" class="err-btn err-btn-primary">{{ auth()->check() ? 'Ke Dashboard' : 'Ke Beranda' }}</a>
        </div>
    </div>
</body>
</html>