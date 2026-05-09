<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-extrabold text-[#041020]">Komposisi Portofolio</h3>
                <p class="text-[10px] text-gray-500">Perbandingan Baki Debet & Agunan per Status Kesiapan</p>
            </div>
        </div>
        <div class="relative h-64 w-full">
            <canvas id="portfolioChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <h3 class="font-extrabold text-[#041020] mb-1">Status Kesiapan</h3>
        <p class="text-[10px] text-gray-500 mb-6">Tahapan data sebelum ditawarkan.</p>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center border border-gray-100 p-3 rounded-xl">
                <div class="flex items-center gap-3"><div class="w-2 h-2 rounded-full bg-gray-400"></div><p class="text-xs font-bold text-gray-700">Draft</p></div>
                <p id="c_draft" class="text-sm font-extrabold text-[#041020]">0</p>
            </div>
            <div class="flex justify-between items-center border border-gray-100 p-3 rounded-xl">
                <div class="flex items-center gap-3"><div class="w-2 h-2 rounded-full bg-yellow-400"></div><p class="text-xs font-bold text-gray-700">Review Legal</p></div>
                <p id="c_review" class="text-sm font-extrabold text-[#041020]">0</p>
            </div>
            <div class="flex justify-between items-center border border-gray-100 p-3 rounded-xl">
                <div class="flex items-center gap-3"><div class="w-2 h-2 rounded-full bg-green-500"></div><p class="text-xs font-bold text-gray-700">Siap Ditawarkan</p></div>
                <p id="c_siap" class="text-sm font-extrabold text-[#041020]">0</p>
            </div>
            <div class="flex justify-between items-center border border-gray-100 p-3 rounded-xl">
                <div class="flex items-center gap-3"><div class="w-2 h-2 rounded-full bg-blue-500"></div><p class="text-xs font-bold text-gray-700">Diminati</p></div>
                <p id="c_minat" class="text-sm font-extrabold text-[#041020]">0</p>
            </div>
            <div class="flex justify-between items-center border border-gray-100 p-3 rounded-xl">
                <div class="flex items-center gap-3"><div class="w-2 h-2 rounded-full bg-red-500"></div><p class="text-xs font-bold text-gray-700">Butuh Update</p></div>
                <p id="c_butuh" class="text-sm font-extrabold text-[#041020]">0</p>
            </div>
        </div>
    </div>
</div>