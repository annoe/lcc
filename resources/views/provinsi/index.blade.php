{{-- resources/views/provinsi/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Master Data Provinsi — Setjen MPR RI')

@section('content')
<div class="page">

    {{-- ── Page Header ─────────────────────────────── --}}
    <div class="page-header">
        <div>
            <div class="page-title">Master Data Provinsi</div>
            <div class="page-sub">Sekretariat Jenderal MPR RI · Manajemen wilayah administratif tingkat I</div>
        </div>
        <div class="page-actions">
            <a href="{{ route('provinsi.export') }}" class="btn btn-export">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M2 11v2a1 1 0 001 1h10a1 1 0 001-1v-2M8 10V2M5 7l3 3 3-3"/></svg>
                Export Excel
            </a>
            <button class="btn" onclick="openImport()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M2 11v2a1 1 0 001 1h10a1 1 0 001-1v-2M8 2v8M5 7l3 3 3-3"/></svg>
                Import Excel
            </button>
            <button class="btn btn-primary" onclick="openAdd()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 16 16"><path d="M8 3v10M3 8h10"/></svg>
                Tambah
            </button>
        </div>
    </div>

    {{-- ── Alert ─────────────────────────────────────── --}}
    <div id="alert-box" class="alert"></div>

    {{-- ── Stats ─────────────────────────────────────── --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Provinsi</div>
            <div class="stat-value" id="stat-total">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Kode Terdaftar</div>
            <div class="stat-value" id="stat-kode">{{ $provinsis->unique('kode')->count() }}</div>
        </div>
    </div>

    {{-- ── Toolbar ──────────────────────────────────── --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><circle cx="6.5" cy="6.5" r="4.5"/><path d="M11 11l3.5 3.5"/></svg>
            <input type="text" id="search-input" placeholder="Cari nama atau kode provinsi…" oninput="filterTable(this.value)">
        </div>
        <select class="form-ctrl" id="sort-select" onchange="filterTable(document.getElementById('search-input').value)">
            <option value="kode">Urutkan: Kode</option>
            <option value="nama">Urutkan: Nama A–Z</option>
        </select>
        <span class="count-info" id="count-info"></span>
    </div>

    {{-- ── Table ────────────────────────────────────── --}}
    <div class="table-card">
        <div class="table-scroll">
            <table id="main-table">
                <thead>
                    <tr>
                        <th style="width:46px">No.</th>
                        <th style="width:78px">Kode</th>
                        <th>Nama Provinsi</th>
                        <th style="width:230px">ID (ULID)</th>
                        <th style="width:130px">Dibuat</th>
                        <th style="width:120px;text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($provinsis as $i => $p)
                    <tr data-id="{{ $p->id }}"
                        data-kode="{{ $p->kode }}"
                        data-nama="{{ $p->nama }}"
                        data-created="{{ $p->created_at?->format('d/m/Y') ?? '-' }}">
                        <td style="color:var(--text-3);text-align:center">{{ $i + 1 }}</td>
                        <td><span class="badge-kode">{{ $p->kode }}</span></td>
                        <td style="font-weight:500">{{ $p->nama }}</td>
                        <td class="ulid-mono">{{ $p->id }}</td>
                        <td style="font-size:12px;color:var(--text-3)">{{ $p->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>
                            <div class="action-cell">
                                <button class="btn btn-sm" onclick="openEdit(this)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="openDelete(this)">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-row">
                        <td colspan="6" style="text-align:center;padding:3rem;color:var(--text-3)">
                            Belum ada data provinsi.<br>Klik <strong>Tambah</strong> atau <strong>Import Excel</strong> untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span id="table-footer-info">Menampilkan {{ $provinsis->count() }} data</span>
            <span style="color:var(--text-3)">Klik baris untuk detail &middot; ULID sebagai primary key</span>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- Modal: Tambah / Edit                            --}}
{{-- ═══════════════════════════════════════════════ --}}
<div id="modal-form" class="modal-backdrop" onclick="backdropClose(event,'modal-form')">
    <div class="modal" style="max-width:420px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div class="modal-title" id="form-modal-title">Tambah Provinsi</div>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">ID (ULID)</label>
                <input class="form-ctrl-full" id="f-id" disabled>
                <div class="form-hint">Dibuat otomatis oleh sistem</div>
            </div>
            <div class="form-group">
                <label class="form-label">Kode Provinsi <span style="color:var(--red-600)">*</span></label>
                <input class="form-ctrl-full" id="f-kode" maxlength="2" placeholder="Contoh: 11"
                    oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)">
                <div class="form-hint">2 digit angka (01–99)</div>
                <div class="field-error" id="err-kode"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Provinsi <span style="color:var(--red-600)">*</span></label>
                <input class="form-ctrl-full" id="f-nama" placeholder="Contoh: Aceh" maxlength="100">
                <div class="field-error" id="err-nama"></div>
            </div>
            <div id="form-alert" class="alert" style="margin-bottom:0"></div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeModal('modal-form')">Batal</button>
            <button class="btn btn-primary" id="btn-save-form" onclick="submitForm()">Simpan</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- Modal: Hapus                                    --}}
{{-- ═══════════════════════════════════════════════ --}}
<div id="modal-delete" class="modal-backdrop" onclick="backdropClose(event,'modal-delete')">
    <div class="modal" style="max-width:380px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div class="modal-title" style="color:var(--red-600)">Hapus Provinsi</div>
        </div>
        <div class="modal-body">
            <p id="del-msg" style="font-size:13px;color:var(--text-2);line-height:1.7"></p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeModal('modal-delete')">Batal</button>
            <button class="btn btn-danger" onclick="confirmDelete()">Ya, Hapus</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- Modal: Import Excel                             --}}
{{-- ═══════════════════════════════════════════════ --}}
<div id="modal-import" class="modal-backdrop" onclick="backdropClose(event,'modal-import')">
    <div class="modal" onclick="event.stopPropagation()">

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

        {{-- Panel 1: Pilih File --}}
        <div id="panel-1" class="step-panel active">
            <div class="modal-body">
                <div style="font-size:12px;color:var(--text-2);margin-bottom:14px;line-height:1.8;background:var(--blue-50);border:1px solid var(--blue-100);border-radius:var(--radius);padding:10px 14px">
                    <strong>Format kolom wajib:</strong>
                    &nbsp;<code>kode</code> (2 digit angka)
                    &nbsp;·&nbsp; <code>nama</code> (nama provinsi)
                    &nbsp;·&nbsp; <code>id</code> (opsional, ULID)
                </div>
                <div class="drop-zone" id="drop-zone"
                    onclick="document.getElementById('file-input').click()"
                    ondragover="event.preventDefault();this.classList.add('drag-over')"
                    ondragleave="this.classList.remove('drag-over')"
                    ondrop="handleDrop(event)">
                    <div class="drop-zone-icon">📄</div>
                    <div class="drop-zone-title">Klik atau seret file Excel ke sini</div>
                    <div class="drop-zone-sub">.xlsx · .xls · .csv &nbsp;|&nbsp; Maks. 5 MB</div>
                </div>
                <input type="file" id="file-input" accept=".xlsx,.xls,.csv" style="display:none" onchange="startUpload(this.files[0])">
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border);padding-top:1rem">
                <button class="btn" onclick="closeModal('modal-import')">Batal</button>
                <button class="btn btn-sm" onclick="window.location.href='{{ route('provinsi.import.template') }}'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M2 11v2a1 1 0 001 1h10a1 1 0 001-1v-2M8 10V2M5 7l3 3 3-3"/></svg>
                    Unduh Template
                </button>
            </div>
        </div>

        {{-- Panel 2: Uploading --}}
        <div id="panel-2" class="step-panel">
            <div class="modal-body">
                <div style="font-size:13px;font-weight:500;margin-bottom:1rem" id="upload-filename">Mengunggah file…</div>
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
                        <div class="proc-step-icon waiting" id="ps-parse-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6"/><path d="M8 5v3l2 2"/></svg>
                        </div>
                        <div class="proc-step-text">
                            <div class="proc-step-title">Parsing kolom &amp; baris</div>
                            <div class="proc-step-sub" id="ps-parse-sub">Menunggu…</div>
                        </div>
                    </div>
                    <div class="proc-step">
                        <div class="proc-step-icon waiting" id="ps-validate-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg>
                        </div>
                        <div class="proc-step-text">
                            <div class="proc-step-title">Validasi format per baris</div>
                            <div class="proc-step-sub" id="ps-validate-sub">Menunggu…</div>
                        </div>
                    </div>
                    <div class="proc-step">
                        <div class="proc-step-icon waiting" id="ps-check-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><ellipse cx="8" cy="8" rx="6" ry="4"/><path d="M2 8c0 2.2 2.7 4 6 4s6-1.8 6-4"/></svg>
                        </div>
                        <div class="proc-step-text">
                            <div class="proc-step-title">Cek duplikat dengan database</div>
                            <div class="proc-step-sub" id="ps-check-sub">Menunggu…</div>
                        </div>
                    </div>
                </div>
                <div id="panel3-err" class="alert alert-error" style="margin-top:1rem;display:none"></div>
            </div>
        </div>

        {{-- Panel 4: Review & Checklist --}}
        <div id="panel-4" class="step-panel">
            <div class="modal-body">
                <div class="review-toolbar">
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <span class="badge badge-valid" id="rv-valid">0 valid</span>
                        <span class="badge badge-conflict" id="rv-conflict" style="display:none">0 konflik</span>
                        <span class="badge badge-invalid" id="rv-invalid">0 invalid</span>
                        <span class="badge badge-selected" id="rv-selected">0 dipilih</span>
                    </div>
                    <div style="display:flex;gap:6px">
                        <button class="btn btn-sm" onclick="selectAll()">Pilih Semua</button>
                        <button class="btn btn-sm" onclick="deselectAll()">Batalkan Semua</button>
                    </div>
                </div>
                <div class="review-table-wrap">
                    <table class="review-table">
                        <thead>
                            <tr>
                                <th style="width:36px">
                                    <input type="checkbox" id="chk-all" onchange="toggleAll(this.checked)" title="Pilih semua valid">
                                </th>
                                <th style="width:40px">Baris</th>
                                <th style="width:64px">Kode</th>
                                <th>Nama Provinsi</th>
                                <th style="width:96px">Status</th>
                            </tr>
                        </thead>
                        <tbody id="review-body"></tbody>
                    </table>
                </div>
                <div id="panel4-err" class="alert alert-error" style="margin-top:10px;display:none"></div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border)">
                <button class="btn" onclick="resetImport()">↩ Ulangi</button>
                <button class="btn btn-success" id="btn-process" onclick="processSelected()" disabled>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg>
                    Simpan Data Terpilih
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
            <div class="modal-footer" style="border-top:1px solid var(--border);justify-content:center">
                <button class="btn btn-primary" onclick="closeModal('modal-import');location.reload()">Tutup &amp; Refresh</button>
                <button class="btn" onclick="resetImport()">Import Lagi</button>
            </div>
        </div>

    </div>{{-- .modal --}}
