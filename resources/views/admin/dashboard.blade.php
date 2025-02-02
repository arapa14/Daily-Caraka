<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    {{-- header --}}
    <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center">
        <!-- Nama Pengguna -->
        <h1 class="text-xl font-bold text-blue-600 mb-2 sm:mb-0 sm:text-lg">Welcome Admin {{ $user->name }}</h1>

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
    
    <div class="container mx-auto mt-8 px-4">
        <div class="bg-white shadow-lg rounded-xl p-6 text-center">
            <h2 class="text-2xl font-bold text-gray-700 mb-6">Admin Menu</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Manage Users -->
                <a href="{{route('user')}}" class="flex flex-col items-center justify-center bg-blue-500 hover:bg-blue-600 text-white py-6 px-8 rounded-xl shadow-lg transition duration-300">
                    <i class="fas fa-users text-4xl mb-2"></i>
                    <span class="text-lg font-semibold">Manage Users</span>
                </a>
    
                <!-- Manage Locations -->
                <a href="#" class="flex flex-col items-center justify-center bg-green-500 hover:bg-green-600 text-white py-6 px-8 rounded-xl shadow-lg transition duration-300">
                    <i class="fas fa-map-marker-alt text-4xl mb-2"></i>
                    <span class="text-lg font-semibold">Manage Locations</span>
                </a>
    
                <!-- Settings -->
                <a href="#" class="flex flex-col items-center justify-center bg-purple-500 hover:bg-purple-600 text-white py-6 px-8 rounded-xl shadow-lg transition duration-300">
                    <i class="fas fa-cog text-4xl mb-2"></i>
                    <span class="text-lg font-semibold">Settings</span>
                </a>
            </div>
    
            <p class="text-gray-500 mt-6">Welcome to the Admin Dashboard! Use the menu above to manage users, locations, and settings.</p>
        </div>
    </div>
    

    <script>
        function updateClock() {
            fetch('/server-time')
                .then(response => response.json())
                .then(data => {
                    const serverTime = new Date(data.time);
                    const hours = serverTime.getHours();
                    const minutes = serverTime.getMinutes();
                    const formattedMinutes = minutes.toString().padStart(2, '0');

                    // Konversi jam ke menit total untuk perbandingan waktu yang akurat
                    const totalMinutes = hours * 60 + minutes;

                    let session = "Invalid"; // Default
                    if (totalMinutes >= 360 && totalMinutes < 720) { // 6:00 - 12:00
                        session = "Pagi";
                    } else if (totalMinutes >= 720 && totalMinutes < 900) { // 12:00 - 15:00
                        session = "Siang";
                    } else if (totalMinutes >= 900 && totalMinutes < 1020) { // 15:00 - 17:00
                        session = "Sore";
                    }

                    const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                    const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                        "September", "Oktober", "November", "Desember"
                    ];

                    const dayName = days[serverTime.getDay()];
                    const day = serverTime.getDate();
                    const month = months[serverTime.getMonth()];
                    const year = serverTime.getFullYear();

                    document.getElementById('realTimeClock').innerText =
                        `${session}, ${dayName} ${day} ${month} ${year} - ${hours.toString().padStart(2, '0')}:${formattedMinutes}`;
                })
                .catch(error => console.error("Gagal mengambil waktu server:", error));
        }

        // Panggil saat halaman dimuat
        updateClock();

        // Update setiap 1 menit (60000 ms) untuk mengurangi beban server
        setInterval(updateClock, 30000);
    </script>
</body>

</html>
