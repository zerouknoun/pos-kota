<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Printer - KOPI TAKALAR</title>
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
</head>
<body class="h-full flex flex-col font-sans bg-slate-950">

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
            <a href="{{ route('admin.dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-100 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 border border-slate-700 hover:border-slate-600 hover:scale-[1.02]">
                📊 Kembali ke Dashboard
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl w-full mx-auto p-6 space-y-8 overflow-y-auto">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-extrabold text-white">⚙️ Pengujian Printer Thermal</h2>
            <p class="text-slate-400 text-xs">Uji koneksi dan format pencetakan printer thermal langsung dari server menggunakan library `mike42/escpos-php`.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left: Current Config Panel (1 column) -->
            <div class="md:col-span-1 space-y-6">
                <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-lg backdrop-blur-sm">
                    <h3 class="text-xs font-extrabold tracking-wider uppercase text-slate-400 mb-4">Konfigurasi .env Aktif</h3>
                    
                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="text-slate-500 block">Metode Koneksi:</span>
                            <span class="font-bold text-white px-2 py-0.5 rounded bg-slate-800 inline-block mt-1">
                                {{ strtoupper($printer_config['connection']) }}
                            </span>
                        </div>
                        
                        @if($printer_config['connection'] === 'windows' || $printer_config['connection'] === 'file')
                        <div>
                            <span class="text-slate-500 block">Nama / Jalur Printer:</span>
                            <span class="font-semibold text-slate-200 block mt-1 break-all">{{ $printer_config['name'] }}</span>
                        </div>
                        @endif

                        @if($printer_config['connection'] === 'network')
                        <div>
                            <span class="text-slate-500 block">IP Address Printer:</span>
                            <span class="font-semibold text-slate-200 block mt-1">{{ $printer_config['ip'] }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Port Printer:</span>
                            <span class="font-semibold text-slate-200 block mt-1">{{ $printer_config['port'] }}</span>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-slate-800/80">
                            <p class="text-[10px] text-slate-500 leading-relaxed leading-4">
                                *Untuk mengubah pengaturan ini secara permanen, silakan edit variabel `PRINTER_CONNECTION`, `PRINTER_NAME`, `PRINTER_IP` dan `PRINTER_PORT` pada file [**.env**](file:///e:/server/POS/pos-kota/.env) di server.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Interactive Test Panel (2 columns) -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 shadow-lg backdrop-blur-sm">
                    <h3 class="text-sm font-extrabold text-white mb-6">Uji Coba Koneksi & Format</h3>

                    <form id="form-test-print" class="space-y-5">
                        @csrf
                        <!-- Row 1: Connection Type -->
                        <div>
                            <label class="text-xs font-bold text-slate-400 block mb-2">Jenis Koneksi</label>
                            <select id="connection_type" name="connection_type" onchange="toggleFormFields()"
                                    class="w-full bg-slate-950 border border-slate-850 hover:border-slate-700 text-xs font-semibold rounded-xl px-4 py-3 text-slate-200 outline-none transition cursor-pointer">
                                <option value="rawbt">RawBT (Android Bluetooth App)</option>
                                <option value="dummy" {{ $printer_config['connection'] === 'dummy' ? 'selected' : '' }}>Dummy (Virtual Simulation)</option>
                                <option value="windows" {{ $printer_config['connection'] === 'windows' ? 'selected' : '' }}>Windows Shared USB (Local Share)</option>
                                <option value="network" {{ $printer_config['connection'] === 'network' ? 'selected' : '' }}>Network (LAN / Wi-Fi IP)</option>
                                <option value="file" {{ $printer_config['connection'] === 'file' ? 'selected' : '' }}>Local Port / File Path (LPT1 / /dev/usb/lp0)</option>
                            </select>
                        </div>

                        <!-- Row 2: Dynamic fields based on type -->
                        <div id="field-printer-name" class="hidden">
                            <label class="text-xs font-bold text-slate-400 block mb-2">Nama Share Printer / Jalur Port</label>
                            <input type="text" id="printer_name" name="printer_name" value="{{ $printer_config['name'] }}" placeholder="contoh: POS-58 atau //localhost/POS-58"
                                   class="w-full bg-slate-950 border border-slate-850 focus:border-theme text-xs rounded-xl px-4 py-3 text-slate-200 outline-none transition">
                        </div>

                        <div id="field-printer-network" class="hidden grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label class="text-xs font-bold text-slate-400 block mb-2">IP Address Printer</label>
                                <input type="text" id="printer_ip" name="printer_ip" value="{{ $printer_config['ip'] }}" placeholder="contoh: 192.168.1.100"
                                       class="w-full bg-slate-950 border border-slate-850 focus:border-theme text-xs rounded-xl px-4 py-3 text-slate-200 outline-none transition">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-400 block mb-2">Port Raw</label>
                                <input type="number" id="printer_port" name="printer_port" value="{{ $printer_config['port'] }}"
                                       class="w-full bg-slate-950 border border-slate-850 focus:border-theme text-xs rounded-xl px-4 py-3 text-slate-200 outline-none transition">
                            </div>
                        </div>

                        <!-- Row 3: Template Style -->
                        <div>
                            <label class="text-xs font-bold text-slate-400 block mb-2">Pilih Template Cetak Uji Coba</label>
                            <select id="test_type" name="test_type"
                                    class="w-full bg-slate-950 border border-slate-850 hover:border-slate-700 text-xs font-semibold rounded-xl px-4 py-3 text-slate-200 outline-none transition cursor-pointer">
                                <option value="text">📑 Struk Uji Coba Standar (Kopi Takalar)</option>
                                <option value="styles">🔠 Uji Coba Gaya Font (Bold, Double Width, Double Height)</option>
                                <option value="alignment">↔️ Uji Coba Perataan Baris (Left, Center, Right)</option>
                                <option value="cut">✂️ Uji Coba Potong Kertas (Paper Cut / Feed Only)</option>
                            </select>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-4">
                            <button type="submit" id="btn-submit"
                                    class="w-full bg-theme hover:bg-theme-dark text-slate-950 text-xs font-extrabold py-3.5 rounded-xl shadow-md shadow-theme/10 hover:shadow-theme/20 hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2">
                                🖨️ Kirim Perintah Cetak
                            </button>
                        </div>
                    </form>

                    <!-- Alert message container -->
                    <div id="test-status" class="mt-5 text-xs font-semibold rounded-xl p-4 border leading-relaxed hidden font-sans"></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleFormFields() {
            const type = document.getElementById('connection_type').value;
            const nameField = document.getElementById('field-printer-name');
            const networkField = document.getElementById('field-printer-network');

            nameField.classList.add('hidden');
            networkField.classList.add('hidden');

            if (type === 'windows' || type === 'file') {
                nameField.classList.remove('hidden');
            } else if (type === 'network') {
                networkField.classList.remove('hidden');
            }
        }

        // Initialize view states
        document.addEventListener('DOMContentLoaded', () => {
            toggleFormFields();
            
            const form = document.getElementById('form-test-print');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const btn = document.getElementById('btn-submit');
                const statusDiv = document.getElementById('test-status');
                
                btn.disabled = true;
                statusDiv.classList.remove('hidden');
                statusDiv.style.color = '#1e3a8a';
                statusDiv.style.backgroundColor = '#eff6ff';
                statusDiv.style.borderColor = '#bfdbfe';
                statusDiv.innerHTML = '⏳ Menghubungkan ke printer dan mengirimkan dokumen uji coba...';
                
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('/admin/test-printer/run', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        statusDiv.style.color = '#14532d';
                        statusDiv.style.backgroundColor = '#f0fdf4';
                        statusDiv.style.borderColor = '#bbf7d0';
                        statusDiv.innerHTML = '✅ ' + result.message;
                        
                        // If connection type is RawBt, send to RawBt app on Android
                        const connType = document.getElementById('connection_type').value;
                        if (connType === 'rawbt' && result.base64) {
                            window.location.href = "rawbt:base64," + result.base64;
                        }
                    } else {
                        statusDiv.style.color = '#7f1d1d';
                        statusDiv.style.backgroundColor = '#fef2f2';
                        statusDiv.style.borderColor = '#fecaca';
                        statusDiv.innerHTML = '❌ ' + result.message;
                    }
                } catch (err) {
                    console.error(err);
                    statusDiv.style.color = '#7f1d1d';
                    statusDiv.style.backgroundColor = '#fef2f2';
                    statusDiv.style.borderColor = '#fecaca';
                    statusDiv.innerHTML = '❌ Terjadi kesalahan jaringan saat mencoba menghubungi server.';
                } finally {
                    btn.disabled = false;
                }
            });
        });
    </script>
</body>
</html>
