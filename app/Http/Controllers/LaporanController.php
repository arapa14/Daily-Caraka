<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Hitung jumlah laporan hari ini
        $jumlahLaporanHariIni = Laporan::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();

        // Batasi maksimal 3 upload per hari
        if ($jumlahLaporanHariIni >= 3) {
            return redirect()->back()->withErrors(['error' => 'Anda telah mencapai batas maksimal 3 laporan hari ini!']);
        }
        // dd($request->all());
        //validasi input dari form
        $request->validate([
            'images' => 'required|file|mimes:jpg,png|max:2048',
            'description' => 'required|string|max:255',
            'location' => 'required|string|notin:Pilih lokasi',
            'status' => 'required|string|in:hadir,izin,sakit',
        ]);

        try {
            // menyimpan laporan ke database
            $laporan = new Laporan();
            $laporan->user_id = Auth::id(); //Mengambil IS user yang sedang login
            $laporan->name = Auth::user()->name;
            $laporan->description = $request['description'];
            $laporan->location = $request['location'];
            $laporan->date = now();
            $laporan->time = 'pagi'; //nanti ini dibikin kondisi
            $laporan->status = 'pending';
            $laporan->presence = $request['status'];

            $laporan->image = $request->file('images')->store('image', 'public');
            $laporan->save();

            return redirect()->back()->with('success', 'Berhasil mengirim laporan.');
        } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()->with('error', 'Gagal mengirim laporan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Laporan $laporan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laporan $laporan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laporan $laporan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laporan $laporan)
    {
        //
    }
}
