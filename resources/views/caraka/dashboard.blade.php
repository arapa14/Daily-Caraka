<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    
    <h1>welcome {{ $user->name }}</h1>
    <h1 id="realTimeClock">Memuat Waktu...</h1>


    <form action="{{route('logout')}}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

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