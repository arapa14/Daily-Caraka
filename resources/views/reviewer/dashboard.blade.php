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

    <!-- Card Dashboard -->
    <div class="flex justify-center items-center min-h-[50vh] mt-7">
        <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md text-center">
            <h2 class="text-3xl font-extrabold text-gray-800">Selamat Datang di Reviewer Dashboard!</h2>
            <p class="text-gray-600 mt-3 text-lg">Lihat laporan yang sudah dibuat oleh Caraka hari ini.</p>

            <a href="{{ route('status') }}"
                class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold py-3 px-6 rounded-xl shadow-lg transition-transform transform hover:scale-105">
                Show Laporan
            </a>
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
