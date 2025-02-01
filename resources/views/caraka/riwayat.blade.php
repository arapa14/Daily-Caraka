<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Styling untuk modal */
        #imageModal {
            display: none;
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
    <!-- Header -->
    <div class="bg-white shadow-lg rounded-lg p-5 flex flex-wrap justify-between items-center gap-4">
        <!-- Username -->
        <h1 class="text-2xl font-semibold text-blue-700">Welcome, {{ $user->name }}</h1>

        <div class="flex items-center gap-3">
            <!-- Tombol Kembali ke Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-gradient-to-r from-blue-700 to-blue-900 transition duration-300">
                Back to Dashboard
            </a>

            <!-- Tombol Logout -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center text-red-600 hover:text-red-800 font-semibold transition duration-300">
                    <!-- Ikon Logout -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 17l5-5-5-5M21 12H9m4-7v14"></path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-5 mt-7">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Riwayat Laporan</h2>

        <div class="overflow-x-auto rounded-lg bg-white p-5">
            <table class="min-w-full border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-500 to-blue-700 text-white text-center">
                        <th class="p-3">ID</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Waktu</th>
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
                            <td class="p-3">{{ $laporan->date }}</td>
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
                                @php
                                    $statusColor = match ($laporan->status) {
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approve' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span
                                    class="px-3 py-1 rounded-md text-sm font-medium 
                                {{ $statusColor }}">
                                    {{ $laporan->status }}
                                </span>
                            </td>
                            <td class="p-3 flex justify-center gap-2">
                                 <!-- Button untuk melihat gambar -->
                                 <button class="bg-blue-500 hover:bg-blue-700 text-white p-2 rounded-md transition" onclick="openModal('{{ asset('storage/' . $laporan->image) }}')">
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
        // Fungsi untuk membuka modal dan menampilkan gambar
        function openModal(imagePath) {
            let modal = document.getElementById('imageModal');
            let modalImage = document.getElementById('modalImage');

            modalImage.src = imagePath;
            modal.style.display = "flex";
        }

        // Menutup modal saat klik tombol "X"
        document.getElementById('closeModal').addEventListener('click', function () {
            document.getElementById('imageModal').style.display = "none";
        });

        // Menutup modal saat klik di luar gambar
        window.onclick = function (event) {
            let modal = document.getElementById('imageModal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>
