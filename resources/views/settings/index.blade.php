{{-- resources/views/settings/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Pengaturan — LCC MPR RI</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --blue-50:#E6F1FB;--blue-100:#B5D4F4;--blue-200:#85B7EB;--blue-400:#378ADD;--blue-600:#185FA5;--blue-800:#0C447C;--blue-900:#042C53;
    --teal-50:#E1F5EE;--teal-100:#9FE1CB;--teal-600:#0F6E56;--teal-800:#085041;
    --green-50:#EAF3DE;--green-100:#C0DD97;--green-600:#3B6D11;--green-800:#27500A;
    --red-50:#FCEBEB;--red-100:#F7C1C1;--red-600:#A32D2D;
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
.page{max-width:780px;margin:0 auto;padding:2rem 1.5rem;}
.page-header{margin-bottom:2rem;}
.page-title{font-size:20px;font-weight:700;color:var(--blue-800);}
.page-sub{font-size:12px;color:var(--text-3);margin-top:3px;}

/* ── Card ────────────────────────────────────── */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow);overflow:hidden;margin-bottom:1.5rem;}
.card-header{padding:1.1rem 1.5rem;border-bottom:1px solid var(--border);background:var(--gray-50);}
.card-title{font-size:14px;font-weight:600;color:var(--blue-800);}
.card-body{padding:1.5rem;}

/* ── Form ────────────────────────────────────── */
.form-group{margin-bottom:1.25rem;}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px;}
.form-ctrl{width:100%;padding:9px 12px;border:1px solid var(--border-md);border-radius:var(--radius);background:var(--surface);color:var(--text);font-size:14px;transition:border-color .15s,box-shadow .15s;}
.form-ctrl:focus{outline:none;border-color:var(--blue-400);box-shadow:0 0 0 3px rgba(55,138,221,.12);}
.form-hint{font-size:11px;color:var(--text-3);margin-top:4px;}
.field-error{font-size:11px;color:var(--red-600);margin-top:4px;display:none;}
.form-ctrl.is-error{border-color:var(--red-600);}

/* ── Buttons ─────────────────────────────────── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:var(--radius);border:1px solid var(--border-md);background:var(--surface);color:var(--text);font-size:13px;font-weight:500;cursor:pointer;transition:all .15s;text-decoration:none;}
.btn:hover{background:var(--gray-50);}
.btn-primary{background:var(--blue-600);color:#fff;border-color:var(--blue-600);}
.btn-primary:hover{background:var(--blue-800);border-color:var(--blue-800);}
.btn:disabled{opacity:.5;cursor:not-allowed;}

/* ── Alert ───────────────────────────────────── */
.alert{padding:11px 16px;border-radius:var(--radius);font-size:13px;margin-bottom:1.25rem;border:1px solid;display:none;}
.alert-success{background:var(--green-50);color:var(--green-600);border-color:var(--green-100);}
.alert-error{background:var(--red-50);color:var(--red-600);border-color:var(--red-100);}

/* ── Preview badge ───────────────────────────── */
.preview-badge{display:inline-flex;align-items:center;gap:6px;background:var(--blue-50);border:1px solid var(--blue-100);color:var(--blue-800);border-radius:6px;padding:4px 10px;font-size:12px;font-weight:600;margin-top:6px;}
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
        <a href="{{ route('lomba-provinsi.index') }}" class="topnav-item">Lomba Provinsi</a>
        <a href="{{ route('provinsi.index') }}" class="topnav-item">Master Provinsi</a>
        <a href="{{ route('settings.index') }}" class="topnav-item active">Pengaturan</a>
    </div>
</nav>

<div class="page">
    <div class="page-header">
        <div class="page-title">Pengaturan Umum</div>
        <div class="page-sub">Variabel default yang digunakan di seluruh modul LCC MPR RI</div>
    </div>

    <div id="alert-box" class="alert"></div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Variabel Default Kegiatan</div>
        </div>
        <div class="card-body">
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

            <div class="form-group" style="margin-bottom:0">
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

<script>
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
const URL_SAVE = '{{ route("settings.update") }}';

const origTahun = document.getElementById('f-tahun').value;
const origNama  = document.getElementById('f-nama').value;

document.getElementById('f-tahun').addEventListener('input', function(){
    document.getElementById('prev-tahun-val').textContent = this.value || '—';
});
document.getElementById('f-nama').addEventListener('input', function(){
    document.getElementById('prev-nama-val').textContent = this.value || '—';
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
    ['tahun','nama'].forEach(k => {
        const el = document.getElementById('err-' + k);
        el.style.display = 'none'; el.textContent = '';
        document.getElementById('f-' + k).classList.remove('is-error');
    });
}
function resetForm(){
    document.getElementById('f-tahun').value = origTahun;
    document.getElementById('f-nama').value  = origNama;
    document.getElementById('prev-tahun-val').textContent = origTahun;
    document.getElementById('prev-nama-val').textContent  = origNama;
    clearErrors();
}

async function saveSettings(){
    clearErrors();
    const tahun = document.getElementById('f-tahun').value.trim();
    const nama  = document.getElementById('f-nama').value.trim();
    const btn   = document.getElementById('btn-save');
    btn.disabled = true;

    try {
        const res  = await fetch(URL_SAVE, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ tahun_default: tahun, nama_kegiatan: nama }),
        });
        const json = await res.json();

        if(json.success){
            showAlert('success', json.message);
        } else if(json.errors){
            Object.entries(json.errors).forEach(([key, msgs]) => {
                const k   = key === 'tahun_default' ? 'tahun' : 'nama';
                const el  = document.getElementById('err-' + k);
                const inp = document.getElementById('f-' + k);
                el.textContent  = msgs[0];
                el.style.display = 'block';
                inp.classList.add('is-error');
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
</body>
</html>
