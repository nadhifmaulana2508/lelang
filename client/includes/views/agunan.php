<?php
// Mengambil session dari auth_logic.php
require_once __DIR__ . '/../../../api/helpers/dropdown.php';

$id_cessie = $_GET['id_cessie'] ?? null;
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-[#041020]"><?= $id_cessie ? 'Kelola Agunan Nasabah' : 'Master Data Agunan' ?></h2>
        <p class="text-[11px] text-gray-500 mt-1">Daftar aset agunan (Filter Otomatis).</p>
    </div>
    
    <div class="flex gap-2">
        <?php if ($id_cessie): ?>
            <a href="?page=calon_cessie" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-xs hover:bg-gray-200 transition-all"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
        <?php endif; ?>
        <a href="?page=form_agunan<?= $id_cessie ? '&id_calon_cessie='.$id_cessie : '' ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold text-xs hover:bg-blue-700 flex items-center shadow-lg transition-all">
            <i class="fas fa-plus md:mr-1"></i> <span class="hidden md:inline">Tambah Agunan Baru</span>
        </a>
    </div>
</div>

<div id="filterContainer" class="flex flex-col md:flex-row items-center gap-2 w-full bg-white p-3 rounded-xl shadow-sm border border-gray-100 mb-6">
    <?php if($is_superadmin): ?>
        <select id="filterCabang" onchange="loadAgunan()" class="w-full md:w-auto px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-400">
            <?= renderDropdownCabang() ?>
        </select>
    <?php else: ?>
        <input type="hidden" id="filterCabang" value="<?= $user_kode ?>">
        <div class="px-4 py-2 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg border border-blue-100">📍 Cabang: <?= $user_kode ?></div>
    <?php endif; ?>

    <select id="filterStatus" onchange="loadAgunan()" class="w-full md:w-auto px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">Semua Status</option>
        <option value="Open">🟢 Open</option>
        <option value="Terjual">🔴 Terjual</option>
    </select>
    
    <div class="relative w-full md:w-64 ml-auto">
        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
        <input type="text" id="filterSearch" oninput="handleSearch()" placeholder="Cari aset / nasabah..." class="w-full px-3 py-2 pl-8 bg-gray-50 border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-400">
    </div>
</div>

<div id="agunanContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <div class="col-span-full py-10 text-center"><i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i><p class="mt-2 text-xs text-gray-500">Memuat data aset...</p></div>
</div>

<div id="modalDelete" class="fixed inset-0 bg-black/60 z-[999] hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden text-center p-8 scale-95 transition-transform" id="contentDelete">
        <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-trash-alt text-4xl"></i>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 mb-2">Hapus Aset?</h3>
        <p class="text-sm text-gray-500 mb-8">Data dan 4 foto aset ini akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.</p>
        <div class="flex gap-3">
            <button onclick="closeModal('modalDelete')" class="flex-1 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-all">Batal</button>
            <button id="btnConfirmDelete" class="flex-1 py-3 bg-red-600 text-white font-bold rounded-xl shadow-lg hover:bg-red-700 transition-all">Ya, Hapus</button>
        </div>
    </div>
</div>

<div id="modalSuccess" class="fixed inset-0 bg-black/60 z-[1000] hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden text-center p-8">
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-4xl"></i>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 mb-2">Berhasil!</h3>
        <p id="msgSuccess" class="text-sm text-gray-500 mb-8">Data telah diperbarui.</p>
        <button onclick="closeModal('modalSuccess'); loadAgunan();" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition-all">Selesai</button>
    </div>
</div>

<script>
const idCessie = '<?= $id_cessie ?>';
const isSuperadmin = <?= $is_superadmin ? 'true' : 'false' ?>;
let debounceTimer;
let deleteId = null;

function rp(angka) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka); }

function handleSearch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadAgunan(), 500); 
}

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}

