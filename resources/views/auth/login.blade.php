<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daily Caraka - Login</title>
  {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-200 to-blue-600 flex items-center justify-center p-6">
  <div class="bg-white shadow-2xl rounded-xl overflow-hidden w-full max-w-4xl">
    <div class="md:flex">
      <!-- Bagian Gambar (hanya tampil di desktop) -->
      <div class="hidden md:block md:w-1/2">
        <img src="Go-Clean-1.jpg" alt="Daily Caraka" class="object-cover h-full w-full">
      </div>
      <!-- Bagian Form Login -->
      <div class="w-full md:w-1/2 p-8">
        <h1 class="text-4xl font-extrabold text-center text-blue-700 mb-4">Daily Caraka</h1>

        <!-- Pesan error jika ada -->
        @if ($errors->any())
          <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded">
            {{ $errors->first() }}
          </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
          @csrf

          <div>
            <label for="email" class="block text-gray-800 font-semibold mb-2">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              required
              placeholder="Masukkan email Anda"
              class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label for="password" class="block text-gray-800 font-semibold mb-2">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              required
              placeholder="Masukkan password"
              class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <button
              type="submit"
              class="w-full py-3 text-lg font-bold text-white bg-blue-700 rounded-md hover:bg-blue-800 transition duration-300"
            >
              Login
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
