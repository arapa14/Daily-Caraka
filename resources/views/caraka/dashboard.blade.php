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
        <a href='{{route('riwayat')}}' class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 w-full sm:w-auto cursor-pointer">
            Lihat Riwayat
        </a>
    </div>

    <div class="mt-6 bg-white shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold">Submit Laporan</h2>
        @if ($errors->any())
            <div class="p-3 mt-3 text-sm text-red-600 bg-red-100 rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        @if(session('success'))
            <div class="p-3 mt-3 text-sm text-green-600 bg-green-100 rounded">
                {{session('success')}}
            </div>
        @endif
        <form action="{{route('laporan.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- file input --}}
            <div class="mt-4">
                <input type="file" name="images" class="w-full p-2 border rounded-md">
            </div>
            {{-- description input --}}
            <div class="mt-4">
                <input type="text" name="description" placeholder="Deskripsi" class="w-full p-2 border rounded-md">
            </div>
            {{-- location input --}}
            <div class="mt-4">
                <select name="location" class="w-full p-2 border rounded-md">
                    <option>Pilih lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{$location->location}}">{{$location->location}}</option>
                    @endforeach
                </select>
            </div>
            {{-- status input --}}
            <div class="mt-4">
                <select name="status" class="w-full p-2 border rounded-md">
                    <option>Pilih Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="sakit">Sakit</option>
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6 ">
        @foreach ($laporanHariIni as $laporan)
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border p-4">
                <div class="w-full max-h-60 flex items-center justify-center bg-gray-200 rounded-md overflow-hidden">
                    <img src="{{ asset('storage/' . $laporan->image) }}" alt="Laporan Image" class="w-full h-auto object-contain aspect-[4/3]">
                </div>
                
                <div class="p-4">
                    <h3 class="font-semibold text-blue-600 text-lg mb-1">{{ ucfirst($laporan->name) }}</h3>
                    <p class="text-gray-500 text-sm">
                        <span class="text-blue-500 font-medium">Laporan :</span>
                        <span class="text-blue-500 font-medium bg-blue-100 px-2 py-1 rounded-md">{{ $laporan->time }}</span>
                    </p>
                    <p class="text-gray-700 mt-2">{{ $laporan->description }}</p>
                </div>
                
                <div class="flex justify-between items-center px-4 py-3 text-sm">
                    <span class="text-gray-400 font-medium">{{ $laporan->location ?? 'Tidak Diketahui' }}</span>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 font-semibold rounded-lg 
                            @if($laporan->presence == 'hadir') bg-green-100 text-green-600 
                            @elseif($laporan->presence == 'sakit') bg-yellow-100 text-yellow-700
                            @elseif($laporan->presence == 'izin') bg-blue-100 text-blue-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($laporan->presence) }}
                        </span>
                        <span class="px-3 py-1 font-semibold rounded-lg
                            @if($laporan->status == 'approved') bg-green-100 text-green-600 
                            @elseif($laporan->status == 'rejected') bg-red-100 text-red-600
                            @else bg-yellow-100 text-yellow-600 @endif">
                            {{ ucfirst($laporan->status) }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
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
                    if (totalMinutes >= 360 && totalMinutes < 720) {  // 6:00 - 12:00
                        session = "Pagi";
                    } else if (totalMinutes >= 720 && totalMinutes < 900) { // 12:00 - 15:00
                        session = "Siang";
                    } else if (totalMinutes >= 900 && totalMinutes < 1020) { // 15:00 - 17:00
                        session = "Sore";
                    }
    
                    const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                    const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
                    const dayName = days[serverTime.getDay()];
                    const day = serverTime.getDate();
                    const month = months[serverTime.getMonth()];
                    const year = serverTime.getFullYear();
    
                    document.getElementById('realTimeClock').innerText = `${session}, ${dayName} ${day} ${month} ${year} - ${hours.toString().padStart(2, '0')}:${formattedMinutes}`;
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
