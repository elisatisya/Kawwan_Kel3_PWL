<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Password — KAWWAN</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8F6F4; font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #181310; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #F8F6F4; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 580px; background-color: #FFFFFF; border-radius: 20px; border: 1px solid #EFEAE5; overflow: hidden; box-shadow: 0 10px 30px rgba(24, 19, 16, 0.05);">
                    
                    {{-- Header Brand --}}
                    <tr>
                        <td align="center" style="padding: 36px 40px 24px; border-bottom: 1px solid #F6EFEA; background-color: #FFFFFF;">
                            <span style="font-size: 26px; font-weight: 800; color: #181310; letter-spacing: -0.5px;">
                                KAW<span style="color: #FF6B00;">WAN</span>
                            </span>
                            <div style="font-size: 11px; font-weight: 700; color: #81766E; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 4px;">
                                Layanan Kesehatan Hewan
                            </div>
                        </td>
                    </tr>

                    {{-- Main Content --}}
                    <tr>
                        <td style="padding: 36px 40px 28px;">
                            <h1 style="margin: 0 0 16px; font-size: 22px; font-weight: 800; color: #181310; line-height: 1.3;">
                                Permintaan Atur Ulang Password
                            </h1>

                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #4B4540;">
                                Halo, <strong>{{ $user->name ?? 'Pengguna KAWWAN' }}</strong>
                            </p>

                            <p style="margin: 0 0 24px; font-size: 14px; line-height: 1.7; color: #6B7280;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi (password) akun Anda di aplikasi KAWWAN. Klik tombol di bawah ini untuk membuat password baru:
                            </p>

                            {{-- Button Reset Password --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 28px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background-color: #FF6B00; color: #FFFFFF; font-size: 15px; font-weight: 700; text-decoration: none; padding: 14px 34px; border-radius: 999px; box-shadow: 0 4px 14px rgba(255, 107, 0, 0.3); letter-spacing: 0.02em;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Info Kadaluwarsa --}}
                            <div style="background-color: #FFF7ED; border: 1px solid #FFEDD5; border-radius: 12px; padding: 14px 18px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #9A3412;">
                                    <strong>Penting:</strong> Tautan pengaturan ulang password ini hanya berlaku selama <strong>{{ $count }} menit</strong> demi keamanan akun Anda.
                                </p>
                            </div>

                            {{-- Abaikan jika bukan pemohon --}}
                            <p style="margin: 0 0 20px; font-size: 13px; line-height: 1.6; color: #81766E;">
                                Apabila Anda tidak pernah merasa meminta pengaturan ulang password, Anda dapat mengabaikan email ini dengan aman. Password akun Anda tidak akan berubah.
                            </p>

                            {{-- URL Cadangan --}}
                            <div style="border-top: 1px dashed #E5E7EB; padding-top: 20px; margin-top: 20px;">
                                <p style="margin: 0 0 8px; font-size: 12px; color: #9CA3AF; line-height: 1.5;">
                                    Jika tombol di atas tidak dapat diklik, salin dan tempel tautan berikut ke peramban (browser) Anda:
                                </p>
                                <p style="margin: 0; font-size: 11px; word-break: break-all; line-height: 1.4;">
                                    <a href="{{ $resetUrl }}" style="color: #FF6B00; text-decoration: underline;">
                                        {{ $resetUrl }}
                                    </a>
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 24px 40px; background-color: #FAF8F5; border-top: 1px solid #F3DCC9; text-align: center;">
                            <p style="margin: 0 0 6px; font-size: 13px; font-weight: 700; color: #181310;">
                                Tim KAWWAN
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #9CA3AF; line-height: 1.4;">
                                Membantu kesehatan hewan lebih mudah, cepat, dan terpercaya.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