</div>{{-- #modal-import --}}

{{-- ═══════════════════════════════════════════════ --}}
{{-- JavaScript                                      --}}
{{-- ═══════════════════════════════════════════════ --}}
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const ROUTES = {
    store:   '{{ route("provinsi.store") }}',
    update:  '{{ route("provinsi.update", ":id") }}',
    destroy: '{{ route("provinsi.destroy", ":id") }}',
    preview: '{{ route("provinsi.import.preview") }}',
    save:    '{{ route("provinsi.import.save") }}',
};

// ── Utilities ─────────────────────────────────────────────────────────
function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function delay(ms){ return new Promise(r => setTimeout(r, ms)); }
function showAlert(type, msg, ms=4500){
    const el = document.getElementById('alert-box');
    el.className = 'alert alert-' + type;
    el.textContent = msg;
    el.style.display = 'block';
    clearTimeout(el._t);
    el._t = setTimeout(() => el.style.display = 'none', ms);
}
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
function openModal(id){ document.getElementById(id).classList.add('open'); }
function backdropClose(e, id){ if(e.target.id === id) closeModal(id); }

// ── Table filter / sort ────────────────────────────────────────────────
function filterTable(q){
    q = q.toLowerCase().trim();
    const sort  = document.getElementById('sort-select').value;
    const tbody = document.getElementById('table-body');
    const rows  = [...tbody.querySelectorAll('tr[data-id]')];

    rows.sort((a, b) => {
        const va = (a.dataset[sort] || '').toLowerCase();
        const vb = (b.dataset[sort] || '').toLowerCase();
        return va.localeCompare(vb, 'id');
    });

    let visible = 0;
    rows.forEach((r, i) => {
        const match = !q
            || r.dataset.kode.includes(q)
            || r.dataset.nama.toLowerCase().includes(q);
        r.style.display = match ? '' : 'none';
        if(match){ r.querySelector('td').textContent = ++visible; }
    });

    const empty = document.getElementById('empty-row');
    if(empty) empty.style.display = visible === 0 ? '' : 'none';

    document.getElementById('count-info').textContent =
        q ? `${visible} dari ${rows.length} data` : '';
    document.getElementById('table-footer-info').textContent =
        `Menampilkan ${visible || rows.length} data`;
}

// ── Stats ──────────────────────────────────────────────────────────────
function refreshStats(){
    const rows = [...document.querySelectorAll('#table-body tr[data-id]')];
    document.getElementById('stat-total').textContent = rows.length;
    document.getElementById('stat-kode').textContent  = new Set(rows.map(r => r.dataset.kode)).size;
    document.getElementById('table-footer-info').textContent = `Menampilkan ${rows.length} data`;
}

// ── ULID generator (client-side, untuk preview saja) ──────────────────
function genULID(){
    const C = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    let t = Date.now(), ts = '', r = '';
    for(let i = 0; i < 10; i++){ ts = C[t % 32] + ts; t = Math.floor(t / 32); }
    for(let i = 0; i < 16; i++) r += C[Math.floor(Math.random() * 32)];
    return ts + r;
}

// ── CRUD: Tambah ───────────────────────────────────────────────────────
let editingId = null;

function clearFormErrors(){
    ['kode','nama'].forEach(k => {
        const el = document.getElementById('err-' + k);
        el.style.display = 'none'; el.textContent = '';
    });
    const fa = document.getElementById('form-alert');
    fa.style.display = 'none';
}

function openAdd(){
    editingId = null;
    document.getElementById('form-modal-title').textContent = 'Tambah Provinsi';
    document.getElementById('f-id').value   = genULID();
    document.getElementById('f-kode').value = '';
    document.getElementById('f-nama').value = '';
    clearFormErrors();
    openModal('modal-form');
    setTimeout(() => document.getElementById('f-kode').focus(), 100);
}

// Dipanggil dari tombol Edit di tabel — ambil data dari data-* row
function openEdit(btn){
    const row   = btn.closest('tr');
    editingId   = row.dataset.id;
    document.getElementById('form-modal-title').textContent = 'Edit Provinsi';
    document.getElementById('f-id').value   = editingId;
    document.getElementById('f-kode').value = row.dataset.kode;
    document.getElementById('f-nama').value = row.dataset.nama;
    clearFormErrors();
    openModal('modal-form');
    setTimeout(() => document.getElementById('f-kode').focus(), 100);
}

async function submitForm(){
    clearFormErrors();
    const kode = document.getElementById('f-kode').value.trim();
    const nama = document.getElementById('f-nama').value.trim();
    const btn  = document.getElementById('btn-save-form');
    btn.disabled = true;

    const url    = editingId ? ROUTES.update.replace(':id', editingId) : ROUTES.store;
    const method = editingId ? 'PUT' : 'POST';

    try {
        const res  = await fetch(url, {
            method,
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ kode, nama }),
        });
        const json = await res.json();

        if(json.success){
            showAlert('success', json.message);
            closeModal('modal-form');
            const d = json.data;

            if(editingId){
                const row = document.querySelector(`tr[data-id="${editingId}"]`);
                if(row){
                    row.dataset.kode = d.kode;
                    row.dataset.nama = d.nama;
                    row.cells[1].innerHTML = `<span class="badge-kode">${esc(d.kode)}</span>`;
                    row.cells[2].textContent = d.nama;
                }
            } else {
                const tbody = document.getElementById('table-body');
                document.getElementById('empty-row')?.remove();
                const num = tbody.querySelectorAll('tr[data-id]').length + 1;
                const tr  = document.createElement('tr');
                tr.dataset.id      = d.id;
                tr.dataset.kode    = d.kode;
                tr.dataset.nama    = d.nama;
                tr.dataset.created = d.created_at ? d.created_at.slice(0,10).split('-').reverse().join('/') : '-';
                tr.innerHTML = `
                    <td style="color:var(--text-3);text-align:center">${num}</td>
                    <td><span class="badge-kode">${esc(d.kode)}</span></td>
                    <td style="font-weight:500">${esc(d.nama)}</td>
                    <td class="ulid-mono">${esc(d.id)}</td>
                    <td style="font-size:12px;color:var(--text-3)">${d.created_at ? d.created_at.slice(0,10).split('-').reverse().join('/') : '-'}</td>
                    <td><div class="action-cell">
                        <button class="btn btn-sm" onclick="openEdit(this)">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="openDelete(this)">Hapus</button>
                    </div></td>`;
                tbody.appendChild(tr);
            }
            refreshStats();
        } else {
            if(json.errors){
                Object.entries(json.errors).forEach(([k, msgs]) => {
                    const el = document.getElementById('err-' + k);
                    if(el){ el.textContent = msgs[0]; el.style.display = 'block'; }
                });
            }
        }
    } catch(e){
        const fa = document.getElementById('form-alert');
        fa.textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
        fa.className = 'alert alert-error'; fa.style.display = 'block';
    }
    btn.disabled = false;
}

