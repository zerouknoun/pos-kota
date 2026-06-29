<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - KOPI TAKALAR</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    
    <style>
        /* Base styles for screen view */
        body { 
            font-family: 'Outfit', sans-serif;
            background: #f1f5f9; /* Slate 100 */
            color: #1e293b; /* Slate 800 */
            margin: 0; 
            padding: 24px 16px; 
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .receipt-container { 
            width: 100%;
            max-width: 380px; 
            background: #ffffff;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border-top: 8px solid #54E868; /* Theme green stripe */
            box-sizing: border-box;
        }

        .receipt-content {
            font-family: 'Share Tech Mono', 'Courier New', Courier, monospace;
            font-size: 14px;
            line-height: 1.5;
            color: #0f172a;
        }

        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-bottom: 1px dashed #cbd5e1; margin: 8px 0; }
        .flex-between { display: flex; justify-content: space-between; }
        .logo-text { font-size: 18px; margin-bottom: 4px; color: #000; font-weight: 700; letter-spacing: 0.05em; }
        
        .theme-btn { 
            background-color: #54E868; 
            color: #0f172a;
            border: none; 
            padding: 12px 16px; 
            font-weight: bold; 
            font-family: 'Outfit', sans-serif;
            width: 100%; 
            cursor: pointer; 
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            box-shadow: 0 4px 6px -1px rgba(84, 232, 104, 0.2), 0 2px 4px -2px rgba(84, 232, 104, 0.2);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .theme-btn:hover {
            background-color: #43cf56;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(84, 232, 104, 0.3), 0 4px 6px -4px rgba(84, 232, 104, 0.3);
        }

        .theme-btn:active {
            transform: translateY(0);
        }

        .back-btn {
            display: block; 
            text-align: center; 
            margin-top: 14px; 
            color: #64748b; 
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.15s ease;
        }

        .back-btn:hover {
            color: #0f172a;
        }

        /* Print overrides to format exactly for 58mm printer */
        @media print {
            body { 
                background: #fff; 
                padding: 0; 
                margin: 0;
                color: #000;
                display: block;
                font-family: 'Courier New', Courier, monospace;
            }
            .receipt-container { 
                width: 58mm; 
                max-width: none;
                margin: 0 auto; 
                box-shadow: none;
                padding: 0;
                border-radius: 0;
                border-top: none;
            }
            .receipt-content {
                font-family: 'Courier New', Courier, monospace;
                font-size: 12px;
                color: #000;
            }
            .divider {
                border-bottom: 1px dashed #000;
                margin: 5px 0;
            }
            .logo-text {
                font-size: 16px;
                margin-bottom: 2px;
            }
            .no-print { 
                display: none !important; 
            }
            @page { 
                margin: 0; 
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-content">
            <div class="text-center">
                <div class="bold logo-text">KOPI TAKALAR</div>
                <div>Jl. Poros Takalar</div>
                <div>------------------------</div>
            </div>
            
            <div style="margin-top: 10px;">Kasir : {{ auth()->user() ? auth()->user()->name : '-' }}</div>
            <div>Waktu : {{ date('d/m/Y H:i') }}</div>
            <div>Metode: {{ $order->payment_method }}</div>
            <div class="divider"></div>

            <div style="margin-top: 5px;">
                @foreach($order->items as $item)
                <div class="flex-between" style="margin-bottom: 4px;">
                    <span>{{ $item->qty }}x {{ $item->product->name }}</span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>

            <div class="divider"></div>
            <div class="flex-between bold" style="font-size: 15px; margin: 10px 0;">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            
            <div class="text-center" style="margin-top: 20px; font-size: 13px;">
                Terima Kasih<br>Silakan Berkunjung Kembali
            </div>
        </div>

        <div class="no-print" style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px; width: 100%;">
            <!-- Direct Server-side Print Button -->
            <button id="btn-print-direct" onclick="printDirect()" class="theme-btn" style="background-color: #3b82f6; color: #ffffff; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);">
                <span>💻 Cetak Struk (Server)</span>
            </button>

            <!-- RawBT Android App Print Button -->
            <button onclick="printRawBt()" class="theme-btn" style="background-color: #f59e0b; color: #ffffff; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);">
                <span>📱 Cetak via RawBT App (Android)</span>
            </button>
            
            <!-- System Print Button -->
            <button onclick="window.print()" class="theme-btn">
                <span>📄 Cetak Biasa (Browser / PDF)</span>
            </button>

            <!-- Printing Status Message -->
            <div id="print-status" style="font-size: 13px; font-weight: 500; text-align: center; display: none; padding: 10px; border-radius: 8px; line-height: 1.4;"></div>
            
            <a href="{{ route('pos.index') }}" class="back-btn">Kembali ke Kasir</a>
        </div>
    </div>

    <!-- Direct printing script -->
    <script>
        async function printDirect() {
            const btn = document.getElementById('btn-print-direct');
            const statusDiv = document.getElementById('print-status');
            
            btn.disabled = true;
            statusDiv.style.display = 'block';
            statusDiv.style.color = '#1e3a8a'; // Dark blue text
            statusDiv.style.background = '#eff6ff'; // Soft blue background
            statusDiv.style.border = '1px solid #bfdbfe';
            statusDiv.innerHTML = '⏳ Sedang mengirim data ke printer thermal...';
            
            try {
                const response = await fetch("/pos/order/{{ $order->id }}/print-direct", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    statusDiv.style.color = '#14532d'; // Dark green text
                    statusDiv.style.background = '#f0fdf4'; // Soft green background
                    statusDiv.style.border = '1px solid #bbf7d0';
                    statusDiv.innerHTML = '✅ ' + result.message;
                } else {
                    statusDiv.style.color = '#7f1d1d'; // Dark red text
                    statusDiv.style.background = '#fef2f2'; // Soft red background
                    statusDiv.style.border = '1px solid #fecaca';
                    statusDiv.innerHTML = '❌ ' + result.message;
                }
            } catch (err) {
                console.error(err);
                statusDiv.style.color = '#7f1d1d';
                statusDiv.style.background = '#fef2f2';
                statusDiv.style.border = '1px solid #fecaca';
                statusDiv.innerHTML = '❌ Terjadi kesalahan koneksi saat mengirim perintah cetak.';
            } finally {
                btn.disabled = false;
            }
        }

        function printRawBt() {
            const base64Data = {!! json_encode($base64Bytes ?? '') !!};
            if (!base64Data || base64Data.trim() === '') {
                alert('Data cetak ESC/POS dari server tidak tersedia.');
                return;
            }
            window.location.href = "rawbt:base64," + base64Data;
        }
    </script>
</body>
</html>