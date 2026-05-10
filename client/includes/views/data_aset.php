<?php
$filter_cabang = $_GET['filter_cabang'] ?? '';
$whereD = $is_superadmin ? "1=1" : "kode_kantor = '$user_kode'";
if ($is_superadmin && $filter_cabang !== '') $whereD .= " AND kode_kantor = '$filter_cabang'";

$limit = 10;
$p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($p - 1) * $limit;
$totData = $pdo->query("SELECT COUNT(*) FROM dummy_asset WHERE $whereD")->fetchColumn();
$totPage = ceil($totData / $limit);

$stmt = $pdo->query("SELECT * FROM dummy_asset WHERE $whereD ORDER BY id DESC LIMIT $limit OFFSET $offset");
$assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <h2 class="text-2xl font-extrabold text-gray-900">Kelola Data Aset</h2>
    <div class="flex gap-3 w-full md:w-auto">
        <?php if($is_superadmin): 
            $cabangs = $pdo->query("SELECT DISTINCT kode_kantor FROM dummy_asset ORDER BY kode_kantor")->fetchAll(PDO::FETCH_COLUMN);
        ?>
        <form method="GET" class="flex gap-2 w-full">
            <input type="hidden" name="page" value="data">
            <select name="filter_cabang" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm font-bold focus:ring-blue-500 outline-none">
                <option value="">Semua Cabang</option>
                <?php foreach($cabangs as $c): ?>
                    <option value="<?= $c ?>" <?= $filter_cabang==$c?'selected':'' ?>>Cabang <?= $c ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/client/form" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl flex items-center justify-center shrink-0 shadow-md">
            <i class="fas fa-plus mr-2"></i> Tambah Aset
        </a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="bg-green-50 text-green-700 p-3 rounded-xl border border-green-200 font-bold mb-4 flex items-center">
        <i class="fas fa-check-circle mr-2"></i> Operasi berhasil dilakukan!
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase font-bold">
                <tr>
                    <th class="p-4">Tampil</th>
                    <th class="p-4">No. Surat / Nasabah</th>
                    <th class="p-4">Harga</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-center">Dilihat</th>
                    <?php if($is_superadmin): ?><th class="p-4">Cabang</th><?php endif; ?>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php foreach($assets as $row): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-4"><?= $row['tampil']==1 ? '<i class="fas fa-eye text-green-500 tooltip" title="Tampil di Web"></i>' : '<i class="fas fa-eye-slash text-red-500 tooltip" title="Disembunyikan"></i>' ?></td>
                    <td class="p-4">
                        <p class="font-bold text-gray-800"><?= $row['jenis_surat'] ?> - <?= $row['nomor_surat'] ?></p>
                        <p class="text-xs text-gray-500"><?= $row['nasabah'] ?></p>
                    </td>
                    <td class="p-4 font-bold text-blue-600">Rp <?= number_format($row['harga_jual'],0,',','.') ?></td>
                    <td class="p-4"><span class="bg-gray-100 px-3 py-1 rounded-md text-[10px] font-bold uppercase"><?= $row['proses_penjualan'] ?></span></td>
                    <td class="p-4 text-center font-bold text-gray-600"><i class="fas fa-eye text-blue-400 mr-1"></i> <?= $row['view_count'] ?? 0 ?></td>
                    <?php if($is_superadmin): ?><td class="p-4 font-bold text-gray-500"><?= $row['kode_kantor'] ?></td><?php endif; ?>
                    <td class="p-4 text-center flex justify-center gap-2">
                        <a href="<?= BASE_URL ?>/client/view?id=<?= $row['id'] ?>" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white"><i class="fas fa-search"></i></a>
                        <a href="<?= BASE_URL ?>/client/form?id=<?= $row['id'] ?>" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white"><i class="fas fa-pen"></i></a>
                        <?php if($is_superadmin): ?>
                        <a href="<?= BASE_URL ?>/client/data?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?');" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; if(count($assets) == 0): ?>
                    <tr><td colspan="7" class="p-8 text-center text-gray-500">Belum ada data aset jaminan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100 flex justify-center gap-2">
        <?php for($i=1; $i<=$totPage; $i++): ?>
            <a href="<?= BASE_URL ?>/client/data?p=<?= $i ?>&filter_cabang=<?= $filter_cabang ?>" class="w-8 h-8 flex items-center justify-center rounded-lg font-bold <?= $i==$p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>