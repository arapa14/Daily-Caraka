<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Geometry\Factories\RectangleFactory;

class LaporanController extends Controller
{
    private function processImage($imageFile, $imageNamePrefix, $directory, $watermarkText, $fontPath, $sizeLimit = 1024)
    {
        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        // Buat nama file gambar
        $imageName = time() . "_{$imageNamePrefix}." . $imageFile->getClientOriginalExtension();

        // Tentukan path lengkap untuk menyimpan gambar (di folder storage/app/public/{$directory})
        $imagePath = storage_path("app/public/{$directory}/{$imageName}");

        // Buat instance gambar menggunakan Intervention Image
        $image = $manager->read($imageFile->getRealPath());

        // --- Konfigurasi Watermark ---
        $padding = 30;
        $fontSize = 40;

        // Pisahkan teks watermark ke baris-baris
        $lines = explode("\n", $watermarkText);
        $lineCount = count($lines);
        $lineHeight = $fontSize + 5;
        $textBoxHeight = $lineCount * $lineHeight;

        // Hitung lebar teks terpanjang menggunakan fungsi imagettfbbox
        $maxTextWidth = 0;
        foreach ($lines as $line) {
            $box = imagettfbbox($fontSize, 0, $fontPath, $line);
            $textWidth = abs($box[2] - $box[0]);
            if ($textWidth > $maxTextWidth) {
                $maxTextWidth = $textWidth;
            }
        }
        $textBoxWidth = $maxTextWidth;

        // Tentukan ukuran background watermark
        $backgroundWidth = $textBoxWidth - 10;
        $backgroundHeight = $textBoxHeight + $padding * 2;

        // Tentukan margin dari tepi gambar
        $margin = 10;

        // Hitung koordinat background (pojok kiri atas)
        $backgroundX = $image->width() - $backgroundWidth - $margin;
        $backgroundY = $image->height() - $backgroundHeight - $margin;

        // Tambahkan background semi-transparan di pojok kanan bawah
        $image->drawRectangle($backgroundX, $backgroundY, function (RectangleFactory $rectangle) use ($backgroundWidth, $backgroundHeight) {
            $rectangle->size($backgroundWidth, $backgroundHeight);
            $rectangle->background('rgba(0, 0, 0, 0.5)');
            $rectangle->border('white', 2);
        });

        // Tentukan posisi teks: ditempatkan dengan align kanan dan bawah di dalam background
        $textX = $backgroundX + $backgroundWidth - $padding;
        $textY = $backgroundY + $backgroundHeight - $padding;

        // Tambahkan teks watermark
        $image->text($watermarkText, $textX, $textY, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->color('rgba(255, 255, 255, 0.9)');
            $font->align('right');
            $font->valign('bottom');
        });


        // Pastikan direktori penyimpanan gambar sudah ada
        if (!file_exists(storage_path("app/public/{$directory}"))) {
            mkdir(storage_path("app/public/{$directory}"), 0755, true);
        }

        // Simpan gambar dengan kualitas awal 80%
        $quality = 80;
        $image->save($imagePath, $quality);

        // Lakukan kompresi tambahan jika ukuran file melebihi batas ($sizeLimit dalam KB)
        while (filesize($imagePath) > $sizeLimit * 1024) {
            // Jika kualitas sudah terlalu rendah, hentikan loop
            if ($quality <= 10) {
                break;
            }
            $quality -= 10;
            $image->save($imagePath, $quality);
        }

        // Kembalikan path gambar yang bisa diakses secara publik
        // (pastikan Anda telah menjalankan "php artisan storage:link" untuk membuat symbolic link ke folder storage)
        return "/{$directory}/{$imageName}";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();
        $currentHour = Carbon::now()->hour;

        // Ambil semua settings sebagai array key-value
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Konfigurasi dinamis dari settings
        // Jika tidak ditemukan, gunakan default value
        $enable_time_restriction = isset($settings['enable_time_restriction'])
            ? ($settings['enable_time_restriction'] == '1')
            : true;

