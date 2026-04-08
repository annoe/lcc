{{-- resources/views/hasil-drawing/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Drawing Lomba — LCC MPR RI')

@section('content')
<div class="page">
    {{-- ── Page Header ──────────────────────────── --}}
    <div class="page-header">
        <div>
            <div class="page-title">Drawing Lomba</div>
            <div class="page-sub">Penentuan Babak Regu untuk Setiap Sekolah</div>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" id="btn-view-tree" onclick="viewTree()" style="display:none;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M3 8l3 3L13 4"/></svg>
                Lihat Tree Lomba
            </button>
        </div>
    </div>

    {{-- ── Alert ────────────────────────────────── --}}
    <div id="alert-box" class="alert"></div>

    {{-- ── Form Selection ────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Pilih Provinsi</div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label" for="provinsi-select">Provinsi</label>
                <select class="form-ctrl-full" id="provinsi-select" onchange="loadLombaData()">
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach($provinsis as $p)
                    <option value="{{ $p->id }}">{{ $p->kode }} - {{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Drawing Section (hidden by default) ───── --}}
    <div id="drawing-section" style="display:none;">
        {{-- Info Lomba --}}
        <div class="stats-row" id="lomba-info" style="margin-top:1.25rem;"></div>

        {{-- Table Sekolah --}}
        <div class="table-card">
            <div class="table-scroll">
                <table id="drawing-table">
                    <thead>
                        <tr>
                            <th style="width:40px">No.</th>
                            <th style="width:70px">Kode</th>
                            <th>Nama Sekolah</th>
                            <th style="width:180px">Babak Regu</th>
                            <th style="width:250px">Uraian</th>
                        </tr>
                    </thead>
                    <tbody id="drawing-table-body">
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <span id="fill-status">Belum ada pilihan</span>
                <button class="btn btn-primary" id="btn-save" onclick="saveDrawing()" disabled>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M3 8l3 3L13 4"/></svg>
                    Simpan Hasil Drawing
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tree View Section (hidden by default) ───── --}}
    <div id="tree-section" style="display:none; margin-top:1.5rem;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Tree Pertandingan</div>
            </div>
            <div class="card-body" id="tree-container">
                <!-- Tree will be rendered here -->
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeTree()">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal info babak regu --}}
<div id="babak-regu-modal" class="modal-backdrop" onclick="closeBabakReguModal(event)">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title">Informasi Babak Regu</div>
            <button class="modal-close" onclick="closeBabakReguModal()">&times;</button>
        </div>
        <div class="modal-body" id="babak-regu-modal-body">
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeBabakReguModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
// State global
let currentLomba = null;
let sekolahList = [];
let babakRegus = [];
let existingBabakReguIds = [];
let selections = {}; // { sekolah_id: lomba_babak_regu_id }
let drawingResults = []; // Store drawing results for tree view

const alertBox = document.getElementById('alert-box');
const drawingSection = document.getElementById('drawing-section');
const drawingTableBody = document.getElementById('drawing-table-body');
const btnSave = document.getElementById('btn-save');
const btnViewTree = document.getElementById('btn-view-tree');
const fillStatus = document.getElementById('fill-status');

function showAlert(message, type = 'error') {
    alertBox.className = `alert alert-${type}`;
    alertBox.textContent = message;
    alertBox.style.display = 'block';
    setTimeout(() => { alertBox.style.display = 'none'; }, 5000);
}

async function loadLombaData() {
    const provinsiId = document.getElementById('provinsi-select').value;
    
    if (!provinsiId) {
        drawingSection.style.display = 'none';
        currentLomba = null;
        return;
    }

    try {
        const response = await fetch(`/hasil-drawing/api/lomba/${provinsiId}`);
        const result = await response.json();

        if (!result.success) {
            showAlert(result.message || 'Gagal memuat data lomba');
            drawingSection.style.display = 'none';
            return;
        }

        const data = result.data;
        currentLomba = data.lomba;
        sekolahList = data.sekolah;
        babakRegus = data.babak_regus;
        existingBabakReguIds = data.existing_babak_regu_ids || [];

        // Initialize selections from existing drawings
        selections = {};
        drawingResults = [];
        sekolahList.forEach(s => {
            if (s.existing_drawing) {
                selections[s.id] = s.existing_drawing.lomba_babak_regu_id;
                // Build drawing results for tree view
                const babakRegu = babakRegus.find(b => b.id === s.existing_drawing.lomba_babak_regu_id);
                if (babakRegu) {
                    drawingResults.push({
                        sekolah_nama: s.nama_sekolah,
                        babak_regu: babakRegu.kode,
                        nomor: babakRegu.nomor,
                    });
                }
            }
        });
        
        // Sort drawing results by nomor
        drawingResults.sort((a, b) => a.nomor - b.nomor);

        renderLombaInfo();
        renderTable();
        drawingSection.style.display = 'block';
        
        // Show/hide tree button based on existing drawings
        const hasDrawings = Object.keys(selections).length > 0;
        btnViewTree.style.display = hasDrawings ? 'inline-flex' : 'none';
    } catch (error) {
        console.error(error);
        showAlert('Terjadi kesalahan saat memuat data lomba');
        drawingSection.style.display = 'none';
    }
}

function renderLombaInfo() {
    const infoDiv = document.getElementById('lomba-info');
    infoDiv.innerHTML = `
        <div class="stat-card">
            <div class="stat-label">Provinsi</div>
            <div class="stat-value" style="font-size:18px">${currentLomba.provinsi.nama}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tahun</div>
            <div class="stat-value" style="font-size:18px">${currentLomba.tahun}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Sekolah</div>
            <div class="stat-value" style="font-size:18px">${sekolahList.length}</div>
        </div>
    `;
}

function renderTable() {
    drawingTableBody.innerHTML = '';
    
    sekolahList.forEach((sekolah, index) => {
        const tr = document.createElement('tr');
        tr.id = `row-${sekolah.id}`;
        
        const selectedBabakReguId = selections[sekolah.id] || '';
        const selectedBabakRegu = babakRegus.find(b => b.id === selectedBabakReguId);
        const uraianText = selectedBabakRegu ? `${selectedBabakRegu.kode} - ${selectedBabakRegu.uraian}` : '-';
        
        // Build options for select, exclude already used babak regu (except current selection)
        let optionsHtml = '<option value="">-- Pilih --</option>';
        const availableBabakRegus = babakRegus.filter(b => 
            !Object.values(selections).includes(b.id) || b.id === selectedBabakReguId
        );
        
        availableBabakRegus.forEach(b => {
            const isSelected = b.id === selectedBabakReguId ? 'selected' : '';
            optionsHtml += `<option value="${b.id}" ${isSelected}>${b.nomor}. ${b.kode}</option>`;
        });

        tr.innerHTML = `
            <td style="text-align:center;color:var(--text-3)">${index + 1}</td>
            <td><span class="badge-kode">${sekolah.kode_sekolah}</span></td>
            <td style="font-weight:500">${sekolah.nama_sekolah}</td>
            <td>
                <select class="form-ctrl" onchange="onBabakReguChange('${sekolah.id}', this.value)" style="width:100%">
                    ${optionsHtml}
                </select>
            </td>
            <td style="font-size:12px;color:var(--text-2)">${uraianText}</td>
        `;
        
        drawingTableBody.appendChild(tr);
    });
    
    updateSaveButton();
}

function onBabakReguChange(sekolahId, babakReguId) {
    if (babakReguId) {
        // Check if this babak regu is already used by another school
        const usedByOther = Object.entries(selections).some(([sid, brid]) => 
            sid !== sekolahId && brid === babakReguId
        );
        
        if (usedByOther) {
            showAlert('Babak regu ini sudah dipilih oleh sekolah lain. Pilih babak regu yang berbeda.', 'error');
            // Reset to previous value or empty
            selections[sekolahId] = '';
            renderTable();
            return;
        }
        
        selections[sekolahId] = babakReguId;
    } else {
        delete selections[sekolahId];
    }
    
    renderTable();
    updateSaveButton();
}

function updateSaveButton() {
    const filledCount = Object.keys(selections).length;
    const totalCount = sekolahList.length;
    
    if (filledCount === totalCount) {
        fillStatus.innerHTML = `<span style="color:var(--green-600);font-weight:600">✓ Semua terisi (${filledCount}/${totalCount})</span>`;
        btnSave.disabled = false;
    } else {
        fillStatus.innerHTML = `<span style="color:var(--text-3)">Terisi ${filledCount} dari ${totalCount} sekolah</span>`;
        btnSave.disabled = true;
    }
}

async function saveDrawing() {
    const filledCount = Object.keys(selections).length;
    if (filledCount !== sekolahList.length) {
        showAlert('Semua sekolah harus dipilih babak regunya sebelum menyimpan.');
        return;
    }

    // Check for duplicates
    const selectedIds = Object.values(selections);
    const uniqueIds = [...new Set(selectedIds)];
    if (selectedIds.length !== uniqueIds.length) {
        showAlert('Terdapat duplikasi babak regu. Pastikan setiap sekolah memiliki babak regu yang berbeda.');
        return;
    }

    const drawings = Object.entries(selections).map(([sekolahId, babakReguId]) => ({
        sekolah_lomba_id: sekolahId,
        lomba_babak_regu_id: babakReguId,
    }));

    try {
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner"></span> Menyimpan...';

        const response = await fetch('/hasil-drawing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                lomba_provinsi_id: currentLomba.id,
                tahun: currentLomba.tahun,
                drawings: drawings,
            }),
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            // Store drawing results for tree view
            drawingResults = sekolahList.map(s => {
                const babakRegu = babakRegus.find(b => b.id === selections[s.id]);
                return {
                    sekolah_nama: s.nama_sekolah,
                    babak_regu: babakRegu ? babakRegu.kode : '',
                    nomor: babakRegu ? babakRegu.nomor : 0,
                };
            }).sort((a, b) => a.nomor - b.nomor);
            
            // Reload to show updated data and enable tree button
            setTimeout(() => loadLombaData(), 1500);
        } else {
            showAlert(result.message || 'Gagal menyimpan hasil drawing', 'error');
            btnSave.disabled = false;
            btnSave.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M3 8l3 3L13 4"/></svg> Simpan Hasil Drawing';
        }
    } catch (error) {
        console.error(error);
        showAlert('Terjadi kesalahan saat menyimpan data', 'error');
        btnSave.disabled = false;
        btnSave.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16"><path d="M3 8l3 3L13 4"/></svg> Simpan Hasil Drawing';
    }
}