// ── CRUD: Hapus ────────────────────────────────────────────────────────
let deletingId = null;

function openDelete(btn){
    const row = btn.closest('tr');
    deletingId = row.dataset.id;
    document.getElementById('del-msg').innerHTML =
        `Yakin ingin menghapus provinsi <strong>${esc(row.dataset.nama)}</strong>
         (kode: <strong>${esc(row.dataset.kode)}</strong>)?<br>
         <span style="color:var(--red-600);font-size:12px">Tindakan ini tidak dapat dibatalkan.</span>`;
    openModal('modal-delete');
}

async function confirmDelete(){
    const url = ROUTES.destroy.replace(':id', deletingId);
    try {
        const res  = await fetch(url, { method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} });
        const json = await res.json();
        if(json.success){
            showAlert('success', json.message);
            closeModal('modal-delete');
            document.querySelector(`tr[data-id="${deletingId}"]`)?.remove();
            document.querySelectorAll('#table-body tr[data-id]').forEach((r, i) =>
                r.querySelector('td').textContent = i + 1);
            refreshStats();
            if(!document.querySelector('#table-body tr[data-id]')){
                document.getElementById('table-body').innerHTML =
                    '<tr id="empty-row"><td colspan="6" style="text-align:center;padding:3rem;color:var(--text-3)">Belum ada data.</td></tr>';
            }
        }
    } catch(e){ showAlert('error', 'Gagal menghapus data.'); }
}

