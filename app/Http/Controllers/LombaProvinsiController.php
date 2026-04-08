<?php

namespace App\Http\Controllers;

use App\Models\LombaProvinsi;
use App\Models\Provinsi;
use App\Models\SekolahLomba;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LombaProvinsiController extends Controller
{
    private const JUMLAH_SEKOLAH = 9;

    // ────────────────────────────────────────────
    //  INDEX
    // ────────────────────────────────────────────
    public function index(Request $request)
    {
        $tahunDefault = Setting::get('tahun_default', (string) now()->year);
        $namaKegiatan = Setting::get('nama_kegiatan', 'Lomba Cerdas Cermat MPR RI');
        $namaKegiatanTemplate = Setting::get('nama_kegiatan_default', 'Lomba Cerdas Cermat MPR RI Tahun {{tahun}} Seleksi Provinsi, Provinsi {{provinsi}}');
        $tahunFilter  = $request->get('tahun', $tahunDefault);

        $query = LombaProvinsi::with(['provinsi', 'sekolah'])
            ->defaultOrder();

        if ($tahunFilter) {
            $query->byTahun($tahunFilter);
        }

        $lombas = $query->get();

        // Daftar tahun yang tersedia (untuk filter)
        $tahunList = LombaProvinsi::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Semua provinsi (untuk dropdown tambah)
        $semuaProvinsi = Provinsi::defaultOrder()->get();

        // Provinsi yang belum terdaftar di tahun ini
        $terdaftarIds = LombaProvinsi::where('tahun', $tahunFilter)
            ->pluck('provinsi_id');

        $tersediaProvinsi = Provinsi::defaultOrder()
            ->whereNotIn('id', $terdaftarIds)
            ->get();

        return view('lomba-provinsi.index', [
            'lombas'               => $lombas,
            'tahunDefault'         => $tahunDefault,
            'namaKegiatan'         => $namaKegiatan,
            'namaKegiatanTemplate' => $namaKegiatanTemplate,
            'tahunFilter'          => $tahunFilter,
            'tahunList'            => $tahunList,
            'semuaProvinsi'        => $semuaProvinsi,
            'tersediaProvinsi'     => $tersediaProvinsi,
            'total'                => $lombas->count(),
        ]);
    }

    // ────────────────────────────────────────────
    //  STORE
    // ────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $tahunDefault = Setting::get('tahun_default', (string) now()->year);

        $request->validate([
            'provinsi_id'                        => ['required', 'exists:provinsis,id',
                Rule::unique('lomba_provinsis')->where('tahun', $tahunDefault)],
            'nama_kegiatan'                      => ['nullable', 'string', 'max:250'],
            'tempat_kegiatan'                    => ['nullable', 'string', 'max:250'],
            'tanggal_kegiatan'                   => ['nullable', 'date'],
            'sekolah'                            => ['required', 'array', 'size:' . self::JUMLAH_SEKOLAH],
            'sekolah.*.nama_sekolah'             => ['required', 'string', 'max:150', 'distinct'],
            'sekolah.*.nomor_telepon'            => ['nullable', 'string', 'regex:/^(\+62|0)[0-9]{7,14}$/'],
            'sekolah.*.email'                    => ['nullable', 'email', 'max:100'],
            'sekolah.*.keterangan'               => ['nullable', 'string', 'max:500'],
        ], [
            'provinsi_id.unique'         => 'Provinsi ini sudah terdaftar pada tahun ' . $tahunDefault . '.',
            'sekolah.size'               => 'Harus tepat ' . self::JUMLAH_SEKOLAH . ' sekolah.',
            'sekolah.*.nama_sekolah.distinct' => 'Nama sekolah tidak boleh duplikat dalam satu provinsi.',
            'sekolah.*.nomor_telepon.regex'   => 'Format nomor telepon tidak valid (contoh: 0811xxxxxxxx atau +6281xxxxxxx).',
        ]);

        $provinsi = Provinsi::findOrFail($request->provinsi_id);

        DB::transaction(function () use ($request, $tahunDefault, $provinsi) {
            $lomba = LombaProvinsi::create([
                'tahun'            => $tahunDefault,
                'provinsi_id'      => $provinsi->id,
                'nama_kegiatan'    => $request->nama_kegiatan ?? null,
                'tempat_kegiatan'  => $request->tempat_kegiatan ?? null,
                'tanggal_kegiatan' => $request->tanggal_kegiatan ?? null,
            ]);

            foreach ($request->sekolah as $i => $s) {
                $urutan = $i + 1; // 1–9
                SekolahLomba::create([
                    'lomba_provinsi_id' => $lomba->id,
                    'kode_sekolah'      => $provinsi->kode . $urutan,
                    'nama_sekolah'      => trim($s['nama_sekolah']),
                    'nomor_telepon'     => $s['nomor_telepon'] ?? null,
                    'email'             => $s['email'] ?? null,
                    'keterangan'        => $s['keterangan'] ?? null,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Provinsi \"{$provinsi->nama}\" berhasil didaftarkan dengan " . self::JUMLAH_SEKOLAH . " sekolah.",
        ]);
    }

    // ────────────────────────────────────────────
    //  GET DATA (untuk modal edit)
    // ────────────────────────────────────────────
    public function show(LombaProvinsi $lombaProvinsi): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $lombaProvinsi->id,
                'tahun'            => $lombaProvinsi->tahun,
                'provinsi_id'      => $lombaProvinsi->provinsi_id,
                'provinsi'         => $lombaProvinsi->provinsi,
                'nama_kegiatan'    => $lombaProvinsi->nama_kegiatan,
                'tempat_kegiatan'  => $lombaProvinsi->tempat_kegiatan,
                'tanggal_kegiatan' => $lombaProvinsi->tanggal_kegiatan,
                'sekolah'          => $lombaProvinsi->sekolah,
            ],
        ]);
    }

    // ────────────────────────────────────────────
    //  UPDATE
    // ────────────────────────────────────────────
    public function update(Request $request, LombaProvinsi $lombaProvinsi): JsonResponse
    {
        $request->validate([
            'nama_kegiatan'                      => ['nullable', 'string', 'max:250'],
            'tempat_kegiatan'                    => ['nullable', 'string', 'max:250'],
            'tanggal_kegiatan'                   => ['nullable', 'date'],
            'sekolah'                            => ['required', 'array', 'size:' . self::JUMLAH_SEKOLAH],
            'sekolah.*.id'                       => ['nullable', 'string'],
            'sekolah.*.nama_sekolah'             => ['required', 'string', 'max:150', 'distinct'],
            'sekolah.*.nomor_telepon'            => ['nullable', 'string', 'regex:/^(\+62|0)[0-9]{7,14}$/'],
            'sekolah.*.email'                    => ['nullable', 'email', 'max:100'],
            'sekolah.*.keterangan'               => ['nullable', 'string', 'max:500'],
        ], [
            'sekolah.size'               => 'Harus tepat ' . self::JUMLAH_SEKOLAH . ' sekolah.',
            'sekolah.*.nama_sekolah.distinct' => 'Nama sekolah tidak boleh duplikat dalam satu provinsi.',
            'sekolah.*.nomor_telepon.regex'   => 'Format nomor telepon tidak valid.',
        ]);

        $provinsi = $lombaProvinsi->provinsi;

        DB::transaction(function () use ($request, $lombaProvinsi, $provinsi) {
            // Update data kegiatan
            $lombaProvinsi->update([
                'nama_kegiatan'    => $request->nama_kegiatan ?? null,
                'tempat_kegiatan'  => $request->tempat_kegiatan ?? null,
                'tanggal_kegiatan' => $request->tanggal_kegiatan ?? null,
            ]);

            // Hapus semua sekolah lama, insert ulang
            $lombaProvinsi->sekolah()->delete();

            foreach ($request->sekolah as $i => $s) {
                $urutan = $i + 1;
                SekolahLomba::create([
                    'lomba_provinsi_id' => $lombaProvinsi->id,
                    'kode_sekolah'      => $provinsi->kode . $urutan,
                    'nama_sekolah'      => trim($s['nama_sekolah']),
                    'nomor_telepon'     => $s['nomor_telepon'] ?? null,
                    'email'             => $s['email'] ?? null,
                    'keterangan'        => $s['keterangan'] ?? null,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Data sekolah \"{$provinsi->nama}\" berhasil diperbarui.",
        ]);
    }

    // ────────────────────────────────────────────
    //  DESTROY
    // ────────────────────────────────────────────
    public function destroy(LombaProvinsi $lombaProvinsi): JsonResponse
    {
        $nama = $lombaProvinsi->provinsi->nama ?? '-';
        $lombaProvinsi->delete(); // cascade ke sekolah_lombas

        return response()->json([
            'success' => true,
            'message' => "Data lomba provinsi \"{$nama}\" berhasil dihapus.",
        ]);
    }

    // ────────────────────────────────────────────
    //  DOWNLOAD TEMPLATE EXCEL
    // ────────────────────────────────────────────
    public function downloadTemplate(): StreamedResponse
    {
        $namaKegiatan = Setting::get('nama_kegiatan', 'Lomba Cerdas Cermat MPR RI');
        $spreadsheet  = new Spreadsheet();

        // ═══ Sheet 1: Template isian ═══
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Sekolah');

        // Banner judul
        $sheet->setCellValue('A1', strtoupper($namaKegiatan) . ' — TEMPLATE IMPORT DATA SEKOLAH');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C447C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Info Provinsi (isi oleh pengguna) — baris 3
        $sheet->setCellValue('A3', 'KODE PROVINSI');
        $sheet->setCellValue('B3', '');  // diisi pengguna
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '0C447C']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6F1FB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('B3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0C447C']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B5D4F4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '378ADD']]],
        ]);
        $sheet->getStyle('B3')->getNumberFormat()->setFormatCode('@');
        $sheet->setCellValue('C3', '← Isi dengan 2 digit kode provinsi (contoh: 15 untuk Jambi)');
        $sheet->mergeCells('C3:E3');
        $sheet->getStyle('C3')->applyFromArray([
            'font'  => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '888780']],
            'fill'  => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F7F4']],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Tempat kegiatan — baris 4
        $sheet->setCellValue('A4', 'TEMPAT KEGIATAN');
        $sheet->setCellValue('B4', '');  // diisi pengguna
        $sheet->getStyle('A4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '0C447C']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6F1FB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('B4')->applyFromArray([
            'font'      => ['size' => 11, 'color' => ['rgb' => '0C447C']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B5D4F4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '378ADD']]],
        ]);
        $sheet->mergeCells('B4:E4');
        $sheet->setCellValue('C4', '');
        $sheet->getRowDimension(4)->setRowHeight(22);

        // Tanggal kegiatan — baris 5
        $sheet->setCellValue('A5', 'TANGGAL KEGIATAN');
        $sheet->setCellValue('B5', '');  // diisi pengguna, format yyyy-mm-dd
        $sheet->getStyle('A5')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '0C447C']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6F1FB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('B5')->applyFromArray([
            'font'      => ['size' => 11, 'color' => ['rgb' => '0C447C']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B5D4F4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '378ADD']]],
        ]);
        $sheet->getStyle('B5')->getNumberFormat()->setFormatCode('@');
        $sheet->setCellValue('C5', '← Format: yyyy-mm-dd (contoh: 2025-03-15)');
        $sheet->mergeCells('C5:E5');
        $sheet->getStyle('C5')->applyFromArray([
            'font'  => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '888780']],
            'fill'  => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F7F4']],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(22);

        // Catatan panduan (baris 7–9)
        $notes = [
            7 => ['warn' => false, 'text' => '• Isi kode provinsi 2 digit di sel B3. Sistem akan mendeteksi provinsi secara otomatis.'],
            8 => ['warn' => false, 'text' => '• Isi tepat 9 baris data sekolah mulai baris 12. Nama sekolah harus unik dalam satu provinsi.'],
            9 => ['warn' => true,  'text' => '⚠  Jangan ubah baris header (baris 11). Data sekolah HARUS tepat 9 baris (tidak boleh lebih atau kurang).'],
        ];
        foreach ($notes as $row => $note) {
            $sheet->setCellValue("A{$row}", $note['text']);
            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['size' => 10, 'color' => ['rgb' => $note['warn'] ? '854F0B' : '27500A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $note['warn'] ? 'FAEEDA' : 'EAF3DE']],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(18);
        }

        // Header kolom (baris 11)
        $headers = ['No.', 'Nama Sekolah', 'Nomor Telepon', 'Email', 'Keterangan'];
        $widths  = [7, 45, 22, 35, 40];
        foreach ($headers as $col => $h) {
            $cell = chr(65 + $col) . '11';
            $sheet->setCellValue($cell, $h);
            $sheet->getColumnDimension(chr(65 + $col))->setWidth($widths[$col]);
            $sheet->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '185FA5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '378ADD']]],
            ]);
        }
        $sheet->getRowDimension(11)->setRowHeight(22);

        // Baris data (12–20, tepat 9 sekolah)
        for ($i = 1; $i <= 9; $i++) {
            $row = $i + 11;
            $sheet->setCellValue("A{$row}", $i);
            $sheet->setCellValue("B{$row}", "Nama Sekolah {$i}");
            $sheet->setCellValue("C{$row}", '0811-xxxx-xxxx');
            $sheet->setCellValue("D{$row}", "sekolah{$i}@example.com");
            $sheet->setCellValue("E{$row}", '');

            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'font'    => ['color' => ['rgb' => '888780'], 'italic' => true],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $i % 2 === 0 ? 'FFFFFF' : 'F8F7F4']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D3D1C7']]],
            ]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->freezePane('A12');

        // ═══ Sheet 2: Referensi kode provinsi ═══
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi Kode');
        $refSheet->setCellValue('A1', 'REFERENSI KODE PROVINSI');
        $refSheet->mergeCells('A1:B1');
        $refSheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C447C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $refSheet->setCellValue('A2', 'Kode');
        $refSheet->setCellValue('B2', 'Nama Provinsi');
        $refSheet->getStyle('A2:B2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '185FA5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $provinsiRef = [
            ['11','Aceh'],['12','Sumatera Utara'],['13','Sumatera Barat'],['14','Riau'],
            ['15','Jambi'],['16','Sumatera Selatan'],['17','Bengkulu'],['18','Lampung'],
            ['19','Kepulauan Bangka Belitung'],['21','Kepulauan Riau'],['31','DKI Jakarta'],
            ['32','Jawa Barat'],['33','Jawa Tengah'],['34','DI Yogyakarta'],['35','Jawa Timur'],
            ['36','Banten'],['51','Bali'],['52','Nusa Tenggara Barat'],['53','Nusa Tenggara Timur'],
            ['61','Kalimantan Barat'],['62','Kalimantan Tengah'],['63','Kalimantan Selatan'],
            ['64','Kalimantan Timur'],['65','Kalimantan Utara'],['71','Sulawesi Utara'],
            ['72','Sulawesi Tengah'],['73','Sulawesi Selatan'],['74','Sulawesi Tenggara'],
            ['75','Gorontalo'],['76','Sulawesi Barat'],['81','Maluku'],['82','Maluku Utara'],
            ['91','Papua Barat'],['92','Papua'],['93','Papua Selatan'],['94','Papua Tengah'],
            ['95','Papua Pegunungan'],['96','Papua Barat Daya'],
        ];
        foreach ($provinsiRef as $i => $ref) {
            $r = $i + 3;
            $refSheet->setCellValue("A{$r}", $ref[0]);
            $refSheet->setCellValue("B{$r}", $ref[1]);
            $refSheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $refSheet->getStyle("A{$r}:B{$r}")->getFill()->setFillType(Fill::FILL_SOLID)
                     ->getStartColor()->setRGB($i % 2 === 0 ? 'F1EFE8' : 'FFFFFF');
            $refSheet->getStyle("A{$r}:B{$r}")->getBorders()->getAllBorders()
                     ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D3D1C7');
            $refSheet->getStyle("A{$r}")->getNumberFormat()->setFormatCode('@');
        }
        $refSheet->getColumnDimension('A')->setWidth(10);
        $refSheet->getColumnDimension('B')->setWidth(38);

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_sekolah.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ────────────────────────────────────────────
    //  IMPORT – PREVIEW
    // ────────────────────────────────────────────
    public function importPreview(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            $path        = $request->file('file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage(),
            ], 422);
        }

        if (empty($rows)) {
            return response()->json(['success' => false, 'message' => 'File kosong.'], 422);
        }

        // ── Cari kode provinsi di baris 3 (index 2), kolom B (index 1) ──
        $kodeProvinsiRaw = trim((string) ($rows[2][1] ?? ''));
        $kodeProvinsi    = str_pad(preg_replace('/\D/', '', $kodeProvinsiRaw), 2, '0', STR_PAD_LEFT);

        // ── Baca tempat kegiatan di baris 4 (index 3), kolom B (index 1) ──
        $tempatKegiatan = trim((string) ($rows[3][1] ?? '')) ?: null;

        // ── Baca tanggal kegiatan di baris 5 (index 4), kolom B (index 1) ──
        $tanggalKegiatanRaw = trim((string) ($rows[4][1] ?? ''));
        $tanggalKegiatan    = null;
        if ($tanggalKegiatanRaw !== '') {
            // Validasi format yyyy-mm-dd
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalKegiatanRaw)) {
                $tanggalKegiatan = $tanggalKegiatanRaw;
            }
        }

        $provinsiValid   = false;
        $provinsiData    = null;

        if (preg_match('/^\d{2}$/', $kodeProvinsi)) {
            $dbProvinsi = Provinsi::where('kode', $kodeProvinsi)->first();
            if ($dbProvinsi) {
                $provinsiValid = true;
                $provinsiData  = [
                    'id'   => $dbProvinsi->id,
                    'kode' => $dbProvinsi->kode,
                    'nama' => $dbProvinsi->nama,
                ];
            }
        }

        // ── Cek apakah sudah terdaftar di tahun aktif ──
        $tahunDefault    = Setting::get('tahun_default', (string) now()->year);
        $sudahTerdaftar  = false;
        $lombaExisting   = null;

        if ($provinsiValid) {
            $lombaExisting = LombaProvinsi::where('tahun', $tahunDefault)
                ->where('provinsi_id', $dbProvinsi->id)
                ->first();
            $sudahTerdaftar = $lombaExisting !== null;
        }

        // ── Cari baris header (baris 11, index 10, atau fallback ke baris 1) ──
        $headerRowIndex = null;
        $colNama = $colTelp = $colEmail = $colKet = false;

        foreach ([10, 0] as $candidateIdx) {
            if (!isset($rows[$candidateIdx])) continue;
            $candidate = array_map(fn($h) => mb_strtolower(trim((string) $h)), $rows[$candidateIdx]);
            // Cari kolom "nama" atau "nama sekolah"
            foreach ($candidate as $ci => $ch) {
                if (str_contains($ch, 'nama')) { $colNama = $ci; break; }
            }
            if ($colNama !== false) {
                $headerRowIndex = $candidateIdx;
                foreach ($candidate as $ci => $ch) {
                    if (str_contains($ch, 'telp') || str_contains($ch, 'telepon') || str_contains($ch, 'phone')) {
                        $colTelp = $ci;
                    }
                    if (str_contains($ch, 'email') || str_contains($ch, 'e-mail')) {
                        $colEmail = $ci;
                    }
                    if (str_contains($ch, 'ket') || str_contains($ch, 'catatan') || str_contains($ch, 'keterangan')) {
                        $colKet = $ci;
                    }
                }
                break;
            }
        }

        if ($headerRowIndex === null) {
            return response()->json([
                'success' => false,
                'message' => 'Header kolom tidak ditemukan. Pastikan ada kolom "nama sekolah" di baris 9 atau baris 1.',
            ], 422);
        }

        // ── Ambil baris data ──
        $dataRows   = array_slice($rows, $headerRowIndex + 1);
        $sekolahRows = [];
        $seenNama   = [];

        // Filter baris tidak kosong total, ambil tepat 9
        $nonEmpty = array_filter($dataRows, function ($row) use ($colNama) {
            return trim((string) ($row[$colNama] ?? '')) !== '';
        });
        $nonEmpty = array_values($nonEmpty);

        $errors = [];

        if (count($nonEmpty) !== self::JUMLAH_SEKOLAH) {
            $errors[] = 'Jumlah sekolah tidak valid: ditemukan ' . count($nonEmpty) . ' baris, harus tepat ' . self::JUMLAH_SEKOLAH . '.';
        }

        foreach ($nonEmpty as $i => $row) {
            $urutan     = $i + 1;
            $nama       = trim((string) ($row[$colNama] ?? ''));
            $telepon    = $colTelp  !== false ? trim((string) ($row[$colTelp]  ?? '')) : '';
            $email      = $colEmail !== false ? trim((string) ($row[$colEmail] ?? '')) : '';
            $keterangan = $colKet   !== false ? trim((string) ($row[$colKet]   ?? '')) : '';

            $rowErrors = [];
            $rowStatus = 'valid';

            if ($nama === '') {
                $rowErrors[] = 'Nama sekolah kosong';
                $rowStatus   = 'invalid';
            } elseif (mb_strlen($nama) > 150) {
                $rowErrors[] = 'Nama terlalu panjang (maks. 150 karakter)';
                $rowStatus   = 'invalid';
            } elseif (in_array(mb_strtolower($nama), $seenNama, true)) {
                $rowErrors[] = 'Nama sekolah duplikat dalam file ini';
                $rowStatus   = 'invalid';
            }

            if ($telepon !== '' && !preg_match('/^(\+62|0)[0-9]{7,14}$/', $telepon)) {
                $rowErrors[] = 'Format nomor telepon tidak valid';
                $rowStatus   = 'invalid';
            }

            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $rowErrors[] = 'Format email tidak valid';
                $rowStatus   = 'invalid';
            }

            if ($rowStatus === 'valid') {
                $seenNama[] = mb_strtolower($nama);
            }

            $kodeSekolah = $provinsiValid ? ($kodeProvinsi . $urutan) : ('??'. $urutan);

            $sekolahRows[] = [
                'urutan'      => $urutan,
                'kode_sekolah'=> $kodeSekolah,
                'nama_sekolah'=> $nama,
                'nomor_telepon' => $telepon,
                'email'       => $email,
                'keterangan'  => $keterangan,
                'status'      => $rowStatus,
                'errors'      => $rowErrors,
            ];
        }

        $validCount   = collect($sekolahRows)->where('status', 'valid')->count();
        $invalidCount = collect($sekolahRows)->where('status', 'invalid')->count();

        return response()->json([
            'success'          => true,
            'kode_provinsi'    => $kodeProvinsiRaw,
            'kode_normalized'  => $kodeProvinsi,
            'provinsi_valid'   => $provinsiValid,
            'provinsi'         => $provinsiData,
            'tahun'            => $tahunDefault,
            'tempat_kegiatan'  => $tempatKegiatan,
            'tanggal_kegiatan' => $tanggalKegiatan,
            'sudah_terdaftar'  => $sudahTerdaftar,
            'lomba_id'         => $lombaExisting?->id,
            'sekolah'          => $sekolahRows,
            'valid_count'      => $validCount,
            'invalid_count'    => $invalidCount,
            'total'            => count($sekolahRows),
            'file_errors'      => $errors,
        ]);
    }

    // ────────────────────────────────────────────
    //  IMPORT – SAVE
    // ────────────────────────────────────────────
    public function importSave(Request $request): JsonResponse
    {
        $request->validate([
            'provinsi_id'                      => ['required', 'exists:provinsis,id'],
            'action'                           => ['required', 'in:insert,replace'],
            'tempat_kegiatan'                  => ['nullable', 'string', 'max:250'],
            'tanggal_kegiatan'                 => ['nullable', 'date_format:Y-m-d'],
            'sekolah'                          => ['required', 'array', 'size:' . self::JUMLAH_SEKOLAH],
            'sekolah.*.kode_sekolah'           => ['required', 'string'],
            'sekolah.*.nama_sekolah'           => ['required', 'string', 'max:150'],
            'sekolah.*.nomor_telepon'          => ['nullable', 'string'],
            'sekolah.*.email'                  => ['nullable', 'string'],
            'sekolah.*.keterangan'             => ['nullable', 'string'],
        ]);

        $tahunDefault = Setting::get('tahun_default', (string) now()->year);
        $provinsi     = Provinsi::findOrFail($request->provinsi_id);
        $action       = $request->action; // 'insert' | 'replace'

        DB::transaction(function () use ($request, $tahunDefault, $provinsi, $action) {
            $lomba = LombaProvinsi::firstOrCreate(
                ['tahun' => $tahunDefault, 'provinsi_id' => $provinsi->id],
            );

            // Update tempat & tanggal kegiatan dari file import
            $lomba->update([
                'tempat_kegiatan'  => $request->tempat_kegiatan ?? null,
                'tanggal_kegiatan' => $request->tanggal_kegiatan ?? null,
            ]);

            if ($action === 'replace') {
                $lomba->sekolah()->delete();
            } elseif ($lomba->sekolah()->count() > 0) {
                // insert tapi sudah ada data → tolak
                throw new \RuntimeException('Provinsi ini sudah memiliki data sekolah. Gunakan opsi "Timpa" untuk mengganti.');
            }

            foreach ($request->sekolah as $s) {
                SekolahLomba::create([
                    'lomba_provinsi_id' => $lomba->id,
                    'kode_sekolah'      => $s['kode_sekolah'],
                    'nama_sekolah'      => $s['nama_sekolah'],
                    'nomor_telepon'     => $s['nomor_telepon'] ?? null,
                    'email'             => $s['email'] ?? null,
                    'keterangan'        => $s['keterangan'] ?? null,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Data {$provinsi->nama} berhasil " . ($action === 'replace' ? 'ditimpa' : 'disimpan') . " (" . self::JUMLAH_SEKOLAH . " sekolah).",
        ]);
    }
}
