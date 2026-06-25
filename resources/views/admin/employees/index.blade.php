<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan - KOPI TAKALAR</title>
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
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Akun Karyawan</h1>
                <p class="text-gray-500 text-sm mt-0.5">Kelola hak akses kasir dan admin untuk operasional KOPI TAKALAR.</p>
            </div>
            <div>
                <a href="{{ route('employees.create') }}" class="theme-bg text-black font-bold px-5 py-2.5 rounded-xl shadow-md hover:bg-[#3bd050] transition inline-flex items-center gap-2">
                    👥 Tambah Karyawan Baru
                </a>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">🔍</span>
                <input type="text" id="search-input" placeholder="Cari nama atau email..." 
                       class="w-full bg-gray-50 border border-gray-200 focus:border-[#54E868] focus:ring-1 focus:ring-[#54E868] rounded-xl py-2 pl-9 pr-4 text-sm outline-none transition-all">
            </div>
            <div class="flex gap-2 w-full sm:w-auto overflow-x-auto">
                <button onclick="filterRole('ALL')" class="role-btn px-4 py-1.5 rounded-lg text-xs font-bold bg-[#54E868] text-black transition-all">Semua</button>
                <button onclick="filterRole('admin')" class="role-btn px-4 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">🔑 Admin</button>
                <button onclick="filterRole('kasir')" class="role-btn px-4 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">☕ Kasir</button>
            </div>
        </div>

        <!-- Tabel Karyawan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-bold uppercase border-b border-gray-100">
                            <th class="py-4 px-6 w-16">No</th>
                            <th class="py-4 px-6">Nama</th>
                            <th class="py-4 px-6">Email</th>
                            <th class="py-4 px-6 w-32">Hak Akses</th>
                            <th class="py-4 px-6 w-44">Bergabung Pada</th>
                            <th class="py-4 px-6 w-48 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="employee-table-body">
                        @foreach($employees as $index => $employee)
                            <tr class="employee-row hover:bg-gray-50/50 transition-colors" data-name="{{ $employee->name }}" data-email="{{ $employee->email }}" data-role="{{ $employee->role }}">
                                <td class="py-4 px-6 font-bold text-gray-400">{{ $index + 1 }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-900">
                                    {{ $employee->name }}
                                    @if($employee->id === auth()->id())
                                        <span class="ml-1.5 bg-slate-900 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">Anda</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-gray-600">{{ $employee->email }}</td>
                                <td class="py-4 px-6">
                                    @if($employee->role === 'admin')
                                        <span class="bg-slate-900 text-white border border-slate-950 px-2.5 py-1 rounded-full text-xs font-bold">🔑 Admin</span>
                                    @else
                                        <span class="bg-blue-50 text-blue-700 border border-blue-100 px-2.5 py-1 rounded-full text-xs font-bold">☕ Kasir</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-gray-500 text-xs">
                                    {{ $employee->created_at ? $employee->created_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="bg-amber-500/10 text-amber-700 hover:bg-amber-500 hover:text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                                            Edit ✏️
                                        </a>
                                        @if($employee->id !== auth()->id())
                                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun karyawan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500/10 text-red-700 hover:bg-red-500 hover:text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                                                    Hapus 🗑️
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 italic">No Action</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Search & Filter JS -->
    <script>
        // Pencarian Karyawan
        document.getElementById('search-input').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.employee-row');
            rows.forEach(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const email = row.getAttribute('data-email').toLowerCase();
                if (name.includes(query) || email.includes(query)) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filter Role Karyawan
        function filterRole(role) {
            // Update Active Tab styles
            const buttons = document.querySelectorAll('.role-btn');
            buttons.forEach(btn => {
                btn.classList.remove('bg-[#54E868]', 'text-black');
                btn.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
            });

            event.target.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
            event.target.classList.add('bg-[#54E868]', 'text-black');

            // Filter Table Rows
            const rows = document.querySelectorAll('.employee-row');
            rows.forEach(row => {
                const itemRole = row.getAttribute('data-role');
                if (role === 'ALL' || itemRole === role) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
