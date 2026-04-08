<?php

namespace App\Http\Controllers;

use App\Models\JenisBabak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisBabakController extends Controller
{
    public function index()
    {
        $jenisBabaks = JenisBabak::orderBy('kode')->get();

        return view('jenis-babak.index', compact('jenisBabaks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'string', 'size:4', 'unique:jenis_babaks,kode'],
            'nama' => ['required', 'string', 'max:100'],
        ]);

        JenisBabak::create([
            'kode' => strtoupper($request->kode),
            'nama' => trim($request->nama),
        ]);

        return redirect()->route('jenis-babak.index')
            ->with('success', 'Jenis babak berhasil ditambahkan.');
    }

    public function update(Request $request, JenisBabak $jenisBabak)
    {
        $request->validate([
            'kode' => ['required', 'string', 'size:4', 'unique:jenis_babaks,kode,' . $jenisBabak->id],
            'nama' => ['required', 'string', 'max:100'],
        ]);

        $jenisBabak->update([
            'kode' => strtoupper($request->kode),
            'nama' => trim($request->nama),
        ]);

        return redirect()->route('jenis-babak.index')
            ->with('success', 'Jenis babak berhasil diperbarui.');
    }

    public function destroy(JenisBabak $jenisBabak)
    {
        $jenisBabak->delete();

        return redirect()->route('jenis-babak.index')
            ->with('success', 'Jenis babak berhasil dihapus.');
    }
}
