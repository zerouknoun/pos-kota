<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - KOPI TAKALAR</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        theme: {
                            light: '#76f586',
                            DEFAULT: '#54E868',
                            dark: '#3bd050',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.3);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.2);
            border-radius: 4px;
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans selection:bg-theme selection:text-slate-900 bg-slate-950">

    <!-- Navbar -->
    <header class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 px-6 py-4 flex items-center justify-between sticky top-0 z-40">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo.png') }}" alt="Logo Kopi Takalar" class="w-10 h-10 rounded-xl object-cover shadow-lg shadow-theme/10">
            <div>
                <h1 class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">KOPI TAKALAR</h1>
                <p class="text-xs text-theme font-bold">Panel Dashboard Admin</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('pos.index') }}" class="bg-theme hover:bg-theme-dark text-slate-950 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 shadow-md shadow-theme/10 hover:shadow-theme/20 hover:scale-[1.02]">
                🛒 Halaman POS Kasir
            </a>
            <div class="h-6 w-px bg-slate-800"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-slate-800 hover:bg-red-500/10 hover:text-red-400 text-slate-300 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 border border-slate-700 hover:border-red-500/20">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6 space-y-8 overflow-y-auto custom-scrollbar">

        <!-- Welcome Banner & Auto-Refresh -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-900/60 to-transparent p-6 rounded-2xl border border-slate-800/80 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-theme/5 rounded-full blur-3xl pointer-events-none"></div>
            <div>
                <h2 class="text-xl font-extrabold text-white">Halo, {{ auth()->user()->name }} 👋</h2>
                <p class="text-slate-400 text-xs mt-1">Berikut adalah analitik penjualan dan operasional outlet KOPI TAKALAR secara real-time.</p>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <span class="text-xs text-slate-400 font-semibold mr-2">Data Real-Time Terhubung</span>
                <button onclick="location.reload();" class="p-2.5 bg-slate-850 hover:bg-slate-800 border border-slate-850 hover:border-slate-700 text-slate-300 rounded-xl transition duration-200 flex items-center justify-center group" title="Segarkan Data">
                    <span class="group-hover:rotate-180 transition-transform duration-500 ease-out text-sm">🔄</span>
                </button>
            </div>
        </div>

        <!-- Widget Unduh Laporan -->
        <div id="download-report-widget" class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-6 shadow-lg backdrop-blur-sm relative overflow-hidden group hover:border-theme/20 transition-colors">
            <div class="absolute top-0 right-0 w-32 h-32 bg-theme/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h3 class="text-sm font-extrabold tracking-wider uppercase text-slate-400">Unduh Laporan Penjualan</h3>
                    <p class="text-xs text-slate-500 mt-1">Ekspor ringkasan penjualan, metode pembayaran, dan rincian transaksi Anda.</p>
                </div>
                
                <form action="{{ route('admin.reports.download') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                    <!-- Dropdown Periode -->
                    <div class="flex-1 sm:flex-initial">
                        <label for="period" class="sr-only">Periode</label>
                        <select name="period" id="period" required
                                class="w-full bg-slate-950 border border-slate-800 focus:border-theme focus:ring-1 focus:ring-theme text-xs font-semibold rounded-xl px-4 py-3 text-slate-300 outline-none transition-all cursor-pointer">
                            <option value="weekly">Mingguan (7 Hari)</option>
                            <option value="monthly" selected>Bulanan (30 Hari)</option>
                            <option value="3_months">3 Bulan Terakhir</option>
                            <option value="yearly">Tahunan (365 Hari)</option>
                        </select>
                    </div>
                    
                    <!-- Dropdown Format -->
                    <div class="flex-1 sm:flex-initial">
                        <label for="format" class="sr-only">Format</label>
                        <select name="format" id="format" required
                                class="w-full bg-slate-950 border border-slate-800 focus:border-theme focus:ring-1 focus:ring-theme text-xs font-semibold rounded-xl px-4 py-3 text-slate-300 outline-none transition-all cursor-pointer">
                            <option value="pdf">📑 Format PDF</option>
                            <option value="excel">📊 Format Excel</option>
                        </select>
                    </div>
                    
                    <!-- Tombol Download -->
                    <button type="submit"
                            class="bg-theme hover:bg-theme-dark text-slate-950 text-xs font-extrabold px-6 py-3 rounded-xl shadow-md shadow-theme/10 hover:shadow-theme/20 hover:scale-[1.02] active:scale-[0.98] transition-all duration-250 flex items-center justify-center gap-2">
                        📥 Unduh Laporan
                    </button>
                </form>
            </div>
        </div>

        @php
            $safe_total = $total_pendapatan ?: 1;
            $pct_cash = ($revenue_cash / $safe_total) * 100;
            $pct_qris = ($revenue_qris / $safe_total) * 100;
            $pct_kasbon = ($revenue_kasbon / $safe_total) * 100;
        @endphp

        <!-- Kartu Statistik Real-time -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Pendapatan Real-time -->
            <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-5 shadow-lg backdrop-blur-sm relative overflow-hidden group hover:border-theme/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-theme/5 rounded-full blur-2xl pointer-events-none"></div>
                <p class="text-[10px] font-extrabold tracking-wider uppercase text-slate-500">Pendapatan Kotor Real-Time</p>
                <h3 class="text-2xl font-black mt-2 text-white group-hover:text-theme transition-colors">
                    <span class="text-sm font-semibold text-slate-400">Rp</span> {{ number_format($total_pendapatan, 0, ',', '.') }}
                </h3>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                    <span>Tunai + QRIS + Kasbon</span>
                    <span class="text-[10px] text-emerald-400 font-extrabold bg-emerald-500/10 px-2 py-0.5 rounded-full">Aktif</span>
                </div>
            </div>

            <!-- Total Transaksi -->
            <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-5 shadow-lg backdrop-blur-sm relative overflow-hidden group hover:border-theme/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl pointer-events-none"></div>
                <p class="text-[10px] font-extrabold tracking-wider uppercase text-slate-500">Total Transaksi POS</p>
                <h3 class="text-2xl font-black mt-2 text-white group-hover:text-blue-400 transition-colors">
                    {{ $total_orders }} <span class="text-sm font-semibold text-slate-400">Pesanan</span>
                </h3>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                    <span>Semua metode bayar</span>
                    <span class="text-[10px] text-blue-400 font-extrabold bg-blue-500/10 px-2 py-0.5 rounded-full">Tercatat</span>
                </div>
            </div>

            <!-- Gelas/Cup Terjual -->
            <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-5 shadow-lg backdrop-blur-sm relative overflow-hidden group hover:border-theme/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>
                <p class="text-[10px] font-extrabold tracking-wider uppercase text-slate-500">Total Cup Terpakai</p>
                <h3 class="text-2xl font-black mt-2 text-white group-hover:text-amber-400 transition-colors">
                    {{ $total_cup_terjual }} <span class="text-sm font-semibold text-slate-400">Cup</span>
                </h3>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                    <span>Khusus kategori ICE & HOT</span>
                    <span class="text-[10px] text-amber-400 font-extrabold bg-amber-500/10 px-2 py-0.5 rounded-full">Fisik</span>
                </div>
            </div>

            <!-- Menu & Karyawan -->
            <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-5 shadow-lg backdrop-blur-sm relative overflow-hidden group hover:border-theme/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/5 rounded-full blur-2xl pointer-events-none"></div>
                <p class="text-[10px] font-extrabold tracking-wider uppercase text-slate-500">Master Data Aktif</p>
                <h3 class="text-2xl font-black mt-2 text-white group-hover:text-purple-400 transition-colors">
                    {{ $total_menu }} <span class="text-sm font-semibold text-slate-400">Menu</span>
                    <span class="text-base font-normal text-slate-500 mx-1">|</span>
                    {{ $total_karyawan }} <span class="text-sm font-semibold text-slate-400">Kasir</span>
                </h3>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                    <span>Database outlet</span>
                    <span class="text-[10px] text-purple-400 font-extrabold bg-purple-500/10 px-2 py-0.5 rounded-full">Sistem</span>
                </div>
            </div>
        </div>

        <!-- Kolom Analitik Detail -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- KIRI (2 Kolom): Metode Bayar & Kasir Aktif -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Analisis Metode Pembayaran -->
                <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-6 shadow-md backdrop-blur-sm">
                    <h3 class="text-sm font-extrabold tracking-wider uppercase text-slate-400 mb-5">Metode Pembayaran (Share)</h3>
                    
                    <div class="space-y-4">
                        <!-- CASH -->
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                                <span class="text-slate-300 flex items-center gap-1.5">💵 Cash (Tunai)</span>
                                <span class="text-white font-extrabold">{{ round($pct_cash) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-950 border border-slate-850 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $pct_cash }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Rp {{ number_format($revenue_cash, 0, ',', '.') }} kotor</p>
                        </div>

                        <!-- QRIS -->
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                                <span class="text-slate-300 flex items-center gap-1.5">📱 QRIS (E-Wallet)</span>
                                <span class="text-white font-extrabold">{{ round($pct_qris) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-950 border border-slate-850 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full rounded-full transition-all duration-500" style="width: {{ $pct_qris }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Rp {{ number_format($revenue_qris, 0, ',', '.') }} kotor</p>
                        </div>

                        <!-- KASBON -->
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                                <span class="text-slate-300 flex items-center gap-1.5">📒 Kasbon</span>
                                <span class="text-white font-extrabold">{{ round($pct_kasbon) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-950 border border-slate-850 rounded-full overflow-hidden">
                                <div class="bg-rose-500 h-full rounded-full transition-all duration-500" style="width: {{ $pct_kasbon }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Rp {{ number_format($revenue_kasbon, 0, ',', '.') }} kotor</p>
                        </div>
                    </div>
                </div>

                <!-- Kasir Sedang Aktif -->
                <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-6 shadow-md backdrop-blur-sm">
                    <h3 class="text-sm font-extrabold tracking-wider uppercase text-slate-400 mb-4">Kasir Sedang Aktif</h3>
                    
                    <div class="space-y-3.5">
                        @forelse($active_shifts as $shift)
                            <div class="bg-slate-950/60 border border-slate-850 rounded-xl p-3.5 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $shift->user->name }}</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Mulai Shift: {{ \Carbon\Carbon::parse($shift->start_time)->format('d M, H:i') }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Cup Awal: {{ $shift->initial_cup }} | Tunai: Rp {{ number_format($shift->total_cash, 0, ',', '.') }}</p>
                                </div>
                                <span class="flex-shrink-0 text-[10px] font-extrabold text-theme bg-theme/10 border border-theme/20 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-theme"></span> Aktif
                                </span>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-slate-600 bg-slate-950/20 border border-dashed border-slate-850 rounded-xl">
                                🚫 Tidak ada kasir bertugas
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- KANAN (2 Kolom): Transaksi Terbaru -->
            <div class="lg:col-span-2">
                <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-6 shadow-md backdrop-blur-sm h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold tracking-wider uppercase text-slate-400 mb-4">Aktivitas Transaksi Terbaru</h3>
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse min-w-[500px]">
                                <thead>
                                    <tr class="text-slate-500 text-[10px] font-extrabold uppercase border-b border-slate-800">
                                        <th class="pb-3 pr-2">Waktu</th>
                                        <th class="pb-3 px-2">Kasir</th>
                                        <th class="pb-3 px-2">Pembayaran</th>
                                        <th class="pb-3 pl-2 text-right">Total Belanja</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-850/40">
                                    @forelse($recent_orders as $order)
                                        <tr class="hover:bg-slate-850/20 transition-colors">
                                            <td class="py-3.5 pr-2 text-xs font-semibold text-slate-400">
                                                {{ $order->created_at->format('H:i') }} <span class="text-[10px] text-slate-600">({{ $order->created_at->diffForHumans() }})</span>
                                            </td>
                                            <td class="py-3.5 px-2 text-xs font-bold text-white">
                                                {{ $order->shift->user->name ?? 'Kasir' }}
                                            </td>
                                            <td class="py-3.5 px-2">
                                                @if($order->payment_method === 'CASH')
                                                    <span class="text-[10px] font-extrabold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full">💵 CASH</span>
                                                @elseif($order->payment_method === 'QRIS')
                                                    <span class="text-[10px] font-extrabold text-blue-400 bg-blue-500/10 border border-blue-500/20 px-2 py-0.5 rounded-full">📱 QRIS</span>
                                                @else
                                                    <span class="text-[10px] font-extrabold text-rose-400 bg-rose-500/10 border border-rose-500/20 px-2 py-0.5 rounded-full">📒 KASBON</span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 pl-2 text-xs font-black text-theme text-right">
                                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-xs text-slate-600 italic">
                                                Belum ada transaksi terekam hari ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-4 border-t border-slate-800 pt-4 text-center">
                        <a href="{{ route('pos.index') }}" class="text-xs text-slate-500 hover:text-theme font-bold transition-colors">
                            Buka Layar Kasir POS &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Menu Pintasan (Grid Menu Utama) -->
        <div>
            <h3 class="text-sm font-extrabold tracking-wider uppercase text-slate-400 mb-4">Navigasi Menu Utama</h3>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                
                <a href="{{ route('pos.index') }}" class="flex flex-col items-center justify-center p-6 bg-slate-900 border border-slate-800 rounded-2xl hover:border-theme/40 hover:-translate-y-1 transition-all duration-200 group border-l-4 border-l-theme">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">🛒</span>
                    <span class="font-extrabold text-white text-xs group-hover:text-theme transition-colors text-center">Kasir POS</span>
                </a>

                <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center p-6 bg-slate-900 border border-slate-800 rounded-2xl hover:border-theme/40 hover:-translate-y-1 transition-all duration-200 group">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">☕</span>
                    <span class="font-extrabold text-white text-xs group-hover:text-theme transition-colors text-center">Edit Menu</span>
                </a>

                <a href="{{ route('employees.index') }}" class="flex flex-col items-center justify-center p-6 bg-slate-900 border border-slate-800 rounded-2xl hover:border-theme/40 hover:-translate-y-1 transition-all duration-200 group">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">👥</span>
                    <span class="font-extrabold text-white text-xs group-hover:text-theme transition-colors text-center">Karyawan</span>
                </a>

                <a href="#download-report-widget" class="flex flex-col items-center justify-center p-6 bg-slate-900 border border-slate-800 rounded-2xl hover:border-theme/40 hover:-translate-y-1 transition-all duration-200 group">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">📊</span>
                    <span class="font-extrabold text-white text-xs group-hover:text-theme transition-colors text-center">Unduh Laporan</span>
                </a>

                <a href="#" class="flex flex-col items-center justify-center p-6 bg-slate-900 border border-slate-800 rounded-2xl hover:border-theme/40 hover:-translate-y-1 transition-all duration-200 group">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">📅</span>
                    <span class="font-extrabold text-white text-xs group-hover:text-theme transition-colors text-center">Riwayat Shift</span>
                </a>

            </div>
        </div>

    </main>

</body>
</html>