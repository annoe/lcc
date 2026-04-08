{{-- resources/views/jenis-babak/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Jenis Babak — LCC MPR RI')

@push('styles')
<style>
.page{max-width:900px;}
.form-group{margin-bottom:1.25rem;}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px;}
.form-hint{font-size:11px;color:var(--text-3);margin-top:4px;}
.field-error{font-size:11px;color:var(--red-600);margin-top:4px;display:none;}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center;z-index:1000;}
.modal-overlay.active{display:flex;}
.modal-card{background:var(--surface);border-radius:var(--radius-lg);width:100%;max-width:480px;box-shadow:var(--shadow-md);}
.modal-header{padding:1rem 1.25rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.modal-title{font-size:16px;font-weight:700;color:var(--blue-800);}
.modal-body{padding:1.25rem;}
.modal-footer{padding:1rem 1.25rem;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;}
.table-responsive{overflow-x:auto;}
.data-table{width:100%;border-collapse:collapse;font-size:13px;}
.data-table th{background:var(--gray-50);text-align:left;padding:10px 12px;font-size:11px;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid var(--border);}
.data-table td{padding:10px 12px;border-bottom:1px solid var(--border);vertical-align:middle;}
.data-table tr:hover{background:var(--gray-50);}
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;}
.badge-kode{background:var(--blue-50);color:var(--blue-800);border:1px solid var(--blue-100);min-width:42px;justify-content:center;font-family:monospace;}
.action-btn{background:none;border:none;padding:6px;cursor:pointer;border-radius:4px;color:var(--text-3);transition:all .15s;}
.action-btn:hover{background:var(--gray-100);color:var(--text);}
.action-btn.edit:hover{color:var(--blue-600);background:var(--blue-50);}
.action-btn.delete:hover{color:var(--red-600);background:var(--red-50);}
.empty-state{text-align:center;padding:2.5rem 1rem;color:var(--text-3);}
.empty-state svg{opacity:.4;margin-bottom:.75rem;}
</style>
@endpush

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <div class="page-title">Pengaturan Jenis Babak</div>
            <div class="page-sub">Kelola jenis babak kompetisi (Penyisihan 1, 2, 3, Final)</div>
        </div>
        <button class="btn btn-primary" onclick="openModal()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M8 3v10M3 8h10"/></svg>
            Tambah Jenis Babak
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="display:block;margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:80px;">Kode</th>
                            <th>Nama Jenis Babak</th>
                            <th style="width:100px;text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisBabaks as $jb)
                        <tr>
                            <td><span class="badge badge-kode">{{ $jb->kode }}</span></td>
                            <td>{{ $jb->nama }}</td>
                            <td style="text-align:center;">
                                <button class="action-btn edit" onclick="editItem('{{ $jb->id }}', '{{ $jb->kode }}', '{{ addslashes($jb->nama) }}')" title="Edit">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 3L4 12 3 13l1-1 9-9"/><path d="M11 5l2 2"/></svg>
                                </button>
                                <form action="{{ route('jenis-babak.destroy', $jb) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus jenis babak ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Hapus">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M3 4h10M6 4V2h4v2M5 4v9h6V4"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1"/><path d="M5 1v3M11 1v3M2 7h12"/></svg>
                                    <p>Belum ada jenis babak.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Form --}}
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Tambah Jenis Babak</div>
            <button class="action-btn" onclick="closeModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M3 3l10 10M13 3L3 13"/></svg>
            </button>
        </div>
        <form id="formJenisBabak" method="POST">
            @csrf
            <input type="hidden" id="jbId" name="_method" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="f-kode">Kode (4 karakter)</label>
                    <input class="form-ctrl" id="f-kode" name="kode" type="text" maxlength="4" placeholder="P1, P2, FN" style="width:100%;text-transform:uppercase;" required>
                    <div class="form-hint">Kode unik 4 karakter, contoh: P1, P2, P3, FN</div>
                    <div class="field-error" id="err-kode"></div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="f-nama">Nama Jenis Babak</label>
                    <input class="form-ctrl" id="f-nama" name="nama" type="text" maxlength="100" placeholder="Penyisihan 1" required>
                    <div class="field-error" id="err-nama"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const modalOverlay = document.getElementById('modalOverlay');
const form = document.getElementById('formJenisBabak');
const modalTitle = document.getElementById('modalTitle');
const jbIdField = document.getElementById('jbId');
const fKode = document.getElementById('f-kode');
const fNama = document.getElementById('f-nama');

function openModal() {
    modalTitle.textContent = 'Tambah Jenis Babak';
    form.action = "{{ route('jenis-babak.store') }}";
    jbIdField.value = 'POST';
    form.querySelector('[name="_method"]').value = 'POST';
    fKode.value = '';
    fNama.value = '';
    clearErrors();
    modalOverlay.classList.add('active');
    fKode.focus();
}

function closeModal() {
    modalOverlay.classList.remove('active');
    clearErrors();
}

function editItem(id, kode, nama) {
    modalTitle.textContent = 'Edit Jenis Babak';
    form.action = `/jenis-babak/${id}`;
    jbIdField.value = 'PUT';
    const methodField = form.querySelector('[name="_method"]');
    if (methodField) methodField.value = 'PUT';
    
    // Remove existing _method if any and add new one
    const existingMethod = form.querySelector('input[name="_method"]');
    if (existingMethod) existingMethod.remove();
    const newMethod = document.createElement('input');
    newMethod.type = 'hidden';
    newMethod.name = '_method';
    newMethod.value = 'PUT';
    form.appendChild(newMethod);
    
    fKode.value = kode;
    fNama.value = nama;
    clearErrors();
    modalOverlay.classList.add('active');
    fKode.focus();
}

function clearErrors() {
    ['kode', 'nama'].forEach(k => {
        const el = document.getElementById('err-' + k);
        const inp = document.getElementById('f-' + k);
        if (el) { el.style.display = 'none'; el.textContent = ''; }
        if (inp) inp.classList.remove('is-error');
    });
}

// Close modal on overlay click
modalOverlay.addEventListener('click', function(e) {
    if (e.target === modalOverlay) closeModal();
});

// Handle form submission
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();
    const btnSave = document.getElementById('btnSave');
    btnSave.disabled = true;

    const formData = new FormData(form);
    const method = formData.get('_method') || 'POST';
    
    try {
        const response = await fetch(form.action, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (response.ok) {
            window.location.reload();
        } else {
            const json = await response.json();
            if (json.errors) {
                Object.entries(json.errors).forEach(([key, msgs]) => {
                    const el = document.getElementById('err-' + key);
                    const inp = document.getElementById('f-' + key);
                    if (el) { el.textContent = msgs[0]; el.style.display = 'block'; }
                    if (inp) inp.classList.add('is-error');
                });
            } else {
                alert(json.message || 'Gagal menyimpan.');
            }
        }
    } catch (err) {
        alert('Koneksi ke server gagal.');
    } finally {
        btnSave.disabled = false;
    }
});
</script>
@endpush
