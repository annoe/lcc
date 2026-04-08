<?php

use App\Http\Controllers\JenisBabakController;
use App\Http\Controllers\LombaProvinsiController;
use App\Http\Controllers\ProvinsiController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('lomba-provinsi.index'));

// ── Pengaturan ────────────────────────────────────────────────────────────
Route::prefix('pengaturan')->name('settings.')->group(function () {
    Route::get('/',  [SettingController::class, 'index'])->name('index');
    Route::post('/', [SettingController::class, 'update'])->name('update');
});

// ── Jenis Babak ────────────────────────────────────────────────────────────
Route::prefix('jenis-babak')->name('jenis-babak.')->group(function () {
    Route::get('/',              [JenisBabakController::class, 'index'])->name('index');
    Route::post('/',             [JenisBabakController::class, 'store'])->name('store');
    Route::put('/{jenisBabak}',  [JenisBabakController::class, 'update'])->name('update');
    Route::delete('/{jenisBabak}', [JenisBabakController::class, 'destroy'])->name('destroy');
});

// ── Master Data Provinsi ──────────────────────────────────────────────────
Route::prefix('master/provinsi')->name('provinsi.')->group(function () {
    Route::get('/',              [ProvinsiController::class, 'index'])->name('index');
    Route::post('/',             [ProvinsiController::class, 'store'])->name('store');
    Route::put('/{provinsi}',    [ProvinsiController::class, 'update'])->name('update');
    Route::delete('/{provinsi}', [ProvinsiController::class, 'destroy'])->name('destroy');

    Route::get('/export',          [ProvinsiController::class, 'export'])->name('export');
    Route::get('/import/template', [ProvinsiController::class, 'downloadTemplate'])->name('import.template');
    Route::post('/import/preview', [ProvinsiController::class, 'importPreview'])->name('import.preview');
    Route::post('/import/save',    [ProvinsiController::class, 'importSave'])->name('import.save');
});

// ── Master Lomba Provinsi ─────────────────────────────────────────────────
Route::prefix('master/lomba-provinsi')->name('lomba-provinsi.')->group(function () {
    Route::get('/',                   [LombaProvinsiController::class, 'index'])->name('index');
    Route::post('/',                  [LombaProvinsiController::class, 'store'])->name('store');
    Route::get('/{lombaProvinsi}',    [LombaProvinsiController::class, 'show'])->name('show');
    Route::put('/{lombaProvinsi}',    [LombaProvinsiController::class, 'update'])->name('update');
    Route::delete('/{lombaProvinsi}', [LombaProvinsiController::class, 'destroy'])->name('destroy');

    Route::get('/import/template',    [LombaProvinsiController::class, 'downloadTemplate'])->name('import.template');
    Route::post('/import/preview',    [LombaProvinsiController::class, 'importPreview'])->name('import.preview');
    Route::post('/import/save',       [LombaProvinsiController::class, 'importSave'])->name('import.save');
});
