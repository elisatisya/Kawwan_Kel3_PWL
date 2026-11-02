
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Informasi Akun Kawwan</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">

    <div style="max-width: 600px; margin: auto; padding: 25px;">

        <h2 style="color: #16a34a;">Kawwan</h2>

        <p>Halo, {{ $user->name }}!</p>

        @if ($status === 'approved')

            <h3 style="color: #16a34a;">
                Pendaftaran Akun Disetujui
            </h3>

            <p>
                Selamat! Pendaftaran akun Kawwan kamu telah disetujui
                oleh Administrator.
            </p>

            <p>
                Sekarang kamu sudah dapat login dan menggunakan
                layanan Kawwan.
            </p>

        @else

            <h3 style="color: #dc2626;">
                Pendaftaran Akun Belum Disetujui
            </h3>

            <p>
                Mohon maaf, pendaftaran akun Kawwan kamu belum
                dapat disetujui oleh Administrator.
            </p>

            <p><strong>Alasan penolakan:</strong></p>

            <div style="background: #fef2f2; padding: 15px; border-radius: 8px;">
                {{ $reason }}
            </div>

            <p>
                Silakan periksa kembali data pendaftaran kamu.
            </p>

        @endif

        <p style="margin-top: 30px;">
            Salam,<br>
            <strong>Tim Kawwan</strong>
        </p>

    </div>

</body>
</html>
