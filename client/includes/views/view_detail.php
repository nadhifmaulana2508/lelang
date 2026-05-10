<?php
$id_view = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM dummy_asset WHERE id=?");
$stmt->execute([$id_view]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$is_superadmin && $r['kode_kantor'] !== $user_kode) die("Akses Ditolak.");
?>
<div class="flex items-center gap-4 mb-6">
    <a href="<?= BASE_URL ?>/client/data" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-200 hover:bg-gray-50"><i class="fas fa-arrow-left"></i></a>
    <h2 class="text-2xl font-extrabold text-gray-900">Detail Aset #<?= $r['id'] ?></h2>
</div>

<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?php for($i=1; $i<=4; $i++): if(!empty($r["foto$i"])): ?>
            <img src="../img/agunan/<?= $r["foto$i"] ?>" class="w-full h-32 md:h-48 object-cover rounded-xl shadow-sm">
        <?php endif; endfor; ?>
    </div>
    
    <h3 class="text-3xl font-black text-blue-600 mb-1">Rp <?= number_format($r['harga_jual'],0,',','.') ?></h3>
    <p class="text-xl font-bold text-gray-800 mb-4"><?= $r['jenis_surat'] ?> - <?= $r['nomor_surat'] ?></p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div class="p-4 bg-gray-50 rounded-xl">
            <p><span class="font-bold text-gray-500 w-32 inline-block">Pemilik/Nasabah:</span> <?= $r['nasabah'] ?> (<?= $r['rekening'] ?>)</p>
            <p><span class="font-bold text-gray-500 w-32 inline-block">Cabang Asal:</span> <?= $r['kode_kantor'] ?></p>
            <p><span class="font-bold text-gray-500 w-32 inline-block">Status Web:</span> <?= $r['tampil']==1?'Tampil Publik':'Disembunyikan' ?></p>
            <p><span class="font-bold text-gray-500 w-32 inline-block">Dilihat:</span> <?= $r['view_count'] ?? 0 ?> kali 👀</p>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
            <p><span class="font-bold text-gray-500 w-32 inline-block">Luas Tanah:</span> <?= $r['luas_tanah'] ?> m²</p>
            <p><span class="font-bold text-gray-500 w-32 inline-block">L. Bangunan:</span> <?= $r['luas_bangunan'] ?> m² (<?= $r['lantai'] ?> Lantai)</p>
            <p><span class="font-bold text-gray-500 w-32 inline-block">Alamat:</span> <?= $r['alamat_asset'] ?></p>
            <p><span class="font-bold text-gray-500 w-32 inline-block">Maps:</span> <a href="<?= $r['link_maps'] ?>" target="_blank" class="text-blue-500 underline">Buka Peta</a></p>
        </div>
    </div>
    <div class="mt-4 p-4 bg-blue-50 rounded-xl text-sm">
        <p class="font-bold text-blue-800 mb-1">Deskripsi Tambahan:</p>
        <p class="text-gray-700"><?= nl2br($r['deskripsi']) ?></p>
    </div>
</div>