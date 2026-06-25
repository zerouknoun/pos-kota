<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #54E868;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header-logo {
            float: left;
            width: 55px;
            height: 55px;
        }
        .header-logo img {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
        }
        .header-text {
            float: left;
            margin-left: 15px;
        }
        .header-info {
            float: right;
            text-align: right;
            font-size: 8.5pt;
            color: #64748b;
        }
        .title {
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 9pt;
            color: #475569;
            margin: 2px 0 0 0;
        }
        .clear {
            clear: both;
        }
        
        /* Summary Widgets */
        .summary-container {
            margin-bottom: 25px;
        }
        .summary-box {
            width: 30%;
            float: left;
            margin-right: 3.3%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 8px;
        }
        .summary-box.last {
            margin-right: 0;
        }
        .summary-title {
            font-size: 7.5pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
        }
        
        /* Payment Breakdown Box */
        .payment-breakdown {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 25px;
            font-size: 9pt;
        }
        .payment-breakdown table {
            margin-bottom: 0;
        }
        .payment-breakdown td {
            border: none;
            padding: 4px 0;
        }

        .table-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 20px;
            margin-bottom: 8px;
            border-left: 3px solid #54E868;
            padding-left: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th {
            background-color: #f1f5f9;
            border-bottom: 1.5px solid #cbd5e1;
            color: #334155;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 8.5pt;
        }
        td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 10px;
            font-size: 8.5pt;
            color: #334155;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .badge-cash { background-color: #dcfce7; color: #166534; }
        .badge-qris { background-color: #dbeafe; color: #1e40af; }
        .badge-kasbon { background-color: #fef3c7; color: #92400e; }
        
        .footer {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    @php
        // Mengubah logo.png ke Base64 agar dapat dibaca DomPDF secara lokal tanpa HTTP requests
        $logoBase64 = '';
        $logoPath = public_path('logo.png');
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = base64_encode($logoData);
        }
    @endphp

    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            @if($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo">
            @else
                <div style="width: 55px; height: 55px; background: #54E868; border-radius: 8px; color: #000; font-weight: bold; text-align: center; line-height: 55px;">KT</div>
            @endif
        </div>
        <div class="header-text">
            <h1 class="title">KOPI TAKALAR</h1>
            <p class="subtitle">Laporan Penjualan Outlet POS Kasir Kota</p>
        </div>
        <div class="header-info">
            <p style="margin: 0; font-weight: bold; color: #0f172a;">{{ strtoupper($period_name) }}</p>
            <p style="margin: 3px 0 0 0;">Dicetak: {{ date('d-m-Y H:i') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Summary Widgets -->
    <div class="summary-container">
        <div class="summary-box">
            <div class="summary-title">Total Pendapatan</div>
            <div class="summary-value" style="color: #166534;">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">Total Transaksi</div>
            <div class="summary-value">{{ $total_orders }} Order</div>
        </div>
        <div class="summary-box last">
            <div class="summary-title">Total Cup Terjual</div>
            <div class="summary-value">{{ $total_cup_terjual }} Cup</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Rincian Metode Pembayaran -->
    <div class="table-title">Metode Pembayaran</div>
    <div class="payment-breakdown">
        <table style="width: 100%;">
            <tr>
                <td style="width: 33%;"><strong style="color: #166534;">💵 Cash:</strong> Rp {{ number_format($revenue_cash, 0, ',', '.') }}</td>
                <td style="width: 33%;"><strong style="color: #1e40af;">📱 QRIS:</strong> Rp {{ number_format($revenue_qris, 0, ',', '.') }}</td>
                <td style="width: 33%;"><strong style="color: #92400e;">📒 Kasbon:</strong> Rp {{ number_format($revenue_kasbon, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Tabel Produk Terlaris -->
    <div class="table-title">Top 5 Produk Terlaris</div>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 47%;">Nama Produk</th>
                <th style="width: 20%;">Kategori</th>
                <th style="width: 10%; text-align: center;">Jumlah</th>
                <th style="width: 15%; text-align: right;">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($top_products as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $item->product_name }}</td>
                    <td>{{ str_replace(['A. ', 'B. ', 'C. '], '', $item->product_category) }}</td>
                    <td class="text-center">{{ $item->total_qty }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #94a3b8; padding: 20px;">Tidak ada data penjualan produk pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tabel Rincian Transaksi -->
    <div class="table-title">Rincian Transaksi Penjualan</div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">ID Transaksi</th>
                <th style="width: 25%;">Tanggal & Waktu</th>
                <th style="width: 25%;">Nama Kasir</th>
                <th style="width: 15%; text-align: center;">Metode</th>
                <th style="width: 20%; text-align: right;">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td style="font-family: monospace; font-weight: bold;">#{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $order->shift->user->name ?? 'N/A' }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ strtolower($order->payment_method) }}">
                            {{ $order->payment_method }}
                        </span>
                    </td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #94a3b8; padding: 20px;">Tidak ada transaksi penjualan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Laporan Penjualan Otomatis &copy; {{ date('Y') }} Kopi Takalar POS. All rights reserved.
    </div>

</body>
</html>
