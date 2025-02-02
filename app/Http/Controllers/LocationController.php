<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::orderBy('created_at', 'desc')->get();
        return view('admin.location', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Jika diperlukan, tampilkan form create location.
        // Namun, pada kasus ini form dikelola via AJAX/JS, sehingga bisa dikosongkan.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'error'   => $validator->errors()->first()
            ]);
        }

        try {
            // Buat data location baru
            $location = Location::create([
                'location' => $request->location,
            ]);
            return response()->json([
                'success'  => true, 
                'location' => $location
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error'   => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        // Jika diperlukan, tampilkan detail location.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        // Form edit bisa ditampilkan via AJAX/JS, sehingga metode ini bisa dikosongkan.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json([
                'success' => false, 
                'error'   => 'Location not found.'
            ]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'error'   => $validator->errors()->first()
            ]);
        }

        try {
            // Perbarui data location
            $location->location = $request->location;
            $location->save();

            return response()->json([
                'success'  => true, 
                'location' => $location
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error'   => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json([
                'success' => false, 
                'error'   => 'Location not found.'
            ]);
        }

        try {
            $location->delete();
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error'   => $e->getMessage()
            ]);
        }
    }
}