        $pagi_start = isset($settings['pagi_start']) ? (int)$settings['pagi_start'] : 6;
        $pagi_end   = isset($settings['pagi_end'])   ? (int)$settings['pagi_end']   : 12;
        $siang_start = isset($settings['siang_start']) ? (int)$settings['siang_start'] : 12;
        $siang_end   = isset($settings['siang_end'])   ? (int)$settings['siang_end']   : 15;
        $sore_start  = isset($settings['sore_start'])  ? (int)$settings['sore_start']  : 15;
        $sore_end    = isset($settings['sore_end'])    ? (int)$settings['sore_end']    : 17;

        // Tentukan sesi berdasarkan waktu saat ini menggunakan setting
        if ($currentHour >= $pagi_start && $currentHour < $pagi_end) {
            $session = 'pagi';
        } elseif ($currentHour >= $siang_start && $currentHour < $siang_end) {
            $session = 'siang';
        } elseif ($currentHour >= $sore_start && $currentHour < $sore_end) {
            $session = 'sore';
        } else {
            if ($enable_time_restriction) {
                return redirect()->back()->withErrors([
                    'error' => "Laporan hanya dapat dikirim antara {$pagi_start}:00 hingga {$sore_end}:00!"
                ]);
            }
            $session = 'invalid';
        }

        // Jika pembatasan waktu diaktifkan, lakukan pengecekan tambahan
        if ($enable_time_restriction) {
            // Cek apakah user sudah mengirim laporan untuk sesi ini hari ini
            $laporanSesiIni = Laporan::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->where('time', $session)
                ->exists();

            if ($laporanSesiIni) {
                return redirect()->back()->withErrors([
                    'error' => "Anda sudah mengirim laporan sesi {$session} hari ini!"
                ]);
            }

            // Hitung jumlah laporan hari ini
            $jumlahLaporanHariIni = Laporan::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();

            // Batasi maksimal 3 laporan per hari
            if ($jumlahLaporanHariIni >= 3) {
                return redirect()->back()->withErrors([
                    'error' => 'Anda telah mencapai batas maksimal 3 laporan hari ini!'
                ]);
            }
        }

        // Validasi input dari form
        $request->validate([
            'images'      => 'required|file|mimes:jpg,png',
            'description' => 'required|string|max:255',
            'location'    => 'required|string|not_in:Pilih lokasi',
            'status'      => 'required|string|in:hadir,izin,sakit',
        ]);

        // Ambil file gambar yang diupload
        $imageFile = $request->file('images');

        // Nama prefix untuk gambar, misalnya "laporan"
        $imageNamePrefix = 'laporan';

        // Direktori penyimpanan gambar (storage/app/public/laporan_images)
        $directory = 'image';

        // Watermark: gabungan nama user dan timestamp
        // Pastikan Anda telah mengaktifkan autentikasi (misalnya dengan auth()->user())
        $userName = auth()->check() ? auth()->user()->name : 'Guest';
        $watermarkText = $userName . " - " . now()->format('d/m/Y H:i:s');

        // Path file font (sesuaikan dengan lokasi file font Anda)
        $fontPath = public_path('arial.ttf');

        // Proses gambar dan dapatkan path gambar yang telah diproses
        $processedImagePath = $this->processImage($imageFile, $imageNamePrefix, $directory, $watermarkText, $fontPath);

        try {
            // Menyimpan laporan ke database
            $laporan = new Laporan();
            $laporan->user_id    = Auth::id();
            $laporan->name       = Auth::user()->name;
            $laporan->description = $request->input('description');
            $laporan->location   = $request->input('location');
            $laporan->date       = now();
            $laporan->time       = $session;
            $laporan->status     = 'pending';
            $laporan->presence   = $request->input('status');

            // $laporan->image = $request->file('images')->store('image', 'public');
            $laporan->image = $processedImagePath;
            $laporan->save();

            return redirect()->back()->with('success', 'Berhasil mengirim laporan.');
        } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()->with('error', 'Gagal mengirim laporan');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi input status yang diterima
        $request->validate([
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        try {
            // Cari laporan berdasarkan ID, jika tidak ditemukan akan memunculkan ModelNotFoundException
            $laporan = Laporan::findOrFail($id);

            // Update status laporan
            $laporan->status = $request->input('status');
            $laporan->save();

            return redirect()->back()->with('success', 'Status laporan berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error("Error updating laporan status: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui status laporan.']);
        }
    }

    // Metode lain (index, create, show, edit, update, destroy, updateStatus) tetap
}
