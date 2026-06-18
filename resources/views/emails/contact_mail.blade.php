<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Us Message - Trackerin</title>
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
            font-size: 15px;
            line-height: 1.6;
            color: #4b5563;
            margin-top: 0;
            margin-bottom: 24px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .detail-table th {
            text-align: left;
            padding: 10px 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            text-transform: uppercase;
            color: #64748b;
            width: 120px;
        }
        .detail-table td {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            color: #1e293b;
        }
        .message-box {
            background-color: #f8fafc;
            border-left: 4px solid #1e293b;
            padding: 20px;
            border-radius: 4px;
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
            margin-bottom: 24px;
            white-space: pre-wrap;
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
                <h2>Pesan Baru Dari Pengunjung Website</h2>
                <p>Halo Admin,</p>
                <p>Ada pesan baru yang dikirimkan oleh pengunjung melalui form Contact Us di website Trackerin. Berikut rincian pesan tersebut:</p>

                <!-- Sender Info Table -->
                <table class="detail-table">
                    <tr>
                        <th>Nama</th>
                        <td>{{ $contact->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><a href="mailto:{{ $contact->email }}" style="color: #2563eb; text-decoration: none;">{{ $contact->email }}</a></td>
                    </tr>
                    <tr>
                        <th>Subjek</th>
                        <td><strong>{{ $contact->subject }}</strong></td>
                    </tr>
                    <tr>
                        <th>Waktu Kirim</th>
                        <td>{{ $contact->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</td>
                    </tr>
                </table>

                <!-- Message Box -->
                <div style="font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 8px; font-weight: bold;">Isi Pesan:</div>
                <div class="message-box">{{ $contact->message }}</div>

                <p style="font-size: 13px; color: #64748b;">
                    <em>Tip: Anda dapat membalas email ini secara langsung untuk menjawab pesan pengunjung di atas.</em>
                </p>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Trackerin. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
