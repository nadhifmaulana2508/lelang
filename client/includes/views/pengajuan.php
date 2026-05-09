<?php
$filter_cabang = $_GET['filter_cabang'] ?? '';
$filter_skema = $_GET['filter_skema'] ?? '';
$whereP = $is_superadmin ? "1=1" : "da.kode_kantor = '$user_kode'";
if ($is_superadmin && $filter_cabang !== '') $whereP .= " AND da.kode_kantor = '$filter_cabang'";
if ($filter_skema !== '') $whereP .= " AND pa.minat_skema = '$filter_skema'";

$limit = 10;
$p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($p - 1) * $limit;
$totData = $pdo->query("SELECT COUNT(*) FROM pengajuan_aset pa JOIN dummy_asset da ON pa.id_aset = da.id WHERE $whereP")->fetchColumn();
$totPage = ceil($totData / $limit);

$stmtP = $pdo->query("SELECT pa.*, da.jenis_surat, da.nomor_surat, da.kode_kantor FROM pengajuan_aset pa JOIN dummy_asset da ON pa.id_aset = da.id WHERE $whereP ORDER BY pa.id DESC LIMIT $limit OFFSET $offset");
$pengajuans = $stmtP->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-900">Data Pengajuan Masuk</h2>
        <p class="text-gray-500 text-sm mt-1">Daftar form peminatan dari website katalog.</p>
    </div>
    <div class="flex flex-wrap gap-3 w-full md:w-auto">
        <form method="GET" class="flex flex-wrap gap-2 w-full md:w-auto">
            <input type="hidden" name="page" value="pengajuan">
            <?php if($is_superadmin): 
                $cabangs = $pdo->query("SELECT DISTINCT kode_kantor FROM dummy_asset ORDER BY kode_kantor")->fetchAll(PDO::FETCH_COLUMN);
            ?>
            <select name="filter_cabang" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm font-bold outline-none">
                <option value="">Semua Cabang</option>
                <?php foreach($cabangs as $c): ?>
                    <option value="<?= $c ?>" <?= $filter_cabang==$c?'selected':'' ?>>Cabang <?= $c ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <select name="filter_skema" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm font-bold outline-none">
                <option value="">Semua Skema</option>
                <option value="Pembiayaan BKK Joglo (KPR)" <?= $filter_skema=='Pembiayaan BKK Joglo (KPR)'?'selected':'' ?>>BKK Joglo (KPR)</option>
                <option value="Mekanisme Lelang" <?= $filter_skema=='Mekanisme Lelang'?'selected':'' ?>>Lelang</option>
                <option value="Mekanisme Cessie" <?= $filter_skema=='Mekanisme Cessie'?'selected':'' ?>>Cessie</option>
                <option value="Hanya Tanya-tanya Dulu" <?= $filter_skema=='Hanya Tanya-tanya Dulu'?'selected':'' ?>>Tanya-tanya</option>
            </select>
        </form>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase font-bold">
                <tr>
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Pengirim</th>
                    <th class="p-4">Aset Dituju</th>
                    <th class="p-4">Skema</th>
                    <?php if($is_superadmin): ?><th class="p-4">Cabang</th><?php endif; ?>
                    <th class="p-4 text-center">Aksi WA</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php if(count($pengajuans) > 0): foreach($pengajuans as $row): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-4 text-gray-600"><?= date('d/m/Y H:i', strtotime($row['created_at'] ?? 'now')) ?></td>
                    <td class="p-4">
                        <p class="font-bold text-gray-800"><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></p>
                        <p class="text-xs text-blue-600 font-bold"><?= htmlspecialchars($row['no_wa'] ?? '-') ?></p>
                    </td>
                    <td class="p-4 font-bold text-gray-800"><?= $row['jenis_surat'] ?> - <?= $row['nomor_surat'] ?></td>
                    <td class="p-4"><span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded text-xs font-bold"><?= htmlspecialchars($row['minat_skema'] ?? '-') ?></span></td>
                    <?php if($is_superadmin): ?><td class="p-4 font-bold text-gray-500"><?= $row['kode_kantor'] ?></td><?php endif; ?>
                    <td class="p-4 text-center">
                        <?php
                            $pesanBalasan = "Halo " . ($row['nama_lengkap'] ?? '') . ", kami dari BKK Jateng menerima pengajuan Anda untuk aset " . $row['jenis_surat'] . " " . $row['nomor_surat'] . " dengan skema " . ($row['minat_skema'] ?? '') . ". Ada yang bisa kami bantu lebih lanjut?";
                            $linkWa = "https://wa.me/" . preg_replace('/[^0-9]/', '', $row['no_wa'] ?? '') . "?text=" . urlencode($pesanBalasan);
                        ?>
                        <a href="<?= $linkWa ?>" target="_blank" class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg shadow-sm">
                            <i class="fab fa-whatsapp mr-1.5"></i> Hubungi
                        </a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">Belum ada pengajuan masuk.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100 flex justify-center gap-2">
        <?php for($i=1; $i<=$totPage; $i++): ?>
            <a href="?page=pengajuan&p=<?= $i ?>&filter_cabang=<?= $filter_cabang ?>&filter_skema=<?= $filter_skema ?>" class="w-8 h-8 flex items-center justify-center rounded-lg font-bold <?= $i==$p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>