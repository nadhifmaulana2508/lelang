<div class="bg-blue-900 pt-32 pb-24">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4">Pusat Bantuan & FAQ</h1>
        <p class="text-blue-200 text-sm md:text-lg max-w-2xl mx-auto">Temukan jawaban atas pertanyaan umum seputar Lelang, Cessie, dan prosedur pembelian aset di PT BPR BKK Jateng (Perseroda).</p>
    </div>
</div>

<div class="container mx-auto px-4 lg:px-6 -mt-12 relative z-20 mb-10">
    <div class="bg-white p-2 md:p-3 rounded-2xl shadow-xl border border-gray-100 max-w-3xl mx-auto flex items-center transition-transform focus-within:scale-[1.02] duration-300">
        <span class="text-gray-400 text-xl pl-4 pr-3"><i class="fas fa-search"></i></span>
        <input type="text" id="searchInput" oninput="searchFAQ()" placeholder="Ketik kata kunci (contoh: lelang, cessie, syarat)..." 
            class="w-full bg-transparent border-none py-3 focus:ring-0 outline-none text-gray-700 text-sm md:text-base font-medium placeholder-gray-400">
    </div>
</div>

<div class="container mx-auto px-4 lg:px-6 mb-24 relative z-10">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        
        <div class="md:col-span-4 lg:col-span-3 bg-white p-5 rounded-2xl shadow-sm border border-gray-100 md:sticky md:top-[100px]">
            <h3 class="text-sm font-bold text-gray-400 mb-4 uppercase tracking-wider pl-1">Kategori Topik</h3>
            
            <div class="space-y-2">
                <button id="btn-lelang" onclick="switchCategory('lelang')" class="faq-cat-btn w-full flex items-center p-3.5 rounded-xl transition-all duration-300 bg-blue-600 text-white shadow-md font-bold group">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><i class="fas fa-gavel text-sm"></i></div>
                    <span class="text-sm md:text-base text-left">Seputar Lelang</span>
                </button>
                
                <button id="btn-cessie" onclick="switchCategory('cessie')" class="faq-cat-btn w-full flex items-center p-3.5 rounded-xl transition-all duration-300 bg-transparent text-gray-600 hover:bg-blue-50 hover:text-blue-600 font-bold group">
                    <div class="w-8 h-8 bg-gray-100 group-hover:bg-white group-hover:shadow-sm rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><i class="fas fa-file-contract text-gray-500 group-hover:text-blue-500 text-sm transition-colors"></i></div>
                    <span class="text-sm md:text-base text-left">Cessie & Damai</span>
                </button>

                <button id="btn-kpknl" onclick="switchCategory('kpknl')" class="faq-cat-btn w-full flex items-center p-3.5 rounded-xl transition-all duration-300 bg-transparent text-gray-600 hover:bg-blue-50 hover:text-blue-600 font-bold group">
                    <div class="w-8 h-8 bg-gray-100 group-hover:bg-white group-hover:shadow-sm rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><i class="fas fa-landmark text-gray-500 group-hover:text-blue-500 text-sm transition-colors"></i></div>
                    <span class="text-sm md:text-base text-left">Prosedur KPKNL</span>
                </button>
            </div>
        </div>

        <div class="md:col-span-8 lg:col-span-9 bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 min-h-[400px]">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                <h3 id="content-title" class="text-xl md:text-2xl font-extrabold text-gray-800">Seputar Lelang</h3>
                <span id="content-icon" class="text-2xl md:text-3xl text-blue-100"><i class="fas fa-gavel"></i></span>
            </div>
            
            <div id="faq-display" class="space-y-3">
                </div>
            
            <div id="empty-search" class="hidden text-center py-12">
                <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl border border-gray-100"><i class="fas fa-search-minus"></i></div>
                <h4 class="text-lg font-bold text-gray-700">Pencarian Tidak Ditemukan</h4>
                <p class="text-gray-500 mt-1 text-sm md:text-base">Coba gunakan kata kunci lain yang lebih umum.</p>
                <button onclick="document.getElementById('searchInput').value=''; searchFAQ();" class="mt-4 text-blue-600 font-bold hover:underline text-sm">Lihat Semua FAQ</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Data Base FAQ Lengkap
    const faqData = {
        lelang: [
            {
                q: "Informasi apa sajakah yang disajikan dalam website Catalog Lelang ini?",
                a: "Website ini menyajikan informasi aset-aset yang dijual, baik melalui mekanisme lelang maupun jual damai. Aset-aset tersebut merupakan agunan (jaminan kredit) dari debitur macet (NPL) pada Bank BPR BKK Jateng (Perseroda)."
            },
            {
                q: "Apa yang dimaksud dengan status 'Dilelang'?",
                a: "Lelang adalah penjualan barang yang terbuka untuk umum dengan penawaran harga secara tertulis/lisan yang meningkat untuk mencapai harga tertinggi. Aset dengan status ini dijual melalui perantaraan Kantor Pelayanan Kekayaan Negara dan Lelang (KPKNL)."
            },
            {
                q: "Siapakah pihak penjual dalam mekanisme lelang ini?",
                a: "Penjual dalam penjualan dengan mekanisme lelang di website ini adalah PT Bank BPR BKK Jateng (Perseroda) selaku pemegang hak tanggungan (kreditur) atas aset jaminan debitur."
            },
            {
                q: "Apakah saya bisa melihat kondisi asli aset sebelum ikut lelang?",
                a: "Sangat bisa dan disarankan! Kami menyarankan calon pembeli untuk melakukan survei lokasi aset secara langsung. Anda bisa menghubungi petugas kami melalui tombol WhatsApp di halaman detail aset untuk janjian survei."
            }
        ],
        cessie: [
            {
                q: "Apa yang dimaksud dengan Cessie (Hak Tagih)?",
                a: "Cessie adalah pengalihan piutang (hak tagih) atas fasilitas kredit macet beserta agunannya dari pihak Bank kepada investor baru (Cessionaris). Sederhananya, Anda membeli 'hutang' debitur beserta hak untuk mengeksekusi jaminannya."
            },
            {
                q: "Apa bedanya beli aset lewat Lelang vs beli lewat Cessie?",
                a: "<ul class='list-disc pl-5 mt-2 space-y-1'><li><b>Lelang:</b> Anda membeli fisik aset/tanah secara sah melalui negara (KPKNL), dan proses balik nama bisa langsung dilakukan.</li><li><b>Cessie:</b> Anda membeli hak tagih utangnya. Aset secara hukum masih milik debitur, namun Anda punya kuasa penuh menagih atau mengeksekusi (melelang) aset tersebut jika debitur tidak melunasi utangnya kepada Anda.</li></ul>"
            },
            {
                q: "Apa keuntungan membeli Cessie bagi Investor?",
                a: "Biasanya nilai atau harga Cessie bisa lebih murah / dinegosiasikan dibandingkan harga pasaran aset. Investor berpeluang mendapat margin keuntungan yang besar saat mengeksekusi agunan atau jika debitur melunasi utang pokok + bunganya kepada Investor."
            },
            {
                q: "Apa itu skema Jual Damai?",
                a: "Jual damai adalah penjualan aset secara langsung (di bawah tangan) atas persetujuan debitur (pemilik aset) dan pihak Bank. Proses ini lebih cepat dan fleksibel dibandingkan lelang negara."
            }
        ],
        kpknl: [
            {
                q: "Bagaimana cara melakukan pembelian aset lelang melalui KPKNL?",
                a: "Anda harus mendaftar akun di situs resmi lelang negara (lelang.go.id), menyetorkan Uang Jaminan Lelang (UJL) ke nomor virtual account yang diberikan, lalu melakukan penawaran (bidding) pada jadwal yang telah ditentukan."
            },
            {
                q: "Apakah Uang Jaminan Lelang (UJL) bisa hangus?",
                a: "Jika Anda kalah dalam penawaran lelang, UJL akan <b class='text-blue-600'>dikembalikan 100%</b> ke rekening Anda tanpa potongan. UJL hanya hangus jika Anda menang lelang namun tidak melunasi sisa pembayaran sesuai batas waktu."
            },
            {
                q: "Jika saya menang lelang, apakah Bank membantu proses balik nama?",
                a: "Tentu. Pemenang lelang akan mendapatkan Kutipan Risalah Lelang dari KPKNL. Dokumen ini adalah bukti sah kepemilikan dan digunakan sebagai dasar balik nama di BPN. Petugas Bank BPR BKK Jateng akan mendampingi dan memberikan berkas pendukung yang diperlukan."
            }
        ]
    };

    let currentCategory = 'lelang'; 

    // Render HTML untuk FAQ Item (Accordion bergaya modern)
    function renderFAQHTML(items) {
        return items.map((item, index) => `
            <div class="border border-gray-100 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-sm bg-gray-50/30">
                <button onclick="toggleItem(this)" class="w-full flex justify-between items-center p-4 md:p-5 text-left font-bold text-gray-800 hover:text-blue-600 focus:outline-none">
                    <span class="pr-4 leading-snug text-sm md:text-base">${item.q}</span>
                    <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center shrink-0 border border-gray-100">
                        <i class="fas fa-chevron-down text-blue-500 transition-transform duration-300 transform text-xs"></i>
                    </div>
                </button>
                <div class="hidden px-4 md:px-5 pb-5 pt-1 text-gray-600 text-sm md:text-base leading-relaxed">
                    <div class="pt-3 border-t border-gray-100">
                        ${item.a}
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Ganti Kategori (Tab Switcher)
    function switchCategory(category) {
        currentCategory = category;
        
        const titles = { lelang: 'Seputar Lelang', cessie: 'Cessie & Jual Damai', kpknl: 'Prosedur KPKNL' };
        const icons = { lelang: '<i class="fas fa-gavel"></i>', cessie: '<i class="fas fa-file-contract"></i>', kpknl: '<i class="fas fa-landmark"></i>' };
        
        document.getElementById('content-title').innerText = titles[category];
        document.getElementById('content-icon').innerHTML = icons[category];

        document.getElementById('searchInput').value = '';
        document.getElementById('empty-search').classList.add('hidden');

        const display = document.getElementById('faq-display');
        display.innerHTML = renderFAQHTML(faqData[category]);
        display.classList.remove('hidden');

        // Styling Sidebar Button
        const cats = ['lelang', 'cessie', 'kpknl'];
        cats.forEach(cat => {
            const btn = document.getElementById(`btn-${cat}`);
            const iconBg = btn.querySelector('div');
            const iconEl = btn.querySelector('i');
            
            if (cat === category) {
                btn.className = "faq-cat-btn w-full flex items-center p-3.5 rounded-xl transition-all duration-300 bg-blue-600 text-white shadow-md font-bold group";
                iconBg.className = "w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3";
                iconEl.className = `${iconEl.className.split(' ')[0]} ${iconEl.className.split(' ')[1]} text-white text-sm`;
            } else {
                btn.className = "faq-cat-btn w-full flex items-center p-3.5 rounded-xl transition-all duration-300 bg-transparent text-gray-600 hover:bg-blue-50 hover:text-blue-600 font-bold group";
                iconBg.className = "w-8 h-8 bg-gray-100 group-hover:bg-white group-hover:shadow-sm rounded-lg flex items-center justify-center mr-3 transition-colors";
                iconEl.className = `${iconEl.className.split(' ')[0]} ${iconEl.className.split(' ')[1]} text-gray-500 group-hover:text-blue-500 text-sm transition-colors`;
            }
        });
    }

    // Toggle Accordion dengan animasi putar icon chevron
    function toggleItem(btn) {
        const content = btn.nextElementSibling;
        const icon = btn.querySelector('i');

        content.classList.toggle('hidden');
        
        if (content.classList.contains('hidden')) {
            icon.classList.remove('rotate-180');
        } else {
            icon.classList.add('rotate-180');
        }
    }

    // Fitur Live Search
    function searchFAQ() {
        const keyword = document.getElementById('searchInput').value.toLowerCase();
        const display = document.getElementById('faq-display');
        const emptyState = document.getElementById('empty-search');
        
        if (!keyword) {
            switchCategory(currentCategory); 
            return;
        }

        let searchResults = [];
        for (const cat in faqData) {
            const matches = faqData[cat].filter(item => 
                item.q.toLowerCase().includes(keyword) || item.a.toLowerCase().includes(keyword)
            );
            searchResults = searchResults.concat(matches);
        }

        if (searchResults.length > 0) {
            document.getElementById('content-title').innerText = `Hasil Pencarian: "${keyword}"`;
            document.getElementById('content-icon').innerHTML = '<i class="fas fa-search"></i>';
            display.innerHTML = renderFAQHTML(searchResults);
            display.classList.remove('hidden');
            emptyState.classList.add('hidden');
            
            // Otomatis buka accordion hasil pencarian
            document.querySelectorAll('#faq-display > div > div').forEach(el => {
                el.classList.remove('hidden');
                const icon = el.previousElementSibling.querySelector('i');
                icon.classList.add('rotate-180');
            });

        } else {
            display.classList.add('hidden');
            emptyState.classList.remove('hidden');
            document.getElementById('content-title').innerText = "Pencarian";
            document.getElementById('content-icon').innerHTML = '<i class="fas fa-search-minus"></i>';
        }

        // Reset Sidebar Active State saat searching
        document.querySelectorAll('.faq-cat-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
            btn.classList.add('bg-transparent', 'text-gray-600');
            const iconBg = btn.querySelector('div');
            const icon = btn.querySelector('i');
            iconBg.classList.remove('bg-white/20');
            iconBg.classList.add('bg-gray-100');
            icon.classList.remove('text-white');
            icon.classList.add('text-gray-500');
        });
    }

    // Load default kategori saat halaman dirender
    document.addEventListener("DOMContentLoaded", () => {
        switchCategory('lelang');
    });
</script>