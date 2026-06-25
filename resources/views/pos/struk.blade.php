<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk - KOPI TAKALAR</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #000; margin: 0; padding: 10px; background: #fff;}
        .receipt-container { width: 58mm; margin: 0 auto; } /* Ukuran standar printer thermal */
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-bottom: 1px dashed #000; margin: 5px 0; }
        .flex-between { display: flex; justify-content: space-between; }
        .logo-text { font-size: 16px; margin-bottom: 2px;}
        .theme-btn { background-color: #54E868; border: none; padding: 10px; font-weight: bold; width: 100%; cursor: pointer; border-radius: 5px;}
        @media print {
            .no-print { display: none; }
            @page { margin: 0; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="text-center">
            <div class="bold logo-text">KOPI TAKALAR</div>
            <div>Jl. Poros Takalar</div>
            <div>------------------------</div>
        </div>
        
        <div>Kasir : {{ auth()->user()->name }}</div>
        <div>Waktu : {{ date('d/m/Y H:i') }}</div>
        <div>Metode: {{ $order->payment_method }}</div>
        <div class="divider"></div>

        @foreach($order->items as $item)
        <div class="flex-between">
            <span>{{ $item->qty }}x {{ $item->product->name }}</span>
            <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach

        <div class="divider"></div>
        <div class="flex-between bold">
            <span>TOTAL</span>
            <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
        <div class="text-center" style="margin-top: 15px;">
            Terima Kasih<br>Silakan Berkunjung Kembali
        </div>

        <div class="no-print" style="margin-top: 20px;">
            <button onclick="window.print()" class="theme-btn">🖨️ Cetak Struk</button>
            <a href="{{ route('pos.index') }}" style="display:block; text-align:center; margin-top:10px; color:#000; text-decoration:none;">Kembali ke Kasir</a>
        </div>
    </div>
</body>
</html>