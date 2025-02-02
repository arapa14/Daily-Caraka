<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
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
            'images'      => 'required|file|mimes:jpg,png|max:2048',
            'description' => 'required|string|max:255',
            'location'    => 'required|string|not_in:Pilih lokasi',
            'status'      => 'required|string|in:hadir,izin,sakit',
        ]);

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

            $laporan->image = $request->file('images')->store('image', 'public');
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