// ── Import: state ──────────────────────────────────────────────────────
let importData  = [];
let procTimer   = null;

function openImport(){ resetImport(); openModal('modal-import'); }

function resetImport(){
    importData = [];
    clearTimeout(procTimer);
    goStep(1);
    document.getElementById('file-input').value = '';
    document.getElementById('upload-bar').style.width = '0';
    document.getElementById('upload-pct').textContent = '0%';
    document.getElementById('review-body').innerHTML   = '';
    document.getElementById('panel3-err').style.display = 'none';
    document.getElementById('panel4-err').style.display = 'none';
    resetProcSteps();
}

// ── Step navigation ────────────────────────────────────────────────────
function goStep(n){
    for(let i = 1; i <= 5; i++){
        document.getElementById('panel-' + i).classList.toggle('active', i === n);
        const dot = document.getElementById('sdot-' + i);
        const lbl = document.getElementById('slbl-' + i);
        dot.classList.remove('active','done');
        lbl.classList.remove('active','done');
        if(i < n){ dot.classList.add('done'); lbl.classList.add('done'); }
        else if(i === n){ dot.classList.add('active'); lbl.classList.add('active'); }
        if(i < 5) document.getElementById('sline-' + i).classList.toggle('done', i < n);
    }
}

// ── Upload ─────────────────────────────────────────────────────────────
function handleDrop(e){
    e.preventDefault();
    document.getElementById('drop-zone').classList.remove('drag-over');
    const f = e.dataTransfer.files[0];
    if(f) startUpload(f);
}

