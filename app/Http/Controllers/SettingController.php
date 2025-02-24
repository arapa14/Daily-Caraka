<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Misal: ambil settings sebagai instance singleton dan juga array key-value
        $setting = Setting::first();
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.setting', compact('setting', 'settings'));
    }

    // Metode create, store, show, edit, dan destroy bisa dikosongkan atau diimplementasikan sesuai kebutuhan.

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'enable_time_restriction' => 'required|in:0,1',
            'pagi_start'              => 'required|min:0|max:23',
            'pagi_end'                => 'required|min:0|max:23',
            'siang_start'             => 'required|min:0|max:23',
            'siang_end'               => 'required|min:0|max:23',
            'sore_start'              => 'required|min:0|max:23',
            'sore_end'                => 'required|min:0|max:23',
            'nama_sistem'             => 'required|string|max:255',
            'logo_sistem'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $currentSettings = Setting::all()->pluck('value', 'key')->toArray();
        
        // Proses upload file untuk logo_sistem jika ada
        if ($request->hasFile('logo_sistem')) {
            $file = $request->file('logo_sistem');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/', $filename);
            $validated['logo_sistem'] = $filePath;
        } else {
            // Jika tidak ada file baru, hapus key logo_sistem dari validated untuk menghindari update kosong
            unset($validated['logo_sistem']);
        }

        $fields = [
            'enable_time_restriction',
            'pagi_start',
            'pagi_end',
            'siang_start',
            'siang_end',
            'sore_start',
            'sore_end',
            'nama_sistem',
            'logo_sistem',
        ];

        $changes = [];
        foreach ($fields as $field) {
            $inputValue = $validated[$field];
            $currentValue = isset($currentSettings[$field]) ? $currentSettings[$field] : null;
            if ((string)$inputValue !== (string)$currentValue) {
                $changes[$field] = $inputValue;
            }
        }

        if (empty($changes)) {
            return response()->json([
                'info' => 'Tidak ada perubahan yang dilakukan.'
            ]);
        }

        try {
            foreach ($changes as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
            return response()->json([
                'success' => 'Pengaturan berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json([
                'error' => 'Gagal mengubah pengaturan: ' . $e->getMessage()
            ], 500);
        }
    }
}
