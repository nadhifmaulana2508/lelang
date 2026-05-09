<?php
$id_edit = $_GET['id'] ?? null;
$r = [];
if($id_edit) {
    $stmt = $pdo->prepare("SELECT * FROM dummy_asset WHERE id=?");
    $stmt->execute([$id_edit]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$is_superadmin && $r['kode_kantor'] !== $user_kode) die("Akses Ditolak.");
}
?>
<div class="flex items-center gap-4 mb-6">
    <a href="?page=data" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-200 hover:bg-gray-50"><i class="fas fa-arrow-left"></i></a>
    <h2 class="text-2xl font-extrabold text-gray-900"><?= $id_edit ? 'Edit Data Aset' : 'Tambah Aset Baru' ?></h2>
</div>

<form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
    <input type="hidden" name="id" value="<?= $id_edit ?>">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="space-y-4">
            <h3 class="font-bold text-blue-600 border-b pb-2">1. Identitas Nasabah</h3>
            <?php if($is_superadmin): ?>
                <div><label class="text-xs font-bold text-gray-500">Kode Kantor</label>
                <input type="text" name="kode_kantor" value="<?= $r['kode_kantor'] ?? '' ?>" required class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></div>
            <?php endif; ?>
            <div><label class="text-xs font-bold text-gray-500">Nama Nasabah</label>
            <input type="text" name="nasabah" value="<?= $r['nasabah'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></div>
            <div><label class="text-xs font-bold text-gray-500">Nomor Rekening</label>
            <input type="text" name="rekening" value="<?= $r['rekening'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></div>
        </div>

        <div class="space-y-4">
            <h3 class="font-bold text-blue-600 border-b pb-2">2. Spesifikasi Dokumen</h3>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-gray-500">Jenis Agunan</label>
                <input type="text" name="jenis_agunan" value="<?= $r['jenis_agunan'] ?? 'Tanah Bangunan' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                <div><label class="text-xs font-bold text-gray-500">Status Aset</label>
                <select name="proses_penjualan" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none">
                    <option value="Jual" <?= ($r['proses_penjualan']??'')=='Jual'?'selected':'' ?>>Jual Damai</option>
                    <option value="Lelang" <?= ($r['proses_penjualan']??'')=='Lelang'?'selected':'' ?>>Lelang</option>
                    <option value="Cessie" <?= ($r['proses_penjualan']??'')=='Cessie'?'selected':'' ?>>Cessie</option>
                    <option value="Sold" <?= ($r['proses_penjualan']??'')=='Sold'?'selected':'' ?>>Sold</option>
                </select></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-gray-500">Jenis Surat</label>
                <input type="text" name="jenis_surat" value="<?= $r['jenis_surat'] ?? 'SHM' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                <div><label class="text-xs font-bold text-gray-500">Nomor Surat</label>
                <input type="text" name="nomor_surat" value="<?= $r['nomor_surat'] ?? '' ?>" required class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div><label class="text-xs font-bold text-gray-500">Luas Tanah</label>
                <input type="number" name="luas_tanah" value="<?= $r['luas_tanah'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                <div><label class="text-xs font-bold text-gray-500">L. Bangunan</label>
                <input type="number" name="luas_bangunan" value="<?= $r['luas_bangunan'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                <div><label class="text-xs font-bold text-gray-500">Lantai</label>
                <input type="number" name="lantai" value="<?= $r['lantai'] ?? '1' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
            </div>
            <div><label class="text-xs font-bold text-gray-500">Harga Jual (Rp)</label>
            <input type="number" name="harga_jual" value="<?= $r['harga_jual'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg font-bold text-blue-600 outline-none"></div>
        </div>
    </div>

    <div class="mb-6 space-y-4">
        <h3 class="font-bold text-blue-600 border-b pb-2">3. Lokasi & Deskripsi</h3>
        <div><label class="text-xs font-bold text-gray-500">Alamat Lengkap Aset</label>
        <textarea name="alamat_asset" rows="2" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"><?= $r['alamat_asset'] ?? '' ?></textarea></div>
        <div><label class="text-xs font-bold text-gray-500">Deskripsi Menarik</label>
        <textarea name="deskripsi" rows="3" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"><?= $r['deskripsi'] ?? '' ?></textarea></div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="text-xs font-bold text-gray-500">Letak Jaminan</label><input type="text" name="letak_jaminan" value="<?= $r['letak_jaminan'] ?? 'Marketable' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg"></div>
            <div><label class="text-xs font-bold text-gray-500">Latitude Maps</label><input type="text" name="latitude" value="<?= $r['latitude'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg"></div>
            <div><label class="text-xs font-bold text-gray-500">Longitude Maps</label><input type="text" name="longitude" value="<?= $r['longitude'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg"></div>
        </div>
        <div><label class="text-xs font-bold text-gray-500">Link Google Maps</label><input type="text" name="link_maps" value="<?= $r['link_maps'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg"></div>
    </div>

    <div class="mb-6">
        <h3 class="font-bold text-blue-600 border-b pb-2 mb-4">4. Upload Foto Aset</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php for($i=1; $i<=4; $i++): $foto_name = $r["foto$i"] ?? ''; ?>
                <div class="border rounded-xl p-3 bg-gray-50 text-center">
                    <p class="text-xs font-bold text-gray-500 mb-2">Foto <?= $i ?></p>
                    <?php if($foto_name): ?>
                        <img src="../img/agunan/<?= $foto_name ?>" class="w-full h-24 object-cover rounded-lg mb-2 shadow-sm">
                        <input type="hidden" name="old_foto<?= $i ?>" value="<?= $foto_name ?>">
                    <?php else: ?>
                        <div class="w-full h-24 bg-gray-200 rounded-lg mb-2 flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
                    <input type="file" name="foto<?= $i ?>" accept="image/*" class="text-[10px] w-full file:bg-blue-50 file:text-blue-600 file:border-0 file:rounded-md file:px-2 file:py-1 file:font-bold">
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="flex items-center gap-3 mb-6 bg-yellow-50 p-4 rounded-xl border border-yellow-200">
        <input type="checkbox" name="tampil" value="1" <?= (!isset($r['tampil']) || $r['tampil']==1) ? 'checked' : '' ?> class="w-5 h-5 text-blue-600 rounded">
        <label class="text-sm font-bold text-gray-800">Tampilkan aset ini di Katalog Web (Public)</label>
    </div>

    <button type="submit" name="save_asset" class="w-full md:w-auto px-8 py-3.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700">
        <i class="fas fa-save mr-2"></i> Simpan Data Aset
    </button>
</form>