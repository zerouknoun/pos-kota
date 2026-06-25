<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan - KOPI TAKALAR</title>
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
                    <a href="{{ route('employees.index') }}" class="bg-black text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-gray-800 transition">
                        &larr; Batal & Kembali
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="max-w-xl mx-auto px-4 py-12">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Daftarkan Karyawan Baru</h1>
            <p class="text-gray-500 text-sm mt-0.5">Buat akun login baru bagi barista kasir atau admin operasional toko.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('employees.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Nama Karyawan -->
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: Rian Fauzi" value="{{ old('name') }}"
                           class="w-full bg-gray-50 border border-gray-200 focus:border-[#54E868] focus:ring-1 focus:ring-[#54E868] rounded-xl py-3 px-4 text-sm text-gray-800 outline-none transition-all">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Karyawan -->
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Alamat Email (Untuk Login)</label>
                    <input type="email" name="email" id="email" required placeholder="Contoh: rian@kopitakalar.com" value="{{ old('email') }}"
                           class="w-full bg-gray-50 border border-gray-200 focus:border-[#54E868] focus:ring-1 focus:ring-[#54E868] rounded-xl py-3 px-4 text-sm text-gray-800 outline-none transition-all">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Karyawan -->
                <div>
                    <label for="role" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Hak Akses (Role)</label>
                    <select name="role" id="role" required
                            class="w-full bg-gray-50 border border-gray-200 focus:border-[#54E868] focus:ring-1 focus:ring-[#54E868] rounded-xl py-3 px-4 text-sm text-gray-800 outline-none transition-all">
                        <option value="" disabled selected>-- Pilih Hak Akses --</option>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>☕ Kasir / Barista</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>🔑 Admin Toko</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Karyawan -->
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kata Sandi (Password)</label>
                    <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                           class="w-full bg-gray-50 border border-gray-200 focus:border-[#54E868] focus:ring-1 focus:ring-[#54E868] rounded-xl py-3 px-4 text-sm text-gray-800 outline-none transition-all">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="w-full theme-bg text-black font-bold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-md hover:bg-[#3bd050] hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                    💾 Daftarkan Karyawan
                </button>
            </form>
        </div>

    </main>

</body>
</html>
