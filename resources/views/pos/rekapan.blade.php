<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapan Shift - KOPI TAKALAR</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <!-- html2canvas for Image Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .receipt-font {
            font-family: 'Share Tech Mono', 'Courier New', Courier, monospace;
        }
        /* Custom Neon Green Theme Colors */
        .theme-bg { background-color: #54E868; }
        .theme-text { color: #2bb93f; }
        .theme-border { border-color: #54E868; }
        .theme-btn:hover {
            background-color: #43cf56;
            transform: translateY(-1px);
        }
        
        /* Receipt Paper Style */
        .receipt-paper {
            position: relative;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; font-size: 11px; }
            .receipt-paper { box-shadow: none; border: none; padding: 0; width: 58mm; margin: 0 auto; border-radius: 0; }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen py-6 px-4 flex flex-col items-center justify-start sm:py-12">

    <!-- Container Utama -->
    <div class="w-full max-w-md flex flex-col gap-6">
        
        <!-- Header Aplikasi (Disembunyikan saat cetak) -->
        <div class="flex items-center justify-between px-2 no-print">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full theme-bg animate-pulse"></span>
                <h1 class="text-lg font-bold text-slate-800 tracking-wide uppercase">KOPI TAKALAR</h1>
            </div>
            <span class="text-xs bg-slate-200 text-slate-700 px-2 py-1 rounded-full font-medium">Rekapan Shift</span>
        </div>

        <!-- Receipt / Struk Fisik -->
        <div id="receipt-card" class="receipt-paper p-6 border-t-8 border-[#54E868] text-slate-800 receipt-font text-sm">
            <!-- Header Struk -->
            <div class="text-center mb-6">
                <h2 class="font-bold text-lg tracking-wider text-slate-900">LAPORAN PENJUALAN KOTA</h2>
                <div class="w-full border-b border-dashed border-slate-300 my-2"></div>
                <div class="flex justify-between text-xs text-slate-600 mt-1">
                    <span>Kasir: {{ strtoupper($shift->user->name) }}</span>
                    <span>{{ \Carbon\Carbon::parse($shift->end_time)->translatedFormat('d/m/y H:i') }}</span>
                </div>
            </div>

            <!-- Stok Cup Section -->
            <div class="mb-4">
                <p class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-1">STOK CUP</p>
                <div class="flex justify-between">
                    <span>Stok Cup Awal</span>
                    <span class="font-semibold">{{ $shift->initial_cup }} pcs</span>
                </div>
                <div class="flex justify-between">
                    <span>Cup Terpakai</span>
                    <span class="font-semibold text-rose-500">-{{ $used_cup }} pcs</span>
                </div>
                <div class="flex justify-between border-t border-dashed border-slate-200 pt-1 mt-1">
                    <span>Sisa Cup Akhir</span>
                    <span class="font-bold theme-text">{{ $shift->final_cup }} pcs</span>
                </div>
            </div>

            <div class="w-full border-b border-dashed border-slate-300 my-3"></div>

            <!-- Detail Penjualan Section -->
            <div class="mb-4">
                <p class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-2">RINCIAN PENJUALAN</p>
                
                @foreach(['A. ICE', 'B. HOT', 'C. Gelas'] as $kategori)
                    <div class="mb-3">
                        <span class="font-bold text-slate-700 bg-slate-100 px-1 rounded text-xs">{{ $kategori }}</span>
                        <div class="mt-1 space-y-1 pl-2">
                            @if(isset($sold_items[$kategori]) && count($sold_items[$kategori]) > 0)
                                @foreach($sold_items[$kategori] as $item)
                                    <div class="flex justify-between text-xs">
                                        <span>• {{ strtoupper($item->name) }}</span>
                                        <span class="font-semibold">{{ $item->total_qty }}x</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-xs text-slate-400 italic">• Tidak ada penjualan</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="w-full border-b border-dashed border-slate-300 my-3"></div>

            <!-- Keuangan Section -->
            <div class="mb-4 space-y-1">
                <p class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-2">REKAPAN KAS (IDR)</p>
                <div class="flex justify-between text-xs">
                    <span>Pembayaran QRIS</span>
                    <span class="font-semibold">{{ number_format($shift->total_qris, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>Pembayaran TUNAI</span>
                    <span class="font-semibold">{{ number_format($shift->total_cash, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>Kasbon / Piutang</span>
                    <span class="font-semibold text-amber-600">{{ number_format($shift->total_kasbon, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold border-t border-dashed border-slate-300 pt-2 mt-2">
                    <span>TOTAL REVENUE</span>
                    <span class="text-emerald-600">{{ number_format($shift->total_revenue, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="w-full border-b border-dashed border-slate-300 my-3"></div>

            <!-- Tanda Tangan Section -->
            <div class="mt-6 flex justify-between items-center text-xs">
                <div class="text-center w-24">
                    <p class="text-slate-400">Verifikator</p>
                    <div class="h-10"></div>
                    <p class="font-bold border-t border-slate-300 pt-1">( Owner )</p>
                </div>
                <div class="text-center w-24">
                    <p class="text-slate-400">Barista</p>
                    <div class="h-10"></div>
                    <p class="font-bold border-t border-slate-300 pt-1">({{ strtoupper($shift->user->name) }})</p>
                </div>
            </div>

            <div class="text-center mt-6 text-[10px] text-slate-400 italic">
                <p>Terima kasih atas kerja keras Anda hari ini!</p>
                <p>Kopi Takalar - POS Kota v1.0</p>
            </div>
        </div>

        <!-- Panel Tombol Aksi Mobile-Friendly (no-print) -->
        <div class="flex flex-col gap-3 no-print px-1">
            <!-- Row Utama Sharing -->
            <div class="grid grid-cols-2 gap-2">
                <button onclick="shareWhatsApp()" class="flex items-center justify-center gap-2 bg-[#25D366] text-white font-medium py-3 px-4 rounded-xl shadow-sm transition hover:bg-[#20ba59] active:scale-[0.98]">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.863-9.73.001-2.595-1.013-5.035-2.855-6.881C16.637 2.15 14.192 1.132 11.6 1.132c-5.441 0-9.864 4.372-9.867 9.732-.001 1.79.489 3.535 1.417 5.086L2.164 21.84l4.483-1.686zM17.487 14.39c-.298-.15-1.766-.88-2.04-.98-.277-.101-.478-.15-.678.15-.2.298-.777.98-.95 1.178-.178.199-.355.223-.654.073-1.2-.598-2.008-1.054-2.8-2.42-.198-.344.198-.32.565-1.05.074-.15.038-.282-.019-.397-.056-.113-.477-1.168-.654-1.6-.172-.419-.347-.361-.477-.361-.124-.005-.266-.006-.409-.006-.143 0-.376.054-.572.27-.197.216-.752.742-.752 1.81 0 1.068.769 2.099.877 2.247.11.147 1.514 2.342 3.667 3.287.51.224.908.359 1.218.458.513.163.98.14 1.349.085.412-.061 1.766-.729 2.016-1.433.25-.705.25-1.312.175-1.433-.075-.12-.275-.195-.573-.346z"/>
                    </svg>
                    <span>Bagikan WA</span>
                </button>

                <button onclick="copyReport()" class="flex items-center justify-center gap-2 bg-slate-800 text-white font-medium py-3 px-4 rounded-xl shadow-sm transition hover:bg-slate-700 active:scale-[0.98]">
                    <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span>Salin Teks</span>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button onclick="shareReceiptImage()" class="flex items-center justify-center gap-2 bg-sky-600 text-white font-medium py-3 px-4 rounded-xl shadow-sm transition hover:bg-sky-500 active:scale-[0.98]">
                    <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <span>Kirim Gambar</span>
                </button>

                <button onclick="window.print()" class="flex items-center justify-center gap-2 bg-slate-200 text-slate-800 font-medium py-3 px-4 rounded-xl shadow-sm transition hover:bg-slate-300 active:scale-[0.98]">
                    <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Cetak Struk</span>
                </button>
            </div>

            <!-- Tombol Keluar Utama -->
            <form action="{{ route('logout') }}" method="POST" class="w-full mt-2">
                @csrf
                <button type="submit" class="w-full theme-bg text-black font-bold py-4 rounded-xl shadow-lg transition hover:bg-[#43cf56] active:scale-[0.98] flex items-center justify-center gap-2 text-base">
                    <span>Selesai & Keluar Shift</span>
                    <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </button>
            </form>
        </div>

    </div>

    <!-- Script Fungsional untuk Mobile Sharing -->
    <script>
        // Generate WhatsApp / Copy-Paste format text
        function getReportText() {
            let text = `*LAPORAN PENJUALAN KOTA*\n`;
            text += `*KOPI TAKALAR*\n`;
            text += `----------------------------------\n`;
            text += `Kasir   : {{ strtoupper($shift->user->name) }}\n`;
            text += `Tanggal : {{ \Carbon\Carbon::parse($shift->end_time)->translatedFormat('d F Y H:i') }}\n`;
            text += `----------------------------------\n`;
            text += `*STOK CUP*\n`;
            text += `Stok Cup Awal : {{ $shift->initial_cup }} pcs\n`;
            text += `Cup Terpakai  : {{ $used_cup }} pcs\n`;
            text += `Sisa Cup Akhir: {{ $shift->final_cup }} pcs\n`;
            text += `----------------------------------\n`;
            text += `*RINCIAN PENJUALAN*\n`;
            
            @foreach(['A. ICE', 'B. HOT', 'C. Gelas'] as $kategori)
                text += `\n*{{ $kategori }}*\n`;
                @if(isset($sold_items[$kategori]) && count($sold_items[$kategori]) > 0)
                    @foreach($sold_items[$kategori] as $item)
                        text += `- {{ strtoupper($item->name) }} : {{ $item->total_qty }}x\n`;
                    @endforeach
                @else
                    text += `- (Tidak ada penjualan)\n`;
                @endif
            @endforeach
            
            text += `----------------------------------\n`;
            text += `*REKAPAN KAS (IDR)*\n`;
            text += `QRIS   : Rp {{ number_format($shift->total_qris, 0, ',', '.') }}\n`;
            text += `CASH   : Rp {{ number_format($shift->total_cash, 0, ',', '.') }}\n`;
            text += `KASBON : Rp {{ number_format($shift->total_kasbon, 0, ',', '.') }}\n`;
            text += `TOTAL  : *Rp {{ number_format($shift->total_revenue, 0, ',', '.') }}*\n`;
            text += `----------------------------------\n`;
            text += `Belanja: \n`;
            text += `----------------------------------\n`;
            text += `_Laporan shift ditutup otomatis_`;
            return text;
        }

        // Salin Teks Laporan
        function copyReport() {
            const text = getReportText();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('📋 Laporan disalin ke clipboard!');
                }).catch(err => {
                    fallbackCopyText(text);
                });
            } else {
                fallbackCopyText(text);
            }
        }

        function fallbackCopyText(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast('📋 Laporan disalin ke clipboard!');
            } catch (err) {
                showToast('❌ Gagal menyalin laporan');
            }
            document.body.removeChild(textArea);
        }

        // Kirim Laporan Teks ke WhatsApp
        function shareWhatsApp() {
            const text = encodeURIComponent(getReportText());
            window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
        }

        // Ambil screenshot struk dan kirim / unduh
        function shareReceiptImage() {
            const receiptElement = document.getElementById('receipt-card');
            
            // Tampilkan loading / feedback visual
            showToast('⏳ Memproses gambar struk...');
            
            html2canvas(receiptElement, {
                scale: 3, // Skala 3x agar resolusi sangat tajam di HP
                backgroundColor: '#ffffff',
                useCORS: true
            }).then(canvas => {
                canvas.toBlob(blob => {
                    if (!blob) {
                        showToast('❌ Gagal membuat gambar');
                        return;
                    }
                    
                    const filename = `Laporan_Shift_{{ $shift->user->name }}_{{ \Carbon\Carbon::parse($shift->end_time)->format('d_m_Y') }}.png`;
                    const file = new File([blob], filename, { type: 'image/png' });
                    
                    // Cek jika API Share didukung oleh HP (Android/iOS)
                    if (navigator.canShare && navigator.canShare({ files: [file] })) {
                        navigator.share({
                            files: [file],
                            title: 'Laporan Shift KOPI TAKALAR',
                            text: 'Laporan Shift POS Kota'
                        }).then(() => {
                            showToast('✅ Berhasil dibagikan!');
                        }).catch(err => {
                            console.log('User cancelled or share failed:', err);
                            // Fallback download jika dibatalkan atau error
                            downloadBlob(blob, filename);
                        });
                    } else {
                        // Fallback unduh langsung jika browser tidak mendukung sharing API
                        downloadBlob(blob, filename);
                    }
                }, 'image/png');
            }).catch(err => {
                showToast('❌ Gagal: ' + err);
            });
        }

        function downloadBlob(blob, filename) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            showToast('💾 Gambar struk berhasil diunduh!');
        }

        // Helper Toast Notification
        function showToast(message) {
            // Remove existing toast if any
            const existingToast = document.getElementById('custom-toast');
            if (existingToast) {
                existingToast.remove();
            }

            const toast = document.createElement('div');
            toast.id = 'custom-toast';
            toast.className = 'fixed bottom-24 left-1/2 -translate-x-1/2 bg-slate-900/90 text-white text-xs px-4 py-2.5 rounded-full shadow-lg z-50 transition-all font-medium whitespace-nowrap backdrop-blur-sm';
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }
    </script>
</body>
</html>