<?php
// Pastikan variabel session login ini valid dari auth_logic.php
$id_edit = $_GET['id'] ?? null;
$id_calon_cessie_url = $_GET['id_calon_cessie'] ?? null;
$r = [];

// 1. AMBIL DATA DETAIL via PDO
if($id_edit) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM dummy_asset WHERE id = :id");
    $stmt->execute([':id' => $id_edit]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        $r = $data;
        $id_calon_cessie_url = $r['id_calon_cessie'] ?? $id_calon_cessie_url;
    }
}

// 2. AMBIL LIST DEBITUR via PDO
global $pdo;
$stmtCessie = $pdo->query("SELECT id, nama_nasabah, no_rekening, kode_kantor FROM calon_cessie ORDER BY id DESC");
$rawData = $stmtCessie->fetchAll(PDO::FETCH_ASSOC);

$listDebitur = [];
if (isset($is_superadmin) && !$is_superadmin) {
    foreach ($rawData as $d) {
        if ($d['kode_kantor'] === $user_kode) {
            $listDebitur[] = $d;
        }
    }
} else { 
    $listDebitur = $rawData; 
}
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container .select2-selection--single { height: 44px !important; border-radius: 0.5rem !important; border: 1px solid #e5e7eb !important; background-color: #f9fafb !important; display: flex; align-items: center; padding-left: 0.5rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #1f2937 !important; font-weight: 700; font-size: 0.875rem; }
    
    /* Animasi Modal */
    .modal-bounce { animation: bounceIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
    @keyframes bounceIn {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="flex items-center gap-4 mb-6">
    <a href="<?= BASE_URL ?>/client/agunan" class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm border border-gray-200 hover:bg-gray-50"><i class="fas fa-arrow-left"></i></a>
    <h2 class="text-2xl font-extrabold text-gray-900" id="pageTitle"><?= $id_edit ? 'Edit Data Agunan' : 'Tambah Agunan Baru' ?></h2>
</div>

<form id="formAgunan" class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100" enctype="multipart/form-data">
    <input type="hidden" name="id" id="h_id_agunan" value="<?= htmlspecialchars($id_edit ?? '') ?>">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <h3 class="font-bold text-blue-600 border-b pb-2 text-sm uppercase mb-4">Informasi Dasar & Legalitas</h3>
            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block">Pemilik Agunan (Debitur) <span class="text-red-500">*</span></label>
                <select name="id_calon_cessie" id="i_calon_cessie" class="select2-debitur w-full" required>
                    <option value="">-- Ketik & Pilih Nasabah --</option>
                    <?php foreach ($listDebitur as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($id_calon_cessie_url == $d['id']) ? 'selected' : '' ?>>
                            <?= $d['nama_nasabah'] ?> (Rek: <?= $d['no_rekening'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block">Jenis Agunan <span class="text-red-500">*</span></label>
                <input type="text" name="jenis_agunan" id="i_jenis_agunan" value="<?= htmlspecialchars($r['jenis_agunan'] ?? '') ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 font-bold text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 mb-1 block">Jenis Surat</label>
                    <select name="jenis_surat" id="i_jenis_surat" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">- Pilih -</option>
                        <option value="SHM">SHM</option>
                        <option value="SHGB">SHGB</option>
                        <option value="BPKB">BPKB</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 mb-1 block">Nomor Surat</label>
                    <input type="text" name="nomor_surat" id="i_nomor_surat" value="<?= htmlspecialchars($r['nomor_surat'] ?? '') ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block">Alamat Aset <span class="text-red-500">*</span></label>
                <textarea name="alamat_agunan" id="i_alamat" rows="2" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($r['alamat_agunan'] ?? '') ?></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 mb-1 block">Koordinat Maps</label>
                    <input type="text" name="koordinat" id="i_koordinat" value="<?= htmlspecialchars($r['koordinat'] ?? '') ?>" placeholder="-6.98234, 110.4312" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 mb-1 block">Status Aset</label>
                    <select name="status" id="i_status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none font-bold text-gray-700 focus:ring-2 focus:ring-blue-500">
                        <option value="Open">🟢 Open</option>
                        <option value="Terjual">🔴 Terjual</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block">Link Google Maps</label>
                <input type="url" name="link_maps" id="i_link_maps" value="<?= htmlspecialchars($r['link_maps'] ?? '') ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="font-bold text-green-600 border-b pb-2 text-sm uppercase mb-4">Nilai & Upload (4 Foto)</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 mb-1 block">Luas Tanah (m²)</label>
                    <input type="text" name="luas_tanah" id="i_lt" value="<?= $r['luas_tanah'] ?? '0' ?>" class="format-ribuan w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg font-bold text-sm">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 mb-1 block">Luas Bangunan (m²)</label>
                    <input type="text" name="luas_bangunan" id="i_lb" value="<?= $r['luas_bangunan'] ?? '0' ?>" class="format-ribuan w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg font-bold text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-xs font-bold text-gray-500 mb-1 block">Nilai Pasar (Rp)</label>
                    <input type="text" name="nilai_pasar" id="i_pasar" value="<?= $r['nilai_pasar'] ?? '0' ?>" class="format-ribuan w-full px-4 py-2 bg-green-50 border border-green-200 rounded-lg font-bold text-green-700 text-sm">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 mb-1 block">Nilai Likuidasi (Rp)</label>
                    <input type="text" name="nilai_likuidasi" id="i_likuidasi" value="<?= $r['nilai_likuidasi'] ?? '0' ?>" class="format-ribuan w-full px-4 py-2 bg-red-50 border border-red-200 rounded-lg font-bold text-red-700 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="border border-gray-200 p-3 rounded-xl">
                    <label class="text-xs font-bold text-gray-700 mb-1 block">Foto 1 (Utama)</label>
                    <p id="txt_foto1" class="text-[10px] text-gray-400 mb-1 truncate"><?= !empty($r['foto1']) ? '<i class="fas fa-check-circle text-green-500"></i> ' . $r['foto1'] : 'Belum ada foto' ?></p>
                    <input type="file" name="foto1" id="f_img1" accept="image/*" class="w-full text-[10px] file:bg-blue-50 file:text-blue-700 file:border-0 file:rounded-md file:px-2 file:py-1">
                </div>
                <div class="border border-gray-200 p-3 rounded-xl">
                    <label class="text-xs font-bold text-gray-700 mb-1 block">Foto 2 (Samping)</label>
                    <p id="txt_foto2" class="text-[10px] text-gray-400 mb-1 truncate"><?= !empty($r['foto2']) ? '<i class="fas fa-check-circle text-green-500"></i> ' . $r['foto2'] : 'Belum ada foto' ?></p>
                    <input type="file" name="foto2" id="f_img2" accept="image/*" class="w-full text-[10px] file:bg-blue-50 file:text-blue-700 file:border-0 file:rounded-md file:px-2 file:py-1">
                </div>
                <div class="border border-gray-200 p-3 rounded-xl">
                    <label class="text-xs font-bold text-gray-700 mb-1 block">Foto 3 (Jalan)</label>
                    <p id="txt_foto3" class="text-[10px] text-gray-400 mb-1 truncate"><?= !empty($r['foto3']) ? '<i class="fas fa-check-circle text-green-500"></i> ' . $r['foto3'] : 'Belum ada foto' ?></p>
                    <input type="file" name="foto3" id="f_img3" accept="image/*" class="w-full text-[10px] file:bg-blue-50 file:text-blue-700 file:border-0 file:rounded-md file:px-2 file:py-1">
                </div>
                <div class="border border-gray-200 p-3 rounded-xl">
                    <label class="text-xs font-bold text-gray-700 mb-1 block">Foto 4 (Dokumen)</label>
                    <p id="txt_foto4" class="text-[10px] text-gray-400 mb-1 truncate"><?= !empty($r['foto4']) ? '<i class="fas fa-check-circle text-green-500"></i> ' . $r['foto4'] : 'Belum ada foto' ?></p>
                    <input type="file" name="foto4" id="f_img4" accept="image/*" class="w-full text-[10px] file:bg-blue-50 file:text-blue-700 file:border-0 file:rounded-md file:px-2 file:py-1">
                </div>
            </div>
        </div>
    </div>

    <hr class="border-gray-100 my-6">
    <button type="submit" id="btnSubmit" class="px-8 py-3 bg-[#041020] text-white font-bold rounded-lg shadow-md hover:bg-blue-900 transition-colors w-full md:w-auto">
        <i class="fas fa-save mr-2"></i> Simpan Data Agunan
    </button>
</form>

<div id="modalSuccess" class="fixed inset-0 bg-black/60 z-[999] hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden modal-bounce text-center p-8">
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-4xl"></i>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 mb-2">Berhasil Disimpan!</h3>
        <p id="msgSuccess" class="text-sm text-gray-500 mb-8">Data agunan telah diperbarui ke sistem pusat.</p>
        <button onclick="window.location.href='agunan'" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition-all">
            Selesai
        </button>
    </div>
</div>

<script>
function formatTitik(angka) {
    if (!angka) return '';
    let num = angka.toString().replace(/[^,\d]/g, '');
    let split = num.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    if (ribuan) { let sep = sisa ? '.' : ''; rupiah += sep + ribuan.join('.'); }
    return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
}

$(document).ready(function() {
    $('.select2-debitur').select2();
    $('.format-ribuan').each(function() { $(this).val(formatTitik($(this).val())); });
    $('.format-ribuan').on('keyup', function() { $(this).val(formatTitik($(this).val())); });

    const idEdit = $('#h_id_agunan').val();
    if (idEdit && $('#i_jenis_agunan').val() === '') {
        fetch(`<?= API_URL ?>/agunan/detail?id=${idEdit}`)
        .then(res => res.json())
        .then(res => {
            const data = res.data?.data || res.data || res.data[0];
            if (data) {
                $('#i_calon_cessie').val(data.id_calon_cessie).trigger('change');
                $('#i_jenis_agunan').val(data.jenis_agunan);
                $('#i_jenis_surat').val(data.jenis_surat);
                $('#i_nomor_surat').val(data.nomor_surat);
                $('#i_alamat').val(data.alamat_agunan);
                $('#i_koordinat').val(data.koordinat);
                $('#i_status').val(data.status);
                $('#i_link_maps').val(data.link_maps);
                $('#i_lt').val(formatTitik(data.luas_tanah));
                $('#i_lb').val(formatTitik(data.luas_bangunan));
                $('#i_pasar').val(formatTitik(data.nilai_pasar));
                $('#i_likuidasi').val(formatTitik(data.nilai_likuidasi));
                if(data.foto1) document.getElementById('txt_foto1').innerHTML = `<i class="fas fa-check-circle text-green-500"></i> ${data.foto1}`;
                if(data.foto2) document.getElementById('txt_foto2').innerHTML = `<i class="fas fa-check-circle text-green-500"></i> ${data.foto2}`;
                if(data.foto3) document.getElementById('txt_foto3').innerHTML = `<i class="fas fa-check-circle text-green-500"></i> ${data.foto3}`;
                if(data.foto4) document.getElementById('txt_foto4').innerHTML = `<i class="fas fa-check-circle text-green-500"></i> ${data.foto4}`;
            }
        });
    }
});

document.getElementById('formAgunan').addEventListener('submit', function(e) {
    e.preventDefault(); 
    const btnSubmit = document.getElementById('btnSubmit');
    const formData = new FormData(this);
    
    ['luas_tanah', 'luas_bangunan', 'nilai_pasar', 'nilai_likuidasi'].forEach(f => {
        let v = formData.get(f); if(v) formData.set(f, v.replace(/\./g, ''));
    });

    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
    btnSubmit.disabled = true;

    const idEdit = $('#h_id_agunan').val();
    const apiUrl = idEdit ? `<?= API_URL ?>/agunan/update?id=${idEdit}` : `<?= API_URL ?>/agunan/create`;

    fetch(apiUrl, { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        if(res.status === 201 || res.status === 200) {
            document.getElementById('msgSuccess').innerText = res.message;
            document.getElementById('modalSuccess').classList.remove('hidden');
        } else {
            alert('Gagal: ' + res.message);
            btnSubmit.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Data Agunan';
            btnSubmit.disabled = false;
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan jaringan/server.');
        btnSubmit.disabled = false;
    });
});
</script>