function loadAgunan() {
    const container = document.getElementById('agunanContainer');
    container.innerHTML = `<div class="col-span-full py-10 text-center"><i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i><p class="mt-2 text-xs text-gray-500">Memuat data aset...</p></div>`;

    const searchVal = document.getElementById('filterSearch').value;
    const cabangVal = document.getElementById('filterCabang').value;
    const statusVal = document.getElementById('filterStatus').value;

    let fetchUrl = `<?= API_URL ?>/agunan/list?1=1`;
    if (idCessie) fetchUrl += `&id_calon_cessie=${idCessie}`;
    if (searchVal) fetchUrl += `&search=${encodeURIComponent(searchVal)}`;
    if (cabangVal) fetchUrl += `&kode_kantor=${encodeURIComponent(cabangVal)}`;

    fetch(fetchUrl)
    .then(res => res.json())
    .then(res => {
        const arrData = res.data?.data || res.data || [];
        if (arrData.length === 0) {
            container.innerHTML = `<div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 shadow-sm"><i class="fas fa-search text-4xl mb-3 text-gray-300"></i><p class="text-sm font-bold text-gray-500">Data tidak ditemukan.</p></div>`;
            return;
        }

        let html = '';
        arrData.forEach(item => {
            if(statusVal && item.status !== statusVal) return; 

            const imgUrl = item.foto1 ? `../uploads/agunan/${item.foto1}` : 'https://placehold.co/400x300/e2e8f0/64748b?text=Error';
            const badgeStatus = item.status === 'Terjual' 
                ? '<span class="bg-red-100 text-red-700 px-2 py-1 rounded-md text-[9px] font-bold absolute top-2 left-2 shadow-sm">Terjual</span>'
                : '<span class="bg-green-100 text-green-700 px-2 py-1 rounded-md text-[9px] font-bold absolute top-2 left-2 shadow-sm">Open</span>';

            const btnDelete = isSuperadmin 
                ? `<button onclick="confirmDelete(${item.id})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center"><i class="fas fa-trash text-xs"></i></button>` 
                : '';

            html += `
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all flex flex-col group">
                <div class="h-44 bg-gray-200 relative overflow-hidden">
                    <img src="${imgUrl}" class="w-full h-full object-cover group-hover:scale-105 transition-all" onerror="this.src='https://placehold.co/400x300/e2e8f0/64748b?text=Error'">
                    ${badgeStatus}
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h4 class="font-extrabold text-[#041020] text-sm mb-1">${item.jenis_agunan}</h4>
                    <p class="text-[11px] text-gray-500 mb-3 line-clamp-1"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> ${item.alamat_agunan}</p>
                    
                    <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl mb-4">
                        <p class="text-[10px] font-bold text-blue-800 mb-1 truncate"><i class="fas fa-user-circle"></i> ${item.nama_nasabah}</p>
                        <p class="text-[9px] text-gray-500">Rek: <b>${item.no_rekening}</b> • Cabang: <b>${item.kode_kantor}</b></p>
                    </div>

                    <div class="mt-auto flex justify-between items-center pt-3 border-t border-gray-100">
                        <div class="text-[10px] font-bold text-green-600">${rp(item.nilai_pasar)}</div>
                        <div class="flex gap-2">
                            <a href="?page=form_agunan&id=${item.id}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center"><i class="fas fa-pen text-xs"></i></a>
                            ${btnDelete}
                        </div>
                    </div>
                </div>
            </div>`;
        });
        container.innerHTML = html || `<div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 shadow-sm"><p class="text-sm font-bold text-gray-500">Data tidak ditemukan.</p></div>`;
    });
}

// LOGIKA DELETE VIA MODAL
function confirmDelete(id) {
    deleteId = id;
    openModal('modalDelete');
}

document.getElementById('btnConfirmDelete').addEventListener('click', function() {
    if(!deleteId) return;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    this.disabled = true;

    fetch(`<?= API_URL ?>/agunan/delete?id=${deleteId}`, { method: 'DELETE' })
    .then(res => res.json())
    .then(res => {
        closeModal('modalDelete');
        if(res.status === 200) {
            document.getElementById('msgSuccess').innerText = "Aset agunan berhasil dihapus dari sistem.";
            openModal('modalSuccess');
        } else { alert('Gagal: ' + res.message); }
    })
    .finally(() => {
        this.innerHTML = 'Ya, Hapus';
        this.disabled = false;
        deleteId = null;
    });
});

document.addEventListener('DOMContentLoaded', loadAgunan);
</script>