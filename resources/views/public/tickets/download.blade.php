<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Tiket - {{ $order->order_number }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f1f5f9;
            padding: 20px;
            color: #1e293b;
        }
        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .ticket {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 32px 40px;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .ticket-badge {
            position: absolute;
            top: 24px;
            right: 24px;
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        .content {
            padding: 32px 40px;
        }
        .order-number-section {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 4px solid #3b82f6;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .order-value {
            font-size: 20px;
            font-weight: 700;
            color: #1e40af;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-used { background: #dbeafe; color: #1e40af; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        .detail-item {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }
        .detail-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }
        .detail-value.price {
            color: #3b82f6;
            font-size: 24px;
        }
        .qr-section {
            background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
            border-radius: 20px;
            padding: 32px;
            text-align: center;
            margin: 24px 0;
            border: 2px dashed #e2e8f0;
        }
        .qr-title {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        #qrcode {
            display: flex;
            justify-content: center;
            margin-bottom: 16px;
        }
        #qrcode canvas {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .qr-instruction {
            color: #64748b;
            font-size: 13px;
        }
        .customer-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title::before {
            content: '👤';
        }
        .customer-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .customer-item {
            font-size: 14px;
        }
        .customer-item span {
            display: block;
            color: #64748b;
            font-size: 12px;
            margin-bottom: 4px;
        }
        .notes-section {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .notes-section strong {
            color: #92400e;
        }
        .footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 24px 40px;
            font-size: 12px;
            line-height: 1.8;
        }
        .footer strong {
            color: white;
            display: block;
            margin-bottom: 8px;
        }
        .footer-info {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #334155;
            display: flex;
            justify-content: space-between;
        }
        .print-actions {
            text-align: center;
            margin-top: 24px;
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        @media print {
            body { 
                background: white; 
                padding: 0;
            }
            .ticket {
                box-shadow: none;
                border-radius: 0;
            }
            .print-actions { 
                display: none !important; 
            }
            .header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        @media (max-width: 600px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
            .customer-info {
                grid-template-columns: 1fr;
            }
            .content {
                padding: 24px;
            }
            .header {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket">
            <div class="header">
                <div class="header-content">
                    <h1>E-TIKET WISATA JEPARA</h1>
                    <p>Dinas Pariwisata dan Kebudayaan Kabupaten Jepara</p>
                </div>
                <div class="ticket-badge">🎫 E-Ticket</div>
            </div>

            <div class="content">
                <div class="order-number-section">
                    <div>
                        <div class="order-label">Nomor Pesanan</div>
                        <div class="order-value">{{ $order->order_number }}</div>
                    </div>
                    <span class="status-badge status-{{ $order->status }}">
                        @if($order->status == 'paid')✓@elseif($order->status == 'pending')⏳@elseif($order->status == 'used')✔@else✕@endif
                        {{ $order->status_label }}
                    </span>
                </div>

                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Destinasi</div>
                        <div class="detail-value">{{ $order->ticket->place->name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Jenis Tiket</div>
                        <div class="detail-value">{{ $order->ticket->name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Tanggal Kunjungan</div>
                        <div class="detail-value">{{ $order->visit_date->translatedFormat('d F Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Jumlah Tiket</div>
                        <div class="detail-value">{{ $order->quantity }} tiket</div>
                    </div>
                </div>

                <div class="detail-item" style="margin-bottom: 24px;">
                    <div class="detail-label">Total Pembayaran</div>
                    <div class="detail-value price">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                </div>

                <div class="qr-section">
                    <div class="qr-title">📱 Scan QR Code untuk Verifikasi</div>
                    <div id="qrcode"></div>
                    <p class="qr-instruction">Tunjukkan QR code ini kepada petugas saat memasuki lokasi wisata</p>
                </div>

                <div class="customer-section">
                    <div class="section-title">Informasi Pemesan</div>
                    <div class="customer-info">
                        <div class="customer-item">
                            <span>Nama Lengkap</span>
                            <strong>{{ $order->customer_name }}</strong>
                        </div>
                        <div class="customer-item">
                            <span>Email</span>
                            <strong>{{ $order->customer_email }}</strong>
                        </div>
                        <div class="customer-item">
                            <span>No. Telepon</span>
                            <strong>{{ $order->customer_phone }}</strong>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                    <div class="notes-section">
                        <strong>📝 Catatan:</strong>
                        {{ $order->notes }}
                    </div>
                @endif
            </div>

            <div class="footer">
                <strong>Syarat & Ketentuan:</strong>
                <p>• Tiket ini berlaku untuk {{ $order->ticket->valid_days }} hari sejak tanggal kunjungan.</p>
                <p>• Harap tunjukkan tiket ini (cetak atau digital) beserta identitas diri saat memasuki lokasi wisata.</p>
                <p>• Tiket tidak dapat dipindahtangankan atau dijual kembali.</p>
                <div class="footer-info">
                    <span>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</span>
                    <span>Dinas Pariwisata Jepara</span>
                </div>
            </div>
        </div>

        <div class="print-actions">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Cetak Tiket
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                ✕ Tutup
            </button>
        </div>
    </div>

    <script>
        // Generate QR code on page load
        document.addEventListener('DOMContentLoaded', function() {
            new QRCode(document.getElementById("qrcode"), {
                text: "{{ $order->order_number }}",
                width: 180,
                height: 180,
                colorDark : "#1e293b",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>
