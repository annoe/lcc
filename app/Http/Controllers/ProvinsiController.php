<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportProvinsiRequest;
use App\Http\Requests\StoreProvinsiRequest;
use App\Http\Requests\UpdateProvinsiRequest;
use App\Models\Provinsi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProvinsiController extends Controller
{
    // ────────────────────────────────────────────
    //  INDEX
    // ────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Provinsi::defaultOrder();

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        $provinsis = $query->get();

        return view('provinsi.index', [
            'provinsis' => $provinsis,
            'total'     => Provinsi::count(),
        ]);
    }

    // ────────────────────────────────────────────
    //  STORE
    // ────────────────────────────────────────────
    public function store(StoreProvinsiRequest $request): JsonResponse
    {
        $provinsi = Provinsi::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Provinsi \"{$provinsi->nama}\" berhasil ditambahkan.",
            'data'    => $provinsi,
        ]);
    }

    // ────────────────────────────────────────────
    //  UPDATE
    // ────────────────────────────────────────────
    public function update(UpdateProvinsiRequest $request, Provinsi $provinsi): JsonResponse
    {
        $provinsi->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Data \"{$provinsi->nama}\" berhasil diperbarui.",
            'data'    => $provinsi->fresh(),
        ]);
    }

    // ────────────────────────────────────────────
    //  DESTROY
    // ────────────────────────────────────────────
    public function destroy(Provinsi $provinsi): JsonResponse
    {
        $nama = $provinsi->nama;
        $provinsi->delete();

        return response()->json([
            'success' => true,
            'message' => "Provinsi \"{$nama}\" berhasil dihapus.",
        ]);
    }

    // ────────────────────────────────────────────
    //  EXPORT EXCEL
    // ────────────────────────────────────────────
    public function export(): StreamedResponse
    {
        $provinsis = Provinsi::defaultOrder()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Provinsi');

        // Banner judul
        $sheet->setCellValue('A1', 'MASTER DATA PROVINSI');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '185FA5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Sub-banner
        $sheet->setCellValue('A2', 'Sekretariat Jenderal MPR RI · Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB');
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '0C447C']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6F1FB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Header kolom (baris 4)
        $headers = ['No.', 'ID (ULID)', 'Kode', 'Nama Provinsi'];
        $widths  = [8, 30, 10, 38];
        foreach ($headers as $col => $h) {
            $cell = chr(65 + $col) . '4';
            $sheet->setCellValue($cell, $h);
            $sheet->getColumnDimension(chr(65 + $col))->setWidth($widths[$col]);
        }
        $sheet->getStyle('A4:D4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C447C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '378ADD']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        // Data rows (mulai baris 5)
        foreach ($provinsis as $i => $p) {
            $row = $i + 5;
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $p->id);
            $sheet->setCellValue("C{$row}", $p->kode);
            $sheet->setCellValue("D{$row}", $p->nama);

            // Alternating row
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$row}:D{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('EEF5FC');
            }

            // Border
            $sheet->getStyle("A{$row}:D{$row}")->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setRGB('D3D1C7');

            // Center No. dan Kode
            $sheet->getStyle("A{$row}:C{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // ULID font kecil
            $sheet->getStyle("B{$row}")->getFont()->setSize(9);

            // Format kode sebagai teks
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('@');
        }

        // Footer total
        $lastRow = $provinsis->count() + 5;
        $sheet->setCellValue("A{$lastRow}", 'Total');
        $sheet->setCellValue("D{$lastRow}", $provinsis->count() . ' provinsi');
        $sheet->mergeCells("A{$lastRow}:C{$lastRow}");
        $sheet->getStyle("A{$lastRow}:D{$lastRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0C447C']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B5D4F4']],
        ]);

        // Freeze header
        $sheet->freezePane('A5');

        // Auto-filter pada header
        $sheet->setAutoFilter('A4:D4');

        // Print settings
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToPage(true);
        $sheet->getHeaderFooter()
            ->setOddHeader('&C&B Master Data Provinsi — Setjen MPR RI')
            ->setOddFooter('&L&D&R Halaman &P dari &N');

        $writer   = new Xlsx($spreadsheet);
        $filename = 'master_provinsi_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ────────────────────────────────────────────
    //  IMPORT – PREVIEW
    // ────────────────────────────────────────────
    public function importPreview(ImportProvinsiRequest $request): JsonResponse
    {
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

        // ── Deteksi baris header ──────────────────────────────────────
        // Prioritas: baris ke-9 (index 8) sesuai template, fallback ke baris 1 (index 0).
        $headerRowIndex = null;
        $header         = [];
        $colKode        = false;
        $colNama        = false;

        foreach ([8, 0] as $candidateIdx) {
            if (!isset($rows[$candidateIdx])) {
                continue;
            }
            $candidate = array_map(fn($h) => mb_strtolower(trim((string) $h)), $rows[$candidateIdx]);
            $ck        = array_search('kode', $candidate);
            $cn        = array_search('nama', $candidate);
            if ($ck !== false && $cn !== false) {
                $headerRowIndex = $candidateIdx;
                $header         = $candidate;
                $colKode        = $ck;
                $colNama        = $cn;
                break;
            }
        }

        if ($headerRowIndex === null) {
            return response()->json([
                'success' => false,
                'message' => 'Header kolom tidak ditemukan. Pastikan ada kolom "kode" dan "nama" (dicari di baris 9 dan baris 1).',
            ], 422);
        }

        // ── Ambil semua data DB untuk pengecekan konflik ──────────────
        // Keyed by kode  : ['11' => Provinsi, ...]
        // Keyed by nama  : ['aceh' => Provinsi, ...]  (lowercase)
        $allProvinsi    = Provinsi::all();
        $dbByKode       = $allProvinsi->keyBy('kode');
        $dbByNama       = $allProvinsi->keyBy(fn($p) => mb_strtolower(trim($p->nama)));

        $seenKodes      = [];   // kode yang sudah muncul di baris-baris sebelumnya dalam file ini
        $result         = [];
        $dataRows       = array_slice($rows, $headerRowIndex + 1);

        foreach ($dataRows as $rowNo => $row) {
            // Nomor baris Excel = headerRowIndex (1-based) + 1 (header itu sendiri) + rowNo (0-based) + 1
            $excelRow = $headerRowIndex + 2 + $rowNo;

            $kode = trim((string) ($row[$colKode] ?? ''));
            $nama = trim((string) ($row[$colNama] ?? ''));
            $id   = Str::ulid()->toString();

            // Lewati baris kosong total
            if ($kode === '' && $nama === '') {
                continue;
            }

            // Normalisasi kode: ambil digit saja, pad ke 2 karakter
            $kodeNum = preg_replace('/\D/', '', $kode);
            if ($kodeNum !== '') {
                $kode = str_pad($kodeNum, 2, '0', STR_PAD_LEFT);
            }

            $errors       = [];
            $status       = 'valid';
            $action       = 'insert';   // insert | update
            $conflictType = null;       // kode_same_nama_diff | nama_same_kode_diff
            $existing     = null;       // data lama di DB yang berkonflik

            // ── Validasi format ───────────────────────────────────────
            if ($kode === '' || !preg_match('/^\d{2}$/', $kode)) {
                $errors[] = 'Kode tidak valid (harus 2 digit angka)';
                $status   = 'invalid';
            } elseif (in_array($kode, $seenKodes, true)) {
                $errors[] = "Kode {$kode} duplikat dalam file ini";
                $status   = 'invalid';
            }

            if ($nama === '') {
                $errors[] = 'Nama provinsi kosong';
                $status   = 'invalid';
            } elseif (mb_strlen($nama) > 100) {
                $errors[] = 'Nama terlalu panjang (maks. 100 karakter)';
                $status   = 'invalid';
            }

            // ── Pengecekan konflik dengan DB (hanya jika format valid) ─
            if ($status === 'valid') {
                $dbMatchKode = $dbByKode->get($kode);
                $dbMatchNama = $dbByNama->get(mb_strtolower(trim($nama)));

                if ($dbMatchKode !== null) {
                    // Kode sudah ada di DB
                    if (mb_strtolower(trim($dbMatchKode->nama)) === mb_strtolower(trim($nama))) {
                        // Kode DAN nama identik → duplikat murni, tidak perlu diimport
                        $errors[] = "Data identik sudah ada di database (kode {$kode}, {$nama})";
                        $status   = 'invalid';
                    } else {
                        // Kode sama, nama BERBEDA → konflik: tawarkan update
                        $status       = 'conflict';
                        $action       = 'update';
                        $conflictType = 'kode_same_nama_diff';
                        $existing     = [
                            'id'   => $dbMatchKode->id,
                            'kode' => $dbMatchKode->kode,
                            'nama' => $dbMatchKode->nama,
                        ];
                    }
                } elseif ($dbMatchNama !== null) {
                    // Nama sama (case-insensitive), kode BERBEDA → konflik: warning, boleh insert
                    $status       = 'conflict';
                    $action       = 'insert';
                    $conflictType = 'nama_same_kode_diff';
                    $existing     = [
                        'id'   => $dbMatchNama->id,
                        'kode' => $dbMatchNama->kode,
                        'nama' => $dbMatchNama->nama,
                    ];
                }
            }

            if (in_array($status, ['valid', 'conflict'])) {
                $seenKodes[] = $kode;
            }

            $result[] = [
                'row'          => $excelRow,
                'id'           => $id,
                'kode'         => $kode,
                'nama'         => $nama,
                'status'       => $status,
                'action'       => $action,
                'conflict_type'=> $conflictType,
                'existing'     => $existing,
                'errors'       => $errors,
            ];
        }

        $validCount    = collect($result)->where('status', 'valid')->count();
        $conflictCount = collect($result)->where('status', 'conflict')->count();
        $invalidCount  = collect($result)->where('status', 'invalid')->count();

        return response()->json([
            'success'        => true,
            'rows'           => $result,
            'valid_count'    => $validCount,
            'conflict_count' => $conflictCount,
            'invalid_count'  => $invalidCount,
            'total'          => count($result),
            'header_row'     => $headerRowIndex + 1,  // 1-based, untuk info ke user
        ]);
    }

    // ────────────────────────────────────────────
    //  IMPORT – SAVE
    // ────────────────────────────────────────────
    public function importSave(Request $request): JsonResponse
    {
        $request->validate([
            'rows'          => ['required', 'array', 'min:1'],
            'rows.*.id'     => ['required', 'string'],
            'rows.*.kode'   => ['required', 'regex:/^\d{2}$/'],
            'rows.*.nama'   => ['required', 'string', 'max:100'],
            'rows.*.action' => ['required', 'in:insert,update'],
        ]);

        $rows    = $request->input('rows');
        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $skippedDetails = [];

        foreach ($rows as $row) {
            $kode   = $row['kode'];
            $nama   = $row['nama'];
            $action = $row['action'];

            if ($action === 'update') {
                // Update nama berdasarkan kode (race-condition safe)
                $affected = Provinsi::where('kode', $kode)->update(['nama' => $nama]);
                if ($affected > 0) {
                    $updated++;
                } else {
                    // Kode tidak ada lagi di DB (dihapus sementara proses) → insert sebagai fallback
                    Provinsi::create(['id' => $row['id'], 'kode' => $kode, 'nama' => $nama]);
                    $inserted++;
                }
            } else {
                // Insert — cek ulang duplikat (race condition safe)
                if (Provinsi::where('kode', $kode)->exists()) {
                    $skipped++;
                    $skippedDetails[] = "Kode {$kode} ({$nama}) sudah ada saat disimpan.";
                    continue;
                }
                Provinsi::create(['id' => $row['id'], 'kode' => $kode, 'nama' => $nama]);
                $inserted++;
            }
        }

        $parts = [];
        if ($inserted > 0) $parts[] = "{$inserted} ditambahkan";
        if ($updated  > 0) $parts[] = "{$updated} diperbarui";
        if ($skipped  > 0) $parts[] = "{$skipped} dilewati";

        return response()->json([
            'success'  => true,
            'message'  => implode(', ', $parts) . '.',
            'inserted' => $inserted,
            'updated'  => $updated,
            'skipped'  => $skipped,
            'details'  => $skippedDetails,
        ]);
    }

    // ────────────────────────────────────────────
    //  DOWNLOAD TEMPLATE EXCEL
    // ────────────────────────────────────────────
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        // ════════════════
        //  Sheet 1 — Template isian
        // ════════════════
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Provinsi');

        // Banner
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT MASTER DATA PROVINSI');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '185FA5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Sub-banner
        $sheet->setCellValue('A2', 'Sekretariat Jenderal MPR RI · Isi data mulai baris 10 ke bawah');
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '0C447C']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6F1FB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Catatan panduan (baris 4–8)
        $notes = [
            4 => ['warn' => false, 'text' => 'Kolom id    — Kosongkan. ULID akan dibuat otomatis oleh sistem.'],
            5 => ['warn' => false, 'text' => 'Kolom kode — WAJIB. 2 digit angka (01–99). Contoh: 11, 32, 73.'],
            6 => ['warn' => false, 'text' => 'Kolom nama — WAJIB. Nama resmi provinsi, maks. 100 karakter.'],
            7 => ['warn' => true,  'text' => 'Kode yang sudah ada di database akan dilewati saat import.'],
            8 => ['warn' => true,  'text' => 'Jangan ubah nama kolom header (baris 9). Kolom id boleh kosong.'],
        ];
        foreach ($notes as $row => $note) {
            $sheet->setCellValue("A{$row}", ($note['warn'] ? '⚠  ' : '•  ') . $note['text']);
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['size' => 10, 'color' => ['rgb' => $note['warn'] ? '854F0B' : '27500A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $note['warn'] ? 'FAEEDA' : 'EAF3DE']],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(18);
        }

        // Header kolom (baris 9)
        foreach (['id', 'kode', 'nama'] as $col => $h) {
            $cell = chr(65 + $col) . '9';
            $sheet->setCellValue($cell, $h);
            $sheet->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C447C']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '378ADD']]],
            ]);
        }
        $sheet->getRowDimension(9)->setRowHeight(24);

        // Contoh data (placeholder, baris 10–14)
        $contoh = [['', '11', 'Aceh'], ['', '12', 'Sumatera Utara'], ['', '13', 'Sumatera Barat'], ['', '14', 'Riau'], ['', '15', 'Jambi']];
        foreach ($contoh as $i => $rowData) {
            $rowNum = 10 + $i;
            foreach ($rowData as $col => $val) {
                $cell = chr(65 + $col) . $rowNum;
                $sheet->setCellValue($cell, $val);
                $sheet->getStyle($cell)->applyFromArray([
                    'font'    => ['color' => ['rgb' => '888780'], 'italic' => true],
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F7F4']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D3D1C7']]],
                ]);
            }
            $sheet->getStyle("B{$rowNum}")->getNumberFormat()->setFormatCode('@');
        }

        $sheet->getColumnDimension('A')->setWidth(34);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(42);
        $sheet->freezePane('A10');

        // ════════════════
        //  Sheet 2 — Referensi kode
        // ════════════════
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi Kode');

        $refSheet->setCellValue('A1', 'REFERENSI KODE PROVINSI (BPS / KEMENDAGRI)');
        $refSheet->mergeCells('A1:B1');
        $refSheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C447C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $refSheet->getRowDimension(1)->setRowHeight(28);
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

        // ════════════════
        //  Sheet 3 — Petunjuk
        // ════════════════
        $guideSheet = $spreadsheet->createSheet();
        $guideSheet->setTitle('Petunjuk');
        $guideSheet->setCellValue('A1', 'PETUNJUK PENGGUNAAN TEMPLATE IMPORT');
        $guideSheet->mergeCells('A1:B1');
        $guideSheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F6E56']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $guideSheet->setCellValue('A2', 'Topik');
        $guideSheet->setCellValue('B2', 'Keterangan');
        $guideSheet->getStyle('A2:B2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F6E56']],
        ]);

        $petunjuk = [
            ['Format file',    'Gunakan .xlsx atau .xls. File .csv juga diterima tetapi rawan masalah encoding.'],
            ['Baris header',   'Jangan hapus atau ubah baris ke-9 (id, kode, nama). Sistem membaca header untuk mendeteksi kolom.'],
            ['Kolom id',       'Opsional. Jika dikosongkan, ULID akan dibuat otomatis. Jika diisi, harus berformat ULID valid.'],
            ['Kolom kode',     'Wajib. 2 digit angka (01–99). Angka 1–9 harus ditulis 01–09 (tambah nol di depan).'],
            ['Kolom nama',     'Wajib. Nama resmi provinsi. Huruf kapital/kecil diterima. Maks. 100 karakter.'],
            ['Duplikat',       'Kode yang sudah ada di database akan dilewati. Status ditampilkan di preview sebelum data disimpan.'],
            ['Ukuran file',    'Maksimal 5 MB. Untuk data besar, bagi menjadi beberapa file.'],
            ['Preview',        'Setelah upload, sistem menampilkan review per baris. Centang baris yang ingin disimpan, lalu klik Simpan.'],
            ['Sheet referensi','Sheet "Referensi Kode" berisi 38 kode provinsi resmi Indonesia sebagai acuan.'],
        ];

        $guideSheet->getColumnDimension('A')->setWidth(20);
        $guideSheet->getColumnDimension('B')->setWidth(72);

        foreach ($petunjuk as $i => $p) {
            $r = $i + 3;
            $guideSheet->setCellValue("A{$r}", $p[0]);
            $guideSheet->setCellValue("B{$r}", $p[1]);
            $guideSheet->getStyle("A{$r}")->getFont()->setBold(true);
            $guideSheet->getStyle("B{$r}")->getAlignment()->setWrapText(true);
            $guideSheet->getStyle("A{$r}:B{$r}")->getFill()->setFillType(Fill::FILL_SOLID)
                       ->getStartColor()->setRGB($i % 2 === 0 ? 'E1F5EE' : 'FFFFFF');
            $guideSheet->getStyle("A{$r}:B{$r}")->getBorders()->getAllBorders()
                       ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('9FE1CB');
            $guideSheet->getRowDimension($r)->setRowHeight(28);
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_provinsi.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
