<?php
// Pastikan file dropdown.php ini ada di /api/helpers/dropdown.php
require_once __DIR__ . '/../../../api/helpers/dropdown.php';
?>

<div class="flex flex-col mb-4 gap-4 shrink-0">
    <div class="flex justify-between items-center w-full">
        <div>
            <h2 class="text-2xl font-extrabold text-[#041020]">Kelola Master Cessie</h2>
            <p class="text-[11px] text-gray-500 mt-1">Daftar master nasabah (Otomatis via API).</p>
        </div>
        
        <div class="flex gap-2">
            <button onclick="toggleFilter()" class="md:hidden px-3 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-xs"><i class="fas fa-filter"></i></button>
            
            <button onclick="toggleRekap()" class="px-3 py-2 bg-indigo-50 text-indigo-600 rounded-lg font-bold text-xs hover:bg-indigo-100 flex items-center shadow-sm transition-all border border-indigo-100">
                <i class="fas fa-chart-pie md:mr-1"></i> <span class="hidden md:inline">Lihat Rekap</span>
            </button>
            
            <a href="<?= BASE_URL ?>/client/form_cessie" class="px-3 py-2 bg-blue-600 text-white rounded-lg font-bold text-xs hover:bg-blue-700 flex items-center shadow-sm transition-all">
                <i class="fas fa-plus md:mr-1"></i> <span class="hidden md:inline">Tambah Data</span>
            </a>
        </div>
    </div>
    
    <div id="filterContainer" class="hidden md:flex flex-col md:flex-row items-center gap-2 w-full bg-gray-50 md:bg-transparent p-3 md:p-0 rounded-xl md:rounded-none">
        
        <?php if($is_superadmin): ?>
        <select id="filterCabang" onchange="fetchData(1)" class="w-full md:w-auto px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-400">
            <?= renderDropdownCabang() ?>
        </select>
        <?php else: ?>
        <input type="hidden" id="filterCabang" value="<?= $user_kode ?>">
        <?php endif; ?>

        <select id="filterStatus" onchange="fetchData(1)" class="w-full md:w-auto px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">Semua Status</option>
            <option value="Draft">Draft</option>
            <option value="Review Legal">Review Legal</option>
            <option value="Siap Ditawarkan">Siap Ditawarkan</option>
            <option value="Diminati">Diminati</option>
        </select>
        
        <div class="relative w-full md:w-64 ml-auto">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="filterSearch" oninput="handleSearch()" placeholder="Cari nasabah / rekening..." class="w-full px-3 py-2 pl-8 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>

    <div id="rekapContainer" class="hidden flex-col gap-3 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mt-2">
        <h4 id="teksJudulRekap" class="text-xs font-extrabold text-indigo-600 uppercase border-b border-gray-100 pb-2">REKAP: SEMUA CABANG</h4>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="px-4 py-2 rounded-xl bg-blue-50/50 border border-blue-100 text-center">
                <p class="text-[10px] font-bold text-gray-500 uppercase">Total Baki Debet</p>
                <p id="r_bd" class="text-sm font-extrabold text-[#041020]">Rp0</p>
            </div>
            <div class="px-4 py-2 rounded-xl bg-green-50/50 border border-green-100 text-center">
                <p class="text-[10px] font-bold text-gray-500 uppercase">Total Agunan</p>
                <p id="r_ag" class="text-sm font-extrabold text-green-700">Rp0</p>
            </div>
            <div class="px-4 py-2 rounded-xl bg-purple-50/50 border border-purple-100 text-center">
                <p class="text-[10px] font-bold text-gray-500 uppercase">% BD Global</p>
                <p id="r_pbd" class="text-sm font-extrabold text-purple-700">0%</p>
            </div>
            <div class="px-4 py-2 rounded-xl bg-orange-50/50 border border-orange-100 text-center">
                <p class="text-[10px] font-bold text-gray-500 uppercase">% SB Global</p>
                <p id="r_psb" class="text-sm font-extrabold text-orange-700">0%</p>
            </div>
            <div class="px-4 py-2 rounded-xl bg-red-50/50 border border-red-100 text-center">
                <p class="text-[10px] font-bold text-gray-500 uppercase">% TT Global</p>
                <p id="r_ptt" class="text-sm font-extrabold text-red-700">0%</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col h-[calc(100vh-210px)] overflow-hidden">
    <div class="overflow-y-auto overflow-x-auto flex-1 relative no-scrollbar">
        <table class="w-full text-left border-collapse whitespace-nowrap min-w-max">
            
            <thead class="sticky top-0 bg-white z-[30] shadow-sm">
                <tr class="outline outline-1 outline-gray-100">
                    <th class="py-3.5 px-3 text-[10px] font-bold text-gray-400 uppercase bg-white">Cabang</th>
                    <th class="py-3.5 px-3 text-[10px] font-bold text-gray-400 uppercase sticky left-0 bg-white z-[35] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.08)]">Nasabah & Rekening</th>
                    <th class="py-3.5 px-3 text-[10px] font-bold text-gray-400 uppercase text-right bg-white">Baki Debet</th>
                    <th class="py-3.5 px-3 text-[10px] font-bold text-gray-400 uppercase text-right bg-white">Nilai Agunan</th>
                    <th class="py-3.5 px-3 text-[10px] font-bold text-blue-500 uppercase text-center bg-white">% BD</th>
                    <th class="py-3.5 px-3 text-[10px] font-bold text-blue-500 uppercase text-center bg-white">% SB</th>
                    <th class="py-3.5 px-3 text-[10px] font-bold text-blue-500 uppercase text-center bg-white">% TT</th>
                    <th class="py-3.5 px-3 text-[10px] font-bold text-gray-400 uppercase text-center bg-white">Status</th>
                    <th class="py-3.5 px-3 text-[10px] font-bold text-gray-400 uppercase text-center bg-white">Aksi</th>
                </tr>

                <tr id="rowTotal" class="hidden bg-yellow-50 outline outline-1 outline-yellow-200">
                    <td class="py-2 px-3 text-[10px] font-extrabold text-yellow-800 text-center bg-yellow-50">TOTAL:</td>
                    <td class="py-2 px-3 text-xs font-extrabold text-yellow-800 text-left sticky left-0 bg-yellow-50 z-[35] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.08)]">5 DATA DI HALAMAN INI</td>
                    <td id="t_bd" class="py-2 px-3 text-xs font-extrabold text-yellow-800 text-right bg-yellow-50">Rp0</td>
                    <td id="t_ag" class="py-2 px-3 text-xs font-extrabold text-yellow-800 text-right bg-yellow-50">Rp0</td>
                    <td id="t_pbd" class="py-2 px-3 text-xs font-extrabold text-center bg-yellow-50">0%</td>
                    <td id="t_psb" class="py-2 px-3 text-xs font-extrabold text-center bg-yellow-50">0%</td>
                    <td id="t_ptt" class="py-2 px-3 text-xs font-extrabold text-center bg-yellow-50">0%</td>
                    <td class="bg-yellow-50"></td>
                    <td class="bg-yellow-50"></td>
                </tr>
            </thead>

            <tbody id="tableBody" class="divide-y divide-gray-50 relative">
                <tr><td colspan="9" class="py-10 text-center"><i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i><p class="mt-2 text-xs text-gray-500">Memuat Data API...</p></td></tr>
            </tbody>
        </table>
    </div>

    <div id="paginationContainer" class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50 shrink-0 hidden">
        <p class="text-xs text-gray-500 font-medium">Halaman <b id="lblCurrentPage" class="text-gray-800">1</b> dari <b id="lblTotalPages" class="text-gray-800">1</b></p>
        <div class="flex gap-1">
            <button id="btnPrev" onclick="changePage(-1)" class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 disabled:opacity-50"><i class="fas fa-chevron-left"></i> Prev</button>
            <button id="btnNext" onclick="changePage(1)" class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 disabled:opacity-50">Next <i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<div id="detailModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden transform scale-95 transition-transform" id="modalContent">
        <div class="bg-[#041020] p-5 flex justify-between items-center">
            <h3 class="text-white font-extrabold text-lg"><i class="fas fa-user-circle text-yellow-400 mr-2"></i> Detail Nasabah</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-6 grid grid-cols-2 gap-y-4 gap-x-6">
            <div class="col-span-2 md:col-span-1"><p class="text-[10px] font-bold text-gray-400 uppercase">Nama Nasabah</p><p id="m_nasabah" class="font-bold text-sm text-gray-900">-</p></div>
            <div class="col-span-2 md:col-span-1"><p class="text-[10px] font-bold text-gray-400 uppercase">No Rekening</p><p id="m_rek" class="font-bold text-sm text-gray-900">-</p></div>
            <div class="col-span-2"><p class="text-[10px] font-bold text-gray-400 uppercase">Cabang / Kantor</p><p id="m_cabang" class="font-bold text-sm text-gray-900">-</p></div>
            <div class="col-span-2"><p class="text-[10px] font-bold text-gray-400 uppercase">Alamat</p><p id="m_alamat" class="text-xs text-gray-700">-</p></div>
            <div class="col-span-2 my-2 border-t border-gray-100"></div>
            <div class="col-span-2 md:col-span-1"><p class="text-[10px] font-bold text-gray-400 uppercase">Baki Debet</p><p id="m_bd" class="font-bold text-sm text-red-600">-</p></div>
            <div class="col-span-2 md:col-span-1"><p class="text-[10px] font-bold text-gray-400 uppercase">Nilai Agunan</p><p id="m_agunan" class="font-bold text-sm text-green-600">-</p></div>
            <div class="col-span-2 md:col-span-1"><p class="text-[10px] font-bold text-gray-400 uppercase">Kolektibilitas</p><p id="m_kolek" class="font-bold text-sm text-gray-900">-</p></div>
            <div class="col-span-2 md:col-span-1"><p class="text-[10px] font-bold text-gray-400 uppercase">Status</p><p id="m_status" class="font-bold text-sm text-gray-900">-</p></div>
        </div>
        <div class="p-4 bg-gray-50 text-right border-t border-gray-100">
            <button onclick="closeModal()" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold text-xs">Tutup</button>
        </div>
    </div>