async function startUpload(file){
    if(!file) return;
    const ext = file.name.split('.').pop().toLowerCase();
    if(!['xlsx','xls','csv'].includes(ext)){
        alert('Format tidak didukung. Gunakan .xlsx, .xls, atau .csv'); return;
    }
    if(file.size > 5 * 1024 * 1024){
        alert('Ukuran file melebihi 5 MB.'); return;
    }

    // Tampilkan step 2
    goStep(2);
    document.getElementById('upload-filename').textContent = '📎 ' + file.name;
    document.getElementById('upload-size-info').textContent =
        'Ukuran: ' + (file.size / 1024).toFixed(1) + ' KB · ' + file.type;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', CSRF);

    let serverJson = null;

    await new Promise((resolve) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', ROUTES.preview);
        xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.onprogress = (e) => {
            if(e.lengthComputable){
                const pct = Math.round(e.loaded / e.total * 100);
                document.getElementById('upload-bar').style.width = pct + '%';
                document.getElementById('upload-pct').textContent = pct + '%';
            }
        };

        xhr.upload.onloadend = () => {
            // Upload selesai → pindah ke step 3
            goStep(3);
            runProcAnimation();
        };

        xhr.onload = () => {
            try { serverJson = JSON.parse(xhr.responseText); }
            catch(e){ serverJson = { success:false, message:'Respons server tidak valid.' }; }
            resolve();
        };
        xhr.onerror = () => { serverJson = { success:false, message:'Koneksi ke server gagal.' }; resolve(); };

        xhr.send(formData);
    });

    // Tunggu animasi minimal 2.2 detik supaya user sempat membaca
    await delay(2200);

    if(!serverJson?.success){
        finishProcError(serverJson?.message || 'Gagal memproses file.');
        return;
    }

    finishProcDone(serverJson.total, serverJson.valid_count, serverJson.conflict_count || 0);
    await delay(450);

    importData = serverJson.rows;
    renderReviewTable();
    goStep(4);
}

