<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Settings Management</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 p-6">
  <!-- Header (Jangan diubah) -->
  <header class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center">
    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600 mb-2 sm:mb-0 hover:bg-blue-100 rounded-xl px-3 py-2">
      Dashboard
    </a>
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="text-red-500 hover:text-red-700">
        <i class="fas fa-sign-out-alt"></i> Logout
      </button>
    </form>
  </header>

  <!-- Container Settings -->
  <main class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mt-6">
    <h2 class="text-2xl font-bold text-gray-700 mb-6 text-center">Settings Management</h2>
    
    <!-- Form Settings -->
    <form id="updateSettingForm" action="{{ route('setting.update') }}" method="POST">
      @csrf

      <!-- Pengaturan Utama -->
      <div class="mb-8 p-4 border rounded-lg bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <i class="fas fa-clock"></i> Pengaturan Waktu
        </h3>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <label for="enable_time_restriction" class="block text-gray-700 font-semibold mb-2 sm:mb-0">
            Aktifkan Pembatasan Waktu:
          </label>
          <select id="enable_time_restriction" name="enable_time_restriction" class="w-full sm:w-1/3 border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="1" {{ $settings['enable_time_restriction'] == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ $settings['enable_time_restriction'] == '0' ? 'selected' : '' }}>Nonaktif</option>
          </select>
        </div>
      </div>

      <!-- Jadwal Sesi -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <i class="fas fa-calendar-alt"></i> Jadwal Sesi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Card Pagi -->
          <div class="p-4 border rounded-lg bg-gray-50">
            <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">Pagi</h4>
            <div class="mb-3">
              <label for="pagi_start" class="block text-gray-600 text-sm font-medium mb-1">Mulai (Jam):</label>
              <input type="number" id="pagi_start" name="pagi_start" value="{{ $settings['pagi_start'] }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label for="pagi_end" class="block text-gray-600 text-sm font-medium mb-1">Berakhir (Jam):</label>
              <input type="number" id="pagi_end" name="pagi_end" value="{{ $settings['pagi_end'] }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>

          <!-- Card Siang -->
          <div class="p-4 border rounded-lg bg-gray-50">
            <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">Siang</h4>
            <div class="mb-3">
              <label for="siang_start" class="block text-gray-600 text-sm font-medium mb-1">Mulai (Jam):</label>
              <input type="number" id="siang_start" name="siang_start" value="{{ $settings['siang_start'] }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label for="siang_end" class="block text-gray-600 text-sm font-medium mb-1">Berakhir (Jam):</label>
              <input type="number" id="siang_end" name="siang_end" value="{{ $settings['siang_end'] }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>

          <!-- Card Sore -->
          <div class="p-4 border rounded-lg bg-gray-50">
            <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">Sore</h4>
            <div class="mb-3">
              <label for="sore_start" class="block text-gray-600 text-sm font-medium mb-1">Mulai (Jam):</label>
              <input type="number" id="sore_start" name="sore_start" value="{{ $settings['sore_start'] }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label for="sore_end" class="block text-gray-600 text-sm font-medium mb-1">Berakhir (Jam):</label>
              <input type="number" id="sore_end" name="sore_end" value="{{ $settings['sore_end'] }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
        </div>
      </div>

      <!-- Tombol Submit -->
      <div class="text-center">
        <button type="submit" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-semibold px-8 py-3 rounded-md shadow-md transition duration-300">
          <i class="fas fa-save mr-2"></i> Simpan Pengaturan
        </button>
      </div>
    </form>
  </main>

  <!-- Notifikasi SweetAlert2 untuk flash messages -->
  @if(session('success'))
  <script>
    Swal.fire('Berhasil', '{{ session("success") }}', 'success');
  </script>
  @endif

  @if(session('error'))
  <script>
    Swal.fire('Gagal', '{{ session("error") }}', 'error');
  </script>
  @endif

  @if(session('info'))
  <script>
    Swal.fire('Info', '{{ session("info") }}', 'info');
  </script>
  @endif

  <!-- JavaScript untuk konfirmasi dan update settings -->
  <script>
    document.getElementById('updateSettingForm').addEventListener('submit', function(event) {
      event.preventDefault(); // cegah submit form bawaan

      Swal.fire({
        title: 'Konfirmasi Perubahan',
        text: "Apakah Anda yakin ingin mengubah pengaturan?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, ubah',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          const form = event.target;
          const formData = new FormData(form);
          const data = {};

          // Daftar field yang harus berupa integer
          const intFields = ['pagi_start', 'pagi_end', 'siang_start', 'siang_end', 'sore_start', 'sore_end'];

          formData.forEach((value, key) => {
            data[key] = intFields.includes(key) ? parseInt(value) : value;
          });

          fetch("{{ route('setting.update') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
          })
          .then(async response => {
            if (!response.ok) {
              const errorData = await response.json();
              throw errorData;
            }
            return response.json();
          })
          .then(resultData => {
            if (resultData.success) {
              Swal.fire("Berhasil!", resultData.success, "success")
                .then(() => location.reload());
            } else if (resultData.info) {
              Swal.fire("Info", resultData.info, "info");
            } else {
              Swal.fire("Gagal!", resultData.error || 'Terjadi kesalahan', "error");
            }
          })
          .catch(error => {
            console.error('Error update settings:', error);
            let message = "Terjadi kesalahan, silahkan coba lagi";
            if (error.errors) {
              message = Object.values(error.errors).join('<br>');
            } else if (error.message) {
              message = error.message;
            }
            Swal.fire("Error", message, "error");
          });
        }
      });
    });
  </script>
</body>
</html>
