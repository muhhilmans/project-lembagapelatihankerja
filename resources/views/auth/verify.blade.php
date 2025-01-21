<!DOCTYPE html>
<html>

<head>
    <title>Verifikasi Email</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
        <thead>
            <tr>
                <td style="background-color: #007bff; color: #ffffff; text-align: center; padding: 20px;">
                    <h1 style="margin: 0; font-size: 24px;">Verifikasi Email</h1>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 20px; text-align: left; color: #333333;">
                    <p style="font-size: 16px; line-height: 1.5;">Halo,</p>
                    <p style="font-size: 16px; line-height: 1.5;">Terima kasih telah mendaftar. Silakan klik tombol di bawah untuk memverifikasi email Anda:</p>
                    <div style="text-align: center; margin: 20px 0;">
                        <a href="{{ $link }}" style="background-color: #007bff; color: #ffffff; padding: 10px 20px; text-decoration: none; font-size: 16px; border-radius: 5px;">Verifikasi Email</a>
                    </div>
                    <p style="font-size: 16px; line-height: 1.5;">Jika Anda tidak merasa mendaftar, Anda dapat mengabaikan email ini.</p>
                    <p style="font-size: 16px; line-height: 1.5;">Terima kasih,</p>
                    <p style="font-size: 16px; line-height: 1.5; font-weight: bold;">Tim {{ config('app.name') }}</p>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td style="background-color: #f1f1f1; text-align: center; padding: 15px; color: #777777; font-size: 14px;">
                    <p style="margin: 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
