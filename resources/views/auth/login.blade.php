<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KOPI TAKALAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .theme-bg { background-color: #54E868; }
        .theme-text { color: #54E868; }
        .theme-border { border-color: #54E868; }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen p-4 font-sans">

    <div class="bg-white w-full max-w-sm p-8 rounded-2xl shadow-xl border-t-8 theme-border">
        
        <!-- Logo / Nama Cafe -->
        <div class="text-center mb-8">
            <img src="{{ asset('logo.png') }}" alt="Logo Kopi Takalar" class="w-20 h-20 rounded-full mx-auto mb-4 object-cover shadow-md">
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">KOPI TAKALAR</h1>
            <p class="text-sm text-gray-500 mt-1">Sistem Point of Sale</p>
        </div>

        <!-- Pesan Error (Jika salah password/email) -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Form Login -->
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#54E868] focus:border-transparent transition" 
                    placeholder="admin@kopitakalar.com">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#54E868] focus:border-transparent transition" 
                    placeholder="••••••••">
            </div>

            <button type="submit" 
                class="w-full theme-bg text-black font-bold text-lg py-3 rounded-lg shadow-md hover:opacity-90 transition transform hover:scale-[1.02]">
                Masuk
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Kopi Takalar POS</p>
        </div>
    </div>

</body>
</html>