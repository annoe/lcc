<?php

namespace App\Http\Controllers;

use App\Models\LombaBabakRegu;
use Illuminate\Http\Request;

class LombaBabakReguController extends Controller
{
    public function index()
    {
        $lombaBabakRegus = LombaBabakRegu::with('jenisBabak')
            ->orderBy('nomor')
            ->orderBy('kode')
            ->get();

        return view('lomba-babak-regu.index', compact('lombaBabakRegus'));
    }

    public function update(Request $request, LombaBabakRegu $lombaBabakRegu)
    {
        $request->validate([
            'uraian' => ['required', 'string', 'max:255'],
        ]);

        $lombaBabakRegu->update([
            'uraian' => trim($request->uraian),
        ]);

        return redirect()->route('lomba-babak-regu.index')
            ->with('success', 'Uraian babak regu berhasil diperbarui.');
    }
}
