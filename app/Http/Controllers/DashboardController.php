<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // Ambil data user yang login
        $locations = Location::all();

        if ($user->role === 'admin'){
            return view('admin.dashboard', compact(['user', 'locations']));
        } elseif ($user->role === 'reviewer') {
            return view('reviewer.dashboard', compact('user'));
        } else {
            return view('caraka.dashboard', compact(['user', 'locations']));
        }
    }

    public function getServerTime(){
        return response()->json([
            'time' => Carbon::now()->locale('id')->translatedFormat('l, d F Y H:i:s')
        ]);
    }
}