function closeBabakReguModal(event) {
    if (!event || event.target === document.getElementById('babak-regu-modal')) {
        document.getElementById('babak-regu-modal').classList.remove('open');
    }
}

// Tree view functions
function viewTree() {
    const treeSection = document.getElementById('tree-section');
    const treeContainer = document.getElementById('tree-container');
    
    if (drawingResults.length === 0) {
        showAlert('Belum ada data drawing untuk ditampilkan dalam tree.', 'error');
        return;
    }
    
    // Group by nomor (1, 2, 3 for Penyisihan 1, 2, 3)
    const groups = {};
    drawingResults.forEach(r => {
        if (!groups[r.nomor]) {
            groups[r.nomor] = [];
        }
        groups[r.nomor].push(r);
    });
    
    let html = '<div style="display:flex;gap:20px;flex-wrap:wrap;justify-content:center;">';
    
    // Sort by nomor and render each group
    const sortedNomors = Object.keys(groups).sort((a, b) => parseInt(a) - parseInt(b));
    
    sortedNomors.forEach(nomor => {
        const groupName = `Lomba Penyisihan ${nomor}`;
        const items = groups[nomor];
        
        html += `<div class="stat-card" style="min-width:280px;">`;
        html += `<div class="stat-label" style="margin-bottom:12px;text-align:center;font-size:13px;">${groupName}</div>`;
        html += `<div style="display:flex;flex-direction:column;gap:8px;">`;
        
        items.forEach((item, idx) => {
            html += `<div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:var(--blue-50);border-radius:6px;border:1px solid var(--blue-100);">`;
            html += `<span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:var(--blue-600);color:#fff;border-radius:50%;font-size:11px;font-weight:700;">${idx + 1}</span>`;
            html += `<div style="flex:1;">`;
            html += `<div style="font-size:12px;font-weight:600;color:var(--blue-800);">${item.sekolah_nama}</div>`;
            html += `<div style="font-size:10px;color:var(--text-3);">Regu ${item.babak_regu}</div>`;
            html += `</div>`;
            html += `</div>`;
        });
        
        html += `</div></div>`;
    });
    
    html += '</div>';
    
    treeContainer.innerHTML = html;
    treeSection.style.display = 'block';
    drawingSection.style.display = 'none';
}

function closeTree() {
    document.getElementById('tree-section').style.display = 'none';
    document.getElementById('drawing-section').style.display = 'block';
}
</script>
@endsection
