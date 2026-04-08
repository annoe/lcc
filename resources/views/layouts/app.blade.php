{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'LCC MPR RI')</title>
<style>
/* ── Reset & Base ─────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --blue-50:#E6F1FB;--blue-100:#B5D4F4;--blue-200:#85B7EB;--blue-400:#378ADD;--blue-600:#185FA5;--blue-800:#0C447C;--blue-900:#042C53;
    --teal-50:#E1F5EE;--teal-100:#9FE1CB;--teal-600:#0F6E56;--teal-800:#085041;
    --green-50:#EAF3DE;--green-100:#C0DD97;--green-600:#3B6D11;--green-800:#27500A;
    --red-50:#FCEBEB;--red-100:#F7C1C1;--red-600:#A32D2D;--red-800:#791F1F;
    --amber-50:#FAEEDA;--amber-100:#FAC775;--amber-600:#854F0B;
    --gold-400:#C9A84C;--gold-500:#B5923C;--gold-600:#9E7C2E;
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
.btn-export{color:var(--teal-600);border-color:var(--teal-100);}
.btn-export:hover{background:var(--teal-50);}
.btn-sm{padding:5px 11px;font-size:12px;}
.btn:disabled{opacity:.5;cursor:not-allowed;transform:none;}

/* ── Alert ───────────────────────────────────── */
.alert{display:none;padding:11px 16px;border-radius:var(--radius);font-size:13px;margin-bottom:1rem;border:1px solid;}
.alert-success{background:var(--green-50);color:var(--green-600);border-color:var(--green-100);}
.alert-error{background:var(--red-50);color:var(--red-600);border-color:var(--red-100);}

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
.toolbar-right{display:flex;gap:8px;margin-left:auto;}
.count-info{font-size:12px;color:var(--text-3);align-self:center;}

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
.ulid-mono{font-family:monospace;font-size:11px;color:var(--text-3);}
.action-cell{display:flex;gap:4px;justify-content:flex-end;}
.table-footer{padding:10px 14px;background:var(--gray-50);border-top:1px solid var(--border);font-size:12px;color:var(--text-3);display:flex;justify-content:space-between;align-items:center;}

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
/* Settings page uses .form-ctrl (not -full) with larger padding */
.form-ctrl{width:100%;padding:9px 12px;border:1px solid var(--border-md);border-radius:var(--radius);background:var(--surface);color:var(--text);font-size:14px;transition:border-color .15s,box-shadow .15s;}
.form-ctrl:focus{outline:none;border-color:var(--blue-400);box-shadow:0 0 0 3px rgba(55,138,221,.12);}
.form-ctrl.is-error{border-color:var(--red-600);}
.form-hint{font-size:11px;color:var(--text-3);margin-top:3px;}
.field-error{font-size:11px;color:var(--red-600);margin-top:3px;display:none;}

/* ── Card (Settings) ─────────────────────────── */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow);overflow:hidden;margin-bottom:1.5rem;}
.card-header{padding:1.1rem 1.5rem;border-bottom:1px solid var(--border);background:var(--gray-50);}
.card-title{font-size:14px;font-weight:600;color:var(--blue-800);}
.card-body{padding:1.5rem;}

/* ── Preview badge (Settings) ────────────────── */
.preview-badge{display:inline-flex;align-items:center;gap:6px;background:var(--blue-50);border:1px solid var(--blue-100);color:var(--blue-800);border-radius:6px;padding:4px 10px;font-size:12px;font-weight:600;margin-top:6px;}

/* ── Param chip ──────────────────────────────── */
.param-chip{display:inline-flex;align-items:center;gap:4px;background:var(--amber-50);border:1px solid var(--amber-100);color:var(--amber-600);border-radius:5px;padding:2px 8px;font-family:monospace;font-size:11px;font-weight:700;margin:0 2px;}

/* ── Sekolah grid in modal ───────────────────── */
.sekolah-section{background:var(--gray-50);border:1px solid var(--border);border-radius:var(--radius);padding:1rem;margin-top:.75rem;}
.sekolah-section-title{font-size:12px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.75rem;}
.sekolah-row{display:grid;grid-template-columns:40px 1fr 160px 200px;gap:8px;align-items:start;padding:8px 0;border-bottom:1px solid var(--border);}
.sekolah-row:last-child{border-bottom:none;padding-bottom:0;}
.sekolah-num{font-size:11px;font-weight:700;color:var(--text-3);text-align:center;padding-top:9px;}
.kode-badge{font-family:monospace;font-size:10px;color:var(--blue-600);background:var(--blue-50);border-radius:4px;padding:1px 5px;display:inline-block;margin-bottom:3px;}

