{{-- resources/views/lomba-provinsi/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Data Lomba — LCC MPR RI')

@section('content')
<div class="page">

    {{-- ── Page Header ──────────────────────────── --}}
    <div class="page-header">
        <div>
            <div class="page-title">Data Lomba</div>
            <div class="page-sub">{{ $namaKegiatan }} · Tahun Aktif: <strong>{{ $tahunDefault }}</strong></div>
        </div>
        <div class="page-actions">
            <button class="btn" onclick="openImport()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M2 11v2a1 1 0 001 1h10a1 1 0 001-1v-2M8 2v8M5 7l3 3 3-3"/></svg>
                Import Excel
            </button>
            <button class="btn btn-primary" onclick="openAdd()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 16 16"><path d="M8 3v10M3 8h10"/></svg>
                Tambah Provinsi
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
                            Belum ada data lomba provinsi.<br>Klik <strong>Tambah Provinsi</strong> atau <strong>Import Excel</strong> untuk memulai.
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

{{-- Modal: Tambah / Edit --}}
<div id="modal-form" class="modal-backdrop" onclick="backdropClose(event,'modal-form')">
    <div class="modal" style="max-width:780px;max-height:90vh;display:flex;flex-direction:column" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div class="modal-title" id="form-modal-title">Tambah Provinsi Lomba</div>
            <button class="modal-close" onclick="closeModal('modal-form')">×</button>
        </div>
        <div class="modal-body" style="overflow-y:auto;flex:1">
            <div class="form-group">
                <label class="form-label">Tahun Kegiatan</label>
                <input class="form-ctrl-full" id="f-tahun" disabled value="{{ $tahunDefault }}">
                <div class="form-hint">Sesuai pengaturan aktif. Ubah di <a href="{{ route('settings.index') }}" style="color:var(--blue-600)">Pengaturan</a>.</div>
            </div>
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
                            <input class="form-ctrl-full sekolah-nama" id="sk-nama-{{ $i }}" placeholder="Nama Sekolah {{ $i }} *" maxlength="150" oninput="clearSekolahError({{ $i }})">
                            <div class="field-error" id="sk-err-nama-{{ $i }}"></div>
                        </div>
                        <div>
                            <input class="form-ctrl-full" id="sk-telp-{{ $i }}" placeholder="0811-xxxx (opsional)" oninput="clearSekolahError({{ $i }})">
                            <div class="field-error" id="sk-err-telp-{{ $i }}"></div>
                        </div>
                        <div>
                            <input class="form-ctrl-full" id="sk-email-{{ $i }}" type="email" placeholder="email@sekolah.sch.id (opsional)" oninput="clearSekolahError({{ $i }})">
                            <div style="margin-top:4px">
                                <input class="form-ctrl-full" id="sk-ket-{{ $i }}" placeholder="Keterangan (opsional)" maxlength="500">
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

{{-- Modal: Hapus --}}
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

{{-- Modal: Import Excel --}}
<div id="modal-import" class="modal-backdrop" onclick="backdropClose(event,'modal-import')">
    <div class="modal" style="max-width:760px;max-height:90vh;display:flex;flex-direction:column" onclick="event.stopPropagation()">
        <div class="step-bar">
            <div class="step-item"><div class="step-dot active" id="sdot-1"><span>1</span></div><div class="step-label active" id="slbl-1">Pilih File</div></div>
            <div class="step-line" id="sline-1"></div>
            <div class="step-item"><div class="step-dot" id="sdot-2"><span>2</span></div><div class="step-label" id="slbl-2">Upload</div></div>
            <div class="step-line" id="sline-2"></div>
            <div class="step-item"><div class="step-dot" id="sdot-3"><span>3</span></div><div class="step-label" id="slbl-3">Validasi</div></div>
            <div class="step-line" id="sline-3"></div>
            <div class="step-item"><div class="step-dot" id="sdot-4"><span>4</span></div><div class="step-label" id="slbl-4">Review</div></div>
            <div class="step-line" id="sline-4"></div>
            <div class="step-item" style="flex:0"><div class="step-dot" id="sdot-5"><span>5</span></div><div class="step-label" id="slbl-5">Selesai</div></div>
        </div>
        <div style="overflow-y:auto;flex:1">
            <div id="panel-1" class="step-panel active">
                <div class="modal-body">
                    <div style="font-size:12px;color:var(--text-2);margin-bottom:14px;line-height:1.8;background:var(--blue-50);border:1px solid var(--blue-100);border-radius:var(--radius);padding:10px 14px">
                        <strong>Format file:</strong> .xlsx / .xls &nbsp;·&nbsp; <strong>Isi:</strong> kode provinsi di sel B3, data 9 sekolah mulai baris 10 &nbsp;·&nbsp; <strong>Maks.:</strong> 5 MB
                    </div>
                    <div class="drop-zone" id="drop-zone" onclick="document.getElementById('file-input').click()" ondragover="event.preventDefault();this.classList.add('drag-over')" ondragleave="this.classList.remove('drag-over')" ondrop="handleDrop(event)">
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
            <div id="panel-2" class="step-panel">
                <div class="modal-body">
                    <div style="font-size:13px;font-weight:500;margin-bottom:1rem" id="upload-filename">Mengunggah…</div>
                    <div class="progress-wrap">
                        <div class="progress-label"><span>Mengunggah ke server</span><span id="upload-pct">0%</span></div>
                        <div class="progress-track"><div class="progress-bar" id="upload-bar"></div></div>
                    </div>
                    <div style="font-size:12px;color:var(--text-3);margin-top:8px" id="upload-size-info"></div>
                </div>
            </div>
            <div id="panel-3" class="step-panel">
                <div class="modal-body">
                    <div style="font-size:13px;font-weight:500;margin-bottom:1rem">Memproses file…</div>
                    <div class="proc-steps">
                        <div class="proc-step">
                            <div class="proc-step-icon waiting" id="ps-read-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><rect x="2" y="2" width="12" height="12" rx="1"/><path d="M5 6h6M5 9h4"/></svg></div>
                            <div class="proc-step-text"><div class="proc-step-title">Membaca file Excel</div><div class="proc-step-sub" id="ps-read-sub">Menunggu…</div></div>
                        </div>
                        <div class="proc-step">
                            <div class="proc-step-icon waiting" id="ps-prov-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><circle cx="8" cy="6" r="3"/><path d="M2 14c0-3 2.7-5 6-5s6 2 6 5"/></svg></div>
                            <div class="proc-step-text"><div class="proc-step-title">Deteksi kode provinsi</div><div class="proc-step-sub" id="ps-prov-sub">Menunggu…</div></div>
                        </div>
                        <div class="proc-step">
                            <div class="proc-step-icon waiting" id="ps-validate-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg></div>
                            <div class="proc-step-text"><div class="proc-step-title">Validasi 9 sekolah</div><div class="proc-step-sub" id="ps-validate-sub">Menunggu…</div></div>
                        </div>
                    </div>
                    <div id="panel3-err" class="alert alert-error" style="margin-top:1rem;display:none"></div>
                </div>
            </div>
            <div id="panel-4" class="step-panel">
                <div class="modal-body">
                    <div id="prov-info-card" class="prov-card valid" style="display:none">
                        <div class="prov-icon" id="prov-info-icon">✅</div>
                        <div><div class="prov-card-title" id="prov-info-title"></div><div class="prov-card-sub" id="prov-info-sub"></div></div>
                    </div>
                    <div id="prov-warning-card" class="prov-card warning" style="display:none">
                        <div class="prov-icon">⚠️</div>
                        <div><div class="prov-card-title">Provinsi sudah terdaftar</div><div class="prov-card-sub" id="prov-warning-sub"></div></div>
                    </div>
                    <div id="file-errors-box" style="display:none;margin-bottom:.75rem"></div>
                    <div class="review-toolbar">
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <span class="badge badge-valid" id="rv-valid">0 valid</span>
                            <span class="badge badge-invalid" id="rv-invalid">0 invalid</span>
                        </div>
                        <div id="action-selector" style="display:none;align-items:center;gap:8px;font-size:12px">
                            <label style="font-weight:600;color:var(--text-2)">Aksi:</label>
                            <label style="display:flex;align-items:center;gap:4px;cursor:pointer"><input type="radio" name="import-action" value="insert" checked> Tambah Baru</label>
                            <label style="display:flex;align-items:center;gap:4px;cursor:pointer"><input type="radio" name="import-action" value="replace"> Timpa</label>
                        </div>
                    </div>
                    <div class="review-table-wrap">
                        <table class="review-table">
                            <thead><tr>
                                <th style="width:40px">No.</th>
                                <th style="width:70px">Kode</th>
                                <th>Nama Sekolah</th>
                                <th style="width:150px">Telepon</th>
                                <th style="width:180px">Email</th>
                                <th style="width:70px;text-align:center">Status</th>
                            </tr></thead>
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
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function delay(ms){ return new Promise(r => setTimeout(r, ms)); }
function showAlert(type, msg, ms=5000){
    const el = document.getElementById('alert-box');
    el.className = 'alert alert-' + type; el.textContent = msg; el.style.display = 'block';
    clearTimeout(el._t); el._t = setTimeout(() => el.style.display='none', ms);
}
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
function openModal(id){ document.getElementById(id).classList.add('open'); }
function backdropClose(e, id){ if(e.target.id === id) closeModal(id); }
function filterTable(q){
    q = q.toLowerCase().trim();
    const rows = [...document.querySelectorAll('#table-body tr[data-id]')];
    let vis = 0;
    rows.forEach(r => {
        const match = !q || r.dataset.provinsi.toLowerCase().includes(q) || r.dataset.kode.includes(q);
        r.style.display = match ? '' : 'none';
        const exp = document.getElementById('expand-' + r.dataset.id);
        if(exp) exp.style.display = 'none';
        if(match){ r.querySelector('td').textContent = ++vis; }
    });
    document.getElementById('count-info').textContent = q ? `${vis} dari ${rows.length}` : '';
    document.getElementById('table-footer-info').textContent = `Menampilkan ${vis || rows.length} provinsi`;
}
function changeTahun(t){ window.location.href = '{{ route("lomba-provinsi.index") }}?tahun=' + t; }
function toggleExpand(id){
    const exp = document.getElementById('expand-' + id); if(!exp) return;
    const isOpen = exp.style.display !== 'none';
    document.querySelectorAll('.expand-row').forEach(r => r.style.display = 'none');
    if(!isOpen) exp.style.display = '';
}
document.getElementById('f-provinsi').addEventListener('change', function(){
    const kode = this.options[this.selectedIndex].getAttribute('data-kode') || '??';
    for(let i = 1; i <= 9; i++) document.getElementById('sk-kode-' + i).textContent = kode + i;
});
function openAdd(){
    editingId = null;
    document.getElementById('form-modal-title').textContent = 'Tambah Provinsi Lomba';
    document.getElementById('f-provinsi').value = '';
    document.getElementById('grp-provinsi').style.display = '';
    for(let i = 1; i <= 9; i++){
        document.getElementById('sk-kode-' + i).textContent = '??' + i;
        ['nama','telp','email','ket'].forEach(f => document.getElementById('sk-' + f + '-' + i).value = '');
    }
    clearFormErrors(); openModal('modal-form');
    setTimeout(() => document.getElementById('f-provinsi').focus(), 100);
}
let editingId = null;
async function openEdit(id){
    editingId = id;
    document.getElementById('form-modal-title').textContent = 'Edit Data Sekolah';
    document.getElementById('grp-provinsi').style.display = 'none';
    clearFormErrors(); openModal('modal-form');
    try {
        const res  = await fetch(ROUTES.show.replace(':id', id), { headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF } });
        const json = await res.json();
        if(!json.success) throw new Error(json.message);
        const d = json.data; const kode = d.provinsi?.kode || '??';
        document.getElementById('f-tahun').value = d.tahun;
        d.sekolah.forEach((s, i) => {
            const n = i + 1;
            document.getElementById('sk-kode-' + n).textContent = s.kode_sekolah || (kode + n);
            document.getElementById('sk-nama-' + n).value  = s.nama_sekolah  || '';
            document.getElementById('sk-telp-' + n).value  = s.nomor_telepon || '';
            document.getElementById('sk-email-' + n).value = s.email         || '';
            document.getElementById('sk-ket-' + n).value   = s.keterangan    || '';
        });
        for(let n = d.sekolah.length + 1; n <= 9; n++){
            document.getElementById('sk-kode-' + n).textContent = kode + n;
            ['nama','telp','email','ket'].forEach(f => document.getElementById('sk-' + f + '-' + n).value = '');
        }
    } catch(e){ showAlert('error', 'Gagal memuat data: ' + e.message); closeModal('modal-form'); }
}
function clearFormErrors(){
    document.querySelectorAll('.field-error').forEach(el => { el.style.display = 'none'; el.textContent = ''; });
    document.querySelectorAll('.is-error').forEach(el => el.classList.remove('is-error'));
    document.getElementById('form-alert').style.display = 'none';
}
function clearSekolahError(n){
    ['nama','telp','email'].forEach(k => { const el = document.getElementById(`sk-err-${k}-${n}`); if(el){ el.style.display='none'; el.textContent=''; } });
}
function collectSekolah(){
    const list = [];
    for(let i = 1; i <= 9; i++){
        list.push({ kode_sekolah: document.getElementById('sk-kode-' + i).textContent, nama_sekolah: document.getElementById('sk-nama-' + i).value.trim(), nomor_telepon: document.getElementById('sk-telp-' + i).value.trim(), email: document.getElementById('sk-email-' + i).value.trim(), keterangan: document.getElementById('sk-ket-' + i).value.trim() });
    }
    return list;
}
async function submitForm(){
    clearFormErrors();
    const btn = document.getElementById('btn-save-form'); btn.disabled = true;
    const sekolah = collectSekolah();
    const provinsiId = document.getElementById('f-provinsi').value;
    const body = editingId ? { sekolah } : { provinsi_id: provinsiId, sekolah };
    const url = editingId ? ROUTES.update.replace(':id', editingId) : ROUTES.store;
    const method = editingId ? 'PUT' : 'POST';
    try {
        const res = await fetch(url, { method, headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' }, body: JSON.stringify(body) });
        const json = await res.json();
        if(json.success){ showAlert('success', json.message); closeModal('modal-form'); location.reload(); }
        else if(json.errors){
            Object.entries(json.errors).forEach(([key, msgs]) => {
                const m = key.match(/sekolah\.(\d+)\.(\w+)/);
                if(m){ const n = parseInt(m[1])+1; const k = m[2]==='nama_sekolah'?'nama':m[2]==='nomor_telepon'?'telp':'email'; const el = document.getElementById(`sk-err-${k}-${n}`); if(el){el.textContent=msgs[0];el.style.display='block';} }
                else if(key==='provinsi_id'){ const el=document.getElementById('err-provinsi'); el.textContent=msgs[0]; el.style.display='block'; }
                else { const fa=document.getElementById('form-alert'); fa.className='alert alert-error'; fa.textContent=msgs.join(', '); fa.style.display='block'; }
            });
        } else { const fa=document.getElementById('form-alert'); fa.className='alert alert-error'; fa.textContent=json.message||'Gagal menyimpan.'; fa.style.display='block'; }
    } catch(e){ const fa=document.getElementById('form-alert'); fa.className='alert alert-error'; fa.textContent='Koneksi ke server gagal.'; fa.style.display='block'; }
    finally { btn.disabled = false; }
}
let deleteId = null;
function openDelete(id, nama){ deleteId = id; document.getElementById('del-msg').innerHTML = `Anda akan menghapus data lomba provinsi <strong>${esc(nama)}</strong> beserta seluruh data sekolahnya. Tindakan ini tidak dapat dibatalkan.`; openModal('modal-delete'); }
async function confirmDelete(){
    if(!deleteId) return;
    const btn = document.getElementById('btn-confirm-delete'); btn.disabled = true;
    try {
        const res = await fetch(ROUTES.destroy.replace(':id', deleteId), { method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} });
        const json = await res.json();
        if(json.success){ showAlert('success', json.message); closeModal('modal-delete'); document.getElementById('row-'+deleteId)?.remove(); document.getElementById('expand-'+deleteId)?.remove(); refreshStats(); }
        else showAlert('error', json.message||'Gagal menghapus.');
    } catch(e){ showAlert('error','Koneksi gagal.'); }
    finally { btn.disabled = false; deleteId = null; }
}
function refreshStats(){
    const rows = [...document.querySelectorAll('#table-body tr[data-id]')];
    document.getElementById('stat-provinsi').textContent = rows.length;
    document.getElementById('table-footer-info').textContent = `Menampilkan ${rows.length} provinsi`;
}
// IMPORT
let importData = null;
function openImport(){ resetImport(); openModal('modal-import'); }
function goStep(n){
    for(let i=1;i<=5;i++){
        const dot=document.getElementById('sdot-'+i), lbl=document.getElementById('slbl-'+i), pan=document.getElementById('panel-'+i), ln=document.getElementById('sline-'+i);
        if(i<n){ dot.className='step-dot done'; lbl.className='step-label done'; if(ln) ln.className='step-line done'; }
        else if(i===n){ dot.className='step-dot active'; lbl.className='step-label active'; }
        else { dot.className='step-dot'; lbl.className='step-label'; }
        pan.className='step-panel'+(i===n?' active':'');
    }
}
function resetImport(){ importData=null; document.getElementById('file-input').value=''; document.getElementById('panel3-err').style.display='none'; document.getElementById('panel4-err').style.display='none'; document.getElementById('review-body').innerHTML=''; goStep(1); }
function handleDrop(e){ e.preventDefault(); document.getElementById('drop-zone').classList.remove('drag-over'); const file=e.dataTransfer.files[0]; if(file) startUpload(file); }
async function startUpload(file){
    if(!file) return;
    if(file.size > 5*1024*1024){ alert('File terlalu besar. Maksimal 5 MB.'); return; }
    goStep(2); document.getElementById('upload-filename').textContent = file.name; document.getElementById('upload-size-info').textContent = (file.size/1024).toFixed(1)+' KB';
    const bar=document.getElementById('upload-bar'), pct=document.getElementById('upload-pct');
    let fakeP=0; const fake=setInterval(()=>{ fakeP=Math.min(fakeP+Math.random()*15,80); bar.style.width=fakeP+'%'; pct.textContent=Math.floor(fakeP)+'%'; },120);
    const fd=new FormData(); fd.append('file',file); fd.append('_token',CSRF);
    try {
        const res=await fetch(ROUTES.preview,{method:'POST',body:fd}); clearInterval(fake); bar.style.width='100%'; pct.textContent='100%'; await delay(300);
        goStep(3); await runValidationAnimation(await res.json());
    } catch(e){ clearInterval(fake); goStep(3); document.getElementById('panel3-err').textContent='Upload gagal: '+e.message; document.getElementById('panel3-err').style.display='block'; }
}
async function runValidationAnimation(json){
    const setStep=(id,status,sub)=>{ const icon=document.getElementById(id+'-icon'), subEl=document.getElementById(id+'-sub'); if(status==='running') icon.innerHTML='<div class="spinner"></div>'; else if(status==='done') icon.innerHTML='<span class="check-icon">✓</span>'; else if(status==='error') icon.innerHTML='<span class="x-icon">✗</span>'; icon.className='proc-step-icon '+(status==='done'?'done':status==='error'?'error':'running'); if(subEl) subEl.textContent=sub; };
    setStep('ps-read','running','Membaca struktur workbook…'); await delay(400);
    if(!json.success){ setStep('ps-read','error',json.message); document.getElementById('panel3-err').textContent=json.message; document.getElementById('panel3-err').style.display='block'; return; }
    setStep('ps-read','done','Berhasil membaca file.');
    setStep('ps-prov','running','Mencari kode provinsi di sel B3…'); await delay(400);
    if(!json.provinsi_valid){ setStep('ps-prov','error',`Kode "${json.kode_provinsi}" tidak ditemukan.`); document.getElementById('panel3-err').textContent=`Kode provinsi "${json.kode_provinsi}" tidak valid.`; document.getElementById('panel3-err').style.display='block'; return; }
    setStep('ps-prov','done',`Provinsi: ${json.provinsi.kode} · ${json.provinsi.nama}`);
    setStep('ps-validate','running','Memvalidasi 9 baris sekolah…'); await delay(500);
    if(json.file_errors&&json.file_errors.length){ setStep('ps-validate','error',json.file_errors[0]); document.getElementById('panel3-err').textContent=json.file_errors[0]; document.getElementById('panel3-err').style.display='block'; return; }
    setStep('ps-validate','done',`${json.valid_count} valid, ${json.invalid_count} invalid.`); await delay(300);
    importData=json; buildReviewPanel(json); goStep(4);
}
function buildReviewPanel(json){
    const card=document.getElementById('prov-info-card'); card.className='prov-card valid'; card.style.display='flex';
    document.getElementById('prov-info-title').textContent=`${json.provinsi.kode} · ${json.provinsi.nama}`;
    document.getElementById('prov-info-sub').textContent=`Tahun: ${json.tahun}`;
    const wcard=document.getElementById('prov-warning-card');
    if(json.sudah_terdaftar){ wcard.style.display='flex'; document.getElementById('prov-warning-sub').textContent='Data sekolah sudah ada. Pilih "Timpa" untuk mengganti.'; document.getElementById('action-selector').style.display='flex'; }
    else { wcard.style.display='none'; document.getElementById('action-selector').style.display='none'; }
    const feBox=document.getElementById('file-errors-box');
    if(json.file_errors&&json.file_errors.length){ feBox.style.display='block'; feBox.innerHTML=json.file_errors.map(e=>`<div class="alert alert-error" style="margin-bottom:4px;display:block">${esc(e)}</div>`).join(''); }
    else feBox.style.display='none';
    document.getElementById('rv-valid').textContent=json.valid_count+' valid';
    document.getElementById('rv-invalid').textContent=json.invalid_count+' invalid';
    const tbody=document.getElementById('review-body'); tbody.innerHTML='';
    json.sekolah.forEach(s=>{ const isValid=s.status==='valid'; const tr=document.createElement('tr'); tr.className='row-'+s.status; tr.innerHTML=`<td style="text-align:center;font-weight:600">${esc(s.urutan)}</td><td><span class="badge-kode" style="font-size:11px">${esc(s.kode_sekolah)}</span></td><td>${esc(s.nama_sekolah)}${s.errors.length?'<div class="error-tags">'+s.errors.map(e=>`<span class="error-tag">${esc(e)}</span>`).join('')+'</div>':''}</td><td style="color:var(--text-2)">${esc(s.nomor_telepon||'—')}</td><td style="color:var(--text-2)">${esc(s.email||'—')}</td><td style="text-align:center">${isValid?'<span class="check-icon">✓</span>':'<span class="x-icon">✗</span>'}</td>`; tbody.appendChild(tr); });
    const allValid=json.valid_count===9&&json.invalid_count===0&&!json.file_errors?.length;
    document.getElementById('btn-process').disabled=!allValid;
    if(!allValid&&json.invalid_count>0){ document.getElementById('panel4-err').textContent=`Terdapat ${json.invalid_count} baris tidak valid.`; document.getElementById('panel4-err').style.display='block'; }
}
async function processImport(){
    if(!importData) return;
    const btn=document.getElementById('btn-process'); btn.disabled=true; btn.innerHTML='<div class="spinner" style="width:13px;height:13px;border-width:2px;border-top-color:#fff"></div> Menyimpan…';
    const action=document.querySelector('input[name="import-action"]:checked')?.value||'insert';
    try {
        const res=await fetch(ROUTES.save,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:JSON.stringify({provinsi_id:importData.provinsi.id,action,sekolah:importData.sekolah})});
        const json=await res.json();
        if(json.success){ goStep(5); document.getElementById('success-sub').textContent=json.message; document.getElementById('success-detail').innerHTML=`<div class="success-stat"><div class="success-stat-num" style="color:var(--green-600)">9</div><div class="success-stat-lbl">Sekolah disimpan</div></div><div class="success-stat"><div class="success-stat-num" style="color:var(--blue-600)">${importData.provinsi.nama}</div><div class="success-stat-lbl">Provinsi</div></div>`; }
        else { document.getElementById('panel4-err').textContent=json.message||'Gagal menyimpan.'; document.getElementById('panel4-err').style.display='block'; btn.disabled=false; btn.innerHTML='<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg> Simpan Data'; }
    } catch(e){ document.getElementById('panel4-err').textContent='Koneksi ke server gagal.'; document.getElementById('panel4-err').style.display='block'; btn.disabled=false; btn.innerHTML='<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg> Simpan Data'; }
}
</script>
@endpush
