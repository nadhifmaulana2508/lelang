<!-- Jumbotron Katalog Lelang & Cessie -->
<section class="bg-gradient-to-br from-yellow-50 to-orange-50 relative py-20 mt-5 overflow-hidden">
    <!-- Dekorasi Background -->
    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-yellow-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>

    <div class="container mx-auto flex flex-col lg:flex-row items-center px-6 relative z-10">
        <!-- Left Content -->
        <div class="lg:w-1/2 space-y-6 mt-10 lg:mt-0 text-center lg:text-left">
            
            <!-- Container Teks (Yang akan dianimasikan) -->
            <div id="hero-text-container" class="space-y-6 transition-all duration-500 transform opacity-100 translate-y-0">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">
                    Temukan <span class="text-blue-700">Aset Terbaik</span>
                </h2>
                <p class="text-lg text-gray-700 font-medium">
                    Pada Catalog Lelang <br class="hidden lg:block">
                    <span class="text-blue-800 text-2xl font-bold bg-blue-100 px-2 py-1 rounded-md inline-block mt-2">PT BPR BKK Jateng (Perseroda)</span>
                </p>
                <p class="text-gray-600 text-base lg:text-lg leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Kami menyediakan berbagai aset lelang berkualitas tinggi, mulai dari tanah, bangunan, kendaraan, hingga barang berharga lainnya. Dapatkan penawaran terbaik untuk investasi Anda!
                </p>
            </div>

            <!-- Ikon 4 kolom -->
            <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-8">
                <a href="<?= BASE_URL ?>/asset" class="bg-white p-4 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 w-24 text-center border border-gray-100 group">
                    <i class="fas fa-box-open text-3xl text-blue-600 group-hover:scale-110 transition-transform duration-300"></i>
                    <p class="text-gray-700 mt-2 text-sm font-semibold">Semua</p>
                </a>
                <a href="<?= BASE_URL ?>/asset" class="bg-white p-4 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 w-24 text-center border border-gray-100 group">
                    <i class="fas fa-map-marked-alt text-3xl text-orange-500 group-hover:scale-110 transition-transform duration-300"></i>
                    <p class="text-gray-700 mt-2 text-sm font-semibold">Tanah</p>
                </a>
                <a href="<?= BASE_URL ?>/asset" class="bg-white p-4 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 w-24 text-center border border-gray-100 group">
                    <i class="fas fa-building text-3xl text-blue-600 group-hover:scale-110 transition-transform duration-300"></i>
                    <p class="text-gray-700 mt-2 text-sm font-semibold">Rumah</p>
                </a>
                <a href="<?= BASE_URL ?>/asset" class="bg-white p-4 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 w-24 text-center border border-gray-100 group">
                    <i class="fas fa-car text-3xl text-orange-500 group-hover:scale-110 transition-transform duration-300"></i>
                    <p class="text-gray-700 mt-2 text-sm font-semibold">Kendaraan</p>
                </a>
            </div>

            <div class="pt-4">
                <a href="<?= BASE_URL ?>/asset" class="inline-block bg-blue-700 text-white px-8 py-4 rounded-xl hover:bg-blue-800 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 text-lg font-bold flex items-center gap-2 w-max mx-auto lg:mx-0">
                    Jelajahi Katalog <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Right Image (Yang akan dianimasikan) -->
        <div class="lg:w-1/2 relative flex justify-center lg:justify-end mt-12 lg:mt-0">
            <img id="hero-image" src="<?= BASE_URL ?>/img/dinda.png" alt="Ilustrasi katalog lelang" class="w-full max-w-md lg:max-w-lg relative z-10 drop-shadow-2xl transition-all duration-500 transform opacity-100 scale-100 hover:scale-105">
        </div>
    </div>
</section>