/* ── Expand row for sekolah detail ──────────────*/
.expand-row td{padding:0 !important;background:var(--blue-50);}
.expand-inner{padding:12px 14px 14px 14px;}
.sekolah-mini-table{width:100%;border-collapse:collapse;font-size:12px;}
.sekolah-mini-table th{background:var(--blue-100);padding:5px 10px;font-size:10px;font-weight:600;color:var(--blue-800);text-transform:uppercase;letter-spacing:.4px;}
.sekolah-mini-table td{padding:6px 10px;border-bottom:1px solid rgba(0,0,0,.05);color:var(--text-2);}
.sekolah-mini-table tbody tr:last-child td{border-bottom:none;}
.sekolah-mini-table tbody tr:hover td{background:var(--blue-50);}

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
.check-icon{color:var(--green-600);font-weight:700;}
.x-icon{color:var(--red-600);font-weight:700;}

/* Provinsi info card in import */
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
.badge-conflict{background:var(--amber-50);color:var(--amber-600);}
.badge-selected{background:var(--blue-50);color:var(--blue-600);}
.review-table-wrap{border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;max-height:320px;overflow-y:auto;}
.review-table{width:100%;border-collapse:collapse;font-size:12px;}
.review-table th{background:var(--gray-50);padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--border);position:sticky;top:0;z-index:1;}
.review-table td{padding:8px 10px;border-bottom:1px solid var(--border);vertical-align:top;}
.review-table tbody tr:last-child td{border-bottom:none;}
.review-table tbody tr.row-valid:hover td{background:var(--green-50);}
.review-table tbody tr.row-invalid td{background:var(--red-50);color:var(--red-600);opacity:.7;}
.review-table tbody tr.row-conflict td{background:var(--amber-50);}
.review-table tbody tr.row-conflict:hover td{background:#FDE8B0;}
.review-table tbody tr.row-selected td{background:var(--blue-50);}
.review-table tbody tr.row-selected.row-conflict td{background:#DBEEFF;}
.error-tags{display:flex;flex-wrap:wrap;gap:3px;margin-top:3px;}
.error-tag{background:var(--red-100);color:var(--red-800);border-radius:4px;padding:1px 6px;font-size:10px;}
input[type=checkbox]{accent-color:var(--blue-600);width:14px;height:14px;cursor:pointer;}

/* Diff panel (provinsi import) */
.diff-panel{margin-top:5px;border-radius:5px;overflow:hidden;border:1px solid var(--amber-100);font-size:11px;}
.diff-row{display:flex;align-items:baseline;gap:6px;padding:3px 8px;}
.diff-row.diff-old{background:var(--red-50);color:var(--red-800);}
.diff-row.diff-new{background:var(--green-50);color:var(--green-800);}
.diff-label{font-weight:600;min-width:36px;flex-shrink:0;font-size:10px;text-transform:uppercase;letter-spacing:.4px;}
.diff-val{word-break:break-word;}
.conflict-hint{font-size:10px;color:var(--amber-600);margin-top:3px;font-style:italic;}

/* Success panel */
.success-panel{text-align:center;padding:1.5rem 1rem;}
.success-icon{font-size:52px;margin-bottom:.75rem;}
.success-title{font-size:18px;font-weight:600;color:var(--green-600);margin-bottom:.5rem;}
.success-sub{font-size:13px;color:var(--text-2);}
.success-detail{display:flex;gap:12px;justify-content:center;margin-top:1rem;flex-wrap:wrap;}
.success-stat{background:var(--gray-50);border-radius:var(--radius);padding:10px 20px;text-align:center;border:1px solid var(--border);}
.success-stat-num{font-size:22px;font-weight:700;}
.success-stat-lbl{font-size:11px;color:var(--text-3);margin-top:2px;}

@stack('styles')
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
        <a href="{{ route('lomba-provinsi.index') }}"
           class="topnav-item {{ Request::routeIs('lomba-provinsi.*') ? 'active' : '' }}">Data Lomba</a>
        <a href="{{ route('provinsi.index') }}"
           class="topnav-item {{ Request::routeIs('provinsi.*') ? 'active' : '' }}">Master Provinsi</a>
        <a href="{{ route('jenis-babak.index') }}"
           class="topnav-item {{ Request::routeIs('jenis-babak.*') ? 'active' : '' }}">Jenis Babak</a>
        <a href="{{ route('settings.index') }}"
           class="topnav-item {{ Request::routeIs('settings.*') ? 'active' : '' }}">Pengaturan</a>
    </div>
</nav>

@yield('content')

@stack('scripts')
</body>
</html>
