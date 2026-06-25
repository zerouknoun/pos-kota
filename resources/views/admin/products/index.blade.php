<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Menu - KOPI TAKALAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .theme-bg { background-color: #54E868; }
        .theme-text { color: #1f2937; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">

    <!-- Navbar -->
    <nav class="theme-bg shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo Kopi Takalar" class="w-8 h-8 rounded-full object-cover">
                    <span class="font-black text-xl tracking-wider">KOPI TAKALAR</span>
                    <span class="text-xs bg-black text-[#54E868] font-bold px-2 py-0.5 rounded-full">ADMIN</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="bg-black text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-gray-800 transition">
                        &larr; Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Notifikasi Alert -->
        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 p-4 rounded-xl shadow-sm mb-6 flex items-center gap-3">
                <span>✅</span>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-100 border border-rose-300 text-rose-800 p-4 rounded-xl shadow-sm mb-6 flex items-center gap-3">
                <span>⚠️</span>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daftar Menu Produk</h1>
                <p class="text-gray-500 text-sm mt-0.5">Kelola seluruh produk, harga, dan kategori menu KOPI TAKALAR.</p>
            </div>
            <div>
                <a href="{{ route('products.create') }}" class="theme-bg text-black font-bold px-5 py-2.5 rounded-xl shadow-md hover:bg-[#3bd050] transition inline-flex items-center gap-2">
                    ➕ Tambah Menu Baru
                </a>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">🔍</span>
                <input type="text" id="search-input" placeholder="Cari menu kopi..." 
                       class="w-full bg-gray-50 border border-gray-200 focus:border-[#54E868] focus:ring-1 focus:ring-[#54E868] rounded-xl py-2 pl-9 pr-4 text-sm outline-none transition-all">
            </div>
            <div class="flex gap-2 w-full sm:w-auto overflow-x-auto">
                <button onclick="filterCategory('ALL')" class="category-btn px-4 py-1.5 rounded-lg text-xs font-bold bg-[#54E868] text-black transition-all">Semua</button>
                <button onclick="filterCategory('A. ICE')" class="category-btn px-4 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">🥤 ICE</button>
                <button onclick="filterCategory('B. HOT')" class="category-btn px-4 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">☕ HOT</button>
                <button onclick="filterCategory('C. Gelas')" class="category-btn px-4 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">🥛 Gelas</button>
            </div>
        </div>

        <!-- Tabel Produk -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-bold uppercase border-b border-gray-100">
                            <th class="py-4 px-6 w-16">No</th>
                            <th class="py-4 px-6">Nama Produk</th>
                            <th class="py-4 px-6 w-40">Kategori</th>
                            <th class="py-4 px-6 w-44">Harga</th>
                            <th class="py-4 px-6 w-48 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="product-table-body">
                        @forelse($products as $index => $product)
                            <tr class="product-row hover:bg-gray-50/50 transition-colors" data-name="{{ $product->name }}" data-category="{{ $product->category }}">
                                <td class="py-4 px-6 font-bold text-gray-400">{{ $index + 1 }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $product->name }}</td>
                                <td class="py-4 px-6">
                                    @if($product->category === 'A. ICE')
                                        <span class="bg-blue-50 text-blue-700 border border-blue-100 px-2.5 py-1 rounded-full text-xs font-bold">🥤 ICE</span>
                                    @elseif($product->category === 'B. HOT')
                                        <span class="bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full text-xs font-bold">☕ HOT</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-700 border border-gray-200 px-2.5 py-1 rounded-full text-xs font-bold">🥛 Gelas</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-extrabold text-gray-800">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('products.edit', $product->id) }}" class="bg-amber-500/10 text-amber-700 hover:bg-amber-500 hover:text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                                            Edit ✏️
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500/10 text-red-700 hover:bg-red-500 hover:text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                                                Hapus 🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 italic">Belum ada menu produk yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Search & Filter JS -->
    <script>
        // Pencarian Menu
        document.getElementById('search-input').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.product-row');
            rows.forEach(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                if (name.includes(query)) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filter Kategori
        function filterCategory(category) {
            // Update Active Tab styles
            const buttons = document.querySelectorAll('.category-btn');
            buttons.forEach(btn => {
                btn.classList.remove('bg-[#54E868]', 'text-black');
                btn.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
            });

            event.target.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
            event.target.classList.add('bg-[#54E868]', 'text-black');

            // Filter Table Rows
            const rows = document.querySelectorAll('.product-row');
            rows.forEach(row => {
                const itemCategory = row.getAttribute('data-category');
                if (category === 'ALL' || itemCategory === category) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
