<?php require_once __DIR__ . '/../../../api/helpers/dropdown.php'; ?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-5 rounded-3xl shadow-sm border border-gray-100">
    <div>
        <h2 class="text-2xl font-extrabold text-[#041020]">Dashboard Cessie</h2>
        <p class="text-[11px] text-gray-500 mt-1">Ringkasan portofolio dan prioritas aset lelang.</p>
    </div>
    
    <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
        <?php if($is_superadmin): ?>
        <select id="dashCabang" onchange="loadDashboard()" class="w-full md:w-auto px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500">
            <?= renderDropdownCabang() ?>
        </select>
        <?php else: ?>
        <input type="hidden" id="dashCabang" value="<?= $user_kode ?>">
        <?php endif; ?>

        <select id="dashStatus" onchange="loadDashboard()" class="w-full md:w-auto px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="Draft">Draft</option>
            <option value="Review Legal">Review Legal</option>
            <option value="Siap Ditawarkan">Siap Ditawarkan</option>
            <option value="Diminati">Diminati</option>
        </select>
    </div>
</div>