// ── Proc-step animation ────────────────────────────────────────────────
const PROC_STEPS = ['read','parse','validate','check'];

function resetProcSteps(){
    PROC_STEPS.forEach(id => {
        setStepState(id, 'waiting');
        document.getElementById('ps-' + id + '-sub').textContent = 'Menunggu…';
    });
}

function setStepState(id, state){
    const icon = document.getElementById('ps-' + id + '-icon');
    icon.className = 'proc-step-icon ' + state;
    if(state === 'running') icon.innerHTML = '<div class="spinner"></div>';
    else if(state === 'done')  icon.innerHTML = '<span class="check-icon">✓</span>';
    else if(state === 'error') icon.innerHTML = '<span class="x-icon">✕</span>';
}

function runProcAnimation(){
    const msgs = [
        { id:'read',     sub:'Membaca worksheet aktif…' },
        { id:'parse',    sub:'Mengidentifikasi kolom header…' },
        { id:'validate', sub:'Memeriksa format kode & nama…' },
        { id:'check',    sub:'Membandingkan dengan data di database…' },
    ];
    let i = 0;
    function next(){
        if(i > 0) setStepState(msgs[i-1].id, 'done');
        if(i >= msgs.length) return;
        const m = msgs[i];
        setStepState(m.id, 'running');
        document.getElementById('ps-' + m.id + '-sub').textContent = m.sub;
        i++;
        procTimer = setTimeout(next, 500);
    }
    next();
}

