<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Trackerin</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f5f7;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f5f7;
            padding: 20px 0;
        }
        .email-container {
            max-width: 570px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e1e4e8;
        }
        .email-header {
            background-color: #1e293b; /* Premium Dark Gray/Blue */
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            font-size: 20px;
            color: #1e293b;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .email-body p {
            font-size: 16px;
            line-height: 1.6;
            color: #4b5563;
            margin-top: 0;
            margin-bottom: 24px;
        }
        .otp-card {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            margin-bottom: 24px;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 6px;
            color: #2563eb; /* Active Blue */
            margin: 0;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .email-footer p {
            font-size: 12px;
            color: #94a3b8;
            margin: 0;
            line-height: 1.5;
        }
        .warning-text {
            font-size: 13px;
            color: #dc2626;
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>TRACKERIN</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                @if($type === 'register')
                    <h2>Verifikasi Email Pendaftaran Anda</h2>
                    <p>Halo,</p>
                    <p>Terima kasih telah melakukan pendaftaran di <strong>Trackerin</strong>. Untuk menyelesaikan pendaftaran dan mengaktifkan akun Anda, silakan gunakan kode OTP (One-Time Password) berikut:</p>
                @else
                    <h2>Permintaan Atur Ulang Kata Sandi</h2>
                    <p>Halo,</p>
                    <p>Kami menerima permintaan untuk melakukan atur ulang kata sandi (reset password) akun Anda di <strong>Trackerin</strong>. Silakan gunakan kode OTP berikut untuk melanjutkan proses:</p>
                @endif

                <!-- OTP Code -->
                <div class="otp-card">
                    <h3 style="margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #64748b;">Kode OTP Anda</h3>
                    <div class="otp-code">{{ $otp }}</div>
                </div>

                <div class="warning-text">
                    <strong>PENTING:</strong> Kode ini hanya berlaku selama <strong>15 menit</strong> sejak email ini dikirimkan. Demi keamanan akun Anda, mohon untuk tidak membagikan kode OTP ini kepada siapa pun.
                </div>

                <p>Jika Anda tidak merasa melakukan tindakan ini, Anda dapat mengabaikan email ini dengan aman.</p>
                
                <p>Salam hangat,<br><strong>Tim Trackerin</strong></p>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Trackerin. All rights reserved.</p>
                <p>Personalized Learning Tracker dengan dukungan AI.</p>
            </div>
        </div>
    </div>
</body>
</html>
