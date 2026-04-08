<?php

namespace App\Http\Controllers;

use App\Models\HasilDrawing;
use App\Models\LombaProvinsi;
use App\Models\Provinsi;
use App\Models\SekolahLomba;
use App\Models\LombaBabakRegu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilDrawingController extends Controller
{
    /**
     * Tampilkan halaman modul drawing.
     */
    public function index()
    {
        $provinsis = Provinsi::defaultOrder()->get();
        
        return view('hasil-drawing.index', [
            'provinsis' => $provinsis,
        ]);
    }

    /**
     * Get data lomba provinsi berdasarkan provinsi_id.
     */
    public function getLombaByProvinsi(string $provinsiId): JsonResponse
    {
        $lomba = LombaProvinsi::with(['provinsi', 'sekolah'])
            ->where('provinsi_id', $provinsiId)
            ->first();

        if (!$lomba) {
            return response()->json([
                'success' => false,
                'message' => 'Data lomba untuk provinsi ini belum terdaftar.',
            ], 404);
        }

        // Ambil semua babak regu yang tersedia
        $babakRegus = LombaBabakRegu::with('jenisBabak')
            ->orderBy('nomor')
            ->get();

        // Ambil hasil drawing yang sudah ada untuk lomba ini
        $existingDrawings = HasilDrawing::where('lomba_provinsi_id', $lomba->id)
            ->with(['sekolahLomba', 'lombaBabakRegu.jenisBabak'])
            ->get()
            ->keyBy('sekolah_lomba_id');

        return response()->json([
            'success' => true,
            'data' => [
                'lomba' => [
                    'id' => $lomba->id,
                    'tahun' => $lomba->tahun,
                    'provinsi' => $lomba->provinsi,
                    'nama_kegiatan' => $lomba->nama_kegiatan,
                    'tempat_kegiatan' => $lomba->tempat_kegiatan,
                    'tanggal_kegiatan' => $lomba->tanggal_kegiatan,
                ],
                'sekolah' => $lomba->sekolah->map(fn($s) => [
                    'id' => $s->id,
                    'kode_sekolah' => $s->kode_sekolah,
                    'nama_sekolah' => $s->nama_sekolah,
                    'nomor_telepon' => $s->nomor_telepon,
                    'email' => $s->email,
                    'keterangan' => $s->keterangan,
                    'existing_drawing' => $existingDrawings->has($s->id) ? [
                        'id' => $existingDrawings->get($s->id)->id,
                        'lomba_babak_regu_id' => $existingDrawings->get($s->id)->lomba_babak_regu_id,
                        'uraian' => $existingDrawings->get($s->id)->lombaBabakRegu->uraian ?? '',
                        'nomor' => $existingDrawings->get($s->id)->lombaBabakRegu->nomor ?? 0,
                        'kode' => $existingDrawings->get($s->id)->lombaBabakRegu->kode ?? '',
                    ] : null,
                ]),
                'babak_regus' => $babakRegus->map(fn($b) => [
                    'id' => $b->id,
                    'nomor' => $b->nomor,
                    'kode' => $b->kode,
                    'uraian' => $b->uraian,
                    'jenis_babak_nama' => $b->jenisBabak?->nama ?? '-',
                ]),
                'existing_babak_regu_ids' => $existingDrawings->pluck('lomba_babak_regu_id')->values(),
            ],
        ]);
    }

    /**
     * Simpan hasil drawing.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'lomba_provinsi_id' => ['required', 'exists:lomba_provinsis,id'],
            'tahun' => ['required', 'string', 'size:4'],
            'drawings' => ['required', 'array'],
            'drawings.*.sekolah_lomba_id' => ['required', 'exists:sekolah_lombas,id'],
            'drawings.*.lomba_babak_regu_id' => ['required', 'exists:lomba_babak_regus,id'],
        ]);

        $lombaProvinsiId = $request->lomba_provinsi_id;
        $tahun = $request->tahun;
        $drawings = $request->drawings;

        // Validasi: semua sekolah harus terisi
        $lomba = LombaProvinsi::with('sekolah')->findOrFail($lombaProvinsiId);
        $jumlahSekolah = $lomba->sekolah->count();

        if (count($drawings) !== $jumlahSekolah) {
            return response()->json([
                'success' => false,
                'message' => "Semua {$jumlahSekolah} sekolah harus dipilih babak regunya. Saat ini baru terisi " . count($drawings) . '.',
            ], 422);
        }

        // Validasi: tidak ada duplikasi babak regu
        $babakReguIds = collect($drawings)->pluck('lomba_babak_regu_id');
        if ($babakReguIds->unique()->count() !== $babakReguIds->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat duplikasi babak regu. Setiap sekolah harus memiliki babak regu yang berbeda.',
            ], 422);
        }

        DB::transaction(function () use ($lombaProvinsiId, $tahun, $drawings) {
            foreach ($drawings as $drawing) {
                HasilDrawing::updateOrCreate(
                    [
                        'lomba_provinsi_id' => $lombaProvinsiId,
                        'sekolah_lomba_id' => $drawing['sekolah_lomba_id'],
                    ],
                    [
                        'tahun' => $tahun,
                        'lomba_babak_regu_id' => $drawing['lomba_babak_regu_id'],
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Hasil drawing berhasil disimpan.',
        ]);
    }

    /**
     * Get info babak regu by ID.
     */
    public function getBabakReguInfo(string $id): JsonResponse
    {
        $babakRegu = LombaBabakRegu::with('jenisBabak')->find($id);

        if (!$babakRegu) {
            return response()->json([
                'success' => false,
                'message' => 'Babak regu tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $babakRegu->id,
                'nomor' => $babakRegu->nomor,
                'kode' => $babakRegu->kode,
                'uraian' => $babakRegu->uraian,
                'jenis_babak_nama' => $babakRegu->jenisBabak?->nama ?? '-',
            ],
        ]);
    }
}
