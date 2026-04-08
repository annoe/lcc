{{-- resources/views/lomba-provinsi/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Master Lomba Provinsi — LCC MPR RI</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --blue-50:#E6F1FB;--blue-100:#B5D4F4;--blue-200:#85B7EB;--blue-400:#378ADD;--blue-600:#185FA5;--blue-800:#0C447C;--blue-900:#042C53;
    --teal-50:#E1F5EE;--teal-100:#9FE1CB;--teal-600:#0F6E56;--teal-800:#085041;
    --green-50:#EAF3DE;--green-100:#C0DD97;--green-600:#3B6D11;--green-800:#27500A;
    --red-50:#FCEBEB;--red-100:#F7C1C1;--red-600:#A32D2D;--red-800:#791F1F;
    --amber-50:#FAEEDA;--amber-100:#FAC775;--amber-600:#854F0B;
    --gold-400:#C9A84C;--gold-500:#B5923C;
    --gray-50:#F1EFE8;--gray-100:#D3D1C7;--gray-200:#B4B2A9;--gray-400:#888780;--gray-600:#5F5E5A;--gray-800:#444441;--gray-900:#2C2C2A;
    --bg:#F8F7F4;--surface:#FFFFFF;
    --border:rgba(0,0,0,.10);--border-md:rgba(0,0,0,.18);
    --text:#2C2C2A;--text-2:#5F5E5A;--text-3:#888780;
    --radius:8px;--radius-lg:12px;
    --shadow:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
    --shadow-md:0 4px 12px rgba(0,0,0,.10);
}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);font-size:14px;line-height:1.6;}

/* ── Top Nav ─────────────────────────────────── */
.topnav{background:var(--blue-900);border-bottom:3px solid var(--gold-400);padding:0 1.5rem;display:flex;align-items:center;gap:0;height:52px;}
.topnav-brand{font-size:14px;font-weight:700;color:var(--gold-400);letter-spacing:.5px;white-space:nowrap;padding-right:1.5rem;border-right:1px solid rgba(255,255,255,.15);display:flex;align-items:center;gap:8px;}
.topnav-brand span{font-size:10px;font-weight:500;color:rgba(255,255,255,.55);letter-spacing:.8px;text-transform:uppercase;display:block;line-height:1.2;}
.topnav-menu{display:flex;align-items:center;gap:0;margin-left:1.5rem;height:100%;}
.topnav-item{display:flex;align-items:center;height:100%;padding:0 14px;font-size:13px;font-weight:500;color:rgba(255,255,255,.65);text-decoration:none;border-bottom:3px solid transparent;margin-bottom:-3px;transition:all .15s;white-space:nowrap;}
.topnav-item:hover{color:#fff;background:rgba(255,255,255,.06);}
.topnav-item.active{color:#fff;border-bottom-color:var(--gold-400);}

/* ── Layout ──────────────────────────────────── */
.page{max-width:1240px;margin:0 auto;padding:2rem 1.5rem;}
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:1.75rem;flex-wrap:wrap;}
.page-title{font-size:20px;font-weight:700;color:var(--blue-800);}
.page-sub{font-size:12px;color:var(--text-3);margin-top:2px;}
.page-actions{display:flex;gap:8px;flex-wrap:wrap;}

/* ── Buttons ─────────────────────────────────── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--radius);border:1px solid var(--border-md);background:var(--surface);color:var(--text);font-size:13px;font-weight:500;cursor:pointer;transition:all .15s;white-space:nowrap;text-decoration:none;}
.btn:hover{background:var(--gray-50);border-color:var(--gray-200);}
.btn:active{transform:scale(.98);}
.btn-primary{background:var(--blue-600);color:#fff;border-color:var(--blue-600);}
.btn-primary:hover{background:var(--blue-800);border-color:var(--blue-800);}
.btn-success{background:var(--green-600);color:#fff;border-color:var(--green-600);}
.btn-success:hover{background:var(--green-800);}
.btn-danger{color:var(--red-600);border-color:var(--red-100);}
.btn-danger:hover{background:var(--red-50);}
.btn-sm{padding:5px 11px;font-size:12px;}
.btn:disabled{opacity:.5;cursor:not-allowed;transform:none;}

/* ── Stats ───────────────────────────────────── */
.stats-row{display:flex;gap:12px;margin-bottom:1.25rem;flex-wrap:wrap;}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:14px 20px;min-width:130px;box-shadow:var(--shadow);}
.stat-label{font-size:11px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.6px;}
.stat-value{font-size:26px;font-weight:700;color:var(--blue-800);margin-top:2px;}

/* ── Toolbar ─────────────────────────────────── */
.toolbar{display:flex;gap:8px;margin-bottom:1rem;flex-wrap:wrap;align-items:center;}
.search-wrap{position:relative;flex:1;min-width:200px;}
.search-wrap svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-3);pointer-events:none;}
.search-wrap input{width:100%;padding:8px 12px 8px 34px;border:1px solid var(--border-md);border-radius:var(--radius);background:var(--surface);color:var(--text);font-size:13px;}
.search-wrap input:focus{outline:none;border-color:var(--blue-400);box-shadow:0 0 0 3px rgba(55,138,221,.12);}
select.form-ctrl{padding:8px 10px;border:1px solid var(--border-md);border-radius:var(--radius);background:var(--surface);color:var(--text);font-size:13px;cursor:pointer;}
select.form-ctrl:focus{outline:none;border-color:var(--blue-400);}

