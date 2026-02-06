<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Tiket - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .ticket {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 2px solid #2563eb;
            border-radius: 10px;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .order-number {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .details {
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-label {
            width: 200px;
            color: #6b7280;
            font-weight: 600;
        }
        .detail-value {
            flex: 1;
            color: #1f2937;
        }
        .qr-section {
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            margin: 20px 0;
        }
        .qr-placeholder {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            background: white;
            border: 2px dashed #d1d5db;
            display: flex;
            align-items: center;
            justify-center;
            color: #9ca3af;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-used { background: #dbeafe; color: #1e40af; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        @media print {
            body { background: white; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h1>E-TIKET WISATA JEPARA</h1>
            <p>Dinas Pariwisata dan Kebudayaan Kabupaten Jepara</p>
        </div>

        <div class="content">
            <div class="order-number">
                No. Pesanan: {{ $order->order_number }}
            </div>

            <div class="details">
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="status-badge status-{{ $order->status }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Destinasi:</div>
                    <div class="detail-value">{{ $order->ticket->place->name }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Jenis Tiket:</div>
                    <div class="detail-value">{{ $order->ticket->name }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tanggal Kunjungan:</div>
                    <div class="detail-value">{{ $order->visit_date->format('d F Y') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Jumlah Tiket:</div>
                    <div class="detail-value">{{ $order->quantity }} tiket</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Nama Pemesan:</div>
                    <div class="detail-value">{{ $order->customer_name }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value">{{ $order->customer_email }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">No. Telepon:</div>
                    <div class="detail-value">{{ $order->customer_phone }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Total Pembayaran:</div>
                    <div class="detail-value" style="font-size: 20px; font-weight: bold; color: #2563eb;">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="qr-section">
                <h3 style="margin-top: 0;">QR Code Tiket</h3>
                <div class="qr-placeholder">
                    <div>
                        <div style="font-size: 48px;">⬜</div>
                        <div>{{ $order->order_number }}</div>
                    </div>
                </div>
                <p style="margin-bottom: 0; color: #6b7280;">Tunjukkan QR code ini saat berkunjung</p>
            </div>

            @if($order->notes)
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin-top: 20px;">
                    <strong>Catatan:</strong><br>
                    {{ $order->notes }}
                </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>Syarat & Ketentuan:</strong></p>
            <p>Tiket ini berlaku untuk {{ $order->ticket->valid_days }} hari sejak tanggal kunjungan.</p>
            <p>Harap tunjukkan tiket ini (cetak atau digital) beserta identitas diri saat memasuki lokasi wisata.</p>
            <p>Untuk informasi lebih lanjut, hubungi Dinas Pariwisata Jepara</p>
            <p style="margin-top: 20px;">Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="background: #2563eb; color: white; padding: 10px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
            <i class="fas fa-print"></i> Cetak Tiket
        </button>
        <button onclick="window.close()" style="background: #6b7280; color: white; padding: 10px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-left: 10px;">
            Tutup
        </button>
    </div>
</body>
</html>
