<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Styling untuk modal */
        #imageModal {
            visibility: hidden;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-content {
            position: relative;
            background: white;
            padding: 10px;
            border-radius: 10px;
            max-width: 90%;
            max-height: 90%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #modalImage {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            color: white;
            cursor: pointer;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            padding: 5px 10px;
        }
    </style>
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
                <a href="{{route('user.index')}}" class="flex flex-col items-center justify-center bg-blue-500 hover:bg-blue-600 text-white py-6 px-8 rounded-xl shadow-lg transition duration-300">
                    <i class="fas fa-users text-4xl mb-2"></i>
                    <span class="text-lg font-semibold">Manage Users</span>
                </a>
    
                <!-- Manage Locations -->
                <a href="{{route('location.index')}}" class="flex flex-col items-center justify-center bg-green-500 hover:bg-green-600 text-white py-6 px-8 rounded-xl shadow-lg transition duration-300">
                    <i class="fas fa-map-marker-alt text-4xl mb-2"></i>
                    <span class="text-lg font-semibold">Manage Locations</span>
                </a>
    
                <!-- Settings -->
                <a href="{{route('setting')}}" class="flex flex-col items-center justify-center bg-purple-500 hover:bg-purple-600 text-white py-6 px-8 rounded-xl shadow-lg transition duration-300">
                    <i class="fas fa-cog text-4xl mb-2"></i>
                    <span class="text-lg font-semibold">Settings</span>
                </a>
            </div>
    
            <p class="text-gray-500 mt-6">Welcome to the Admin Dashboard! Use the menu above to manage users, locations, and settings.</p>
        </div>
    </div>

    @if ($errors->any())
        <div id="error-message" class="p-3 mt-3 text-sm text-red-600 bg-red-100 rounded">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if (session('success'))
        <div id="success-message" class="p-3 mt-3 text-sm text-green-600 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg p-5 mt-7">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Riwayat Laporan</h2>

        <div class="overflow-x-auto rounded-lg bg-white p-5">
            <table class="min-w-full border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-500 to-blue-700 text-white text-center">
                        <th class="p-3">ID</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Sesi</th>
                        <th class="p-3">Deskripsi</th>
                        <th class="p-3">Lokasi</th>
                        <th class="p-3">Kehadiran</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($laporans as $laporan)
                        <tr class="border-b hover:bg-gray-50 text-center transition duration-200">
                            <td class="p-3 font-semibold text-gray-700">{{ $laporan->id }}</td>
                            <td class="p-3">{{ $laporan->name }}</td>
                            <td class="p-3">{{ $laporan->created_at }}</td>
                            <td class="p-3">{{ $laporan->time }}</td>
                            <td class="p-3 text-left">{{ $laporan->description }}</td>
                            <td class="p-3">{{ $laporan->location }}</td>
                            <td class="p-3">
                                @php
                                    $presenceColor = match ($laporan->presence) {
                                        'hadir' => 'bg-green-100 text-green-700',
                                        'sakit' => 'bg-yellow-100 text-yellow-700',
                                        'izin' => 'bg-blue-100 text-blue-700',
                                        'alpa' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span
                                    class="px-3 py-1 rounded-md text-sm font-medium 
                                {{ $presenceColor }}">
                                    {{ $laporan->presence }}
                                </span>
                            </td>
                            <td class="p-3">
                                <form action="{{ route('updateAdmin', $laporan->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                        class="border p-1 rounded 
                                            {{ $laporan->status == 'pending' ? 'bg-yellow-200 text-yellow-800' : '' }} 
                                            {{ $laporan->status == 'approved' ? 'bg-green-200 text-green-800' : '' }} 
                                            {{ $laporan->status == 'rejected' ? 'bg-red-200 text-red-800' : '' }}"
                                        onchange="this.form.submit()">
                                        <option value="pending" {{ $laporan->status == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="approved"
                                            {{ $laporan->status == 'approved' ? 'selected' : '' }}>
                                            Approve
                                        </option>
                                        <option value="rejected"
                                            {{ $laporan->status == 'rejected' ? 'selected' : '' }}>
                                            Rejected
                                        </option>
                                    </select>
                                </form>
                            </td>


                            <td class="p-3 flex justify-center gap-2">
                                <!-- Button untuk melihat gambar -->
                                <button class="bg-blue-500 hover:bg-blue-700 text-white p-2 rounded-md transition"
                                    onclick="openModal('{{ asset('storage/' . $laporan->image) }}')">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Button untuk mengunduh gambar -->
                                <a href="{{ asset('storage/' . $laporan->image) }}" download>
                                    <button
                                        class="bg-green-500 hover:bg-green-700 text-white p-2 rounded-md transition">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <div class="flex justify-between items-center mt-4 text-gray-600">
            <p>Page {{ $laporans->currentPage() }} of {{ $laporans->lastPage() }}</p>
            <div class="flex gap-2">
                @if ($laporans->onFirstPage())
                    <span class="px-3 py-1 bg-gray-300 rounded-lg cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $laporans->previousPageUrl() }}"
                        class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300">Previous</a>
                @endif

                @if ($laporans->hasMorePages())
                    <a href="{{ $laporans->nextPageUrl() }}"
                        class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300">Next</a>
                @else
                    <span class="px-3 py-1 bg-gray-300 rounded-lg cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal untuk melihat gambar -->
    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-gray-600 bg-opacity-50 hidden">
        <div class="bg-white p-5 rounded-lg">
            <span id="closeModal" class="absolute top-0 right-0 p-2 cursor-pointer">X</span>
            <img id="modalImage" src="" alt="Image" class="w-full h-auto">
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

        // Fungsi untuk membuka modal dan menampilkan gambar
        function openModal(imagePath) {
            // console.log("Image path:", imagePath); // Debugging
            // if (!imagePath) return;

            let modal = document.getElementById('imageModal');
            let modalImage = document.getElementById('modalImage');

            modalImage.src = imagePath;
            modal.style.visibility = "visible";
        }

        // Menutup modal saat klik tombol "X"
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('imageModal').style.visibility = 'hidden';
        });


        // Menutup modal saat klik di luar gambar
        window.onclick = function(event) {

            let modal = document.getElementById('imageModal');
            if (event.target === modal) {
                modal.style.visibility = "hidden";
            }
        }

        // Menghilangkan pesan setelah 5 detik (5000ms)
        setTimeout(function() {
            let errorMessage = document.getElementById('error-message');
            let successMessage = document.getElementById('success-message');

            if (errorMessage) {
                errorMessage.style.transition = "opacity 0.5s";
                errorMessage.style.opacity = "0";
                setTimeout(() => errorMessage.remove(), 500);
            }

            if (successMessage) {
                successMessage.style.transition = "opacity 0.5s";
                successMessage.style.opacity = "0";
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 5000);
    </script>
</body>

</html>