function finishProcDone(total, valid, conflict){
    clearTimeout(procTimer);
    PROC_STEPS.forEach(id => setStepState(id, 'done'));
    const parts = [`${total} baris`, `${valid} valid`];
    if(conflict > 0) parts.push(`${conflict} konflik`);
    document.getElementById('ps-check-sub').textContent = 'Selesai: ' + parts.join(', ') + '.';
}

function finishProcError(msg){
    clearTimeout(procTimer);
    const running = PROC_STEPS.find(id =>
        document.getElementById('ps-' + id + '-icon').className.includes('running'));
    if(running) setStepState(running, 'error');
    const el = document.getElementById('panel3-err');
    el.textContent = msg; el.style.display = 'block';
}

// ── Review table ───────────────────────────────────────────────────────
function renderReviewTable(){
    const tbody = document.getElementById('review-body');
    tbody.innerHTML = '';

    const validCount    = importData.filter(r => r.status === 'valid').length;
    const conflictCount = importData.filter(r => r.status === 'conflict').length;
    const invalidCount  = importData.filter(r => r.status === 'invalid').length;

    document.getElementById('rv-valid').textContent    = validCount    + ' valid';
    document.getElementById('rv-conflict').textContent = conflictCount + ' konflik';
    document.getElementById('rv-conflict').style.display = conflictCount > 0 ? '' : 'none';
    document.getElementById('rv-invalid').textContent  = invalidCount  + ' invalid';

    importData.forEach((row, idx) => {
        const isSelectable = row.status === 'valid' || row.status === 'conflict';
        const tr = document.createElement('tr');

        if (row.status === 'conflict')      tr.className = 'row-conflict';
        else if (row.status === 'valid')    tr.className = 'row-valid';
        else                                tr.className = 'row-invalid';

        tr.dataset.idx = idx;

        // ── Diff panel untuk baris konflik ──────────────────────────
        let diffHtml = '';
        if (row.status === 'conflict' && row.existing) {
            if (row.conflict_type === 'kode_same_nama_diff') {
                diffHtml = `
                    <div class="diff-panel">
                        <div class="diff-row diff-old">
                            <span class="diff-label">DB</span>
                            <span class="diff-val">${esc(row.existing.nama)}</span>
                        </div>
                        <div class="diff-row diff-new">
                            <span class="diff-label">Baru</span>
                            <span class="diff-val">${esc(row.nama)}</span>
                        </div>
                    </div>
                    <div class="conflict-hint">⚠ Kode sudah ada · Centang untuk UPDATE nama di database</div>`;
            } else if (row.conflict_type === 'nama_same_kode_diff') {
                diffHtml = `
                    <div class="diff-panel">
                        <div class="diff-row diff-old">
                            <span class="diff-label">DB</span>
                            <span class="diff-val">kode <strong>${esc(row.existing.kode)}</strong> juga bernama "${esc(row.existing.nama)}"</span>
                        </div>
                        <div class="diff-row diff-new">
                            <span class="diff-label">Baru</span>
                            <span class="diff-val">kode <strong>${esc(row.kode)}</strong> → "${esc(row.nama)}"</span>
                        </div>
                    </div>
                    <div class="conflict-hint">⚠ Nama serupa sudah ada · Centang untuk INSERT dengan kode baru</div>`;
            }
        }

        // ── Error tags untuk baris invalid ──────────────────────────
        const errorHtml = row.errors.length
            ? `<div class="error-tags">${row.errors.map(e => `<span class="error-tag">${esc(e)}</span>`).join('')}</div>`
            : '';

        // ── Badge status ─────────────────────────────────────────────
        let badgeHtml;
        if (row.status === 'valid') {
            badgeHtml = '<span class="badge badge-valid">✓ Valid</span>';
        } else if (row.status === 'conflict') {
            const label = row.action === 'update' ? '↻ Update' : '⚠ Konflik';
            badgeHtml = `<span class="badge badge-conflict" title="${row.conflict_type === 'kode_same_nama_diff' ? 'Kode sama, nama berbeda' : 'Nama sama, kode berbeda'}">${label}</span>`;
        } else {
            badgeHtml = '<span class="badge badge-invalid">✕ Invalid</span>';
        }

        tr.innerHTML = `
            <td><input type="checkbox" class="row-chk" data-idx="${idx}"
                ${isSelectable ? '' : 'disabled'}
                ${isSelectable ? 'checked' : ''}
                onchange="updateSelectedCount()"></td>
            <td style="color:var(--text-3);text-align:center">${row.row}</td>
            <td><span class="badge-kode">${esc(row.kode || '—')}</span></td>
            <td>${esc(row.nama || '—')}${diffHtml}${errorHtml}</td>
            <td>${badgeHtml}</td>`;
        tbody.appendChild(tr);
    });

    updateSelectedCount();
    document.getElementById('chk-all').checked =
        (validCount + conflictCount) > 0;
}

