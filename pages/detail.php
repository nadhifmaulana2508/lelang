<?php
function formatRupiah($angka, $prefix = 'Rp') {
    if (!is_numeric($angka)) {
        return $prefix . ' 0';
    }
    return $prefix . ' ' . number_format($angka, 0, ',', '.');
}

// ✅ FIX AAPANEL: Sanitasi input GET untuk mencegah blokir WAF (XSS/SQLi prevention)
$raw_id = $_GET['id'] ?? null;
$id = $raw_id ? htmlspecialchars($raw_id, ENT_QUOTES, 'UTF-8') : null;
$id_api = urlencode($id);

if (!$id) {
    echo "<div class='mt-32 text-center text-red-500 font-bold text-xl'>ID tidak ditemukan</div>";
    exit;
}

// Fetch data menggunakan PHP
$response = @file_get_contents(BASE_URL . "/api/asset/detail/?id=$id_api");
if (!$response) {
    echo "<div class='mt-32 text-center text-red-500 font-bold text-xl'>Gagal mengambil data asset.</div>";
    exit;
}

$data = json_decode($response, true);

if (!isset($data['data']) || empty($data['data'])) {
    echo "<div class='mt-32 text-center text-red-500 font-bold text-xl'>Data asset tidak tersedia.</div>";
    exit;
}

$asset = $data['data'];

// ==========================================
// ✅ BLOKIR AKSES JIKA STATUS SOLD
// ==========================================
if (strtolower($asset['proses_penjualan']) === 'sold') {
    echo "
    <div class='max-w-3xl mx-auto mt-32 mb-20 px-6 text-center min-h-[50vh] flex flex-col items-center justify-center'>
        <div class='w-24 h-24 bg-red-100 text-red-500 rounded-full flex items-center justify-center mb-6'>
            <i class='fas fa-times-circle text-5xl'></i>
        </div>
        <h1 class='text-3xl md:text-4xl font-extrabold text-gray-800 mb-3'>Aset Sudah Terjual</h1>
        <p class='text-gray-500 text-lg mb-8'>Maaf brokuu, aset <b>{$asset['jenis_surat']} No. {$asset['nomor_surat']}</b> yang kamu cari sudah laku terjual.</p>
        <a href='" . BASE_URL . "/asset' class='bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-xl transition-all shadow-md'>
            Lihat Aset Lainnya
        </a>
    </div>";
    include("views/footer.php");
    exit; // Stop proses render sisa halaman
}
?>

