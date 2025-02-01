<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Location;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // Ambil data user yang login
        $locations = Location::all();
        $today = Carbon::today();

        // Ambil laporan user hari ini
        $laporanHariIni = Laporan::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->latest()
            ->take(3)
            ->get();

        // Hitung jumlah upload hari ini
        $jumlahUploadHariIni = $laporanHariIni->count();

        if ($user->role === 'admin') {
            return view('admin.dashboard', compact(['user', 'locations']));
        } elseif ($user->role === 'reviewer') {
            return view('reviewer.dashboard', compact('user'));
        } else {
            return view('caraka.dashboard', compact(['user', 'locations', 'laporanHariIni', 'jumlahUploadHariIni']));
        }
    }

    public function getServerTime()
    {
        return response()->json([
            'time' => Carbon::now()->locale('id')->translatedFormat('l, d F Y H:i:s')
        ]);
    }
}