/* ── Table ───────────────────────────────────── */
.table-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow);}
.table-scroll{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th{background:var(--gray-50);text-align:left;font-size:11px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.6px;padding:10px 14px;border-bottom:1px solid var(--border);white-space:nowrap;}
td{padding:11px 14px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover td{background:var(--blue-50);}
.badge-kode{display:inline-block;background:var(--blue-50);color:var(--blue-800);border-radius:5px;padding:2px 9px;font-family:monospace;font-size:12px;font-weight:700;letter-spacing:.5px;}
.badge-tahun{display:inline-block;background:var(--amber-50);color:var(--amber-600);border-radius:5px;padding:2px 9px;font-size:12px;font-weight:700;}
.badge-count{display:inline-flex;align-items:center;gap:4px;background:var(--teal-50);color:var(--teal-600);border-radius:99px;padding:2px 9px;font-size:11px;font-weight:600;}
.action-cell{display:flex;gap:4px;justify-content:flex-end;}
.table-footer{padding:10px 14px;background:var(--gray-50);border-top:1px solid var(--border);font-size:12px;color:var(--text-3);display:flex;justify-content:space-between;}

/* ── Alert ───────────────────────────────────── */
.alert{display:none;padding:11px 16px;border-radius:var(--radius);font-size:13px;margin-bottom:1rem;border:1px solid;}
.alert-success{background:var(--green-50);color:var(--green-600);border-color:var(--green-100);}
.alert-error{background:var(--red-50);color:var(--red-600);border-color:var(--red-100);}

/* ── Modal Base ──────────────────────────────── */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;align-items:center;justify-content:center;padding:1rem;}
.modal-backdrop.open{display:flex;}
.modal{background:var(--surface);border-radius:var(--radius-lg);border:1px solid var(--border-md);width:100%;box-shadow:0 8px 32px rgba(0,0,0,.20);animation:modal-in .18s ease;overflow:hidden;}
@keyframes modal-in{from{opacity:0;transform:translateY(-10px) scale(.98)}to{opacity:1;transform:none}}
.modal-header{padding:1.1rem 1.5rem 1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-size:16px;font-weight:600;color:var(--blue-800);}
.modal-close{background:none;border:none;cursor:pointer;color:var(--text-3);padding:2px;line-height:1;font-size:20px;}
.modal-close:hover{color:var(--text);}
.modal-body{padding:1.25rem 1.5rem;}
.modal-footer{padding:.75rem 1.5rem 1.25rem;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid var(--border);}

/* ── Form ────────────────────────────────────── */
.form-group{margin-bottom:1rem;}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:4px;}
.form-ctrl-full{width:100%;padding:8px 11px;border:1px solid var(--border-md);border-radius:var(--radius);background:var(--surface);color:var(--text);font-size:13px;}
.form-ctrl-full:focus{outline:none;border-color:var(--blue-400);box-shadow:0 0 0 3px rgba(55,138,221,.12);}
.form-ctrl-full:disabled{background:var(--gray-50);color:var(--text-3);cursor:not-allowed;}
.form-ctrl-full.is-error{border-color:var(--red-600);}
.form-hint{font-size:11px;color:var(--text-3);margin-top:3px;}
.field-error{font-size:11px;color:var(--red-600);margin-top:3px;display:none;}

/* ── Sekolah grid in modal ───────────────────── */
.sekolah-section{background:var(--gray-50);border:1px solid var(--border);border-radius:var(--radius);padding:1rem;margin-top:.75rem;}
.sekolah-section-title{font-size:12px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.75rem;}
.sekolah-row{display:grid;grid-template-columns:40px 1fr 160px 200px;gap:8px;align-items:start;padding:8px 0;border-bottom:1px solid var(--border);}
.sekolah-row:last-child{border-bottom:none;padding-bottom:0;}
.sekolah-num{font-size:11px;font-weight:700;color:var(--text-3);text-align:center;padding-top:9px;}
.sekolah-row-inner{display:contents;}
.kode-badge{font-family:monospace;font-size:10px;color:var(--blue-600);background:var(--blue-50);border-radius:4px;padding:1px 5px;display:inline-block;margin-bottom:3px;}
.sekolah-sub-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px;}

/* ── Import Modal ────────────────────────────── */
.step-bar{display:flex;align-items:center;padding:1rem 1.5rem;background:var(--gray-50);border-bottom:1px solid var(--border);}
.step-item{display:flex;align-items:center;gap:6px;flex:1;}
.step-item:last-child{flex:0;}
.step-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;border:2px solid var(--gray-100);background:var(--surface);color:var(--text-3);transition:all .3s;flex-shrink:0;}
.step-dot.active{border-color:var(--blue-400);background:var(--blue-50);color:var(--blue-600);}
.step-dot.done{border-color:var(--green-600);background:var(--green-600);color:#fff;}
.step-dot.done span{display:none;}
.step-dot.done::after{content:'✓';font-size:13px;}
.step-label{font-size:11px;font-weight:500;color:var(--text-3);transition:color .3s;white-space:nowrap;}
.step-label.active{color:var(--blue-600);}
.step-label.done{color:var(--green-600);}
.step-line{flex:1;height:1px;background:var(--gray-100);margin:0 6px;min-width:8px;transition:background .3s;}
.step-line.done{background:var(--green-600);}
.step-panel{display:none;}
.step-panel.active{display:block;animation:fade-in .2s ease;}
@keyframes fade-in{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:none}}

/* Drop zone */
.drop-zone{border:2px dashed var(--border-md);border-radius:var(--radius-lg);padding:2.5rem 1.5rem;text-align:center;cursor:pointer;transition:all .15s;}
.drop-zone:hover,.drop-zone.drag-over{border-color:var(--blue-400);background:var(--blue-50);}
.drop-zone-icon{font-size:36px;margin-bottom:.75rem;}
.drop-zone-title{font-size:14px;font-weight:600;margin-bottom:4px;}
.drop-zone-sub{font-size:12px;color:var(--text-3);}

/* Progress */
.progress-wrap{margin:1rem 0;}
.progress-label{display:flex;justify-content:space-between;font-size:12px;color:var(--text-2);margin-bottom:6px;}
.progress-track{height:6px;background:var(--gray-100);border-radius:99px;overflow:hidden;}
.progress-bar{height:100%;background:var(--blue-400);border-radius:99px;width:0;transition:width .2s;}

/* Process steps */
.proc-steps{display:flex;flex-direction:column;gap:10px;margin:1rem 0;}
.proc-step{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:var(--radius);border:1px solid var(--border);background:var(--surface);}
.proc-step-icon{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.proc-step-icon.waiting{background:var(--gray-50);color:var(--text-3);}
.proc-step-icon.running{background:var(--blue-50);}
.proc-step-icon.done{background:var(--green-50);}
.proc-step-icon.error{background:var(--red-50);}
.proc-step-text{flex:1;}
.proc-step-title{font-size:13px;font-weight:500;}
.proc-step-sub{font-size:11px;color:var(--text-3);margin-top:1px;}
.spinner{width:16px;height:16px;border:2px solid var(--blue-100);border-top-color:var(--blue-600);border-radius:50%;animation:spin .7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg)}}

/* Provinsi card in import */
.prov-card{border-radius:var(--radius);padding:10px 14px;font-size:13px;display:flex;align-items:center;gap:10px;margin-bottom:1rem;}
.prov-card.valid{background:var(--green-50);border:1px solid var(--green-100);}
.prov-card.invalid{background:var(--red-50);border:1px solid var(--red-100);}
.prov-card.warning{background:var(--amber-50);border:1px solid var(--amber-100);}
.prov-icon{font-size:22px;}
.prov-card-title{font-weight:600;}
.prov-card-sub{font-size:11px;color:var(--text-2);}

