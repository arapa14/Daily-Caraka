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
            'pagi_start'              => 'required|integer|min:0|max:23',
            'pagi_end'                => 'required|integer|min:0|max:23',
            'siang_start'             => 'required|integer|min:0|max:23',
            'siang_end'               => 'required|integer|min:0|max:23',
            'sore_start'              => 'required|integer|min:0|max:23',
            'sore_end'                => 'required|integer|min:0|max:23',
        ]);

        $currentSettings = Setting::all()->pluck('value', 'key')->toArray();
        $fields = [
            'enable_time_restriction',
            'pagi_start',
            'pagi_end',
            'siang_start',
            'siang_end',
            'sore_start',
            'sore_end'
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