<!-- Modal Hapus Data -->
<div id="deleteModal" class="fixed inset-0 bg-black/60 z-[60] hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
    <div id="deleteModalContent" class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 transform scale-95 transition-transform duration-200">
        <div class="w-16 h-16 mx-auto rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-4">
            <i class="fas fa-exclamation-triangle text-2xl"></i>
        </div>
        <h3 class="text-center font-extrabold text-gray-900 text-lg mb-2">Hapus Data?</h3>
        <p class="text-center text-sm text-gray-500 mb-6">Data yang dihapus tidak bisa dikembalikan. Lanjutkan?</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors">Batal</button>
            <button id="btnConfirmDelete" class="flex-1 py-2.5 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition-colors shadow-lg shadow-red-500/30">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
const isSuperadmin = <?= $is_superadmin ? 'true' : 'false' ?>;
let debounceTimer;

// Helper Format Uang
function formatUangRingkas(angka) {
    if (angka >= 1000000000) return 'Rp' + (angka / 1000000000).toFixed(2) + 'M';
    if (angka >= 1000000) return 'Rp' + (angka / 1000000).toFixed(2) + 'Jt';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
}
function rp(angka) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka); }

// UI Toggles
function toggleFilter() { document.getElementById('filterContainer').classList.toggle('hidden'); }
function toggleRekap() {
    const rContainer = document.getElementById('rekapContainer');
    if(rContainer.classList.contains('hidden')) {
        rContainer.classList.remove('hidden'); rContainer.classList.add('flex');
    } else {
        rContainer.classList.add('hidden'); rContainer.classList.remove('flex');
    }
}

