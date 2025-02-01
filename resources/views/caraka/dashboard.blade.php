<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    {{-- header --}}
    <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center">
        <!-- Nama Pengguna -->
        <h1 class="text-xl font-bold text-blue-600 mb-2 sm:mb-0 sm:text-lg">Welcome {{ $user->name }}</h1>

        <!-- Jam (Real-time) -->
        <span id="realTimeClock" class="text-gray-600 mb-2 sm:mb-0 sm:text-md">Memuat Waktu...</span>

        <!-- Tombol Logout dengan Ikon -->
        <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="flex items-center text-blue-600 hover:text-blue-900 text-lg w-full sm:w-auto">
                <!-- Ikon Logout -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 17l5-5-5-5M21 12H9m4-7v14"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>

    {{-- button --}}
    <div class="text-center mt-6">
        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 w-full sm:w-auto">
            Lihat Riwayat
        </button>
    </div>

    <div class="mt-6 bg-white shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold">Submit Laporan</h2>
        <form action="">
            @csrf
            {{-- description input --}}
            <div class="mt-4">
                <input type="text" name="description" placeholder="Deskripsi" class="w-full p-2 border rounded-md">
            </div>
            {{-- file input --}}
            <div class="mt-4">
                <input type="file" name="file" class="w-full p-2 border rounded-md">
            </div>
            {{-- location input --}}
            <div class="mt-4">
                <select name="location" class="w-full p-2 border rounded-md">
                    <option>Pilih lokasi</option>
                    <option value="lokasi1">Lokasi 1</option>
                    <option value="lokasi2">Lokasi 2</option>
                </select>
            </div>
            {{-- status input --}}
            <div class="mt-4">
                <select name="status" class="w-full p-2 border rounded-md">
                    <option>Pilih Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="izin">Sakit</option>
                    <option value="izin">Izin</option>
                </select>
            </div>
            {{-- submit button --}}
            <div class="mt-6">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 w-full">Submit</button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-white shadow-md rounded-lg p-6 text-center">
        <p class="text-gray-500">📄 Belum Ada Laporan</p>
        <p class="text-gray-400"> Ayo Buat Laporan Pertama Anda!</p>
    </div>

    <script>
        // Ambil data lewat api 
        function updateClock() {
            fetch('/server-time')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('realTimeClock').innerText = data.time;
                })
                .catch(error => console.error("Gagal mengambil waktu server:", error));
        }

        // Panggil pertamakali saat halaman dimuat
        updateClock();

        // Update setiap detik
        setInterval(updateClock, 1000);
    </script>
</body>

</html>