<style>
    html {
        scroll-behavior: smooth;
    }
    .scroll-target {
        scroll-margin-top: 180px; 
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;  
        scrollbar-width: none;  
    }
    #map {
        width: 100%;
        height: 400px;
        border-radius: 8px;
        z-index: 1;
    }
    @media (max-width: 768px) {
        #map {
            height: 250px;
        }
        .scroll-target {
            scroll-margin-top: 150px; 
        }
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<div class="max-w-6xl mx-auto mt-24 lg:mt-32 flex flex-col-reverse lg:flex-row gap-6 px-4 mb-24">

    <div id="leftSection" class="w-full lg:w-2/3">
        
        <div id="fotoSection">
            <div id="fotoContainer" class="bg-white shadow-md rounded-xl overflow-hidden relative group">
                <div class="absolute inset-0 flex items-center justify-center bg-gray-100 z-0" id="mainImageLoader">
                    <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <img id="mainImage" 
                     src="<?= BASE_URL ?>/img/agunan/<?= htmlspecialchars($asset['foto1'] ?? '-') ?>" 
                     class="w-full h-[250px] sm:h-[350px] md:h-[400px] object-cover transition-all duration-300 relative z-10"
                     onload="document.getElementById('mainImageLoader').classList.add('hidden')">
            </div>

            <div class="flex overflow-x-auto space-x-3 p-2 mt-2 no-scrollbar">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php if (!empty($asset["foto$i"])): ?>
                        <img src="<?= BASE_URL ?>/img/agunan/<?= htmlspecialchars($asset["foto$i"]) ?>" 
                             class="thumbnail w-20 h-16 sm:w-24 sm:h-20 object-cover rounded-lg shrink-0 cursor-pointer hover:opacity-80 border-2 border-transparent hover:border-blue-500 transition-all duration-200"
                             data-img="<?= BASE_URL ?>/img/agunan/<?= htmlspecialchars($asset["foto$i"]) ?>">
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <p class="text-gray-500 text-[10px] md:text-sm mt-3 font-medium px-1">
                <i class="fas fa-building mr-1"></i> Dipublikasikan oleh <?= htmlspecialchars($asset['nama_kantor'] ?? '-') ?> (<?= htmlspecialchars($asset['kode_kantor'] ?? '-') ?>)
            </p>
        </div>

        <div id="stickyMenu" class="sticky top-[85px] md:top-[100px] bg-white/95 backdrop-blur-md px-2 md:px-4 py-3 md:py-3 shadow-sm border border-gray-100 mt-6 rounded-xl flex justify-between md:justify-start overflow-x-auto no-scrollbar gap-2 md:gap-6 z-40">
            <a href="#deskripsi" class="flex flex-col md:flex-row items-center justify-center min-w-[60px] md:min-w-auto text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-info-circle text-xl md:text-base md:mr-2"></i>
                <span class="text-[10px] md:text-sm mt-1.5 md:mt-0">Deskripsi</span>
            </a>
            <a href="#spesifikasi" class="flex flex-col md:flex-row items-center justify-center min-w-[60px] md:min-w-auto text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-list-ul text-xl md:text-base md:mr-2"></i>
                <span class="text-[10px] md:text-sm mt-1.5 md:mt-0">Spek</span>
            </a>
            <a href="#lokasi" class="flex flex-col md:flex-row items-center justify-center min-w-[60px] md:min-w-auto text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-map-marker-alt text-xl md:text-base md:mr-2"></i>
                <span class="text-[10px] md:text-sm mt-1.5 md:mt-0">Lokasi</span>
            </a>
            <a href="#pembelian" class="flex flex-col md:flex-row items-center justify-center min-w-[60px] md:min-w-auto text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-shopping-cart text-xl md:text-base md:mr-2"></i>
                <span class="text-[10px] md:text-sm mt-1.5 md:mt-0">Beli</span>
            </a>
            <a href="#kalkulator" class="flex flex-col md:flex-row items-center justify-center min-w-[60px] md:min-w-auto text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-calculator text-xl md:text-base md:mr-2"></i>
                <span class="text-[10px] md:text-sm mt-1.5 md:mt-0">Cicilan</span>
            </a>
        </div>

        <?php
        $jadwal_lelang = ""; 
        $lokasi_lelang = "KPKNL Surakarta";
        ?>

        <div class="flex flex-col <?= !empty($jadwal_lelang) ? 'md:flex-row gap-4' : '' ?> mt-6">
            <div id="deskripsi" class="scroll-target <?= empty($jadwal_lelang) ? 'w-full' : 'w-full md:w-1/2' ?> bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg md:text-xl font-extrabold text-gray-800">Deskripsi Aset</h2>
                <p class="text-sm md:text-base text-gray-600 mt-3 leading-relaxed"><?= htmlspecialchars($asset['deskripsi'] ?? '-') ?></p>
            </div>

            <?php if (!empty($jadwal_lelang)) : ?>
            <div class="w-full md:w-1/2 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg md:text-xl font-extrabold text-gray-800 flex items-center">
                    Jadwal Lelang <span class="ml-2">📅</span>
                </h2>
                <p class="flex items-center text-sm md:text-base text-gray-600 mt-3">
                    <span class="mr-2 text-blue-500"><i class="fas fa-clock"></i></span> <?= $jadwal_lelang ?>
                </p>
                <p class="flex items-center text-sm md:text-base text-gray-600 mt-2">
                    <span class="mr-2 text-red-500"><i class="fas fa-map-marker-alt"></i></span> <?= $lokasi_lelang ?>
                </p>
                <button onclick="bukaModalPengajuan('Mekanisme Lelang')" class="mt-5 w-full px-4 py-2.5 border-2 border-blue-600 text-blue-600 font-bold rounded-xl hover:bg-blue-600 hover:text-white transition-colors duration-300">
                    Ikut Lelang
                </button>
            </div>
            <?php endif; ?>
        </div>

        <div id="spesifikasi" class="scroll-target mt-6 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-lg md:text-xl font-extrabold text-gray-800 mb-5">Spesifikasi Aset</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl font-black text-sm sm:text-base">LT</span>
                    <div>
                        <p class="text-[10px] sm:text-xs text-gray-500">Luas Tanah</p>
                        <p class="text-sm sm:text-base text-gray-800 font-bold"><?= htmlspecialchars($asset['luas_tanah'] ?? '-') ?> m²</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl font-black text-sm sm:text-base">LB</span>
                    <div>
                        <p class="text-[10px] sm:text-xs text-gray-500">Luas Bangunan</p>
                        <p class="text-sm sm:text-base text-gray-800 font-bold"><?= htmlspecialchars($asset['luas_bangunan'] ?? '-') ?> m²</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-orange-50 text-orange-500 rounded-xl font-black"><i class="fas fa-layer-group"></i></span>
                    <div>
                        <p class="text-[10px] sm:text-xs text-gray-500">Jumlah Lantai</p>
                        <p class="text-sm sm:text-base text-gray-800 font-bold"><?= htmlspecialchars($asset['lantai'] ?? '-') ?> Lantai</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 col-span-2 sm:col-span-3">
                    <span class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-green-50 text-green-600 rounded-xl font-black"><i class="fas fa-file-alt"></i></span>
                    <div>
                        <p class="text-[10px] sm:text-xs text-gray-500">Legalitas</p>
                        <p class="text-sm sm:text-base text-gray-800 font-bold"><?= htmlspecialchars($asset['jenis_surat'] ?? '-') ?> No. <?= htmlspecialchars($asset['nomor_surat'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $google_maps_link = $asset['link_maps'] ?? "https://maps.app.goo.gl/UChgvYGzTLqDQyeb7";
        $latitude = (!empty($asset['latitude'])) ? $asset['latitude'] : '-7.004966136697902';
        $longitude = (!empty($asset['longitude'])) ? $asset['longitude'] : '110.47268348264556';
        ?>

        <div id="lokasi" class="scroll-target mt-6 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-lg md:text-xl font-extrabold text-gray-800 mb-4">Lokasi Aset</h2>
            <p class="text-sm md:text-base text-gray-600 mb-4 leading-relaxed"><i class="fas fa-map-marker-alt text-red-500 mr-2"></i> <?= htmlspecialchars($asset['alamat_asset'] ?? '-') ?></p>
            
            <div id="map" class="shadow-inner border border-gray-200"></div>

            <div class="mt-4 flex justify-end">
                <a id="google-maps-link" href="<?= $google_maps_link ?>" target="_blank" 
                   class="inline-flex items-center justify-center bg-blue-50 text-blue-600 text-sm md:text-base font-bold py-2.5 px-5 rounded-xl hover:bg-blue-600 hover:text-white transition-colors duration-300">
                    Buka di Google Maps <i class="fas fa-external-link-alt ml-2"></i>
                </a>
            </div>
        </div>

        <div id="pembelian" class="scroll-target mt-6 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-lg md:text-xl font-extrabold text-gray-800 mb-4">Skema Pembelian</h2>

            <div class="flex flex-wrap gap-2 sm:gap-3 mt-4">
                <button id="btnLelang" class="px-4 py-2 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-bold text-white bg-blue-600 rounded-xl transition-colors shadow-sm">
                    Mekanisme Lelang / Cessie
                </button>
                <button id="btnKPR" class="px-4 py-2 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-colors">
                    Pembiayaan BKK Joglo
                </button>
            </div>

            <div id="contentLelang" class="mt-6 animate-fade-in-up">
                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                    Proses lelang dilakukan <strong>1 (satu) pintu</strong> melalui website dan aplikasi dengan ketentuan <strong>Direktorat Jenderal Kekayaan Negara (DJKN)</strong>. Pastikan sudah melakukan registrasi pada <a href="#" class="text-blue-600 font-semibold hover:underline">link lelang</a> sesuai jadwal yang tersedia.
                </p>
                <div class="flex justify-center mt-6 bg-blue-50 rounded-xl p-4">
                    <img src="<?= BASE_URL ?>/img/Group 6 (4).png" alt="Skema Lelang" class="w-full max-w-sm h-auto object-contain">
                </div>
            </div>

            <div id="contentKPR" class="mt-6 hidden animate-fade-in-up">
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl mb-6">
                    <p class="text-blue-800 text-xs sm:text-sm leading-relaxed">
                        PT BPR BKK Jateng (PERSERODA) menyediakan fasilitas pembelian dengan pembiayaan Solusi yang mudah dan cepat.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                    <div>
                        <h3 class="font-extrabold text-base sm:text-lg text-blue-600 mb-2">BKK Joglo</h3>
                        <p class="text-gray-600 text-xs sm:text-sm mb-3">Fasilitas kredit untuk pembelian tanah atau bangunan.</p>
                        <ul class="space-y-2">
                            <li class="flex items-center text-xs sm:text-sm text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2"></i> Rumah & Ruko</li>
                            <li class="flex items-center text-xs sm:text-sm text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2"></i> Apartemen</li>
                            <li class="flex items-center text-xs sm:text-sm text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2"></i> Tanah Kavling</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base sm:text-lg text-gray-800 mb-2">Dokumen Syarat:</h3>
                        <ul class="space-y-2">
                            <li class="flex items-center text-xs sm:text-sm text-gray-700"><i class="far fa-file-alt text-gray-400 mr-2"></i> KTP, NPWP, KK, Buku Nikah</li>
                            <li class="flex items-center text-xs sm:text-sm text-gray-700"><i class="far fa-file-alt text-gray-400 mr-2"></i> Surat Keterangan Gaji / Usaha</li>
                            <li class="flex items-center text-xs sm:text-sm text-gray-700"><i class="far fa-file-alt text-gray-400 mr-2"></i> Rekening Koran Terakhir</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kalkulator" class="scroll-target mt-6 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 pb-8">
            <h2 class="text-lg md:text-xl font-extrabold text-gray-800">Kalkulator KPR</h2>
            <p class="text-gray-500 text-xs sm:text-sm mb-6">Simulasikan estimasi cicilan per bulan Anda.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 bg-gray-50 p-4 sm:p-6 rounded-xl border border-gray-100">
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1">Harga Aset</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-gray-500 font-bold text-sm">Rp</span>
                        <input id="harga" type="text" 
                               value="<?= isset($asset['harga_jual']) ? number_format($asset['harga_jual'], 0, ',', '.') : '-'; ?>" 
                               readonly
                               class="pl-10 sm:pl-12 w-full border border-gray-200 rounded-xl p-2.5 sm:p-3 text-right bg-white shadow-sm font-semibold outline-none text-sm sm:text-base">
                    </div>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1">Uang Muka (DP)</label>
                    <div class="flex items-center bg-white border border-gray-200 p-1 rounded-xl shadow-sm">
                        <input id="dp" type="number" value="10" 
                               class="w-16 p-1.5 text-center font-bold focus:outline-none bg-transparent text-sm sm:text-base">  
                        <span class="text-gray-400 font-bold px-2 border-r border-gray-200 text-sm">%</span>
                        <span id="dpAmount" class="text-blue-600 font-bold ml-auto pr-3 text-xs sm:text-sm">Rp 0</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1">Suku Bunga (%)</label>
                    <input id="bunga" type="number" value="10.5" step="0.01" min="1" max="20"
                           class="w-full border border-gray-200 rounded-xl p-2.5 sm:p-3 text-right shadow-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none text-sm sm:text-base">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1">Jangka Waktu (Tenor)</label>
                    <select id="tenor" class="w-full border border-gray-200 rounded-xl p-2.5 sm:p-3 shadow-sm font-bold bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm sm:text-base">
                        <option value="5">5 Tahun</option>
                        <option value="10" selected>10 Tahun</option>
                        <option value="15">15 Tahun</option>
                    </select>
                </div>
            </div>

            <div class="text-center mt-6 p-5 sm:p-6 bg-blue-600 rounded-2xl shadow-lg text-white">
                <p class="text-blue-100 font-medium text-sm">Estimasi Cicilan per Bulan:</p>
                <p id="cicilan" class="text-2xl sm:text-4xl font-black mt-1">Rp 0</p>
            </div>
            <p class="text-gray-400 text-[10px] sm:text-xs mt-3 text-center leading-relaxed">
                *Catatan: Perhitungan ini bersifat simulasi estimasi. Hubungi petugas kami untuk detail akurat.
            </p>
        </div>

    </div>

    <div id="rightSectionWrapper" class="w-full lg:w-1/3 relative">
        <div id="rightSection" class="bg-white p-5 sm:p-6 shadow-xl shadow-blue-900/5 rounded-2xl border border-blue-50 lg:sticky lg:top-24 mt-2 md:mt-0 z-20 transition-all duration-300">
            
            <div class="flex justify-between items-center mb-2">
                <span class="inline-block bg-green-100 text-green-700 text-[10px] sm:text-xs font-black px-3 py-1 rounded-lg uppercase tracking-wide">
                    <?= htmlspecialchars($asset['proses_penjualan'] ?? '-') ?>
                </span>
                <div class="flex items-center text-gray-500 text-xs sm:text-sm font-bold tooltip" title="Jumlah dilihat">
                    <i class="fas fa-eye mr-1.5 text-blue-500"></i>
                    <span id="view-count-text"><?= htmlspecialchars(isset($asset['view_count']) ? $asset['view_count'] : '0') ?></span>
                </div>
            </div>

            <h2 class="text-base md:text-lg font-bold text-gray-800 leading-tight"><?= htmlspecialchars($asset['jenis_surat'] ?? '-') ?> No. <?= htmlspecialchars($asset['nomor_surat'] ?? '-') ?></h2>
            <p class="text-blue-600 text-2xl md:text-3xl font-black mt-2"><?= formatRupiah($asset['harga_jual']) ?? '-'; ?></p>

            <div class="grid grid-cols-2 gap-2 sm:gap-3 mt-4 sm:mt-5 border-t border-b border-gray-100 py-4">
                <div class="bg-gray-50 rounded-xl p-2 sm:p-3 text-center">
                    <p class="text-[10px] sm:text-xs text-gray-500 font-medium mb-1">Luas Tanah</p>
                    <p class="font-bold text-gray-800 text-sm sm:text-base"><?= htmlspecialchars($asset['luas_tanah'] ?? '-') ?> m²</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-2 sm:p-3 text-center">
                    <p class="text-[10px] sm:text-xs text-gray-500 font-medium mb-1">Luas Bangunan</p>
                    <p class="font-bold text-gray-800 text-sm sm:text-base"><?= htmlspecialchars($asset['luas_bangunan'] ?? '-') ?> m²</p>
                </div>
            </div>

            <div class="hidden lg:block mt-5 space-y-3">
                <button onclick="bukaModalPengajuan('Pembiayaan BKK Joglo (KPR)')" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl hover:bg-blue-700 hover:shadow-sm transition-all duration-300">
                    Ajukan Pembiayaan
                </button>
                <button onclick="bukaModalPengajuan('')" class="w-full flex items-center justify-center gap-2 border-2 border-green-500 text-green-600 font-bold py-3.5 rounded-xl hover:bg-green-50 transition-all duration-300">
                    <i class="fab fa-whatsapp text-xl"></i> Hubungi Petugas
                </button>
            </div>

            <p class="text-gray-400 text-[10px] sm:text-xs text-center mt-5"><i class="fas fa-info-circle mr-1"></i> Senin - Jumat, 08:00 - 16:00 WIB</p>
        </div>
    </div>

    <div id="mobileButtons" class="fixed inset-x-0 bottom-0 z-50 lg:hidden bg-white border-t border-gray-200 shadow-[0_-10px_20px_rgba(0,0,0,0.05)] p-3 sm:p-4 transition-transform duration-300">
        <div class="flex gap-2 sm:gap-3">
            <button onclick="bukaModalPengajuan('Pembiayaan BKK Joglo (KPR)')" class="w-1/2 bg-blue-600 text-white text-sm sm:text-base font-bold py-3 sm:py-3 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                Ajukan Pembiayaan
            </button>
            <button onclick="bukaModalPengajuan('')" class="w-1/2 flex items-center justify-center gap-2 border-2 border-green-500 text-green-600 text-sm sm:text-base font-bold py-3 sm:py-3 rounded-xl bg-green-50 transition-colors">
                <i class="fab fa-whatsapp text-lg"></i> Hubungi
            </button>
        </div>
    </div>
</div>

<div id="modalPengajuan" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="modalContent">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Form Pengajuan Aset</h3>
            <button onclick="tutupModalPengajuan()" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none focus:outline-none">×</button>
        </div>
        <form id="formPengajuanAset" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Aset Pilihan</label>
                <input type="text" value="<?= htmlspecialchars($asset['jenis_surat'] ?? '') ?> No. <?= htmlspecialchars($asset['nomor_surat'] ?? '') ?>" readonly class="w-full border border-gray-200 rounded-lg p-2.5 bg-gray-100 text-gray-500 text-sm font-semibold outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="formNama" required placeholder="Masukkan nama Anda" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">No. WhatsApp <span class="text-red-500">*</span></label>
                <input type="number" id="formWa" required placeholder="08123456789" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Minat Skema <span class="text-red-500">*</span></label>
                <select id="formSkema" required class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="" disabled selected>Pilih skema...</option>
                    <option value="Pembiayaan BKK Joglo (KPR)">Pembiayaan BKK Joglo (KPR)</option>
                    <option value="Mekanisme Lelang">Mekanisme Lelang</option>
                    <option value="Mekanisme Cessie">Mekanisme Cessie (Hak Tagih)</option>
                    <option value="Hanya Tanya-tanya Dulu">Hanya Tanya-tanya Dulu</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Pesan Tambahan (Opsional)</label>
                <textarea id="formPesan" rows="3" placeholder="Tuliskan pertanyaan atau pesan Anda..." class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
            </div>
            
            <div class="pt-3 space-y-3">
                <button type="button" onclick="submitToDB()" id="btnSubmitDB" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                    Ajukan Pembiayaan
                </button>
                <button type="button" onclick="submitToWA()" class="w-full flex items-center justify-center gap-2 border-2 border-green-500 text-green-600 font-bold py-3 rounded-xl hover:bg-green-50 transition-colors">
                    <i class="fab fa-whatsapp text-xl"></i> Hubungi Petugas
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalNotif" class="fixed inset-0 z-[110] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform scale-95 transition-transform duration-300 text-center p-6" id="modalNotifContent">
        <div id="notifIconWrapper" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i id="notifIcon" class="fas fa-check text-3xl"></i>
        </div>
        <h3 id="notifTitle" class="text-xl font-bold text-gray-800 mb-2">Berhasil!</h3>
        <p id="notifMessage" class="text-gray-600 text-sm mb-6 leading-relaxed">Pesan notifikasi di sini.</p>
        <button onclick="tutupModalNotif()" id="notifButton" class="w-full text-white font-bold py-3 rounded-xl transition-colors">
            Tutup
        </button>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // ----------------------------------------------------
    // 0. SCRIPT TRACK VIEW COUNT
    // ----------------------------------------------------
    fetch("<?= BASE_URL ?>/api/asset/track-view", {
        method: "POST", 
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_aset: "<?= $id ?>" })
    })
    .then(res => res.json())
    .then(data => console.log("View tracked successfully"))
    .catch(err => console.log("Note: API endpoint track-view belum siap di Backend."));

    // ----------------------------------------------------
    // 1. Script Modal Form Pengajuan & Dual Action Submit
    // ----------------------------------------------------
    const modal = document.getElementById('modalPengajuan');
    const modalContent = document.getElementById('modalContent');
    const formSkemaSelect = document.getElementById('formSkema');
    const formElement = document.getElementById("formPengajuanAset");

    const modalNotif = document.getElementById('modalNotif');
    const modalNotifContent = document.getElementById('modalNotifContent');
    const notifIconWrapper = document.getElementById('notifIconWrapper');
    const notifIcon = document.getElementById('notifIcon');
    const notifTitle = document.getElementById('notifTitle');
    const notifMessage = document.getElementById('notifMessage');
    const notifButton = document.getElementById('notifButton');

    function bukaModalPengajuan(defaultSkema = '') {
        if (defaultSkema) {
            formSkemaSelect.value = defaultSkema;
        } else {
            formSkemaSelect.selectedIndex = 0; 
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }, 10);
    }

    function tutupModalPengajuan() {
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function tampilNotif(type, title, message) {
        notifTitle.innerText = title;
        notifMessage.innerText = message;

        if(type === 'success') {
            notifIconWrapper.className = "w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4";
            notifIcon.className = "fas fa-check text-3xl";
            notifButton.className = "w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition-colors";
        } else {
            notifIconWrapper.className = "w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4";
            notifIcon.className = "fas fa-times text-3xl";
            notifButton.className = "w-full bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 transition-colors";
        }

        modalNotif.classList.remove('hidden');
        setTimeout(() => {
            modalNotif.classList.remove('opacity-0');
            modalNotifContent.classList.remove('scale-95');
        }, 10);
    }

    function tutupModalNotif() {
        modalNotif.classList.add('opacity-0');
        modalNotifContent.classList.add('scale-95');
        setTimeout(() => {
            modalNotif.classList.add('hidden');
        }, 300);
    }

    function checkFormValidity() {
        if (!formElement.checkValidity()) {
            formElement.reportValidity();
            return false;
        }
        return true;
    }

    function getFormPayload() {
        return {
            id_aset: "<?= $id ?>",
            nama_lengkap: document.getElementById('formNama').value,
            no_wa: document.getElementById('formWa').value,
            minat_skema: document.getElementById('formSkema').value,
            pesan: document.getElementById('formPesan').value
        };
    }

    function submitToDB() {
        if (!checkFormValidity()) return;
        const payload = getFormPayload();

        const btnSubmit = document.getElementById('btnSubmitDB');
        const originalText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Memproses...`;
        btnSubmit.disabled = true;

        fetch("<?= BASE_URL ?>/api/asset/store", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        })
        .then(res => {
            if (!res.ok) throw new Error("Gagal kirim ke server");
            return res.json();
        })
        .then(data => {
            btnSubmit.innerHTML = originalText;
            btnSubmit.disabled = false;
            tutupModalPengajuan();
            formElement.reset();
            tampilNotif('success', 'Berhasil!', 'Data pengajuan pembiayaan berhasil dikirim ke database PT BPR BKK Jateng.');
        })
        .catch(err => {
            btnSubmit.innerHTML = originalText;
            btnSubmit.disabled = false;
            tampilNotif('error', 'Gagal', 'Terjadi kesalahan saat mengirim data. Pastikan endpoint API BE sudah benar.');
        });
    }

    function submitToWA() {
        if (!checkFormValidity()) return;
        const payload = getFormPayload();

        const asetInfo = "<?= htmlspecialchars($asset['jenis_surat'] ?? '') ?> No. <?= htmlspecialchars($asset['nomor_surat'] ?? '') ?>";
        let textWa = `Halo Admin BKK Jateng, saya berminat untuk menanyakan aset berikut:\n\n`;
        textWa += `*Detail Aset:* ${asetInfo}\n`;
        textWa += `*Nama Lengkap:* ${payload.nama_lengkap}\n`;
        textWa += `*Nomor WA:* ${payload.no_wa}\n`;
        textWa += `*Minat Skema:* ${payload.minat_skema}\n`;
        if(payload.pesan) textWa += `*Pesan:* ${payload.pesan}\n\n`;
        textWa += `Mohon informasi dan arahan selanjutnya. Terima kasih.`;

        tutupModalPengajuan();
        formElement.reset();

        window.open(`https://wa.me/6288228659668?text=${encodeURIComponent(textWa)}`, '_blank');
    }

    // ----------------------------------------------------
    // 2. Script Thumbnail Gallery
    // ----------------------------------------------------
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('mainImage');
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', () => {
            const imgSrc = thumb.getAttribute('data-img');
            mainImage.classList.add('opacity-50'); 
            setTimeout(() => {
                mainImage.setAttribute('src', imgSrc);
                mainImage.classList.remove('opacity-50');
            }, 150);
        });
    });

    // ----------------------------------------------------
    // 3. Script Switch Skema Pembelian
    // ----------------------------------------------------
    document.getElementById('btnLelang').addEventListener('click', function () {
        document.getElementById('contentLelang').classList.remove('hidden');
        document.getElementById('contentKPR').classList.add('hidden');

        this.classList.add('bg-blue-600', 'text-white');
        this.classList.remove('text-gray-600', 'bg-gray-100', 'hover:bg-blue-50');

        const btnKPR = document.getElementById('btnKPR');
        btnKPR.classList.remove('bg-blue-600', 'text-white');
        btnKPR.classList.add('text-gray-600', 'bg-gray-100', 'hover:bg-blue-50');
    });

    document.getElementById('btnKPR').addEventListener('click', function () {
        document.getElementById('contentKPR').classList.remove('hidden');
        document.getElementById('contentLelang').classList.add('hidden');

        this.classList.add('bg-blue-600', 'text-white');
        this.classList.remove('text-gray-600', 'bg-gray-100', 'hover:bg-blue-50');

        const btnLelang = document.getElementById('btnLelang');
        btnLelang.classList.remove('bg-blue-600', 'text-white');
        btnLelang.classList.add('text-gray-600', 'bg-gray-100', 'hover:bg-blue-50');
    });

    // ----------------------------------------------------
    // 4. Kalkulator KPR
    // ----------------------------------------------------
    function hitungKPR() {
        let harga = parseFloat(document.getElementById("harga").value.replace(/\D/g, '')) || 0;
        let dpPersen = parseFloat(document.getElementById("dp").value) || 0;
        let bunga = parseFloat(document.getElementById("bunga").value) || 0;
        let tenor = parseInt(document.getElementById("tenor").value) || 5;

        let dp = harga * (dpPersen / 100);
        let pinjaman = harga - dp;
        let bungaBulanan = bunga / 100 / 12;
        let bulan = tenor * 12;
        
        let cicilan = 0;
        if (pinjaman > 0 && bungaBulanan > 0) {
            cicilan = (pinjaman * bungaBulanan) / (1 - Math.pow(1 + bungaBulanan, -bulan));
        }

        document.getElementById("dpAmount").innerText = "Rp " + new Intl.NumberFormat("id-ID").format(Math.round(dp));
        document.getElementById("cicilan").innerText = "Rp " + new Intl.NumberFormat("id-ID").format(Math.round(cicilan));
    }

    document.getElementById("dp").addEventListener("input", hitungKPR);
    document.getElementById("bunga").addEventListener("input", hitungKPR);
    document.getElementById("tenor").addEventListener("change", hitungKPR);
    hitungKPR();

    // ----------------------------------------------------
    // 5. Inisialisasi Map Leaflet
    // ----------------------------------------------------
    document.addEventListener("DOMContentLoaded", function() {
        var latitude = <?= $latitude ?>;
        var longitude = <?= $longitude ?>;

        var map = L.map('map').setView([latitude, longitude], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        L.marker([latitude, longitude]).addTo(map).bindPopup("<b>Lokasi Aset</b>").openPopup();
    });

// ----------------------------------------------------
    // 6. Fix Mobile Btn Hide: Hilang SEBELUM kena footer
    // ----------------------------------------------------
    const mobileButtons = document.getElementById('mobileButtons');
    
    // Kita pakai window scroll listener
    window.addEventListener('scroll', () => {
        if(!mobileButtons) return;

        // Ambil elemen footer atau batas bawah halaman
        const footer = document.querySelector('footer'); // Pastikan tagnya <footer> atau ganti ke id footer kamu
        const windowHeight = window.innerHeight;
        const scrollY = window.scrollY;
        const bodyHeight = document.documentElement.scrollHeight;

        // LOGIKA 1: Jika pakai deteksi elemen footer (Lebih Presisi)
        if (footer) {
            const footerTop = footer.getBoundingClientRect().top;
            // Jika jarak atas footer ke layar sudah kurang dari tinggi layar (footer mulai kelihatan)
            if (footerTop < windowHeight) {
                mobileButtons.classList.add('translate-y-full', 'opacity-0');
            } else {
                mobileButtons.classList.remove('translate-y-full', 'opacity-0');
            }
        } 
        
        // LOGIKA 2: Backup jika footer tidak ditemukan (Pakai kalkulasi tinggi halaman)
        else {
            // Hilangkan tombol 100px sebelum mentok bawah
            if ((windowHeight + scrollY) >= (bodyHeight - 150)) {
                mobileButtons.classList.add('translate-y-full', 'opacity-0');
            } else {
                mobileButtons.classList.remove('translate-y-full', 'opacity-0');
            }
        }
    });
</script>