// Fitur Search (Anti Muter-Muter)
function handleSearch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => fetchData(1), 500); 
}

// FUNGSI UTAMA: TARIK DATA
function fetchData(page = 1) {
    currentPage = page;
    const cabang = document.getElementById('filterCabang')?.value || '';
    const status = document.getElementById('filterStatus').value;
    const keyword = document.getElementById('filterSearch').value;

    // Tampilkan Loading
    document.getElementById('tableBody').innerHTML = '<tr><td colspan="9" class="py-10 text-center"><i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i><p class="mt-2 text-xs text-gray-500">Mencari Data...</p></td></tr>';
    document.getElementById('rowTotal').classList.add('hidden');
    document.getElementById('paginationContainer').classList.add('hidden');

    const url = `<?= API_URL ?>/cessie/list?page=${page}&limit=5&status=${encodeURIComponent(status)}&kode_kantor=${encodeURIComponent(cabang)}&keyword=${encodeURIComponent(keyword)}`;

    fetch(url)
    .then(async res => {
        if(!res.ok) throw new Error("HTTP Error " + res.status);
        return res.json();
    })
    .then(res => {
        renderTable(res.data.data);
        renderGlobalRekap(res.data.rekap);
        renderPagination(res.data.pagination);
    })
    .catch(err => {
        console.error("Gagal API:", err);
        // Error Handler Biar Gak Muter Terus
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="9" class="py-10 text-center text-red-500"><i class="fas fa-exclamation-triangle text-3xl mb-2"></i><br><p class="text-sm font-bold">Gagal mengambil data.</p><p class="text-xs">Periksa Backend / Koneksi Anda.</p></td></tr>`;
    });
}

// RENDER BARIS TABEL
function renderTable(data) {
    const tbody = document.getElementById('tableBody');
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="py-10 text-center text-gray-500 text-sm"><i class="fas fa-folder-open text-3xl mb-2 text-gray-300"></i><br>Data Cessie tidak ditemukan.</td></tr>';
        return;
    }

    let html = '';
    let pageBd = 0, pageSb = 0, pageTt = 0, pageAg = 0;

    data.forEach(row => {
        pageBd += parseFloat(row.baki_debet) || 0;
        pageSb += parseFloat(row.saldo_bank) || 0;
        pageTt += parseFloat(row.totung) || 0;
        pageAg += parseFloat(row.nilai_agunan) || 0;

        const inisial = row.nama_nasabah.substring(0, 2).toUpperCase();
        const pct_bd = row.baki_debet > 0 ? (row.nilai_agunan / row.baki_debet) * 100 : 0;
        const pct_sb = row.saldo_bank > 0 ? (row.nilai_agunan / row.saldo_bank) * 100 : 0;
        const pct_tt = row.totung > 0 ? (row.nilai_agunan / row.totung) * 100 : 0;

        let stClass = "bg-gray-50 text-gray-600 border-gray-200";
        if(row.status === 'Siap Ditawarkan') stClass = "bg-green-50 text-green-600 border-green-200";
        else if(row.status === 'Review Legal') stClass = "bg-yellow-50 text-yellow-600 border-yellow-200";
        else if(row.status === 'Diminati') stClass = "bg-blue-50 text-blue-600 border-blue-200";

        const btnDel = isSuperadmin ? `<button onclick="openDeleteModal(${row.id})" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus"><i class="fas fa-trash"></i></button>` : '';
        const rowJson = JSON.stringify(row).replace(/"/g, '&quot;');

        html += `
        <tr class="group hover:bg-[#f4f7fb]/60 transition-colors">
            <td class="py-3 px-3">
                <p class="text-xs font-bold text-[#041020]">${row.nama_kantor}</p>
                <p class="text-[10px] text-gray-500">Kode: ${row.kode_kantor}</p>
            </td>
            
            <td class="py-3 px-3 sticky left-0 bg-white z-[10] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.08)] group-hover:bg-[#f8fafc] transition-colors">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-600 font-bold text-[10px] flex items-center justify-center shrink-0">${inisial}</div>
                    <div>
                        <p class="text-xs font-bold text-[#041020] whitespace-nowrap">${row.nama_nasabah}</p>
                        <p class="text-[9px] text-gray-500 whitespace-nowrap">Rek: ${row.no_rekening} • Kolek: ${row.kolektibilitas}</p>
                    </div>
                </div>
            </td>
            
            <td class="py-3 px-3 text-xs font-bold text-[#041020] text-right">${formatUangRingkas(row.baki_debet)}</td>
            <td class="py-3 px-3 text-xs font-bold text-green-600 text-right">${formatUangRingkas(row.nilai_agunan)}</td>
            <td class="py-3 px-3 text-xs font-bold text-center ${pct_bd >= 100 ? 'text-green-600' : 'text-red-500'}">${pct_bd.toFixed(2)}%</td>
            <td class="py-3 px-3 text-xs font-bold text-center ${pct_sb >= 100 ? 'text-green-600' : 'text-red-500'}">${pct_sb.toFixed(2)}%</td>
            <td class="py-3 px-3 text-xs font-bold text-center ${pct_tt >= 100 ? 'text-green-600' : 'text-red-500'}">${pct_tt.toFixed(2)}%</td>
            <td class="py-3 px-3 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-extrabold border ${stClass} whitespace-nowrap">${row.status}</span>
            </td>
            <td class="py-3 px-3 text-center space-x-2 whitespace-nowrap">
                <button onclick="openDetailModal(${rowJson})" class="text-slate-500 hover:text-blue-600" title="Lihat Detail"><i class="fas fa-eye"></i></button>
                <a href="<?= BASE_URL ?>/client/agunan?id_cessie=${row.id}" class="text-blue-500 hover:text-blue-700" title="Kelola Agunan"><i class="fas fa-home"></i></a>
                <a href="<?= BASE_URL ?>/client/form_cessie?id=${row.id}" class="text-indigo-500 hover:text-indigo-700" title="Edit"><i class="fas fa-pen"></i></a>
                ${btnDel}
            </td>
        </tr>`;
    });
    tbody.innerHTML = html;

    // Render Total 5 Baris Halaman Ini
    const pagePctBd = pageBd > 0 ? (pageAg / pageBd) * 100 : 0;
    const pagePctSb = pageSb > 0 ? (pageAg / pageSb) * 100 : 0;
    const pagePctTt = pageTt > 0 ? (pageAg / pageTt) * 100 : 0;

    document.getElementById('t_bd').innerText = formatUangRingkas(pageBd);
    document.getElementById('t_ag').innerText = formatUangRingkas(pageAg);
    document.getElementById('t_pbd').innerText = pagePctBd.toFixed(2) + '%';
    document.getElementById('t_pbd').className = `py-2 px-3 text-xs font-extrabold text-center bg-yellow-50 text-${pagePctBd >= 100 ? 'green' : 'red'}-600`;
    document.getElementById('t_psb').innerText = pagePctSb.toFixed(2) + '%';
    document.getElementById('t_psb').className = `py-2 px-3 text-xs font-extrabold text-center bg-yellow-50 text-${pagePctSb >= 100 ? 'green' : 'red'}-600`;
    document.getElementById('t_ptt').innerText = pagePctTt.toFixed(2) + '%';
    document.getElementById('t_ptt').className = `py-2 px-3 text-xs font-extrabold text-center bg-yellow-50 text-${pagePctTt >= 100 ? 'green' : 'red'}-600`;

    document.getElementById('rowTotal').classList.remove('hidden');
}

// RENDER REKAP GLOBAL
function renderGlobalRekap(rekap) {
    if(!rekap) return;
    
    // Ambil teks dari dropdown filter Cabang
    const cabEl = document.getElementById('filterCabang');
    let namaCabang = "SEMUA CABANG";
    if(cabEl && cabEl.selectedIndex >= 0 && cabEl.value !== "") {
        namaCabang = cabEl.options[cabEl.selectedIndex].text.replace(/📍 |&nbsp;/g, '').trim();
    }
    document.getElementById('teksJudulRekap').innerText = `REKAP: ${namaCabang}`;

    document.getElementById('r_bd').innerText = formatUangRingkas(rekap.total_baki_debet);
    document.getElementById('r_ag').innerText = formatUangRingkas(rekap.total_nilai_agunan);
    document.getElementById('r_pbd').innerText = rekap.pct_bd + '%';
    document.getElementById('r_psb').innerText = rekap.pct_sb + '%';
    document.getElementById('r_ptt').innerText = rekap.pct_tt + '%';
}

// PAGINATION LOGIC
function renderPagination(pageData) {
    if (!pageData || pageData.total_pages <= 1) {
        document.getElementById('paginationContainer').classList.add('hidden');
        return;
    }
    document.getElementById('paginationContainer').classList.remove('hidden');
    document.getElementById('lblCurrentPage').innerText = pageData.current_page;
    document.getElementById('lblTotalPages').innerText = pageData.total_pages;

    document.getElementById('btnPrev').disabled = pageData.current_page <= 1;
    document.getElementById('btnNext').disabled = pageData.current_page >= pageData.total_pages;
}
function changePage(direction) { fetchData(currentPage + direction); }

// MODAL & DELETE
function openDetailModal(data) {
    document.getElementById('m_nasabah').innerText = data.nama_nasabah;
    document.getElementById('m_rek').innerText = data.no_rekening;
    document.getElementById('m_cabang').innerText = data.nama_kantor + ' (' + data.kode_kantor + ')';
    document.getElementById('m_alamat').innerText = data.alamat || '-';
    document.getElementById('m_kolek').innerText = data.kolektibilitas;
    document.getElementById('m_status').innerText = data.status;
    document.getElementById('m_bd').innerText = rp(data.baki_debet);
    document.getElementById('m_agunan').innerText = rp(data.nilai_agunan);

    const modal = document.getElementById('detailModal');
    modal.classList.remove('hidden');
    setTimeout(() => document.getElementById('modalContent').classList.remove('scale-95'), 10);
}
function closeModal() {
    document.getElementById('modalContent').classList.add('scale-95');
    setTimeout(() => document.getElementById('detailModal').classList.add('hidden'), 200);
}

let deleteTargetId = null;
function openDeleteModal(id) {
    deleteTargetId = id;
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    setTimeout(() => document.getElementById('deleteModalContent').classList.remove('scale-95'), 10);
}
function closeDeleteModal() {
    document.getElementById('deleteModalContent').classList.add('scale-95');
    setTimeout(() => document.getElementById('deleteModal').classList.add('hidden'), 200);
}

document.getElementById('btnConfirmDelete').addEventListener('click', function() {
    if(!deleteTargetId) return;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    this.disabled = true;
    
    fetch(`<?= API_URL ?>/cessie/delete?id=${deleteTargetId}`, { method: 'DELETE' })
    .then(res => res.json())
    .then(res => { 
        if(res.code === 200) {
            closeDeleteModal();
            // Tunggu modal nutup baru refresh datanya biar smooth
            setTimeout(() => { fetchData(currentPage); }, 300);
        } else {
            alert('Gagal: ' + res.message); 
        }
        this.innerHTML = 'Ya, Hapus';
        this.disabled = false;
    })
    .catch(err => {
        alert('Terjadi kesalahan jaringan.');
        this.innerHTML = 'Ya, Hapus';
        this.disabled = false;
    });
});

// AUTO LOAD
document.addEventListener('DOMContentLoaded', () => { fetchData(1); });
</script>