/* Review table */
.review-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;flex-wrap:wrap;gap:8px;}
.badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;}
.badge-valid{background:var(--green-50);color:var(--green-600);}
.badge-invalid{background:var(--red-50);color:var(--red-600);}
.review-table-wrap{border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;max-height:320px;overflow-y:auto;}
.review-table{width:100%;border-collapse:collapse;font-size:12px;}
.review-table th{background:var(--gray-50);padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--border);position:sticky;top:0;z-index:1;}
.review-table td{padding:8px 10px;border-bottom:1px solid var(--border);vertical-align:top;}
.review-table tbody tr:last-child td{border-bottom:none;}
.review-table tbody tr.row-valid:hover td{background:var(--green-50);}
.review-table tbody tr.row-invalid td{opacity:.65;}
.review-table tbody tr.row-invalid:hover td{background:var(--red-50);}
.error-tags{display:flex;flex-wrap:wrap;gap:3px;margin-top:3px;}
.error-tag{background:var(--red-100);color:var(--red-800);border-radius:4px;padding:1px 6px;font-size:10px;}
.check-icon{color:var(--green-600);font-weight:700;}
.x-icon{color:var(--red-600);font-weight:700;}

/* Success panel */
.success-panel{text-align:center;padding:1.5rem 1rem;}
.success-icon{font-size:52px;margin-bottom:.75rem;}
.success-title{font-size:18px;font-weight:600;color:var(--green-600);margin-bottom:.5rem;}
.success-sub{font-size:13px;color:var(--text-2);}
.success-detail{display:flex;gap:12px;justify-content:center;margin-top:1rem;flex-wrap:wrap;}
.success-stat{background:var(--gray-50);border-radius:var(--radius);padding:10px 20px;text-align:center;border:1px solid var(--border);}
.success-stat-num{font-size:22px;font-weight:700;}
.success-stat-lbl{font-size:11px;color:var(--text-3);margin-top:2px;}

/* ── Expand row for sekolah detail ──────────────*/
.expand-row td{padding:0 !important;background:var(--blue-50);}
.expand-inner{padding:12px 14px 14px 14px;}
.sekolah-mini-table{width:100%;border-collapse:collapse;font-size:12px;}
.sekolah-mini-table th{background:var(--blue-100);padding:5px 10px;font-size:10px;font-weight:600;color:var(--blue-800);text-transform:uppercase;letter-spacing:.4px;}
.sekolah-mini-table td{padding:6px 10px;border-bottom:1px solid rgba(0,0,0,.05);color:var(--text-2);}
.sekolah-mini-table tbody tr:last-child td{border-bottom:none;}
.sekolah-mini-table tbody tr:hover td{background:var(--blue-50);}
</style>
</head>
<body>

{{-- ── Top Navigation ─────────────────────────── --}}
<nav class="topnav">
    <div class="topnav-brand">
        <div>
            <div>LCC MPR RI</div>
            <span>Setjen MPR RI</span>
        </div>
    </div>
    <div class="topnav-menu">
        <a href="{{ route('lomba-provinsi.index') }}" class="topnav-item active">Data Sekolah Partisipan</a>
        <a href="{{ route('provinsi.index') }}" class="topnav-item">Master Provinsi</a>
        <a href="{{ route('settings.index') }}" class="topnav-item">Pengaturan</a>
    </div>
</nav>

