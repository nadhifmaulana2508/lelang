<div class="bg-blue-900 pt-32 pb-16">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4">Katalog Aset Terbaik</h1>
        <p class="text-blue-200 text-sm md:text-lg max-w-2xl mx-auto">Temukan berbagai pilihan aset lelang dan damai dari PT BPR BKK Jateng (Perseroda) yang sesuai dengan kebutuhan Anda.</p>
    </div>
</div>

<div class="container mx-auto px-4 lg:px-6 -mt-8 mb-24 relative z-10">

    <div class="sticky top-[80px] z-[50] mt-5 md:static md:mt-0 mb-6">
        
        <button id="toggleFilterBtn" class="md:hidden w-full bg-white text-blue-700 font-bold py-3.5 px-5 rounded-2xl shadow-[0_8px_20px_rgba(0,0,0,0.08)] border border-gray-100 flex justify-between items-center transition-all active:scale-95">
            <div class="flex items-center">
                <i class="fas fa-filter text-blue-600 mr-3 text-lg"></i> 
                <span>Filter & Cari Aset</span>
            </div>
            <i id="filterIcon" class="fas fa-chevron-down transition-transform duration-300 text-gray-400"></i>
        </button>

        <div id="filterWrapper" class="hidden md:block absolute md:relative top-full left-0 w-full bg-white p-5 md:p-6 rounded-2xl shadow-2xl md:shadow-xl border border-gray-100 mt-3 md:mt-8 transition-all duration-300 origin-top">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-5">
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Cari Aset</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-gray-400"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchKeyword" placeholder="Cari lokasi, no sertifikat..." 
                               class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all text-sm font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Status</label>
                    <select id="filterStatus" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all text-sm font-medium text-gray-700">
                        <option value="Semua">Semua Status</option>
                        <option value="Lelang">Lelang</option>
                        <option value="Cessie">Cessie</option>
                        <option value="Jual">Jual Damai</option>
                        <option value="Sold">Sold (Terjual)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Jenis Aset</label>
                    <select id="filterJenis" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all text-sm font-medium text-gray-700">
                        <option value="Semua">Semua Jenis</option>
                        <option value="Tanah">Tanah</option>
                        <option value="Bangunan">Rumah / Bangunan</option>
                        <option value="Kendaraan">Kendaraan</option>
                    </select>
                </div>

            </div>
        </div>
    </div>
    <div class="flex justify-between items-end mb-4">
        <p class="text-gray-500 font-semibold text-sm" id="resultCount">Memuat data aset...</p>
    </div>

    <div id="asset-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        </div>

    <div id="pagination-controls" class="flex justify-center flex-wrap gap-2 mt-12">
        </div>

</div>

