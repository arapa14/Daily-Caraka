<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daily Caraka - Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-200 to-blue-600 flex items-center justify-center p-6">
  <div class="bg-white shadow-2xl rounded-xl overflow-hidden w-full max-w-4xl">
    <div class="md:flex">
      <!-- Bagian Gambar (hanya tampil di desktop) -->
      <div class="hidden md:block md:w-1/2">
        <img src="data:image/jpeg;base64,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" alt="Daily Caraka" class="object-cover h-full w-full">
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
