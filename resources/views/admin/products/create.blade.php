<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu - KOPI TAKALAR</title>
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
                    <a href="{{ route('products.index') }}" class="bg-black text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-gray-800 transition">
                        &larr; Batal & Kembali
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="max-w-xl mx-auto px-4 py-12">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Menu Baru</h1>
            <p class="text-gray-500 text-sm mt-0.5">Lengkapi formulir di bawah ini untuk menambahkan menu baru ke kasir POS.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Nama Produk -->
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Produk</label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: Kopi Susu Caramel" value="{{ old('name') }}"
                           class="w-full bg-gray-50 border border-gray-200 focus:border-[#54E868] focus:ring-1 focus:ring-[#54E868] rounded-xl py-3 px-4 text-sm text-gray-800 outline-none transition-all">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kategori Menu</label>
                    <select name="category" id="category" required
                            class="w-full bg-gray-50 border border-gray-200 focus:border-[#54E868] focus:ring-1 focus:ring-[#54E868] rounded-xl py-3 px-4 text-sm text-gray-800 outline-none transition-all">
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="A. ICE" {{ old('category') === 'A. ICE' ? 'selected' : '' }}>🥤 A. ICE</option>
                        <option value="B. HOT" {{ old('category') === 'B. HOT' ? 'selected' : '' }}>☕ B. HOT</option>
                        <option value="C. Gelas" {{ old('category') === 'C. Gelas' ? 'selected' : '' }}>🥛 C. Gelas</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div>
                    <label for="price" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Harga Jual (Rp)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 text-sm">Rp</span>
                        <input type="number" name="price" id="price" required placeholder="Contoh: 18000" min="0" value="{{ old('price') }}"
                               class="w-full bg-gray-50 border border-gray-200 focus:border-[#54E868] focus:ring-1 focus:ring-[#54E868] rounded-xl py-3 pl-10 pr-4 text-sm text-gray-800 outline-none transition-all">
                    </div>
                    @error('price')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="w-full theme-bg text-black font-bold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-md hover:bg-[#3bd050] hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                    💾 Simpan Menu Baru
                </button>
            </form>
        </div>

    </main>

</body>
</html>
