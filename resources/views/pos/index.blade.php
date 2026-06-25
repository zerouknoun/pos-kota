<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir POS - KOPI TAKALAR</title>
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
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.3);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.2);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.4);
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans selection:bg-theme selection:text-slate-900">

    <!-- Header POS -->
    <header class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 px-4 md:px-6 py-3 md:py-4 flex items-center justify-between sticky top-0 z-40">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo.png') }}" alt="Logo Kopi Takalar" class="w-10 h-10 rounded-xl object-cover shadow-lg shadow-theme/10">
            <div>
                <h1 class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">KOPI TAKALAR</h1>
                <p class="text-xs text-slate-500 font-medium">Point of Sale Kasir Kota</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-semibold text-slate-200">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 capitalize">Role: {{ auth()->user()->role }}</p>
            </div>
            <div class="h-8 w-px bg-slate-800 hidden sm:block"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-slate-800 hover:bg-red-500/10 hover:text-red-400 text-slate-300 px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 border border-slate-700 hover:border-red-500/20">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Notifikasi Alert -->
    @if(session('success') || session('error') || session('info'))
        <div class="fixed top-20 right-6 z-50 max-w-sm w-full animate-bounce">
            @if(session('success'))
                <div class="bg-emerald-950/80 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl shadow-2xl backdrop-blur-md flex items-center gap-3">
                    <span>✅</span>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-950/80 border border-rose-500/30 text-rose-400 p-4 rounded-xl shadow-2xl backdrop-blur-md flex items-center gap-3">
                    <span>⚠️</span>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-950/80 border border-blue-500/30 text-blue-400 p-4 rounded-xl shadow-2xl backdrop-blur-md flex items-center gap-3">
                    <span>ℹ️</span>
                    <p class="text-sm font-medium">{{ session('info') }}</p>
                </div>
            @endif
        </div>
        <script>
            setTimeout(() => {
                const alerts = document.querySelectorAll('.fixed.z-50');
                alerts.forEach(alert => alert.remove());
            }, 4000);
        </script>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1 flex overflow-hidden relative">

        @if(!$activeShift)
            <!-- SCREEN: MULAI SHIFT -->
            <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm flex items-center justify-center z-30 p-4">
                <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-theme/5 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-slate-800 border border-slate-700 text-theme rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">
                            ☕
                        </div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-white">Mulai Shift Kasir</h2>
                        <p class="text-slate-400 text-sm mt-1">Harap isi saldo jumlah cup awal sebelum memulai operasional kasir.</p>
                    </div>

                    <form action="{{ route('shift.start') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="initial_cup" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Jumlah Cup Awal</label>
                            <div class="relative">
                                <input type="number" name="initial_cup" id="initial_cup" value="30" min="0" required
                                       class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-theme focus:ring-1 focus:ring-theme rounded-xl py-3.5 px-4 text-center text-xl font-bold text-white transition-all outline-none">
                            </div>
                            <p class="text-slate-500 text-xs mt-2 text-center">Biasanya disesuaikan dengan stok fisik cup kosong di booth.</p>
                        </div>

                        <button type="submit" class="w-full bg-theme hover:bg-theme-dark text-slate-950 font-bold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-lg shadow-theme/10 hover:shadow-theme/20 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                            Mulai Shift Sekarang &rarr;
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- LAYOUT POS (Dua Kolom) -->
        <div class="flex-1 flex flex-col md:flex-row overflow-hidden">
            
            <!-- Mobile Tab Toggle (Hanya muncul di HP / Mobile) -->
            <div class="flex md:hidden bg-slate-900 border-b border-slate-800 p-2.5 gap-2.5 sticky top-0 z-30">
                <button id="mobile-tab-menu" onclick="switchMobileTab('menu')" class="flex-1 py-3 text-center rounded-xl text-xs font-extrabold tracking-wider uppercase bg-theme text-slate-950 transition-all flex items-center justify-center gap-2 shadow-md shadow-theme/10">
                    📋 Pilih Menu
                </button>
                <button id="mobile-tab-cart" onclick="switchMobileTab('cart')" class="flex-1 py-3 text-center rounded-xl text-xs font-extrabold tracking-wider uppercase bg-slate-950 text-slate-400 border border-slate-800 transition-all flex items-center justify-center gap-1.5">
                    🛒 Keranjang <span id="mobile-cart-badge" class="bg-red-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full hidden">0</span>
                </button>
            </div>
            
            <!-- KOLOM KIRI: PRODUK & GRID -->
            <div id="menu-container" class="flex-1 flex flex-col bg-slate-950 border-r border-slate-900 overflow-hidden">
                <!-- Search & Kategori -->
                <div class="p-4 md:p-6 bg-slate-900/50 border-b border-slate-900 flex flex-col gap-4">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">🔍</span>
                        <input type="text" id="search-input" placeholder="Cari menu kopi atau produk..." 
                               class="w-full bg-slate-950 border border-slate-800 focus:border-theme focus:ring-1 focus:ring-theme rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 outline-none transition-all">
                    </div>

                    <div class="flex gap-2 overflow-x-auto pb-1 custom-scrollbar">
                        <button onclick="filterCategory('ALL')" class="category-tab px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-theme text-slate-950 transition-all">
                            Semua Menu
                        </button>
                        <button onclick="filterCategory('A. ICE')" class="category-tab px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-900 text-slate-400 hover:text-white transition-all">
                            🥤 A. ICE
                        </button>
                        <button onclick="filterCategory('B. HOT')" class="category-tab px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-900 text-slate-400 hover:text-white transition-all">
                            ☕ B. HOT
                        </button>
                        <button onclick="filterCategory('C. Gelas')" class="category-tab px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-900 text-slate-400 hover:text-white transition-all">
                            🥛 C. Gelas
                        </button>
                    </div>
                </div>

                <!-- Grid Produk -->
                <div class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar">
                    <div id="product-grid" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                        @foreach($products as $product)
                            <div class="product-card bg-slate-900 border border-slate-800 rounded-xl md:rounded-2xl p-3 md:p-4 flex flex-col justify-between hover:border-theme/40 hover:-translate-y-1 transition-all duration-200 cursor-pointer shadow-md group"
                                 data-id="{{ $product->id }}" 
                                 data-name="{{ $product->name }}" 
                                 data-price="{{ $product->price }}" 
                                 data-category="{{ $product->category }}"
                                 onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->category }}')">
                                
                                <div class="mb-4">
                                    <span class="text-[10px] font-extrabold tracking-wider uppercase bg-slate-950 px-2.5 py-1 rounded-full border border-slate-800 text-slate-400">
                                        {{ str_replace(['A. ', 'B. ', 'C. '], '', $product->category) }}
                                    </span>
                                    <h3 class="text-sm font-bold mt-2 text-white group-hover:text-theme transition-colors line-clamp-2">
                                        {{ $product->name }}
                                    </h3>
                                </div>
                                <div class="flex items-center justify-between mt-auto">
                                    <span class="text-sm font-extrabold text-theme">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                    <div class="w-8 h-8 rounded-lg bg-slate-800 group-hover:bg-theme group-hover:text-slate-950 flex items-center justify-center text-slate-400 text-sm font-bold transition-all">
                                        +
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: STATUS SHIFT & DETAIL PEMBAYARAN -->
            <div id="cart-container" class="w-full md:w-96 lg:w-[420px] bg-slate-900/90 border-t md:border-t-0 md:border-l border-slate-800 flex flex-col overflow-hidden backdrop-blur-md hidden md:flex">
                
                @if($activeShift)
                    <!-- Detail Shift Aktif -->
                    <div class="p-4 bg-slate-950 border-b border-slate-800 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs text-slate-500 font-semibold uppercase">Kasir Aktif</p>
                            <p class="text-sm font-bold text-white">{{ $activeShift->user->name }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Mulai: {{ \Carbon\Carbon::parse($activeShift->start_time)->format('H:i') }} | Cup Awal: {{ $activeShift->initial_cup }}</p>
                        </div>
                        <form action="{{ route('shift.end') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengakhiri shift kasir sekarang? Seluruh rekapan penjualan akan dihitung.');">
                            @csrf
                            <button type="submit" class="bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white px-3.5 py-2 rounded-xl text-xs font-bold border border-red-500/20 transition-all duration-200">
                                Akhiri Shift 🚫
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Keranjang Belanja -->
                <div class="flex-1 flex flex-col min-h-[200px] overflow-hidden">
                    <div class="px-4 md:px-6 py-3.5 md:py-4 flex items-center justify-between border-b border-slate-800 bg-slate-900/30">
                        <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Keranjang Belanja</h2>
                        <button onclick="clearCart()" class="text-xs text-slate-500 hover:text-slate-300 font-semibold transition-colors">
                            Kosongkan
                        </button>
                    </div>

                    <!-- List Cart Items -->
                    <div id="cart-list" class="flex-1 overflow-y-auto px-4 md:px-6 py-3.5 md:py-4 space-y-3 md:space-y-4 custom-scrollbar">
                        <!-- Keranjang kosong view -->
                        <div id="empty-cart-message" class="h-full flex flex-col items-center justify-center text-center py-10">
                            <span class="text-4xl mb-3">🛒</span>
                            <p class="text-sm font-medium text-slate-500">Belum ada menu yang dipilih</p>
                            <p class="text-xs text-slate-600 mt-1">Klik item produk di sebelah kiri untuk memasukkan ke keranjang</p>
                        </div>
                    </div>
                </div>

                <!-- Formulir Checkout & Perhitungan -->
                <div class="p-4 md:p-6 bg-slate-950 border-t border-slate-800 space-y-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs text-slate-500 font-semibold">
                            <span>SUBTOTAL</span>
                            <span id="summary-subtotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-lg text-white font-extrabold border-t border-slate-800 pt-2">
                            <span>TOTAL BAYAR</span>
                            <span id="summary-total" class="text-theme">Rp 0</span>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="payment-btn border border-slate-800 bg-slate-900 hover:border-theme/40 text-slate-300 hover:text-white px-3 py-2.5 rounded-xl text-center text-xs font-bold cursor-pointer transition-all flex items-center justify-center gap-1.5 active-payment" id="btn-CASH">
                                <input type="radio" name="payment_method_select" value="CASH" checked class="hidden" onchange="changePaymentMethod('CASH')">
                                💵 Cash
                            </label>
                            <label class="payment-btn border border-slate-800 bg-slate-900 hover:border-theme/40 text-slate-300 hover:text-white px-3 py-2.5 rounded-xl text-center text-xs font-bold cursor-pointer transition-all flex items-center justify-center gap-1.5" id="btn-QRIS">
                                <input type="radio" name="payment_method_select" value="QRIS" class="hidden" onchange="changePaymentMethod('QRIS')">
                                📱 QRIS
                            </label>
                            <label class="payment-btn border border-slate-800 bg-slate-900 hover:border-theme/40 text-slate-300 hover:text-white px-3 py-2.5 rounded-xl text-center text-xs font-bold cursor-pointer transition-all flex items-center justify-center gap-1.5" id="btn-KASBON">
                                <input type="radio" name="payment_method_select" value="KASBON" class="hidden" onchange="changePaymentMethod('KASBON')">
                                📒 Kasbon
                            </label>
                        </div>
                    </div>

                    <!-- Bagian Cash Input -->
                    <div id="cash-calculator-section" class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Uang Diterima (Cash)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-xs">Rp</span>
                                    <input type="number" id="cash-received-input" value="0" min="0" oninput="calculateChange()"
                                           class="w-full bg-slate-900 border border-slate-800 focus:border-theme focus:ring-1 focus:ring-theme rounded-xl py-2.5 pl-8 pr-3 text-sm font-bold text-white outline-none transition-all">
                                </div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kembalian</label>
                                <div class="w-full bg-slate-900/50 border border-slate-800/80 rounded-xl py-2.5 px-3 text-sm font-extrabold text-slate-300 min-h-[42px] flex items-center" id="cash-change-display">
                                    Rp 0
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Cepat Nominal -->
                        <div class="flex gap-1.5 flex-wrap">
                            <button onclick="setCashAmount('PAS')" class="bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 rounded-lg px-2.5 py-1 text-[10px] font-bold transition-all">Uang Pas</button>
                            <button onclick="setCashAmount(10000)" class="bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 rounded-lg px-2.5 py-1 text-[10px] font-bold transition-all">10k</button>
                            <button onclick="setCashAmount(20000)" class="bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 rounded-lg px-2.5 py-1 text-[10px] font-bold transition-all">20k</button>
                            <button onclick="setCashAmount(50000)" class="bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 rounded-lg px-2.5 py-1 text-[10px] font-bold transition-all">50k</button>
                            <button onclick="setCashAmount(100000)" class="bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 rounded-lg px-2.5 py-1 text-[10px] font-bold transition-all">100k</button>
                        </div>
                    </div>

                    <!-- Tombol Proses Transaksi -->
                    <form action="{{ route('pos.checkout') }}" method="POST" id="checkout-form" onsubmit="return handleFormSubmit(event)">
                        @csrf
                        <input type="hidden" name="payment_method" id="form-payment-method" value="CASH">
                        <button type="submit" id="checkout-submit-btn" disabled
                                class="w-full bg-slate-800 text-slate-500 font-extrabold py-3.5 px-6 rounded-xl cursor-not-allowed transition-all duration-200 flex items-center justify-center gap-2">
                            🛒 Keranjang Belanja Kosong
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </main>

    <!-- Javascript POS Logic -->
    <script>
        let cart = [];
        let totalAmount = 0;
        let selectedPaymentMethod = 'CASH';

        // Filter pencarian
        document.getElementById('search-input').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.product-card');

            cards.forEach(card => {
                const name = card.getAttribute('data-name').toLowerCase();
                if (name.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Filter Kategori
        function filterCategory(category) {
            // Update UI Tab Aktif
            const tabs = document.querySelectorAll('.category-tab');
            tabs.forEach(tab => {
                tab.classList.remove('bg-theme', 'text-slate-950');
                tab.classList.add('bg-slate-900', 'text-slate-400');
            });

            // Set tab terpilih sebagai aktif
            const activeTab = event.target;
            activeTab.classList.remove('bg-slate-900', 'text-slate-400');
            activeTab.classList.add('bg-theme', 'text-slate-950');

            // Saring kartu produk
            const cards = document.querySelectorAll('.product-card');
            cards.forEach(card => {
                const itemCategory = card.getAttribute('data-category');
                if (category === 'ALL' || itemCategory === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Tambah Item ke Keranjang
        function addToCart(id, name, price, category) {
            const existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.qty += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    qty: 1,
                    category: category
                });
            }

            renderCart();
            playFeedbackAnimation(id);
        }

        // Jalankan Efek Animasi Feedback saat klik produk
        function playFeedbackAnimation(id) {
            const card = document.querySelector(`.product-card[data-id="${id}"]`);
            if (card) {
                card.classList.add('scale-95', 'border-theme');
                setTimeout(() => {
                    card.classList.remove('scale-95', 'border-theme');
                }, 150);
            }
        }

        // Kurangi Kuantitas Item
        function decreaseQty(id) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.qty -= 1;
                if (item.qty <= 0) {
                    removeFromCart(id);
                } else {
                    renderCart();
                }
            }
        }

        // Tambah Kuantitas Item
        function increaseQty(id) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.qty += 1;
                renderCart();
            }
        }

        // Hapus Item dari Keranjang
        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            renderCart();
        }

        // Kosongkan Keranjang
        function clearCart() {
            if (cart.length > 0 && confirm('Kosongkan semua pesanan di keranjang?')) {
                cart = [];
                renderCart();
            }
        }

        // Render data Keranjang ke Tampilan HTML
        function renderCart() {
            const cartList = document.getElementById('cart-list');
            const emptyMessage = document.getElementById('empty-cart-message');
            
            // Bersihkan tampilan keranjang
            cartList.querySelectorAll('.cart-item-row').forEach(el => el.remove());

            if (cart.length === 0) {
                emptyMessage.style.display = 'flex';
                totalAmount = 0;
            } else {
                emptyMessage.style.display = 'none';
                totalAmount = 0;

                cart.forEach(item => {
                    const subtotal = item.price * item.qty;
                    totalAmount += subtotal;

                    const row = document.createElement('div');
                    row.className = 'cart-item-row flex items-center justify-between gap-3 p-3 bg-slate-950/40 border border-slate-800 rounded-xl transition-all duration-200';
                    row.innerHTML = `
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-white truncate">${item.name}</h4>
                            <p class="text-xs text-slate-500">Rp ${formatRupiah(item.price)}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="decreaseQty(${item.id})" class="w-6 h-6 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 font-extrabold text-xs flex items-center justify-center">-</button>
                            <span class="text-sm font-bold text-white min-w-[20px] text-center">${item.qty}</span>
                            <button onclick="increaseQty(${item.id})" class="w-6 h-6 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 font-extrabold text-xs flex items-center justify-center">+</button>
                        </div>
                        <div class="text-right min-w-[80px]">
                            <p class="text-sm font-extrabold text-white">Rp ${formatRupiah(subtotal)}</p>
                            <button onclick="removeFromCart(${item.id})" class="text-[10px] text-red-500 hover:text-red-400 font-medium mt-0.5 transition-colors">Hapus</button>
                        </div>
                    `;
                    cartList.appendChild(row);
                });
            }

            // Update Ringkasan Total
            document.getElementById('summary-subtotal').innerText = 'Rp ' + formatRupiah(totalAmount);
            document.getElementById('summary-total').innerText = 'Rp ' + formatRupiah(totalAmount);

            // Update Tombol Submit Checkout
            const submitBtn = document.getElementById('checkout-submit-btn');
            if (cart.length === 0) {
                submitBtn.disabled = true;
                submitBtn.className = 'w-full bg-slate-800 text-slate-500 font-extrabold py-3.5 px-6 rounded-xl cursor-not-allowed transition-all duration-200 flex items-center justify-center gap-2';
                submitBtn.innerHTML = '🛒 Keranjang Belanja Kosong';
            } else {
                submitBtn.disabled = false;
                submitBtn.className = 'w-full bg-theme hover:bg-theme-dark text-slate-950 font-extrabold py-3.5 px-6 rounded-xl cursor-pointer hover:shadow-lg hover:shadow-theme/10 hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2';
                submitBtn.innerHTML = `Bayar & Cetak Struk (${cart.reduce((a, b) => a + b.qty, 0)} Item) &rarr;`;
            }

            // Update badge cart untuk mobile
            const badge = document.getElementById('mobile-cart-badge');
            const totalQty = cart.reduce((a, b) => a + b.qty, 0);
            if (totalQty > 0) {
                badge.innerText = totalQty;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            calculateChange();
        }

        // Tab switching untuk mobile
        function switchMobileTab(tab) {
            const menuTab = document.getElementById('mobile-tab-menu');
            const cartTab = document.getElementById('mobile-tab-cart');
            const menuContainer = document.getElementById('menu-container');
            const cartContainer = document.getElementById('cart-container');

            if (tab === 'menu') {
                menuTab.className = 'flex-1 py-3 text-center rounded-xl text-xs font-extrabold tracking-wider uppercase bg-theme text-slate-950 transition-all flex items-center justify-center gap-2 shadow-md shadow-theme/10';
                cartTab.className = 'flex-1 py-3 text-center rounded-xl text-xs font-extrabold tracking-wider uppercase bg-slate-950 text-slate-400 border border-slate-800 transition-all flex items-center justify-center gap-1.5';
                
                menuContainer.className = 'flex-1 flex flex-col bg-slate-950 border-r border-slate-900 overflow-hidden';
                cartContainer.className = 'w-full md:w-96 lg:w-[420px] bg-slate-900/90 border-t md:border-t-0 md:border-l border-slate-800 flex flex-col overflow-hidden backdrop-blur-md hidden md:flex';
            } else {
                menuTab.className = 'flex-1 py-3 text-center rounded-xl text-xs font-extrabold tracking-wider uppercase bg-slate-950 text-slate-400 border border-slate-800 transition-all flex items-center justify-center gap-2';
                cartTab.className = 'flex-1 py-3 text-center rounded-xl text-xs font-extrabold tracking-wider uppercase bg-theme text-slate-950 transition-all flex items-center justify-center gap-1.5 shadow-md shadow-theme/10';
                
                menuContainer.className = 'flex-1 hidden md:flex flex-col bg-slate-950 border-r border-slate-900 overflow-hidden';
                cartContainer.className = 'w-full md:w-96 lg:w-[420px] bg-slate-900/90 border-t md:border-t-0 md:border-l border-slate-800 flex flex-col overflow-hidden backdrop-blur-md flex';
            }
        }

        // Mengubah Metode Pembayaran
        function changePaymentMethod(method) {
            selectedPaymentMethod = method;
            document.getElementById('form-payment-method').value = method;

            // Update Style tombol metode
            const buttons = document.querySelectorAll('.payment-btn');
            buttons.forEach(btn => {
                btn.classList.remove('border-theme/40', 'bg-slate-950', 'text-theme', 'border-theme');
                btn.classList.add('border-slate-800', 'bg-slate-900', 'text-slate-300');
            });

            const activeBtn = document.getElementById('btn-' + method);
            activeBtn.classList.remove('border-slate-800', 'bg-slate-900', 'text-slate-300');
            activeBtn.classList.add('border-theme', 'bg-slate-950', 'text-theme');

            const cashSection = document.getElementById('cash-calculator-section');
            if (method === 'CASH') {
                cashSection.style.display = 'block';
            } else {
                cashSection.style.display = 'none';
                document.getElementById('cash-received-input').value = totalAmount;
            }
            calculateChange();
        }

        // Set Nominal Cepat untuk Uang Diterima
        function setCashAmount(amount) {
            const input = document.getElementById('cash-received-input');
            if (amount === 'PAS') {
                input.value = totalAmount;
            } else {
                input.value = amount;
            }
            calculateChange();
        }

        // Hitung uang kembalian
        function calculateChange() {
            if (selectedPaymentMethod !== 'CASH') {
                document.getElementById('cash-change-display').innerText = 'Rp 0';
                return;
            }

            const received = parseInt(document.getElementById('cash-received-input').value) || 0;
            const change = received - totalAmount;
            const display = document.getElementById('cash-change-display');

            if (change < 0) {
                display.innerText = 'Kurang: Rp ' + formatRupiah(Math.abs(change));
                display.className = 'w-full bg-slate-900/50 border border-red-500/20 rounded-xl py-2.5 px-3 text-sm font-extrabold text-red-400 min-h-[42px] flex items-center';
            } else {
                display.innerText = 'Rp ' + formatRupiah(change);
                display.className = 'w-full bg-slate-900/50 border border-slate-800 rounded-xl py-2.5 px-3 text-sm font-extrabold text-slate-300 min-h-[42px] flex items-center';
            }
        }

        // Konversi Angka ke format Rupiah
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Handle form submit: Buat hidden inputs dinamis dari array cart
        function handleFormSubmit(event) {
            if (cart.length === 0) {
                alert('Keranjang belanja kosong.');
                return false;
            }

            // Validasi uang diterima jika menggunakan CASH
            if (selectedPaymentMethod === 'CASH') {
                const received = parseInt(document.getElementById('cash-received-input').value) || 0;
                if (received < totalAmount) {
                    alert('Uang tunai yang diterima kurang dari total belanja.');
                    return false;
                }
            }

            const form = document.getElementById('checkout-form');
            
            // Hapus input dinamis sebelumnya jika ada
            form.querySelectorAll('.dynamic-cart-input').forEach(el => el.remove());

            // Buat input tersembunyi untuk tiap item di keranjang
            cart.forEach((item, index) => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = `cart[${index}][id]`;
                idInput.value = item.id;
                idInput.className = 'dynamic-cart-input';

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `cart[${index}][qty]`;
                qtyInput.value = item.qty;
                qtyInput.className = 'dynamic-cart-input';

                form.appendChild(idInput);
                form.appendChild(qtyInput);
            });

            return true;
        }

        // Inisialisasi awal
        window.addEventListener('DOMContentLoaded', () => {
            renderCart();
            changePaymentMethod('CASH');
        });
    </script>
</body>
</html>