<!-- Fitur Tambahan (Siap Huni dll) -->
<section class="bg-white py-10 relative z-20 -mt-10">
    <div class="container mx-auto px-6">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="flex items-start space-x-4 group">
                <div class="bg-blue-50 p-4 rounded-2xl group-hover:bg-blue-600 transition-colors duration-300 shrink-0">
                    <i class="fas fa-home text-2xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-1">Siap Huni</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Aset sudah bisa langsung dihuni. Ikuti proses yang cepat dan mudah.</p>
                </div>
            </div>
            <div class="flex items-start space-x-4 group">
                <div class="bg-orange-50 p-4 rounded-2xl group-hover:bg-orange-500 transition-colors duration-300 shrink-0">
                    <i class="fas fa-file-signature text-2xl text-orange-500 group-hover:text-white transition-colors duration-300"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-1">Dokumen Lengkap</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Transaksi bebas khawatir dengan dokumentasi aset yang terjamin legalitasnya.</p>
                </div>
            </div>
            <div class="flex items-start space-x-4 group">
                <div class="bg-green-50 p-4 rounded-2xl group-hover:bg-green-500 transition-colors duration-300 shrink-0">
                    <i class="fas fa-exchange-alt text-2xl text-green-500 group-hover:text-white transition-colors duration-300"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-1">Mudah Balik Nama</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Proses balik nama akan dibantu tanpa perlu ribet dan membuang waktu lama.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Catalog Asset -->
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-6">
        <div class="flex justify-between items-end mb-10">
            <div>
                <span class="text-blue-600 font-bold tracking-wider text-sm uppercase">Katalog Pilihan</span>
                <h3 class="text-3xl font-extrabold text-gray-900 mt-2">Asset Terbaik ✨</h3>
            </div>
            <a href="<?= BASE_URL ?>/asset" class="hidden md:flex text-blue-600 font-semibold hover:text-blue-800 items-center gap-2 hover:translate-x-1 transition-transform">
                Lihat Semua <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>

        <div id="asset-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="animate-pulse bg-white rounded-2xl p-4 shadow-sm h-80"></div>
            <div class="animate-pulse bg-white rounded-2xl p-4 shadow-sm h-80 hidden sm:block"></div>
            <div class="animate-pulse bg-white rounded-2xl p-4 shadow-sm h-80 hidden lg:block"></div>
            <div class="animate-pulse bg-white rounded-2xl p-4 shadow-sm h-80 hidden lg:block"></div>
        </div>
        
        <div class="mt-8 text-center md:hidden">
             <a href="<?= BASE_URL ?>/asset" class="inline-block bg-white border-2 border-blue-600 text-blue-600 font-bold py-3 px-8 rounded-xl w-full">Lihat Semua Aset</a>
        </div>
    </div>
</section>

