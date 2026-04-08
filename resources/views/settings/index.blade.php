{{-- resources/views/settings/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pengaturan — LCC MPR RI')

@push('styles')
<style>
.page{max-width:820px;}
.form-group{margin-bottom:1.25rem;}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px;}
.form-hint{font-size:11px;color:var(--text-3);margin-top:4px;}
.field-error{font-size:11px;color:var(--red-600);margin-top:4px;display:none;}
.param-info{display:flex;gap:6px;flex-wrap:wrap;margin-top:6px;align-items:center;font-size:11px;color:var(--text-3);}
</style>
@endpush

@section('content')
<div class="page" style="max-width:820px">
    <div class="page-header" style="margin-bottom:2rem;">
        <div>
            <div class="page-title">Pengaturan Umum</div>
            <div class="page-sub">Variabel default yang digunakan di seluruh modul LCC MPR RI</div>
        </div>
    </div>

    <div id="alert-box" class="alert"></div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Variabel Default Kegiatan</div>
        </div>
        <div class="card-body">

            {{-- Tahun Kegiatan --}}
            <div class="form-group">
                <label class="form-label">Tahun Kegiatan</label>
                <input class="form-ctrl" id="f-tahun" type="number" min="2000" max="2099"
                    value="{{ $settings['tahun_default']?->value ?? date('Y') }}"
                    placeholder="Contoh: {{ date('Y') }}">
                <div class="form-hint">{{ $settings['tahun_default']?->description }}</div>
                <div class="field-error" id="err-tahun"></div>
                <div class="preview-badge" id="prev-tahun">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1"/><path d="M5 1v3M11 1v3M2 7h12"/></svg>
                    Tahun aktif: <strong id="prev-tahun-val">{{ $settings['tahun_default']?->value ?? date('Y') }}</strong>
                </div>
            </div>

            {{-- Nama Kegiatan --}}
            <div class="form-group">
                <label class="form-label">Nama Kegiatan</label>
                <input class="form-ctrl" id="f-nama" type="text" maxlength="150"
                    value="{{ $settings['nama_kegiatan']?->value ?? '' }}"
                    placeholder="Contoh: Lomba Cerdas Cermat MPR RI">
                <div class="form-hint">{{ $settings['nama_kegiatan']?->description }}</div>
                <div class="field-error" id="err-nama"></div>
                <div class="preview-badge" id="prev-nama" style="max-width:100%">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M2 4h12M2 8h8M2 12h5"/></svg>
                    <span id="prev-nama-val" style="word-break:break-word">{{ $settings['nama_kegiatan']?->value ?? '' }}</span>
                </div>
            </div>

            {{-- Nama Kegiatan Default (template) --}}
            <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Nama Kegiatan Default</label>
                <input class="form-ctrl" id="f-nama-default" type="text" maxlength="300"
                    value="{{ $settings['nama_kegiatan_default']?->value ?? '' }}"
                    placeholder="Contoh: Lomba Cerdas Cermat MPR RI Tahun {{tahun}} Seleksi Provinsi, Provinsi {{provinsi}}">
                <div class="form-hint">
                    Template nama kegiatan lengkap. Gunakan parameter:
                    <span class="param-chip">{{"{{"}}tahun{{"}}"}}</span> untuk tahun aktif dan
                    <span class="param-chip">{{"{{"}}provinsi{{"}}"}}</span> untuk nama provinsi.
                </div>
                <div class="field-error" id="err-nama-default"></div>
                <div class="preview-badge" id="prev-nama-default" style="max-width:100%;margin-top:6px">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M3 4h10M3 8h7M3 12h4"/></svg>
                    <span id="prev-nama-default-val" style="word-break:break-word;font-weight:400">{{ $settings['nama_kegiatan_default']?->value ?? '' }}</span>
                </div>
            </div>

        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:8px">
        <button class="btn" onclick="resetForm()">Reset</button>
        <button class="btn btn-primary" id="btn-save" onclick="saveSettings()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M13 4L6 11 3 8"/></svg>
            Simpan Pengaturan
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const URL_SAVE = '{{ route("settings.update") }}';

const origTahun       = document.getElementById('f-tahun').value;
const origNama        = document.getElementById('f-nama').value;
const origNamaDefault = document.getElementById('f-nama-default').value;

document.getElementById('f-tahun').addEventListener('input', function(){
    document.getElementById('prev-tahun-val').textContent = this.value || '—';
});
document.getElementById('f-nama').addEventListener('input', function(){
    document.getElementById('prev-nama-val').textContent = this.value || '—';
});
document.getElementById('f-nama-default').addEventListener('input', function(){
    document.getElementById('prev-nama-default-val').textContent = this.value || '—';
});

function showAlert(type, msg){
    const el = document.getElementById('alert-box');
    el.className = 'alert alert-' + type;
    el.textContent = msg;
    el.style.display = 'block';
    clearTimeout(el._t);
    el._t = setTimeout(() => el.style.display = 'none', 5000);
}
function clearErrors(){
    ['tahun','nama','nama-default'].forEach(k => {
        const el  = document.getElementById('err-' + k);
        const inp = document.getElementById('f-' + k);
        el.style.display = 'none'; el.textContent = '';
        inp.classList.remove('is-error');
    });
}
function resetForm(){
    document.getElementById('f-tahun').value       = origTahun;
    document.getElementById('f-nama').value        = origNama;
    document.getElementById('f-nama-default').value = origNamaDefault;
    document.getElementById('prev-tahun-val').textContent        = origTahun;
    document.getElementById('prev-nama-val').textContent         = origNama;
    document.getElementById('prev-nama-default-val').textContent = origNamaDefault;
    clearErrors();
}

async function saveSettings(){
    clearErrors();
    const tahun       = document.getElementById('f-tahun').value.trim();
    const nama        = document.getElementById('f-nama').value.trim();
    const namaDefault = document.getElementById('f-nama-default').value.trim();
    const btn         = document.getElementById('btn-save');
    btn.disabled = true;

    try {
        const res  = await fetch(URL_SAVE, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ tahun_default: tahun, nama_kegiatan: nama, nama_kegiatan_default: namaDefault }),
        });
        const json = await res.json();

        if(json.success){
            showAlert('success', json.message);
        } else if(json.errors){
            Object.entries(json.errors).forEach(([key, msgs]) => {
                const map = { tahun_default: 'tahun', nama_kegiatan: 'nama', nama_kegiatan_default: 'nama-default' };
                const k   = map[key] || key;
                const el  = document.getElementById('err-' + k);
                const inp = document.getElementById('f-' + k);
                if(el){ el.textContent = msgs[0]; el.style.display = 'block'; }
                if(inp) inp.classList.add('is-error');
            });
        } else {
            showAlert('error', json.message || 'Gagal menyimpan.');
        }
    } catch(e){
        showAlert('error', 'Koneksi ke server gagal.');
    } finally {
        btn.disabled = false;
    }
}
</script>
@endpush