function updateSelectedCount(){
    const checked = document.querySelectorAll('.row-chk:checked').length;
    const total   = document.querySelectorAll('.row-chk:not(:disabled)').length;
    document.getElementById('rv-selected').textContent = checked + ' dipilih';
    document.getElementById('btn-process').disabled = checked === 0;

    const allChk = document.getElementById('chk-all');
    allChk.indeterminate = checked > 0 && checked < total;
    allChk.checked = total > 0 && checked === total;

    // Highlight selected
    document.querySelectorAll('.row-chk:not(:disabled)').forEach(chk => {
        chk.closest('tr').classList.toggle('row-selected', chk.checked);
    });
}

function toggleAll(checked){
    document.querySelectorAll('.row-chk:not(:disabled)').forEach(c => c.checked = checked);
    updateSelectedCount();
}
function selectAll(){ toggleAll(true); }
function deselectAll(){ toggleAll(false); }

// ── Process & Save ─────────────────────────────────────────────────────
async function processSelected(){
    const selected = [];
    document.querySelectorAll('.row-chk:checked').forEach(chk => {
        selected.push(importData[parseInt(chk.dataset.idx)]);
    });
    if(!selected.length) return;

    const btn = document.getElementById('btn-process');
    btn.disabled = true;
    btn.textContent = 'Menyimpan…';

    // Kirim kode, nama, action per baris
    const rows = selected.map(r => ({
        id:     r.id,
        kode:   r.kode,
        nama:   r.nama,
        action: r.action,   // 'insert' | 'update'
    }));

    try {
        const res  = await fetch(ROUTES.save, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ rows }),
        });
        const json = await res.json();

        if(json.success){
            goStep(5);
            document.getElementById('success-sub').textContent = json.message;
            const detail = [];
            if(json.inserted > 0) detail.push(`
                <div class="success-stat">
                    <div class="success-stat-num" style="color:var(--green-600)">${json.inserted}</div>
                    <div class="success-stat-lbl">Ditambahkan</div>
                </div>`);
            if(json.updated > 0) detail.push(`
                <div class="success-stat">
                    <div class="success-stat-num" style="color:var(--blue-600)">${json.updated}</div>
                    <div class="success-stat-lbl">Diperbarui</div>
                </div>`);
            if(json.skipped > 0) detail.push(`
                <div class="success-stat">
                    <div class="success-stat-num" style="color:var(--amber-600)">${json.skipped}</div>
                    <div class="success-stat-lbl">Dilewati</div>
                </div>`);
            document.getElementById('success-detail').innerHTML = detail.join('');
        } else {
            const el = document.getElementById('panel4-err');
            el.textContent = json.message || 'Gagal menyimpan.';
            el.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg> Simpan Data Terpilih';
        }
    } catch(e){
        const el = document.getElementById('panel4-err');
        el.textContent = 'Koneksi ke server gagal.';
        el.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg> Simpan Data Terpilih';
    }
}

// Init
refreshStats();

</script>
@endpush
