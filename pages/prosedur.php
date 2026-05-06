<div class="container mx-auto px-4 mt-20 py-12 max-w-5xl">
    <!-- Main Wrapper -->
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden relative pb-10">
        
        <!-- Background Decoration (Optional subtle gradient at top) -->
        <div class="absolute top-0 left-0 w-full h-40 bg-gradient-to-b from-blue-50/50 to-transparent z-0"></div>

        <!-- Header Section (Padding bawah dikurangi dari pb-8 jadi pb-6) -->
        <div class="relative z-10 text-center px-8 pt-12 pb-6">
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-4 py-1.5 rounded-full mb-4 inline-block tracking-wide uppercase">Panduan Layanan</span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Prosedur Pembelian Aset</h1>
            <p class="text-gray-500 mt-3 max-w-2xl mx-auto text-sm md:text-base">Pilih mekanisme pembelian yang sesuai dengan kebutuhan Anda untuk melihat langkah-langkah detail proses transaksi di BKK Jateng.</p>
        </div>

        <!-- Modern Segmented Control Tabs (Margin bawah dikurangi dari mb-12 jadi mb-6) -->
        <div class="relative z-10 flex justify-center mb-6 px-4">
            <div class="bg-gray-100/80 p-1.5 rounded-2xl inline-flex w-full sm:w-auto shadow-inner">
                <button id="btn-lelang" onclick="switchTab('lelang')" class="flex-1 sm:w-48 py-2.5 px-4 rounded-xl text-sm font-semibold transition-all duration-300 bg-white text-blue-700 shadow-sm border border-gray-200/50 flex items-center justify-center gap-2 focus:outline-none">
                    <i class="fas fa-gavel"></i> Lelang & Damai
                </button>
                <button id="btn-cessie" onclick="switchTab('cessie')" class="flex-1 sm:w-48 py-2.5 px-4 rounded-xl text-sm font-semibold transition-all duration-300 text-gray-500 hover:text-gray-700 hover:bg-gray-200/50 flex items-center justify-center gap-2 focus:outline-none">
                    <i class="fas fa-file-contract"></i> Cessie
                </button>
            </div>
        </div>

        <!-- Tab Content Wrapper -->
        <div class="relative z-10 px-6 md:px-12">
            
            <!-- Tab Content: Lelang -->
            <div id="content-lelang" class="tab-content transition-all duration-500 opacity-100 transform translate-y-0 block">
                <div class="grid gap-4">
                    <!-- Step 1 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">1</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Cari aset sesuai lokasi, harga, dan preferensi keinginanmu. Dapat melakukan pencarian berdasarkan lokasi, area sekitar atau nama aset.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">2</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Pilih aset yang diinginkan. Akan muncul detail aset berupa foto, deskripsi, kelengkapan, akses, lokasi (terhubung ke Google Maps), nomor HP petugas, dan kalkulator KPR.</p>
                    </div>
                    <!-- Step 3 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">3</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Untuk mengetahui lokasi dan kondisi aset lebih lanjut, hubungi petugas BKK melalui tombol WhatsApp yang dapat diklik langsung di halaman aset.</p>
                    </div>
                    <!-- Step 4 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">4</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Penjualan aset secara umum berupa <span class="font-semibold text-gray-800">penjualan damai</span> (kesepakatan langsung antara pemilik aset dan calon pembeli) dan atau melalui <span class="font-semibold text-gray-800">mekanisme lelang</span>.</p>
                    </div>
                    <!-- Step 5 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">5</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Untuk status lelang, transaksi dilakukan secara terbuka sesuai <a href="https://lelang.go.id/page/syarat-dan-ketentuan" target="_blank" class="text-blue-600 font-semibold hover:underline">Syarat dan Ketentuan</a>. Jika terdapat link lelang online, calon pembeli dapat melanjutkan ke <span class="font-semibold text-gray-800">lelang.go.id</span> setelah registrasi.</p>
                    </div>
                    <!-- Step 6 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">6</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Pastikan dokumen aset tersebut sudah sesuai dan valid, kemudian lakukan pelunasan serta melengkapi berkas sesuai ketentuan.</p>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Cessie -->
            <div id="content-cessie" class="tab-content transition-all duration-500 opacity-0 transform translate-y-4 hidden">
                
                <!-- Modern Alert Box -->
                <div class="bg-blue-50/80 border border-blue-100 rounded-2xl p-5 mb-6 flex items-start gap-4">
                    <div class="bg-blue-100 text-blue-600 p-2 rounded-full flex-shrink-0 mt-0.5">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <p class="text-sm text-blue-800 leading-relaxed">
                        <strong class="font-semibold block mb-1">Informasi Hak Tagih:</strong> 
                        Cessie adalah pengalihan piutang (hak tagih) atas fasilitas kredit macet beserta agunannya dari pihak PT BPR BKK Jateng kepada investor baru (Cessionaris).
                    </p>
                </div>

                <div class="grid gap-4">
                    <!-- Step 1 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">1</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Cari aset pada katalog yang memiliki status penawaran berupa <span class="font-semibold text-gray-800">Cessie</span>.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">2</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Pilih aset yang diinginkan untuk melihat detail informasi seperti estimasi nilai agunan (taksasi), baki debet, serta rincian jaminan.</p>
                    </div>
                    <!-- Step 3 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">3</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Hubungi petugas BKK melalui tombol WhatsApp yang tersedia di halaman aset untuk berdiskusi lebih lanjut, survey dokumen, dan negosiasi nilai Cessie.</p>
                    </div>
                    <!-- Step 4 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">4</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Mengajukan penawaran pembelian Cessie secara resmi. Jika sepakat, calon investor wajib melengkapi dokumen persyaratan administrasi sesuai ketentuan Bank.</p>
                    </div>
                    <!-- Step 5 -->
                    <div class="group flex gap-4 p-5 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">5</div>
                        </div>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">Melakukan pembayaran/pelunasan, dilanjutkan dengan <span class="font-semibold text-gray-800">Penandatanganan Akta Perjanjian Cessie</span> di hadapan Notaris, dan serah terima dokumen asli.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        const contentLelang = document.getElementById('content-lelang');
        const contentCessie = document.getElementById('content-cessie');
        const btnLelang = document.getElementById('btn-lelang');
        const btnCessie = document.getElementById('btn-cessie');

        // Style aktif (Putih, text biru, ada border/shadow)
        const activeClass = ['bg-white', 'text-blue-700', 'shadow-sm', 'border', 'border-gray-200/50'];
        // Style non-aktif (Transparent, text abu)
        const inactiveClass = ['text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-200/50', 'border-transparent'];

        if (tabName === 'lelang') {
            // Tampilkan Lelang
            contentLelang.classList.remove('hidden');
            setTimeout(() => {
                contentLelang.classList.remove('opacity-0', 'translate-y-4');
                contentLelang.classList.add('opacity-100', 'translate-y-0');
            }, 10);
            
            // Sembunyikan Cessie
            contentCessie.classList.add('opacity-0', 'translate-y-4');
            contentCessie.classList.remove('opacity-100', 'translate-y-0');
            setTimeout(() => contentCessie.classList.add('hidden'), 300);

            // Update Buttons
            btnLelang.classList.add(...activeClass);
            btnLelang.classList.remove(...inactiveClass, 'border-transparent');
            
            btnCessie.classList.add(...inactiveClass, 'border-transparent');
            btnCessie.classList.remove(...activeClass);

        } else if (tabName === 'cessie') {
            // Tampilkan Cessie
            contentCessie.classList.remove('hidden');
            setTimeout(() => {
                contentCessie.classList.remove('opacity-0', 'translate-y-4');
                contentCessie.classList.add('opacity-100', 'translate-y-0');
            }, 10);
            
            // Sembunyikan Lelang
            contentLelang.classList.add('opacity-0', 'translate-y-4');
            contentLelang.classList.remove('opacity-100', 'translate-y-0');
            setTimeout(() => contentLelang.classList.add('hidden'), 300);

            // Update Buttons
            btnCessie.classList.add(...activeClass);
            btnCessie.classList.remove(...inactiveClass, 'border-transparent');
            
            btnLelang.classList.add(...inactiveClass, 'border-transparent');
            btnLelang.classList.remove(...activeClass);
        }
    }
</script>