<script>
    let currentPage = 1;
    let debounceTimer;
    
    // Pagination Responsif (8 Desktop, 6 Mobile)
    function getItemsPerPage() {
        return window.innerWidth < 768 ? 6 : 8;
    }

    // Elemen DOM
    const assetGrid = document.getElementById('asset-grid');
    const paginationControls = document.getElementById('pagination-controls');
    const resultCount = document.getElementById('resultCount');
    const inputSearch = document.getElementById('searchKeyword');
    const filterStatus = document.getElementById('filterStatus');
    const filterJenis = document.getElementById('filterJenis');

    // ---------------------------------------------------------
    // 1. CEK AUTO-FILTER DARI URL (Menerima parameter dari Home)
    // ---------------------------------------------------------
    function cekAutoFilterURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const jenisParam = urlParams.get('jenis'); 
        
        if (jenisParam) {
            const val = jenisParam.toLowerCase();
            if (val === 'rumah' || val === 'bangunan') filterJenis.value = 'Bangunan';
            else if (val === 'tanah') filterJenis.value = 'Tanah';
            else if (val === 'kendaraan') filterJenis.value = 'Kendaraan';
        }
    }

    // ---------------------------------------------------------
    // 2. FETCH DATA DARI API BACKEND
    // ---------------------------------------------------------
    async function fetchAssets() {
        renderSkeleton();

        // Ambil parameter dari inputan user
        const limit = getItemsPerPage();
        const keyword = encodeURIComponent(inputSearch.value.trim());
        const status = encodeURIComponent(filterStatus.value);
        const jenis = encodeURIComponent(filterJenis.value);

        // URL diarahkan ke backend yang sudah kita buat tadi
        const url = `<?= BASE_URL ?>/api/asset/all?page=${currentPage}&limit=${limit}&keyword=${keyword}&status=${status}&jenis=${jenis}`;

        try {
            const response = await fetch(url);
            const resData = await response.json();
            
            // Asumsi response dari helper PHP: resData.data berisi arr(data, pagination)
            if (resData.status === 200 && resData.data && resData.data.data) {
                const assets = resData.data.data;
                const pagination = resData.data.pagination;
                
                renderGrid(assets);
                renderPagination(pagination.total_pages);
                resultCount.innerHTML = `Menampilkan <span class="text-blue-600 font-bold">${pagination.total_data}</span> Aset Ditemukan`;
                
            } else {
                showEmptyState("Gagal memuat data dari server.");
            }
        } catch (error) {
            console.error("Error fetching data:", error);
            showEmptyState("Terjadi kesalahan jaringan.");
        }
    }

    // ---------------------------------------------------------
    // 3. RENDER GRID (HTML)
    // ---------------------------------------------------------
    function renderGrid(assets) {
        if (assets.length === 0) {
            showEmptyState("Tidak ada aset yang cocok dengan filter Anda.");
            paginationControls.innerHTML = '';
            return;
        }

        assetGrid.innerHTML = '';

        assets.forEach(asset => {
            const detailUrl = `<?= BASE_URL ?>/detail?id=${asset.id}`;
            const imgUrl = `<?= BASE_URL ?>/img/agunan/${asset.foto1}`;
            const defaultImg = `<?= BASE_URL ?>/img/agunan/byl.jpg`;
            const formatHarga = new Intl.NumberFormat('id-ID').format(asset.harga_jual);

            // WARNA BADGE DINAMIS
            let badgeColor = 'bg-green-500'; 
            const status = asset.proses_penjualan.toLowerCase();
            if(status === 'sold') badgeColor = 'bg-red-600';
            else if(status === 'lelang') badgeColor = 'bg-blue-600';
            else if(status === 'cessie') badgeColor = 'bg-orange-500';

            const cardHTML = `
                <a href="${detailUrl}" class="block h-full">
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 relative group flex flex-col h-full">
                        <div class="relative h-48 overflow-hidden bg-gray-100 shrink-0">
                            <img src="${imgUrl}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='${defaultImg}';">
                            <span class="absolute top-3 left-3 ${badgeColor} text-white text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider shadow-sm">${asset.proses_penjualan}</span>
                        </div>
                        <div class="p-4 flex flex-col flex-grow">
                            <p class="text-blue-600 font-black text-xl mb-1">Rp ${formatHarga}</p>
                            <p class="text-gray-800 font-bold text-sm mb-2 line-clamp-1">${asset.jenis_surat} - ${asset.nomor_surat}</p>
                            <p class="text-gray-500 text-xs mb-4 line-clamp-2 leading-relaxed"><i class="fas fa-map-marker-alt text-red-400 mr-1"></i> ${asset.alamat_asset}</p>
                            
                            <div class="mt-auto grid grid-cols-2 gap-2 border-t border-gray-50 pt-3">
                                <div class="bg-gray-50 rounded-lg p-2 text-center">
                                    <p class="text-[10px] text-gray-500 font-medium">LT</p>
                                    <p class="text-xs font-bold text-gray-800">${asset.luas_tanah} m²</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2 text-center">
                                    <p class="text-[10px] text-gray-500 font-medium">LB</p>
                                    <p class="text-xs font-bold text-gray-800">${asset.luas_bangunan} m²</p>
                                </div>
                            </div>
                        </div>
                    </article>
                </a>
            `;
            assetGrid.insertAdjacentHTML('beforeend', cardHTML);
        });
    }

    // ---------------------------------------------------------
    // 4. RENDER TOMBOL PAGINATION
    // ---------------------------------------------------------
    function renderPagination(totalPages) {
        paginationControls.innerHTML = '';
        if (totalPages <= 1) return; 

        // Tombol Prev
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = `<i class="fas fa-chevron-left"></i>`;
        prevBtn.className = `w-10 h-10 rounded-xl font-bold flex items-center justify-center transition-colors border ${currentPage === 1 ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-blue-600 border-blue-200 hover:bg-blue-50'}`;
        prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; fetchAssets(); window.scrollTo({top: 0, behavior: 'smooth'}); } };
        paginationControls.appendChild(prevBtn);

        // Angka Halaman (Dibuat Max 5 tombol biar di HP gak numpuk)
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            paginationControls.insertAdjacentHTML('beforeend', `<span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>`);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.innerText = i;
            pageBtn.className = `w-10 h-10 rounded-xl font-bold flex items-center justify-center transition-colors border ${currentPage === i ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'}`;
            pageBtn.onclick = () => { currentPage = i; fetchAssets(); window.scrollTo({top: 0, behavior: 'smooth'}); };
            paginationControls.appendChild(pageBtn);
        }

        if (endPage < totalPages) {
            paginationControls.insertAdjacentHTML('beforeend', `<span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>`);
        }

        // Tombol Next
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = `<i class="fas fa-chevron-right"></i>`;
        nextBtn.className = `w-10 h-10 rounded-xl font-bold flex items-center justify-center transition-colors border ${currentPage === totalPages ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-blue-600 border-blue-200 hover:bg-blue-50'}`;
        nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; fetchAssets(); window.scrollTo({top: 0, behavior: 'smooth'}); } };
        paginationControls.appendChild(nextBtn);
    }

    // ---------------------------------------------------------
    // 5. EVENT LISTENER FILTER (Reset Halaman & Reload Data)
    // ---------------------------------------------------------
    function onFilterChange() {
        currentPage = 1;
        fetchAssets();
    }

    // Debounce khusus untuk Input Search agar tidak berat
    inputSearch.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            onFilterChange();
        }, 500); // Tunggu user ngetik 0.5 detik baru hit API
    });

    filterStatus.addEventListener('change', onFilterChange);
    filterJenis.addEventListener('change', onFilterChange);

    // ---------------------------------------------------------
    // 6. HELPER UI & RESPONSIVE
    // ---------------------------------------------------------
    function renderSkeleton() {
        assetGrid.innerHTML = '';
        const limit = getItemsPerPage();
        for(let i=0; i<limit; i++){
            assetGrid.insertAdjacentHTML('beforeend', `<div class="animate-pulse bg-white rounded-2xl shadow-sm border border-gray-100 h-80"></div>`);
        }
    }

    function showEmptyState(msg) {
        assetGrid.innerHTML = `
            <div class="col-span-full py-16 flex flex-col items-center justify-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                <div class="w-16 h-16 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center mb-4 text-2xl"><i class="fas fa-folder-open"></i></div>
                <h3 class="text-lg font-bold text-gray-700">Oops!</h3>
                <p class="text-gray-500">${msg}</p>
                <button onclick="inputSearch.value=''; filterStatus.value='Semua'; filterJenis.value='Semua'; onFilterChange();" class="mt-4 text-blue-600 font-semibold hover:underline">Reset Filter</button>
            </div>
        `;
    }

    // Jika resize window melewati breakpoint mobile/desktop, reload data untuk menyesuaikan limit (6 vs 8)
    let resizeTimer;
    let currentLimit = getItemsPerPage();
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const newLimit = getItemsPerPage();
            if (newLimit !== currentLimit) {
                currentLimit = newLimit;
                currentPage = 1; // Reset halaman biar gak offset
                fetchAssets();
            }
        }, 300);
    });

    // Toggle Menu Filter Mobile
    const toggleFilterBtn = document.getElementById('toggleFilterBtn');
    const filterWrapper = document.getElementById('filterWrapper');
    const filterIcon = document.getElementById('filterIcon');

    toggleFilterBtn.addEventListener('click', () => {
        filterWrapper.classList.toggle('hidden');
        filterIcon.classList.toggle('rotate-180');
        toggleFilterBtn.classList.toggle('bg-blue-50');
    });

    // ---------------------------------------------------------
    // JALANKAN SAAT HALAMAN DIMUAT
    // ---------------------------------------------------------
    cekAutoFilterURL();
    fetchAssets();

</script>