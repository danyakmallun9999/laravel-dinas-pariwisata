<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan SIG Desa Mayong Lor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .stat-card h3 {
            margin: 0;
            font-size: 32px;
            color: #2563eb;
        }
        .stat-card p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #2563eb;
            color: white;
        }
        tr:nth-child(even) {
            background: #f8fafc;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistem Informasi Geografis</h1>
        <h2>Desa Mayong Lor</h2>
        <p>Laporan Data Spasial</p>
        <p>Tanggal: {{ date('d F Y') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>{{ $stats['places_count'] }}</h3>
            <p>Titik Lokasi</p>
        </div>
        <div class="stat-card">
            <h3>{{ $stats['boundaries_count'] }}</h3>
            <p>Batas Wilayah</p>
        </div>
        <div class="stat-card">
            <h3>{{ $stats['infrastructures_count'] }}</h3>
            <p>Infrastruktur</p>
        </div>
        <div class="stat-card">
            <h3>{{ $stats['land_uses_count'] }}</h3>
            <p>Penggunaan Lahan</p>
        </div>
    </div>

    <h2>Statistik per Kategori Lokasi</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats['categories'] as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->places_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Statistik Infrastruktur per Tipe</h2>
    <table>
        <thead>
            <tr>
                <th>Tipe</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats['infrastructure_types'] as $infra)
                <tr>
                    <td>{{ ucfirst($infra->type) }}</td>
                    <td>{{ $infra->count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($stats['total_infrastructure_length'])
        <p><strong>Total Panjang Infrastruktur:</strong> {{ number_format($stats['total_infrastructure_length'], 2) }} meter</p>
    @endif

    <h2>Statistik Penggunaan Lahan</h2>
    <table>
        <thead>
            <tr>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Total Luas (ha)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats['land_use_types'] as $landUse)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $landUse->type)) }}</td>
                    <td>{{ $landUse->count }}</td>
                    <td>{{ number_format($landUse->total_area ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($stats['total_land_area'])
        <p><strong>Total Luas Lahan:</strong> {{ number_format($stats['total_land_area'], 2) }} hektar</p>
    @endif

    <div class="footer">
        <p>Dibuat oleh Sistem Informasi Geografis Desa Mayong Lor</p>
        <p>© {{ date('Y') }} Pemerintah Desa Mayong Lor</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Cetak sebagai PDF
        </button>
    </div>
</body>
</html>