<!-- Cara Beli Aset (Timeline Style) -->
<section class="bg-white py-16 border-t border-gray-100">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-orange-500 font-bold tracking-wider text-sm uppercase">Panduan Singkat</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-2">Gimana Caranya Beli Aset?</h2>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start gap-8 md:gap-4 max-w-5xl mx-auto">
            <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-1 bg-blue-100 z-0 rounded-full"></div>

            <div class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5 group">
                <div class="w-16 h-16 bg-white border-4 border-blue-100 group-hover:border-blue-500 group-hover:bg-blue-50 transition-colors duration-300 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl shadow-sm mb-4">1</div>
                <h3 class="text-lg font-bold text-gray-900">Cari Aset</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Cari berdasarkan harga dan lokasi yang sesuai.</p>
            </div>

            <div class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5 group">
                <div class="w-16 h-16 bg-white border-4 border-blue-100 group-hover:border-blue-500 group-hover:bg-blue-50 transition-colors duration-300 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl shadow-sm mb-4">2</div>
                <h3 class="text-lg font-bold text-gray-900">Pilih Aset</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Lihat detail taksasi, fasilitas, dan foto aset.</p>
            </div>

            <div class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5 group">
                <div class="w-16 h-16 bg-white border-4 border-blue-100 group-hover:border-blue-500 group-hover:bg-blue-50 transition-colors duration-300 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl shadow-sm mb-4">3</div>
                <h3 class="text-lg font-bold text-gray-900">Hubungi Kami</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Kontak petugas via WhatsApp untuk cek dokumen.</p>
            </div>

            <div class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5 group">
                <div class="w-16 h-16 bg-white border-4 border-blue-100 group-hover:border-blue-500 group-hover:bg-blue-50 transition-colors duration-300 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl shadow-sm mb-4">4</div>
                <h3 class="text-lg font-bold text-gray-900">Mekanisme</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Proses melalui lelang online, Cessie, atau jual damai.</p>
            </div>

            <div class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5 group">
                <div class="w-16 h-16 bg-blue-600 border-4 border-blue-200 text-white transition-transform duration-300 group-hover:scale-110 rounded-full flex items-center justify-center font-bold text-xl shadow-md mb-4"><i class="fas fa-check"></i></div>
                <h3 class="text-lg font-bold text-gray-900">Pelunasan</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Selesaikan pembayaran dan terima dokumen asli.</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Modern dengan Tab Lelang & Cessie -->
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-6 max-w-5xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">Pertanyaan Seputar Lelang & Cessie</h2>
            <p class="text-gray-500 mt-3">Jawaban cepat untuk pertanyaan yang sering diajukan nasabah.</p>
        </div>

        <!-- Segmented Control Tabs (Lelang vs Cessie) -->
        <div class="flex justify-center mb-8">
            <div class="bg-gray-200/60 p-1.5 rounded-2xl inline-flex w-full sm:w-auto shadow-inner">
                <button id="tab-faq-lelang" onclick="switchFaqTab('lelang')" class="flex-1 sm:w-48 py-2.5 px-4 rounded-xl text-sm font-bold transition-all duration-300 bg-white text-blue-700 shadow-sm border border-gray-200 focus:outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-gavel"></i> Info Lelang
                </button>
                <button id="tab-faq-cessie" onclick="switchFaqTab('cessie')" class="flex-1 sm:w-48 py-2.5 px-4 rounded-xl text-sm font-bold transition-all duration-300 text-gray-500 hover:text-gray-700 hover:bg-gray-300/50 focus:outline-none border border-transparent flex items-center justify-center gap-2">
                    <i class="fas fa-file-contract"></i> Info Cessie
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 md:p-6 transition-all duration-500">
            
            <!-- CONTAINER FAQ: LELANG -->
            <div id="faq-container-lelang" class="block animate-fade-in-up">
                <!-- FAQ 1 -->
                <div class="border-b border-gray-100 last:border-0">
                    <button onclick="toggleDropdown('faq-l1', this)" class="w-full flex justify-between items-center p-4 md:p-5 text-left font-bold text-gray-800 hover:text-blue-600 transition-colors duration-200 focus:outline-none">
                        <span>Informasi apa saja yang disajikan dalam website ini?</span>
                        <i class="fas fa-chevron-down transform transition-transform duration-300 text-gray-400"></i>
                    </button>
                    <div id="faq-l1" class="hidden px-4 md:px-5 pb-5 text-gray-600 text-sm md:text-base leading-relaxed">
                        Website ini menyajikan informasi aset-aset yang dijual, baik melalui mekanisme lelang maupun jual damai (termasuk Cessie). Aset-aset yang diinformasikan merupakan aset jaminan kredit nasabah pada PT BPR BKK Jateng (Perseroda).
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="border-b border-gray-100 last:border-0">
                    <button onclick="toggleDropdown('faq-l2', this)" class="w-full flex justify-between items-center p-4 md:p-5 text-left font-bold text-gray-800 hover:text-blue-600 transition-colors duration-200 focus:outline-none">
                        <span>Siapakah pihak penjual dalam mekanisme lelang?</span>
                        <i class="fas fa-chevron-down transform transition-transform duration-300 text-gray-400"></i>
                    </button>
                    <div id="faq-l2" class="hidden px-4 md:px-5 pb-5 text-gray-600 text-sm md:text-base leading-relaxed">
                        Penjual adalah orang, badan hukum/usaha atau instansi yang berwenang untuk menjual barang secara lelang. Penjual dalam website catalog lelang ini adalah PT BPR BKK Jateng (Perseroda).
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="border-b border-gray-100 last:border-0">
                    <button onclick="toggleDropdown('faq-l3', this)" class="w-full flex justify-between items-center p-4 md:p-5 text-left font-bold text-gray-800 hover:text-blue-600 transition-colors duration-200 focus:outline-none">
                        <span>Apa yang dimaksud dengan status "Dilelang"?</span>
                        <i class="fas fa-chevron-down transform transition-transform duration-300 text-gray-400"></i>
                    </button>
                    <div id="faq-l3" class="hidden px-4 md:px-5 pb-5 text-gray-600 text-sm md:text-base leading-relaxed">
                        Lelang adalah penjualan barang yang terbuka untuk umum dengan penawaran harga secara tertulis/lisan yang meningkat untuk mencapai harga tertinggi, didahului dengan Pengumuman Lelang resmi dari pihak berwenang (seperti KPKNL).
                    </div>
                </div>
            </div>

            <!-- CONTAINER FAQ: CESSIE -->
            <div id="faq-container-cessie" class="hidden animate-fade-in-up">
                <!-- FAQ 1 -->
                <div class="border-b border-gray-100 last:border-0">
                    <button onclick="toggleDropdown('faq-c1', this)" class="w-full flex justify-between items-center p-4 md:p-5 text-left font-bold text-gray-800 hover:text-orange-600 transition-colors duration-200 focus:outline-none">
                        <span>Apa yang dimaksud dengan Cessie?</span>
                        <i class="fas fa-chevron-down transform transition-transform duration-300 text-gray-400"></i>
                    </button>
                    <div id="faq-c1" class="hidden px-4 md:px-5 pb-5 text-gray-600 text-sm md:text-base leading-relaxed">
                        Cessie adalah pengalihan piutang (hak tagih) atas fasilitas kredit macet beserta agunannya dari pihak PT BPR BKK Jateng (Perseroda) kepada investor baru (Cessionaris) melalui perjanjian resmi.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="border-b border-gray-100 last:border-0">
                    <button onclick="toggleDropdown('faq-c2', this)" class="w-full flex justify-between items-center p-4 md:p-5 text-left font-bold text-gray-800 hover:text-orange-600 transition-colors duration-200 focus:outline-none">
                        <span>Apa keuntungan membeli aset melalui mekanisme Cessie?</span>
                        <i class="fas fa-chevron-down transform transition-transform duration-300 text-gray-400"></i>
                    </button>
                    <div id="faq-c2" class="hidden px-4 md:px-5 pb-5 text-gray-600 text-sm md:text-base leading-relaxed">
                        Investor berpotensi mendapatkan aset dengan nilai atau harga yang lebih fleksibel dan menarik. Investor (Cessionaris) juga akan memiliki hak tagih serta hak eksekusi agunan sepenuhnya sesuai dengan ketentuan hukum yang berlaku.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="border-b border-gray-100 last:border-0">
                    <button onclick="toggleDropdown('faq-c3', this)" class="w-full flex justify-between items-center p-4 md:p-5 text-left font-bold text-gray-800 hover:text-orange-600 transition-colors duration-200 focus:outline-none">
                        <span>Bagaimana cara mengikuti atau membeli Cessie?</span>
                        <i class="fas fa-chevron-down transform transition-transform duration-300 text-gray-400"></i>
                    </button>
                    <div id="faq-c3" class="hidden px-4 md:px-5 pb-5 text-gray-600 text-sm md:text-base leading-relaxed">
                        Anda dapat mencari aset dengan status penawaran "Cessie" di katalog. Selanjutnya, hubungi petugas kami melalui WhatsApp untuk tahap diskusi, survey dokumen, pengajuan penawaran, hingga proses penandatanganan Akta Perjanjian Cessie di hadapan Notaris.
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- SCRIPT JS UNTUK ANIMASI SLIDER, FETCH, DAN FAQ -->
<!-- ============================================== -->
<script>
    // ------------------------------------
    // 1. Script Animasi Slider Jumbotron
    // ------------------------------------
    document.addEventListener("DOMContentLoaded", function() {
        const heroTextContainer = document.getElementById("hero-text-container");
        const heroImage = document.getElementById("hero-image");

        const slides = [
            {
                image: "<?= BASE_URL ?>/img/dinda.png",
                html: `
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">
                        Temukan <span class="text-blue-700">Aset Terbaik</span>
                    </h2>
                    <p class="text-lg text-gray-700 font-medium">
                        Pada Catalog Lelang <br class="hidden lg:block">
                        <span class="text-blue-800 text-2xl font-bold bg-blue-100 px-2 py-1 rounded-md inline-block mt-2">PT BPR BKK Jateng (Perseroda)</span>
                    </p>
                    <p class="text-gray-600 text-base lg:text-lg leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Kami menyediakan berbagai aset lelang berkualitas tinggi, mulai dari tanah, bangunan, kendaraan, hingga barang berharga lainnya. Dapatkan penawaran terbaik untuk investasi Anda!
                    </p>
                `
            },
            {
                image: "<?= BASE_URL ?>/img/addo.png",
                html: `
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">
                        Investasi <span class="text-orange-600">Hak Tagih (Cessie)</span>
                    </h2>
                    <p class="text-lg text-gray-700 font-medium">
                        Peluang Pengambilalihan Piutang <br class="hidden lg:block">
                        <span class="text-orange-800 text-2xl font-bold bg-orange-100 px-2 py-1 rounded-md inline-block mt-2">PT BPR BKK Jateng (Perseroda)</span>
                    </p>
                    <p class="text-gray-600 text-base lg:text-lg leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Dapatkan peluang investasi menguntungkan melalui pengambilalihan hak tagih (Cessie) atas fasilitas kredit macet beserta agunannya dengan nilai yang bisa dinegosiasikan.
                    </p>
                `
            }
        ];

        let currentIndex = 0;

        setInterval(() => {
            heroTextContainer.classList.add("opacity-0", "translate-y-4");
            heroImage.classList.add("opacity-0", "scale-95");

            setTimeout(() => {
                currentIndex = (currentIndex + 1) % slides.length;
                heroTextContainer.innerHTML = slides[currentIndex].html;
                heroImage.src = slides[currentIndex].image;

                heroTextContainer.classList.remove("opacity-0", "translate-y-4");
                heroImage.classList.remove("opacity-0", "scale-95");
            }, 500); 
        }, 5000);
    });

    // ------------------------------------
    // 2. Script Fetch API Asset
    // ------------------------------------
    fetch("<?= BASE_URL ?>/api/asset/home")
        .then(res => res.json())
        .then(res => {
            const container = document.getElementById("asset-list");

            if (res.status === 200 && Array.isArray(res.data) && res.data.length > 0) {
                container.innerHTML = ""; 
                
                res.data.slice(0, 4).forEach(asset => {
                    const imageSrc = `<?= BASE_URL ?>/img/agunan/${asset.foto1}`;
                    const detailUrl = `<?= BASE_URL ?>/detail?id=${asset.id}`;
                    const defaultImg = `<?= BASE_URL ?>/img/agunan/byl.jpg`;
                    
                    const cardHTML = `
                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transform hover:-translate-y-2 transition-all duration-300 relative flex flex-col h-full group">
                            <div class="relative h-48 overflow-hidden">
                                <img src="${imageSrc}" alt="Asset" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.onerror=null;this.src='${defaultImg}';">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-blue-800 text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm">
                                    ${asset.proses_penjualan}
                                </span>
                            </div>

                            <div class="p-5 flex-1 flex flex-col">
                                <h4 class="text-xl font-black text-gray-900 mb-1">Rp ${formatRupiah(asset.harga_jual)}</h4>
                                <p class="text-blue-600 font-bold text-sm mb-3">${asset.jenis_surat} - ${asset.nomor_surat}</p>
                                
                                <div class="flex items-start gap-2 mb-4 text-gray-500">
                                    <i class="fas fa-map-marker-alt mt-1 text-red-500"></i>
                                    <p class="text-sm line-clamp-2 leading-tight">${asset.alamat_asset}</p>
                                </div>

                                <div class="mt-auto grid grid-cols-2 gap-2 border-t border-gray-100 pt-4">
                                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                                        <p class="text-xs text-gray-500">Luas Tanah</p>
                                        <p class="text-sm font-bold text-gray-800">${asset.luas_tanah} m&sup2;</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                                        <p class="text-xs text-gray-500">Luas Bangunan</p>
                                        <p class="text-sm font-bold text-gray-800">${asset.luas_bangunan} m&sup2;</p>
                                    </div>
                                </div>
                            </div>

                            <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <a href="${detailUrl}" class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-md hover:bg-blue-700 tooltip" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="https://wa.me/6288228659668?text=Halo%20saya%20tertarik%20dengan%20aset%20${asset.nomor_surat}" target="_blank" class="bg-green-500 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-md hover:bg-green-600 tooltip" title="Hubungi Petugas">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </a>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', cardHTML);
                });
            } else {
                container.innerHTML = `<div class="col-span-full text-center py-10 bg-white rounded-2xl border border-dashed border-gray-300"><p class="text-gray-500 font-medium">Data asset belum tersedia saat ini.</p></div>`;
            }
        })
        .catch(error => {
            console.error("Error fetching assets:", error);
            document.getElementById("asset-list").innerHTML = `<div class="col-span-full text-center py-10 bg-red-50 rounded-2xl border border-red-200"><p class="text-red-500 font-medium">Gagal memuat data asset. Periksa koneksi internet Anda.</p></div>`;
        });

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'decimal' }).format(angka);
    }

    // ------------------------------------
    // 3. Script Toggle & Tab Switcher FAQ
    // ------------------------------------
    
    // Buka Tutup Accordion
    function toggleDropdown(id, buttonElement) {
        const content = document.getElementById(id);
        const icon = buttonElement.querySelector('i');
        
        content.classList.toggle('hidden');
        
        if (content.classList.contains('hidden')) {
            icon.classList.remove('rotate-180');
        } else {
            icon.classList.add('rotate-180');
        }
    }

    // Pindah Tab Lelang / Cessie
    function switchFaqTab(tabName) {
        const lelangContainer = document.getElementById('faq-container-lelang');
        const cessieContainer = document.getElementById('faq-container-cessie');
        const btnLelang = document.getElementById('tab-faq-lelang');
        const btnCessie = document.getElementById('tab-faq-cessie');

        const activeClasses = ['bg-white', 'text-blue-700', 'shadow-sm', 'border-gray-200'];
        const inactiveClasses = ['text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-300/50', 'border-transparent'];

        if (tabName === 'lelang') {
            // Tampilkan Lelang
            lelangContainer.classList.remove('hidden');
            cessieContainer.classList.add('hidden');

            // Style Tombol
            btnLelang.classList.add(...activeClasses);
            btnLelang.classList.remove(...inactiveClasses);
            
            btnCessie.classList.add(...inactiveClasses);
            btnCessie.classList.remove(...activeClasses);
            
        } else if (tabName === 'cessie') {
            // Tampilkan Cessie
            cessieContainer.classList.remove('hidden');
            lelangContainer.classList.add('hidden');

            // Style Tombol
            btnCessie.classList.add(...activeClasses);
            btnCessie.classList.remove(...inactiveClasses);
            
            btnLelang.classList.add(...inactiveClasses);
            btnLelang.classList.remove(...activeClasses);
        }
    }
</script>

<!-- Tambahan Style CSS (Bisa ditaruh di <head> / file CSS jika belum ada) -->
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.4s ease-out forwards;
    }
</style>