<div class="page">

    {{-- ── Page Header ──────────────────────────── --}}
    <div class="page-header">
        <div>
            <div class="page-title">Master Data Sekolah Partisipan</div>
            <div class="page-sub">{{ $namaKegiatan }} · Tahun Aktif: <strong>{{ $tahunDefault }}</strong></div>
        </div>
        <div class="page-actions">
            <button class="btn" onclick="openImport()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M2 11v2a1 1 0 001 1h10a1 1 0 001-1v-2M8 2v8M5 7l3 3 3-3"/></svg>
                Import Excel
            </button>
            <button class="btn btn-primary" onclick="openAdd()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 16 16"><path d="M8 3v10M3 8h10"/></svg>
                Tambah Data Sekolah Partisipan
            </button>
        </div>
    </div>

    {{-- ── Alert ────────────────────────────────── --}}
    <div id="alert-box" class="alert"></div>

    {{-- ── Stats ────────────────────────────────── --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Provinsi Terdaftar</div>
            <div class="stat-value" id="stat-provinsi">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Sekolah</div>
            <div class="stat-value" id="stat-sekolah">{{ $lombas->sum(fn($l) => $l->sekolah->count()) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tahun Aktif</div>
            <div class="stat-value" style="font-size:20px">{{ $tahunDefault }}</div>
        </div>
    </div>

    {{-- ── Toolbar ──────────────────────────────── --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><circle cx="6.5" cy="6.5" r="4.5"/><path d="M11 11l3.5 3.5"/></svg>
            <input type="text" id="search-input" placeholder="Cari nama provinsi…" oninput="filterTable(this.value)">
        </div>
        @if($tahunList->count() > 1)
        <select class="form-ctrl" id="tahun-select" onchange="changeTahun(this.value)">
            @foreach($tahunList as $t)
            <option value="{{ $t }}" {{ $t == $tahunFilter ? 'selected' : '' }}>Tahun: {{ $t }}</option>
            @endforeach
        </select>
        @endif
        <span id="count-info" style="font-size:12px;color:var(--text-3);align-self:center"></span>
    </div>

    {{-- ── Table ────────────────────────────────── --}}
    <div class="table-card">
        <div class="table-scroll">
            <table id="main-table">
                <thead>
                    <tr>
                        <th style="width:42px">No.</th>
                        <th style="width:70px">Kode</th>
                        <th>Nama Provinsi</th>
                        <th style="width:80px;text-align:center">Tahun</th>
                        <th style="width:110px;text-align:center">Sekolah</th>
                        <th style="width:130px">Dibuat</th>
                        <th style="width:130px;text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse($lombas as $i => $lomba)
                    <tr id="row-{{ $lomba->id }}"
                        data-id="{{ $lomba->id }}"
                        data-provinsi="{{ $lomba->provinsi?->nama }}"
                        data-kode="{{ $lomba->provinsi?->kode }}"
                        data-tahun="{{ $lomba->tahun }}"
                        style="cursor:pointer"
                        onclick="toggleExpand('{{ $lomba->id }}')">
                        <td style="color:var(--text-3);text-align:center">{{ $i + 1 }}</td>
                        <td><span class="badge-kode">{{ $lomba->provinsi?->kode ?? '-' }}</span></td>
                        <td style="font-weight:500">{{ $lomba->provinsi?->nama ?? '-' }}</td>
                        <td style="text-align:center"><span class="badge-tahun">{{ $lomba->tahun }}</span></td>
                        <td style="text-align:center">
                            <span class="badge-count">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><circle cx="8" cy="6" r="3"/><path d="M2 14c0-3.3 2.7-6 6-6s6 2.7 6 6"/></svg>
                                {{ $lomba->sekolah->count() }} / 9
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--text-3)">{{ $lomba->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td onclick="event.stopPropagation()">
                            <div class="action-cell">
                                <button class="btn btn-sm" onclick="openEdit('{{ $lomba->id }}')">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="openDelete('{{ $lomba->id }}', '{{ addslashes($lomba->provinsi?->nama) }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    {{-- Expand row --}}
                    <tr id="expand-{{ $lomba->id }}" class="expand-row" style="display:none">
                        <td colspan="7">
                            <div class="expand-inner">
                                <table class="sekolah-mini-table">
                                    <thead>
                                        <tr>
                                            <th style="width:70px">Kode</th>
                                            <th>Nama Sekolah</th>
                                            <th style="width:160px">Telepon</th>
                                            <th style="width:200px">Email</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lomba->sekolah as $s)
                                        <tr>
                                            <td><span class="badge-kode" style="font-size:11px">{{ $s->kode_sekolah }}</span></td>
                                            <td style="font-weight:500">{{ $s->nama_sekolah }}</td>
                                            <td>{{ $s->nomor_telepon ?? '—' }}</td>
                                            <td>{{ $s->email ?? '—' }}</td>
                                            <td style="color:var(--text-3)">{{ $s->keterangan ?? '—' }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" style="text-align:center;color:var(--text-3)">Belum ada data sekolah.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-row">
                        <td colspan="7" style="text-align:center;padding:3rem;color:var(--text-3)">
                            Belum ada data sekolah partisipan.<br>Klik <strong>Tambah Provinsi</strong> atau <strong>Import Excel</strong> untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span id="table-footer-info">Menampilkan {{ $lombas->count() }} provinsi</span>
            <span>Klik baris untuk detail sekolah</span>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- Modal: Tambah / Edit Provinsi + 9 Sekolah              --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="modal-form" class="modal-backdrop" onclick="backdropClose(event,'modal-form')">
    <div class="modal" style="max-width:780px;max-height:90vh;display:flex;flex-direction:column" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div class="modal-title" id="form-modal-title">Tambah Provinsi Lomba</div>
            <button class="modal-close" onclick="closeModal('modal-form')">×</button>
        </div>
        <div class="modal-body" style="overflow-y:auto;flex:1">

            {{-- Tahun (readonly, dari setting) --}}
            <div class="form-group">
                <label class="form-label">Tahun Kegiatan</label>
                <input class="form-ctrl-full" id="f-tahun" disabled value="{{ $tahunDefault }}">
                <div class="form-hint">Sesuai pengaturan aktif. Ubah di <a href="{{ route('settings.index') }}" style="color:var(--blue-600)">Pengaturan</a>.</div>
            </div>

            {{-- Provinsi --}}
            <div class="form-group" id="grp-provinsi">
                <label class="form-label">Provinsi <span style="color:var(--red-600)">*</span></label>
                <select class="form-ctrl-full" id="f-provinsi">
                    <option value="">— Pilih Provinsi —</option>
                    @foreach($semuaProvinsi as $p)
                    <option value="{{ $p->id }}" data-kode="{{ $p->kode }}">{{ $p->kode }} · {{ $p->nama }}</option>
                    @endforeach
                </select>
                <div class="field-error" id="err-provinsi"></div>
            </div>

            {{-- 9 Sekolah --}}
            <div class="sekolah-section">
                <div class="sekolah-section-title">Data 9 Sekolah Peserta</div>
                <div style="font-size:11px;color:var(--text-3);margin-bottom:.75rem">
                    <strong>Nama Sekolah</strong> wajib diisi dan harus unik per provinsi. <span style="color:var(--amber-600)">Telepon, Email, Keterangan bersifat opsional.</span>
                </div>
                <div style="display:grid;grid-template-columns:40px 1fr 160px 200px;gap:8px;padding:0 0 6px 0;border-bottom:1px solid var(--border);margin-bottom:4px">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.4px;text-align:center">#</div>
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.4px">Nama Sekolah <span style="color:var(--red-600)">*</span></div>
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.4px">Telepon <span style="color:var(--gray-400);font-weight:400;text-transform:none">(opsional)</span></div>
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.4px">Email &amp; Keterangan <span style="color:var(--gray-400);font-weight:400;text-transform:none">(opsional)</span></div>
                </div>
                <div id="sekolah-list">
                    @for($i = 1; $i <= 9; $i++)
                    <div class="sekolah-row" id="sk-row-{{ $i }}">
                        <div class="sekolah-num">
                            <span class="kode-badge" id="sk-kode-{{ $i }}">??{{ $i }}</span>
                            <div style="margin-top:2px">{{ $i }}</div>
                        </div>
                        <div>
                            <input class="form-ctrl-full sekolah-nama" id="sk-nama-{{ $i }}"
                                placeholder="Nama Sekolah {{ $i }} *" maxlength="150"
                                oninput="clearSekolahError({{ $i }})">
                            <div class="field-error" id="sk-err-nama-{{ $i }}"></div>
                        </div>
                        <div>
                            <input class="form-ctrl-full" id="sk-telp-{{ $i }}"
                                placeholder="0811-xxxx (opsional)"
                                oninput="clearSekolahError({{ $i }})">
                            <div class="field-error" id="sk-err-telp-{{ $i }}"></div>
                        </div>
                        <div>
                            <input class="form-ctrl-full" id="sk-email-{{ $i }}"
                                type="email" placeholder="email@sekolah.sch.id (opsional)"
                                oninput="clearSekolahError({{ $i }})">
                            <div style="margin-top:4px">
                                <input class="form-ctrl-full" id="sk-ket-{{ $i }}"
                                    placeholder="Keterangan (opsional)" maxlength="500">
                            </div>
                            <div class="field-error" id="sk-err-email-{{ $i }}"></div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <div id="form-alert" class="alert" style="margin-top:1rem;margin-bottom:0"></div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeModal('modal-form')">Batal</button>
            <button class="btn btn-primary" id="btn-save-form" onclick="submitForm()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg>
                Simpan
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- Modal: Hapus                                           --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="modal-delete" class="modal-backdrop" onclick="backdropClose(event,'modal-delete')">
    <div class="modal" style="max-width:380px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div class="modal-title" style="color:var(--red-600)">Hapus Data Lomba Provinsi</div>
            <button class="modal-close" onclick="closeModal('modal-delete')">×</button>
        </div>
        <div class="modal-body">
            <p id="del-msg" style="font-size:13px;color:var(--text-2);line-height:1.7"></p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeModal('modal-delete')">Batal</button>
            <button class="btn btn-danger" id="btn-confirm-delete" onclick="confirmDelete()">Ya, Hapus</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- Modal: Import Excel                                    --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="modal-import" class="modal-backdrop" onclick="backdropClose(event,'modal-import')">
    <div class="modal" style="max-width:760px;max-height:90vh;display:flex;flex-direction:column" onclick="event.stopPropagation()">

        {{-- Step bar --}}
        <div class="step-bar">
            <div class="step-item">
                <div class="step-dot active" id="sdot-1"><span>1</span></div>
                <div class="step-label active" id="slbl-1">Pilih File</div>
            </div>
            <div class="step-line" id="sline-1"></div>
            <div class="step-item">
                <div class="step-dot" id="sdot-2"><span>2</span></div>
                <div class="step-label" id="slbl-2">Upload</div>
            </div>
            <div class="step-line" id="sline-2"></div>
            <div class="step-item">
                <div class="step-dot" id="sdot-3"><span>3</span></div>
                <div class="step-label" id="slbl-3">Validasi</div>
            </div>
            <div class="step-line" id="sline-3"></div>
            <div class="step-item">
                <div class="step-dot" id="sdot-4"><span>4</span></div>
                <div class="step-label" id="slbl-4">Review</div>
            </div>
            <div class="step-line" id="sline-4"></div>
            <div class="step-item" style="flex:0">
                <div class="step-dot" id="sdot-5"><span>5</span></div>
                <div class="step-label" id="slbl-5">Selesai</div>
            </div>
        </div>

        <div style="overflow-y:auto;flex:1">

        {{-- Panel 1: Pilih File --}}
        <div id="panel-1" class="step-panel active">
            <div class="modal-body">
                <div style="font-size:12px;color:var(--text-2);margin-bottom:14px;line-height:1.8;background:var(--blue-50);border:1px solid var(--blue-100);border-radius:var(--radius);padding:10px 14px">
                    <strong>Format file:</strong> .xlsx / .xls &nbsp;·&nbsp;
                    <strong>Isi:</strong> kode provinsi di sel B3, kemudian data 9 sekolah mulai baris 10 &nbsp;·&nbsp;
                    <strong>Maks.:</strong> 5 MB
                </div>
                <div class="drop-zone" id="drop-zone"
                    onclick="document.getElementById('file-input').click()"
                    ondragover="event.preventDefault();this.classList.add('drag-over')"
                    ondragleave="this.classList.remove('drag-over')"
                    ondrop="handleDrop(event)">
                    <div class="drop-zone-icon">📊</div>
                    <div class="drop-zone-title">Klik atau seret file Excel ke sini</div>
                    <div class="drop-zone-sub">.xlsx · .xls · Maks. 5 MB</div>
                </div>
                <input type="file" id="file-input" accept=".xlsx,.xls" style="display:none" onchange="startUpload(this.files[0])">
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('modal-import')">Batal</button>
                <button class="btn btn-sm" onclick="window.location.href='{{ route('lomba-provinsi.import.template') }}'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M2 11v2a1 1 0 001 1h10a1 1 0 001-1v-2M8 10V2M5 7l3 3 3-3"/></svg>
                    Unduh Template
                </button>
            </div>
        </div>

        {{-- Panel 2: Upload --}}
        <div id="panel-2" class="step-panel">
            <div class="modal-body">
                <div style="font-size:13px;font-weight:500;margin-bottom:1rem" id="upload-filename">Mengunggah…</div>
                <div class="progress-wrap">
                    <div class="progress-label">
                        <span>Mengunggah ke server</span>
                        <span id="upload-pct">0%</span>
                    </div>
                    <div class="progress-track"><div class="progress-bar" id="upload-bar"></div></div>
                </div>
                <div style="font-size:12px;color:var(--text-3);margin-top:8px" id="upload-size-info"></div>
            </div>
        </div>

        {{-- Panel 3: Validasi --}}
        <div id="panel-3" class="step-panel">
            <div class="modal-body">
                <div style="font-size:13px;font-weight:500;margin-bottom:1rem">Memproses file…</div>
                <div class="proc-steps">
                    <div class="proc-step">
                        <div class="proc-step-icon waiting" id="ps-read-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><rect x="2" y="2" width="12" height="12" rx="1"/><path d="M5 6h6M5 9h4"/></svg>
                        </div>
                        <div class="proc-step-text">
                            <div class="proc-step-title">Membaca file Excel</div>
                            <div class="proc-step-sub" id="ps-read-sub">Menunggu…</div>
                        </div>
                    </div>
                    <div class="proc-step">
                        <div class="proc-step-icon waiting" id="ps-prov-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><circle cx="8" cy="6" r="3"/><path d="M2 14c0-3 2.7-5 6-5s6 2 6 5"/></svg>
                        </div>
                        <div class="proc-step-text">
                            <div class="proc-step-title">Deteksi kode provinsi</div>
                            <div class="proc-step-sub" id="ps-prov-sub">Menunggu…</div>
                        </div>
                    </div>
                    <div class="proc-step">
                        <div class="proc-step-icon waiting" id="ps-validate-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg>
                        </div>
                        <div class="proc-step-text">
                            <div class="proc-step-title">Validasi 9 sekolah</div>
                            <div class="proc-step-sub" id="ps-validate-sub">Menunggu…</div>
                        </div>
                    </div>
                </div>
                <div id="panel3-err" class="alert alert-error" style="margin-top:1rem;display:none"></div>
            </div>
        </div>

        {{-- Panel 4: Review --}}
        <div id="panel-4" class="step-panel">
            <div class="modal-body">

                {{-- Info Provinsi --}}
                <div id="prov-info-card" class="prov-card valid" style="display:none">
                    <div class="prov-icon" id="prov-info-icon">✅</div>
                    <div>
                        <div class="prov-card-title" id="prov-info-title"></div>
                        <div class="prov-card-sub" id="prov-info-sub"></div>
                    </div>
                </div>

                {{-- Warning sudah terdaftar --}}
                <div id="prov-warning-card" class="prov-card warning" style="display:none">
                    <div class="prov-icon">⚠️</div>
                    <div>
                        <div class="prov-card-title">Provinsi sudah terdaftar</div>
                        <div class="prov-card-sub" id="prov-warning-sub"></div>
                    </div>
                </div>

                {{-- File-level errors --}}
                <div id="file-errors-box" style="display:none;margin-bottom:.75rem"></div>

                {{-- Review Table --}}
                <div class="review-toolbar">
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <span class="badge badge-valid" id="rv-valid">0 valid</span>
                        <span class="badge badge-invalid" id="rv-invalid">0 invalid</span>
                    </div>
                    <div id="action-selector" style="display:none;align-items:center;gap:8px;font-size:12px">
                        <label style="font-weight:600;color:var(--text-2)">Aksi:</label>
                        <label style="display:flex;align-items:center;gap:4px;cursor:pointer">
                            <input type="radio" name="import-action" value="insert" checked> Tambah Baru
                        </label>
                        <label style="display:flex;align-items:center;gap:4px;cursor:pointer">
                            <input type="radio" name="import-action" value="replace"> Timpa
                        </label>
                    </div>
                </div>
                <div class="review-table-wrap">
                    <table class="review-table">
                        <thead>
                            <tr>
                                <th style="width:40px">No.</th>
                                <th style="width:70px">Kode</th>
                                <th>Nama Sekolah</th>
                                <th style="width:150px">Telepon <span style="font-weight:400;text-transform:none;letter-spacing:0">(ops.)</span></th>
                                <th style="width:180px">Email <span style="font-weight:400;text-transform:none;letter-spacing:0">(ops.)</span></th>
                                <th style="width:70px;text-align:center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="review-body"></tbody>
                    </table>
                </div>
                <div id="panel4-err" class="alert alert-error" style="margin-top:10px;display:none"></div>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="resetImport()">↩ Ulangi</button>
                <button class="btn btn-success" id="btn-process" onclick="processImport()" disabled>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg>
                    Simpan Data
                </button>
            </div>
        </div>

        {{-- Panel 5: Selesai --}}
        <div id="panel-5" class="step-panel">
            <div class="modal-body">
                <div class="success-panel">
                    <div class="success-icon">✅</div>
                    <div class="success-title">Import Berhasil!</div>
                    <div class="success-sub" id="success-sub"></div>
                    <div class="success-detail" id="success-detail"></div>
                </div>
            </div>
            <div class="modal-footer" style="justify-content:center">
                <button class="btn btn-primary" onclick="closeModal('modal-import');location.reload()">Tutup &amp; Refresh</button>
                <button class="btn" onclick="resetImport()">Import Lagi</button>
            </div>
        </div>

        </div>{{-- overflow scroll --}}
    </div>{{-- .modal --}}
</div>{{-- #modal-import --}}

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- JavaScript                                             --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const ROUTES = {
    store:   '{{ route("lomba-provinsi.store") }}',
    show:    '{{ route("lomba-provinsi.show", ":id") }}',
    update:  '{{ route("lomba-provinsi.update", ":id") }}',
    destroy: '{{ route("lomba-provinsi.destroy", ":id") }}',
    preview: '{{ route("lomba-provinsi.import.preview") }}',
    save:    '{{ route("lomba-provinsi.import.save") }}',
};

// ── Utilities ─────────────────────────────────────────────
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function delay(ms){ return new Promise(r => setTimeout(r, ms)); }
function showAlert(type, msg, ms=5000){
    const el = document.getElementById('alert-box');
    el.className = 'alert alert-' + type;
    el.textContent = msg;
    el.style.display = 'block';
    clearTimeout(el._t);
    el._t = setTimeout(() => el.style.display='none', ms);
}
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
function openModal(id){ document.getElementById(id).classList.add('open'); }
function backdropClose(e, id){ if(e.target.id === id) closeModal(id); }

// ── Table search ───────────────────────────────────────────
function filterTable(q){
    q = q.toLowerCase().trim();
    const rows = [...document.querySelectorAll('#table-body tr[data-id]')];
    let vis = 0;
    rows.forEach((r, i) => {
        const match = !q
            || r.dataset.provinsi.toLowerCase().includes(q)
            || r.dataset.kode.includes(q);
        r.style.display = match ? '' : 'none';
        const exp = document.getElementById('expand-' + r.dataset.id);
        if(exp) exp.style.display = 'none'; // close expand on filter
        if(match){ r.querySelector('td').textContent = ++vis; }
    });
    document.getElementById('count-info').textContent = q ? `${vis} dari ${rows.length}` : '';
    document.getElementById('table-footer-info').textContent = `Menampilkan ${vis || rows.length} provinsi`;
}

function changeTahun(t){
    window.location.href = '{{ route("lomba-provinsi.index") }}?tahun=' + t;
}

// ── Expand row ─────────────────────────────────────────────
function toggleExpand(id){
    const exp = document.getElementById('expand-' + id);
    if(!exp) return;
    const isOpen = exp.style.display !== 'none';
    // Close all
    document.querySelectorAll('.expand-row').forEach(r => r.style.display = 'none');
    if(!isOpen) exp.style.display = '';
}

// ── Kode sekolah update on provinsi change ─────────────────
document.getElementById('f-provinsi').addEventListener('change', function(){
    const opt  = this.options[this.selectedIndex];
    const kode = opt.getAttribute('data-kode') || '??';
    for(let i = 1; i <= 9; i++){
        document.getElementById('sk-kode-' + i).textContent = kode + i;
    }
});

// ── CRUD: Open Add ─────────────────────────────────────────
function openAdd(){
    editingId = null;
    document.getElementById('form-modal-title').textContent = 'Tambah Provinsi Lomba';
    document.getElementById('f-provinsi').value = '';
    document.getElementById('grp-provinsi').style.display = '';
    // Reset kode badges
    for(let i = 1; i <= 9; i++){
        document.getElementById('sk-kode-' + i).textContent = '??' + i;
        document.getElementById('sk-nama-' + i).value  = '';
        document.getElementById('sk-telp-' + i).value  = '';
        document.getElementById('sk-email-' + i).value = '';
        document.getElementById('sk-ket-' + i).value   = '';
    }
    clearFormErrors();
    openModal('modal-form');
    setTimeout(() => document.getElementById('f-provinsi').focus(), 100);
}

let editingId = null;

// ── CRUD: Open Edit ────────────────────────────────────────
async function openEdit(id){
    editingId = id;
    document.getElementById('form-modal-title').textContent = 'Edit Data Sekolah';
    document.getElementById('grp-provinsi').style.display = 'none'; // provinsi tidak bisa diubah
    clearFormErrors();
    openModal('modal-form');

    try {
        const res  = await fetch(ROUTES.show.replace(':id', id), {
            headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF }
        });
        const json = await res.json();
        if(!json.success) throw new Error(json.message);

        const d = json.data;
        document.getElementById('f-tahun').value = d.tahun;

        const kode = d.provinsi?.kode || '??';
        d.sekolah.forEach((s, i) => {
            const n = i + 1;
            document.getElementById('sk-kode-' + n).textContent = s.kode_sekolah || (kode + n);
            document.getElementById('sk-nama-' + n).value  = s.nama_sekolah  || '';
            document.getElementById('sk-telp-' + n).value  = s.nomor_telepon || '';
            document.getElementById('sk-email-' + n).value = s.email         || '';
            document.getElementById('sk-ket-' + n).value   = s.keterangan    || '';
        });
        // fill empty slots
        for(let n = d.sekolah.length + 1; n <= 9; n++){
            document.getElementById('sk-kode-' + n).textContent = kode + n;
            document.getElementById('sk-nama-' + n).value  = '';
            document.getElementById('sk-telp-' + n).value  = '';
            document.getElementById('sk-email-' + n).value = '';
            document.getElementById('sk-ket-' + n).value   = '';
        }
    } catch(e){
        showAlert('error', 'Gagal memuat data: ' + e.message);
        closeModal('modal-form');
    }
}

function clearFormErrors(){
    document.querySelectorAll('.field-error').forEach(el => {
        el.style.display = 'none'; el.textContent = '';
    });
    document.querySelectorAll('.is-error').forEach(el => el.classList.remove('is-error'));
    const fa = document.getElementById('form-alert');
    fa.style.display = 'none';
}
function clearSekolahError(n){
    ['nama','telp','email'].forEach(k => {
        const el = document.getElementById(`sk-err-${k}-${n}`);
        if(el){ el.style.display='none'; el.textContent=''; }
    });
}

function collectSekolah(){
    const list = [];
    for(let i = 1; i <= 9; i++){
        list.push({
            kode_sekolah: document.getElementById('sk-kode-' + i).textContent,
            nama_sekolah: document.getElementById('sk-nama-' + i).value.trim(),
            nomor_telepon: document.getElementById('sk-telp-' + i).value.trim(),
            email:         document.getElementById('sk-email-' + i).value.trim(),
            keterangan:    document.getElementById('sk-ket-' + i).value.trim(),
        });
    }
    return list;
}

// ── Submit form ────────────────────────────────────────────
async function submitForm(){
    clearFormErrors();
    const btn = document.getElementById('btn-save-form');
    btn.disabled = true;

    const sekolah    = collectSekolah();
    const provinsiId = document.getElementById('f-provinsi').value;
    const body       = editingId
        ? { sekolah }
        : { provinsi_id: provinsiId, sekolah };
    const url    = editingId ? ROUTES.update.replace(':id', editingId) : ROUTES.store;
    const method = editingId ? 'PUT' : 'POST';

    try {
        const res  = await fetch(url, {
            method,
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify(body),
        });
        const json = await res.json();

        if(json.success){
            showAlert('success', json.message);
            closeModal('modal-form');
            location.reload();
        } else if(json.errors){
            // Map Laravel validation errors
            Object.entries(json.errors).forEach(([key, msgs]) => {
                // e.g. sekolah.0.nama_sekolah
                const m = key.match(/sekolah\.(\d+)\.(\w+)/);
                if(m){
                    const n = parseInt(m[1]) + 1;
                    const f = m[2];
                    const k = f === 'nama_sekolah' ? 'nama' : f === 'nomor_telepon' ? 'telp' : 'email';
                    const el = document.getElementById(`sk-err-${k}-${n}`);
                    if(el){ el.textContent = msgs[0]; el.style.display = 'block'; }
                } else if(key === 'provinsi_id'){
                    const el = document.getElementById('err-provinsi');
                    el.textContent = msgs[0]; el.style.display = 'block';
                } else {
                    const fa = document.getElementById('form-alert');
                    fa.className = 'alert alert-error';
                    fa.textContent = msgs.join(', ');
                    fa.style.display = 'block';
                }
            });
        } else {
            const fa = document.getElementById('form-alert');
            fa.className = 'alert alert-error';
            fa.textContent = json.message || 'Gagal menyimpan.';
            fa.style.display = 'block';
        }
    } catch(e){
        const fa = document.getElementById('form-alert');
        fa.className = 'alert alert-error';
        fa.textContent = 'Koneksi ke server gagal.';
        fa.style.display = 'block';
    } finally {
        btn.disabled = false;
    }
}

// ── Delete ─────────────────────────────────────────────────
let deleteId = null;
function openDelete(id, nama){
    deleteId = id;
    document.getElementById('del-msg').innerHTML =
        `Anda akan menghapus data lomba provinsi <strong>${esc(nama)}</strong> beserta seluruh data sekolahnya. Tindakan ini tidak dapat dibatalkan.`;
    openModal('modal-delete');
}
async function confirmDelete(){
    if(!deleteId) return;
    const btn = document.getElementById('btn-confirm-delete');
    btn.disabled = true;
    try {
        const res  = await fetch(ROUTES.destroy.replace(':id', deleteId), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
        });
        const json = await res.json();
        if(json.success){
            showAlert('success', json.message);
            closeModal('modal-delete');
            // Remove rows
            document.getElementById('row-' + deleteId)?.remove();
            document.getElementById('expand-' + deleteId)?.remove();
            refreshStats();
        } else {
            showAlert('error', json.message || 'Gagal menghapus.');
        }
    } catch(e){
        showAlert('error', 'Koneksi gagal.');
    } finally {
        btn.disabled = false;
        deleteId = null;
    }
}
function refreshStats(){
    const rows = [...document.querySelectorAll('#table-body tr[data-id]')];
    document.getElementById('stat-provinsi').textContent = rows.length;
    document.getElementById('table-footer-info').textContent = `Menampilkan ${rows.length} provinsi`;
}

// ═══════════════════════════════════════════════
// IMPORT EXCEL
// ═══════════════════════════════════════════════
let importData = null; // hasil preview dari server

function openImport(){
    resetImport();
    openModal('modal-import');
}

function goStep(n){
    for(let i = 1; i <= 5; i++){
        const dot = document.getElementById('sdot-' + i);
        const lbl = document.getElementById('slbl-' + i);
        const pan = document.getElementById('panel-' + i);
        const ln  = document.getElementById('sline-' + i);
        if(i < n){
            dot.className = 'step-dot done';
            lbl.className = 'step-label done';
            if(ln) ln.className = 'step-line done';
        } else if(i === n){
            dot.className = 'step-dot active';
            lbl.className = 'step-label active';
        } else {
            dot.className = 'step-dot';
            lbl.className = 'step-label';
        }
        pan.className = 'step-panel' + (i === n ? ' active' : '');
    }
}

function resetImport(){
    importData = null;
    document.getElementById('file-input').value = '';
    document.getElementById('panel3-err').style.display = 'none';
    document.getElementById('panel4-err').style.display = 'none';
    document.getElementById('review-body').innerHTML = '';
    goStep(1);
}

function handleDrop(e){
    e.preventDefault();
    document.getElementById('drop-zone').classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if(file) startUpload(file);
}

async function startUpload(file){
    if(!file) return;
    if(file.size > 5 * 1024 * 1024){
        alert('File terlalu besar. Maksimal 5 MB.');
        return;
    }
    goStep(2);
    document.getElementById('upload-filename').textContent = file.name;
    document.getElementById('upload-size-info').textContent = (file.size / 1024).toFixed(1) + ' KB';

    // Simulate upload progress then do actual XHR
    const bar = document.getElementById('upload-bar');
    const pct = document.getElementById('upload-pct');

    // Animate to 80% during upload
    let fakeP = 0;
    const fake = setInterval(() => {
        fakeP = Math.min(fakeP + Math.random() * 15, 80);
        bar.style.width = fakeP + '%';
        pct.textContent = Math.floor(fakeP) + '%';
    }, 120);

    const fd = new FormData();
    fd.append('file', file);
    fd.append('_token', CSRF);

    try {
        const res = await fetch(ROUTES.preview, { method: 'POST', body: fd });
        clearInterval(fake);
        bar.style.width = '100%';
        pct.textContent = '100%';
        await delay(300);

        goStep(3);
        await runValidationAnimation(await res.json());
    } catch(e){
        clearInterval(fake);
        goStep(3);
        document.getElementById('panel3-err').textContent = 'Upload gagal: ' + e.message;
        document.getElementById('panel3-err').style.display = 'block';
    }
}

async function runValidationAnimation(json){
    // Step 1: reading
    const setStep = (id, status, sub) => {
        const icon = document.getElementById(id + '-icon');
        const subEl= document.getElementById(id + '-sub');
        if(status === 'running') icon.innerHTML = '<div class="spinner"></div>';
        else if(status === 'done') icon.innerHTML = '<span class="check-icon">✓</span>';
        else if(status === 'error') icon.innerHTML = '<span class="x-icon">✗</span>';
        icon.className = 'proc-step-icon ' + (status === 'done' ? 'done' : status === 'error' ? 'error' : 'running');
        if(subEl) subEl.textContent = sub;
    };

    setStep('ps-read', 'running', 'Membaca struktur workbook…');
    await delay(400);

    if(!json.success){
        setStep('ps-read', 'error', json.message);
        document.getElementById('panel3-err').textContent = json.message;
        document.getElementById('panel3-err').style.display = 'block';
        return;
    }
    setStep('ps-read', 'done', 'Berhasil membaca file.');

    // Step 2: provinsi
    setStep('ps-prov', 'running', 'Mencari kode provinsi di sel B3…');
    await delay(400);

    if(!json.provinsi_valid){
        setStep('ps-prov', 'error', `Kode "${json.kode_provinsi}" tidak ditemukan di database.`);
        document.getElementById('panel3-err').textContent = `Kode provinsi "${json.kode_provinsi}" tidak valid atau tidak terdaftar.`;
        document.getElementById('panel3-err').style.display = 'block';
        return;
    }
    setStep('ps-prov', 'done', `Provinsi: ${json.provinsi.kode} · ${json.provinsi.nama}`);

    // Step 3: validate sekolah
    setStep('ps-validate', 'running', 'Memvalidasi 9 baris sekolah…');
    await delay(500);

    if(json.file_errors && json.file_errors.length){
        setStep('ps-validate', 'error', json.file_errors[0]);
        document.getElementById('panel3-err').textContent = json.file_errors[0];
        document.getElementById('panel3-err').style.display = 'block';
        return;
    }

    const invalid = json.invalid_count;
    setStep('ps-validate', 'done', `${json.valid_count} valid, ${invalid} invalid.`);
    await delay(300);

    importData = json;
    buildReviewPanel(json);
    goStep(4);
}

function buildReviewPanel(json){
    // Provinsi info card
    const card = document.getElementById('prov-info-card');
    card.className = 'prov-card valid';
    card.style.display = 'flex';
    document.getElementById('prov-info-title').textContent = `${json.provinsi.kode} · ${json.provinsi.nama}`;
    document.getElementById('prov-info-sub').textContent   = `Tahun: ${json.tahun}`;

    // Warning if already registered
    const wcard = document.getElementById('prov-warning-card');
    if(json.sudah_terdaftar){
        wcard.style.display = 'flex';
        document.getElementById('prov-warning-sub').textContent =
            'Data sekolah sudah ada. Pilih "Timpa" untuk mengganti, atau "Tambah Baru" akan gagal.';
        document.getElementById('action-selector').style.display = 'flex';
    } else {
        wcard.style.display = 'none';
        document.getElementById('action-selector').style.display = 'none';
    }

    // File errors
    const feBox = document.getElementById('file-errors-box');
    if(json.file_errors && json.file_errors.length){
        feBox.style.display = 'block';
        feBox.innerHTML = json.file_errors.map(e => `<div class="alert alert-error" style="margin-bottom:4px;display:block">${esc(e)}</div>`).join('');
    } else {
        feBox.style.display = 'none';
    }

    // Counters
    document.getElementById('rv-valid').textContent   = json.valid_count + ' valid';
    document.getElementById('rv-invalid').textContent = json.invalid_count + ' invalid';

    // Review table
    const tbody = document.getElementById('review-body');
    tbody.innerHTML = '';
    json.sekolah.forEach(s => {
        const isValid = s.status === 'valid';
        const tr = document.createElement('tr');
        tr.className = 'row-' + s.status;
        tr.innerHTML = `
            <td style="text-align:center;font-weight:600">${esc(s.urutan)}</td>
            <td><span class="badge-kode" style="font-size:11px">${esc(s.kode_sekolah)}</span></td>
            <td>
                ${esc(s.nama_sekolah)}
                ${s.errors.length ? '<div class="error-tags">' + s.errors.map(e => `<span class="error-tag">${esc(e)}</span>`).join('') + '</div>' : ''}
            </td>
            <td style="color:var(--text-2)">${esc(s.nomor_telepon || '—')}</td>
            <td style="color:var(--text-2)">${esc(s.email || '—')}</td>
            <td style="text-align:center">${isValid ? '<span class="check-icon">✓</span>' : '<span class="x-icon">✗</span>'}</td>`;
        tbody.appendChild(tr);
    });

    // Enable save button only if all 9 are valid
    const allValid = json.valid_count === 9 && json.invalid_count === 0 && !json.file_errors?.length;
    document.getElementById('btn-process').disabled = !allValid;
    if(!allValid && json.invalid_count > 0){
        document.getElementById('panel4-err').textContent = `Terdapat ${json.invalid_count} baris tidak valid. Perbaiki file Excel dan upload ulang.`;
        document.getElementById('panel4-err').style.display = 'block';
    }
}

async function processImport(){
    if(!importData) return;
    const btn = document.getElementById('btn-process');
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner" style="width:13px;height:13px;border-width:2px;border-top-color:#fff"></div> Menyimpan…';

    const action = document.querySelector('input[name="import-action"]:checked')?.value || 'insert';

    try {
        const res  = await fetch(ROUTES.save, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({
                provinsi_id: importData.provinsi.id,
                action,
                sekolah: importData.sekolah,
            }),
        });
        const json = await res.json();

        if(json.success){
            goStep(5);
            document.getElementById('success-sub').textContent = json.message;
            document.getElementById('success-detail').innerHTML = `
                <div class="success-stat">
                    <div class="success-stat-num" style="color:var(--green-600)">9</div>
                    <div class="success-stat-lbl">Sekolah disimpan</div>
                </div>
                <div class="success-stat">
                    <div class="success-stat-num" style="color:var(--blue-600)">${importData.provinsi.nama}</div>
                    <div class="success-stat-lbl">Provinsi</div>
                </div>`;
        } else {
            document.getElementById('panel4-err').textContent = json.message || 'Gagal menyimpan.';
            document.getElementById('panel4-err').style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg> Simpan Data';
        }
    } catch(e){
        document.getElementById('panel4-err').textContent = 'Koneksi ke server gagal.';
        document.getElementById('panel4-err').style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg> Simpan Data';
    }
}
</script>
</body>
</html>
