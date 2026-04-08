<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');

        return view('settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'tahun_default' => ['required', 'digits:4', 'integer', 'min:2000', 'max:2099'],
            'nama_kegiatan' => ['required', 'string', 'max:150'],
        ]);

        Setting::set('tahun_default', $request->input('tahun_default'));
        Setting::set('nama_kegiatan', trim($request->input('nama_kegiatan')));

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan.',
        ]);
    }
}
