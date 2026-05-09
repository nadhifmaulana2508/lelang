<?php
$id_edit = $_GET['id'] ?? null;
$r = [];

// Jika Mode Edit, fetch data dari endpoint Detail API
if($id_edit) {
    $apiUrl = API_URL . "/cessie/detail?id=" . $id_edit;
    $response = @file_get_contents($apiUrl);
    
    if ($response) {
        $res_data = json_decode($response, true);
        $r = $res_data['data'] ?? [];
    }
}
?>

<div class="flex items-center gap-4 mb-6">
    <a href="calon_cessie" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors"><i class="fas fa-arrow-left"></i></a>
    <h2 class="text-2xl font-extrabold text-gray-900"><?= $id_edit ? 'Edit Master Cessie' : 'Tambah Master Cessie' ?></h2>
</div>

<form id="cessieForm" class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100">
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="space-y-4">
            <h3 class="font-bold text-blue-600 border-b pb-2 text-sm uppercase tracking-wider mb-4">Identitas & Cabang</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">Kode Kantor</label>
                <input type="text" name="kode_kantor" value="<?= $r['kode_kantor'] ?? ($is_superadmin ? '' : $user_kode) ?>" <?= !$is_superadmin ? 'readonly' : '' ?> required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"></div>
                
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">Nama Kantor</label>
                <input type="text" name="nama_kantor" value="<?= $r['nama_kantor'] ?? '' ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">No Rekening</label>
                <input type="text" name="no_rekening" value="<?= $r['no_rekening'] ?? '' ?>" <?= $id_edit ? 'readonly' : '' ?> required class="w-full px-4 py-2.5 <?= $id_edit ? 'bg-gray-200' : 'bg-gray-50' ?> border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"></div>
                
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">Kolektibilitas</label>
                <input type="text" name="kolektibilitas" placeholder="Cth: 5" value="<?= $r['kolektibilitas'] ?? '' ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"></div>
            </div>

            <div><label class="text-xs font-bold text-gray-500 mb-1 block">Nama Nasabah</label>
            <input type="text" name="nama_nasabah" value="<?= $r['nama_nasabah'] ?? '' ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"></div>

            <div><label class="text-xs font-bold text-gray-500 mb-1 block">Alamat Nasabah</label>
            <textarea name="alamat" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"><?= $r['alamat'] ?? '' ?></textarea></div>
        </div>

        <div class="space-y-4">
            <h3 class="font-bold text-green-600 border-b pb-2 text-sm uppercase tracking-wider mb-4">Data Finansial (Rupiah)</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">Baki Debet</label>
                <input type="text" name="baki_debet" value="<?= $r['baki_debet'] ?? '0' ?>" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-gray-900 focus:ring-2 focus:ring-green-500"></div>
                
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">TOTUNG (Baki + Bunga)</label>
                <input type="text" name="totung" value="<?= $r['totung'] ?? '0' ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-gray-900 focus:ring-2 focus:ring-green-500"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">CKPN</label>
                <input type="text" name="ckpn" value="<?= $r['ckpn'] ?? '0' ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-gray-900 focus:ring-2 focus:ring-green-500"></div>
                
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">Saldo Bank</label>
                <input type="text" name="saldo_bank" value="<?= $r['saldo_bank'] ?? '0' ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-gray-900 focus:ring-2 focus:ring-green-500"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">Nilai Taksasi</label>
                <input type="text" name="nilai_taksasi" value="<?= $r['nilai_taksasi'] ?? '0' ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-blue-600 focus:ring-2 focus:ring-green-500"></div>
                
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">Nilai APHT</label>
                <input type="text" name="nilai_apht" value="<?= $r['nilai_apht'] ?? '0' ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-blue-600 focus:ring-2 focus:ring-green-500"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">Nilai Agunan (Total)</label>
                <input type="text" name="nilai_agunan" value="<?= $r['nilai_agunan'] ?? '0' ?>" required class="w-full px-4 py-2 bg-green-50 border border-green-200 rounded-xl outline-none font-bold text-green-700 focus:ring-2 focus:ring-green-500"></div>
                
                <div><label class="text-xs font-bold text-gray-500 mb-1 block">Status Kesiapan</label>
                <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-gray-700 focus:ring-2 focus:ring-blue-500">
                    <?php $st = $r['status'] ?? 'Draft'; ?>
                    <option value="Draft" <?= $st=='Draft'?'selected':'' ?>>Draft</option>
                    <option value="Review Legal" <?= $st=='Review Legal'?'selected':'' ?>>Review Legal</option>
                    <option value="Butuh Update" <?= $st=='Butuh Update'?'selected':'' ?>>Butuh Update</option>
                    <option value="Siap Ditawarkan" <?= $st=='Siap Ditawarkan'?'selected':'' ?>>Siap Ditawarkan</option>
                    <option value="Diminati" <?= $st=='Diminati'?'selected':'' ?>>Diminati</option>
                </select></div>
            </div>
        </div>
    </div>

    <hr class="border-gray-100 my-8">

    <button type="submit" id="btnSubmit" class="w-full md:w-auto px-10 py-3.5 bg-[#041020] text-white font-bold rounded-xl shadow-lg hover:bg-blue-900 transition-colors">
        <i class="fas fa-paper-plane mr-2"></i> Simpan via API
    </button>
</form>

<div id="alertModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden transform scale-95 transition-transform" id="alertModalContent">
        <div class="p-6 text-center">
            <div id="alertIcon" class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                </div>
            <h3 id="alertTitle" class="text-xl font-extrabold text-gray-900 mb-2">Peringatan</h3>
            <p id="alertMessage" class="text-sm text-gray-500 mb-6">Pesan detail di sini.</p>
            <button id="alertBtn" class="w-full py-3 text-white font-bold rounded-xl shadow-lg transition-colors">
                Mengerti
            </button>
        </div>
    </div>
</div>

<script>
// Fungsi Khusus Menampilkan Modal
function showAlert(type, title, message, redirectUrl = null) {
    const modal = document.getElementById('alertModal');
    const content = document.getElementById('alertModalContent');
    const iconDiv = document.getElementById('alertIcon');
    const titleEl = document.getElementById('alertTitle');
    const msgEl = document.getElementById('alertMessage');
    const btnEl = document.getElementById('alertBtn');

    // Reset Class bawaan
    iconDiv.className = 'w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4';
    btnEl.className = 'w-full py-3 font-bold rounded-xl shadow-lg transition-colors text-white';

    // Styling khusus Sukses vs Error
    if (type === 'success') {
        iconDiv.classList.add('bg-green-100', 'text-green-600');
        iconDiv.innerHTML = '<i class="fas fa-check text-2xl"></i>';
        btnEl.classList.add('bg-green-600', 'hover:bg-green-700');
        btnEl.innerText = 'Kembali ke Data Cessie';
    } else {
        iconDiv.classList.add('bg-red-100', 'text-red-600');
        iconDiv.innerHTML = '<i class="fas fa-exclamation-triangle text-2xl"></i>';
        btnEl.classList.add('bg-red-600', 'hover:bg-red-700');
        btnEl.innerText = 'Tutup & Perbaiki';
    }

    titleEl.innerText = title;
    msgEl.innerText = message;

    // Munculkan Modal dengan animasi
    modal.classList.remove('hidden');
    setTimeout(() => content.classList.remove('scale-95'), 10);

    // Hapus event listener lama di tombol (mencegah double click action)
    const newBtnEl = btnEl.cloneNode(true);
    btnEl.parentNode.replaceChild(newBtnEl, btnEl);

    // Kasih action ke tombol baru
    newBtnEl.addEventListener('click', () => {
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            if (redirectUrl) window.location.href = redirectUrl; // Redirect kalau ada URL
        }, 200);
    });
}

document.getElementById('cessieForm').addEventListener('submit', function(e) {
    e.preventDefault(); 
    
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
    btnSubmit.disabled = true;

    const formData = new FormData(this);
    const payload = Object.fromEntries(formData.entries());
    
    const isEdit = <?= $id_edit ? 'true' : 'false' ?>;
    const idEdit = '<?= $id_edit ?>';
    
    const apiUrl = isEdit 
        ? `<?= API_URL ?>/cessie/update?id=${idEdit}` 
        : `<?= API_URL ?>/cessie/create`;

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const result = await res.json();
        // Cek kode sukses dari Backend (Bisa res.code atau res.status tergantung helper)
        if(result.code === 201 || result.code === 200 || result.status === 201 || result.status === 200) {
            // Panggil Modal Sukses dan Redirect ke clean URL 'calon_cessie'
            showAlert('success', 'Berhasil Disimpan!', 'Data Master Cessie telah berhasil diupdate ke sistem API.', 'calon_cessie');
        } else {
            // Panggil Modal Error
            showAlert('error', 'Gagal Disimpan', result.message || 'Periksa kembali data yang dimasukkan.');
            btnSubmit.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Simpan via API';
            btnSubmit.disabled = false;
        }
    })
    .catch(err => {
        console.error(err);
        showAlert('error', 'Kesalahan Jaringan', 'Gagal menghubungi server API. Pastikan internet / backend menyala.');
        btnSubmit.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Simpan via API';
        btnSubmit.disabled = false;
    });
});
</script>