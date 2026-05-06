<?php
function formatRupiah($angka, $prefix = 'Rp') {
    if (!is_numeric($angka)) {
        return $prefix . ' 0';
    }
    return $prefix . ' ' . number_format($angka, 0, ',', '.');
}

// ✅ FIX AAPANEL: Sanitasi ID agar tidak diblokir WAF/Firewall
$raw_id = $_GET['id'] ?? null;
$id = $raw_id ? htmlspecialchars($raw_id, ENT_QUOTES, 'UTF-8') : null;
$id_api = urlencode($id);

if (!$id) {
    echo "<div class='mt-32 text-center text-red-500 font-bold text-xl'>ID tidak ditemukan</div>";
    exit;
}

// Fetch data dari API
$response = @file_get_contents(BASE_URL . "/api/asset/detail/?id=$id_api");
if (!$response) {
    echo "<div class='mt-32 text-center text-red-500 font-bold text-xl'>Gagal mengambil data asset.</div>";
    exit;
}

$data = json_decode($response, true);
$asset = $data['data'] ?? null;

if (!$asset) {
    echo "<div class='mt-32 text-center text-red-500 font-bold text-xl'>Data asset tidak tersedia.</div>";
    exit;
}

// Blokir jika status SOLD
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
    exit;
}
?>

<style>
    html { scroll-behavior: smooth; }
    .scroll-target { scroll-margin-top: 180px; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    #map { width: 100%; height: 400px; border-radius: 8px; z-index: 1; }
    @media (max-width: 768px) {
        #map { height: 250px; }
        .scroll-target { scroll-margin-top: 150px; }
    }
    /* Animasi Halus untuk Tombol Mobile */
    #mobileButtons { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
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
                <img id="mainImage" src="<?= BASE_URL ?>/img/agunan/<?= $asset['foto1'] ?? '-'; ?>" class="w-full h-[250px] sm:h-[350px] md:h-[400px] object-cover transition-all duration-300 relative z-10" onload="document.getElementById('mainImageLoader').classList.add('hidden')">
            </div>

            <div class="flex overflow-x-auto space-x-3 p-2 mt-2 no-scrollbar">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php if (!empty($asset["foto$i"])): ?>
                        <img src="<?= BASE_URL ?>/img/agunan/<?= $asset["foto$i"]; ?>" class="thumbnail w-20 h-16 sm:w-24 sm:h-20 object-cover rounded-lg shrink-0 cursor-pointer hover:opacity-80 border-2 border-transparent hover:border-blue-500 transition-all duration-200" data-img="<?= BASE_URL ?>/img/agunan/<?= $asset["foto$i"]; ?>">
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>

        <div id="stickyMenu" class="sticky top-[85px] md:top-[100px] bg-white/95 backdrop-blur-md px-2 md:px-4 py-3 md:py-3 shadow-sm border border-gray-100 mt-6 rounded-xl flex justify-between md:justify-start overflow-x-auto no-scrollbar gap-2 md:gap-6 z-40">
            <a href="#deskripsi" class="flex flex-col md:flex-row items-center justify-center min-w-[60px] md:min-w-auto text-gray-500 hover:text-blue-600 font-bold transition-colors"><i class="fas fa-info-circle text-xl md:text-base md:mr-2"></i><span class="text-[10px] md:text-sm mt-1.5 md:mt-0">Deskripsi</span></a>
            <a href="#spesifikasi" class="flex flex-col md:flex-row items-center justify-center min-w-[60px] md:min-w-auto text-gray-500 hover:text-blue-600 font-bold transition-colors"><i class="fas fa-list-ul text-xl md:text-base md:mr-2"></i><span class="text-[10px] md:text-sm mt-1.5 md:mt-0">Spek</span></a>
            <a href="#lokasi" class="flex flex-col md:flex-row items-center justify-center min-w-[60px] md:min-w-auto text-gray-500 hover:text-blue-600 font-bold transition-colors"><i class="fas fa-map-marker-alt text-xl md:text-base md:mr-2"></i><span class="text-[10px] md:text-sm mt-1.5 md:mt-0">Lokasi</span></a>
            <a href="#kalkulator" class="flex flex-col md:flex-row items-center justify-center min-w-[60px] md:min-w-auto text-gray-500 hover:text-blue-600 font-bold transition-colors"><i class="fas fa-calculator text-xl md:text-base md:mr-2"></i><span class="text-[10px] md:text-sm mt-1.5 md:mt-0">Cicilan</span></a>
        </div>

        <div id="deskripsi" class="scroll-target mt-6 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-lg md:text-xl font-extrabold text-gray-800">Deskripsi Aset</h2>
            <p class="text-sm md:text-base text-gray-600 mt-3 leading-relaxed"><?= $asset['deskripsi'] ?? '-'; ?></p>
        </div>

        <div id="spesifikasi" class="scroll-target mt-6 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-lg md:text-xl font-extrabold text-gray-800 mb-5">Spesifikasi Aset</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl font-black text-sm sm:text-base">LT</span>
                    <div><p class="text-[10px] sm:text-xs text-gray-500">Luas Tanah</p><p class="text-sm sm:text-base text-gray-800 font-bold"><?= $asset['luas_tanah'] ?? '-'; ?> m²</p></div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl font-black text-sm sm:text-base">LB</span>
                    <div><p class="text-[10px] sm:text-xs text-gray-500">Luas Bangunan</p><p class="text-sm sm:text-base text-gray-800 font-bold"><?= $asset['luas_bangunan'] ?? '-'; ?> m²</p></div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-green-50 text-green-600 rounded-xl font-black"><i class="fas fa-file-alt"></i></span>
                    <div><p class="text-[10px] sm:text-xs text-gray-500">Legalitas</p><p class="text-sm sm:text-base text-gray-800 font-bold"><?= $asset['jenis_surat'] ?? '-'; ?></p></div>
                </div>
            </div>
        </div>

        <div id="lokasi" class="scroll-target mt-6 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-lg md:text-xl font-extrabold text-gray-800 mb-4">Lokasi Aset</h2>
            <p class="text-sm md:text-base text-gray-600 mb-4"><i class="fas fa-map-marker-alt text-red-500 mr-2"></i> <?= $asset['alamat_asset'] ?? '-'; ?></p>
            <div id="map" class="shadow-inner border border-gray-200"></div>
        </div>

        <div id="kalkulator" class="scroll-target mt-6 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 pb-8">
            <h2 class="text-lg md:text-xl font-extrabold text-gray-800">Kalkulator KPR</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 bg-gray-50 p-4 rounded-xl">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Harga Aset</label>
                    <input id="harga" type="text" value="<?= number_format($asset['harga_jual'], 0, ',', '.'); ?>" readonly class="w-full border border-gray-200 rounded-xl p-2.5 text-right font-bold outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tenor (Tahun)</label>
                    <select id="tenor" class="w-full border border-gray-200 rounded-xl p-2.5 font-bold outline-none">
                        <option value="5">5 Tahun</option><option value="10" selected>10 Tahun</option><option value="15">15 Tahun</option>
                    </select>
                </div>
            </div>
            <div class="text-center mt-6 p-6 bg-blue-600 rounded-2xl shadow-lg text-white">
                <p class="text-blue-100 text-sm">Estimasi Cicilan per Bulan:</p>
                <p id="cicilan" class="text-3xl font-black mt-1">Rp 0</p>
            </div>
        </div>
    </div>

    <div id="rightSectionWrapper" class="w-full lg:w-1/3 relative">
        <div id="rightSection" class="bg-white p-6 shadow-xl rounded-2xl border border-blue-50 lg:sticky lg:top-24 z-20">
            <span class="inline-block bg-green-100 text-green-700 text-xs font-black px-3 py-1 rounded-lg uppercase"><?= $asset['proses_penjualan'] ?? '-'; ?></span>
            <h2 class="text-lg font-bold text-gray-800 mt-2"><?= $asset['jenis_surat'] ?? '-'; ?> No. <?= $asset['nomor_surat'] ?? '-'; ?></h2>
            <p class="text-blue-600 text-3xl font-black mt-2"><?= formatRupiah($asset['harga_jual']); ?></p>
            <div class="hidden lg:block mt-5 space-y-3">
                <button onclick="bukaModalPengajuan('Pembiayaan BKK Joglo (KPR)')" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl hover:bg-blue-700 transition-all">Ajukan Pembiayaan</button>
                <button onclick="bukaModalPengajuan('')" class="w-full flex items-center justify-center gap-2 border-2 border-green-500 text-green-600 font-bold py-3.5 rounded-xl hover:bg-green-50"><i class="fab fa-whatsapp"></i> Hubungi Petugas</button>
            </div>
        </div>
    </div>

    <div id="mobileButtons" class="fixed inset-x-0 bottom-0 z-50 lg:hidden bg-white border-t border-gray-200 p-4 flex gap-3 shadow-[0_-10px_20px_rgba(0,0,0,0.05)]">
        <button onclick="bukaModalPengajuan('Pembiayaan BKK Joglo (KPR)')" class="w-1/2 bg-blue-600 text-white text-sm font-bold py-3.5 rounded-xl">Ajukan</button>
        <button onclick="bukaModalPengajuan('')" class="w-1/2 border-2 border-green-500 text-green-600 text-sm font-bold py-3.5 rounded-xl flex items-center justify-center gap-2"><i class="fab fa-whatsapp"></i> Hubungi</button>
    </div>
</div>

<div id="modalPengajuan" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="modalContent">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Form Pengajuan Aset</h3>
            <button onclick="tutupModalPengajuan()" class="text-gray-400 hover:text-red-500 text-2xl font-bold focus:outline-none">×</button>
        </div>
        <form id="formPengajuanAset" class="p-5 space-y-4">
            <div><label class="block text-xs font-bold text-gray-700 mb-1">Aset Pilihan</label><input type="text" value="<?= $asset['jenis_surat'] ?? '' ?> No. <?= $asset['nomor_surat'] ?? '' ?>" readonly class="w-full border rounded-lg p-2.5 bg-gray-100 text-sm font-semibold outline-none"></div>
            <div><label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap *</label><input type="text" id="formNama" required class="w-full border rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500"></div>
            <div><label class="block text-xs font-bold text-gray-700 mb-1">No. WhatsApp *</label><input type="number" id="formWa" required class="w-full border rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500"></div>
            <div><label class="block text-xs font-bold text-gray-700 mb-1">Minat Skema *</label>
                <select id="formSkema" required class="w-full border rounded-lg p-2.5 text-sm outline-none bg-white">
                    <option value="" disabled selected>Pilih skema...</option>
                    <option value="Pembiayaan BKK Joglo (KPR)">Pembiayaan BKK Joglo (KPR)</option>
                    <option value="Mekanisme Lelang">Mekanisme Lelang</option>
                    <option value="Hanya Tanya-tanya Dulu">Hanya Tanya-tanya Dulu</option>
                </select>
            </div>
            <div><label class="block text-xs font-bold text-gray-700 mb-1">Pesan (Opsional)</label><textarea id="formPesan" rows="3" class="w-full border rounded-lg p-2.5 text-sm outline-none resize-none"></textarea></div>
            <div class="pt-3 space-y-3">
                <button type="button" onclick="submitToDB()" id="btnSubmitDB" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl hover:bg-blue-700">Ajukan Pembiayaan</button>
                <button type="button" onclick="submitToWA()" class="w-full border-2 border-green-500 text-green-600 font-bold py-3 rounded-xl hover:bg-green-50 flex items-center justify-center gap-2"><i class="fab fa-whatsapp"></i> Hubungi Petugas</button>
            </div>
        </form>
    </div>
</div>

<div id="modalNotif" class="fixed inset-0 z-[110] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden p-6 text-center" id="modalNotifContent">
        <div id="notifIconWrapper" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"></div>
        <h3 id="notifTitle" class="text-xl font-bold mb-2"></h3>
        <p id="notifMessage" class="text-gray-600 text-sm mb-6"></p>
        <button onclick="tutupModalNotif()" id="notifButton" class="w-full text-white font-bold py-3 rounded-xl">Tutup</button>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const modal = document.getElementById('modalPengajuan');
    const modalContent = document.getElementById('modalContent');
    const formSkemaSelect = document.getElementById('formSkema');
    const formElement = document.getElementById("formPengajuanAset");

    // Modal Notif
    const modalNotif = document.getElementById('modalNotif');
    const notifIconWrapper = document.getElementById('notifIconWrapper');
    const notifTitle = document.getElementById('notifTitle');
    const notifMessage = document.getElementById('notifMessage');
    const notifButton = document.getElementById('notifButton');

    function bukaModalPengajuan(defaultSkema = '') {
        if (defaultSkema) formSkemaSelect.value = defaultSkema;
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
    }

    function tutupModalPengajuan() {
        modal.classList.add('opacity-0'); modalContent.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    function tampilNotif(type, title, message) {
        notifTitle.innerText = title; notifMessage.innerText = message;
        if(type === 'success') {
            notifIconWrapper.className = "w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4";
            notifIconWrapper.innerHTML = '<i class="fas fa-check text-3xl"></i>';
            notifButton.className = "w-full bg-blue-600 text-white font-bold py-3 rounded-xl";
        } else {
            notifIconWrapper.className = "w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4";
            notifIconWrapper.innerHTML = '<i class="fas fa-times text-3xl"></i>';
            notifButton.className = "w-full bg-red-600 text-white font-bold py-3 rounded-xl";
        }
        modalNotif.classList.remove('hidden');
        setTimeout(() => { modalNotif.classList.remove('opacity-0'); }, 10);
    }

    function tutupModalNotif() { modalNotif.classList.add('opacity-0'); setTimeout(() => { modalNotif.classList.add('hidden'); }, 300); }

    function submitToDB() {
        if (!formElement.checkValidity()) { formElement.reportValidity(); return; }
        const btn = document.getElementById('btnSubmitDB');
        btn.disabled = true; btn.innerHTML = 'Memproses...';
        
        const payload = {
            id_aset: "<?= $id ?>",
            nama_lengkap: document.getElementById('formNama').value,
            no_wa: document.getElementById('formWa').value,
            minat_skema: document.getElementById('formSkema').value,
            pesan: document.getElementById('formPesan').value
        };

        fetch("<?= BASE_URL ?>/api/asset/store", {
            method: "POST", headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        }).then(res => res.json()).then(data => {
            btn.disabled = false; btn.innerHTML = 'Ajukan Pembiayaan';
            tutupModalPengajuan(); formElement.reset();
            tampilNotif('success', 'Berhasil!', 'Data pengajuan pembiayaan berhasil dikirim.');
        }).catch(() => {
            btn.disabled = false; btn.innerHTML = 'Ajukan Pembiayaan';
            tampilNotif('error', 'Gagal', 'Terjadi kesalahan saat mengirim data.');
        });
    }

    function submitToWA() {
        if (!formElement.checkValidity()) { formElement.reportValidity(); return; }
        const nama = document.getElementById('formNama').value;
        const skema = document.getElementById('formSkema').value;
        const info = "<?= $asset['jenis_surat'] ?> No. <?= $asset['nomor_surat'] ?>";
        const text = encodeURIComponent(`Halo BKK Jateng, saya ${nama} tertarik aset: ${info}\nSkema: ${skema}`);
        window.open(`https://wa.me/6288228659668?text=${text}`, '_blank');
        tutupModalPengajuan();
    }

    // Leaflet Map
    document.addEventListener("DOMContentLoaded", function() {
        var lat = <?= !empty($asset['latitude']) ? $asset['latitude'] : '-7.0049' ?>;
        var lng = <?= !empty($asset['longitude']) ? $asset['longitude'] : '110.4726' ?>;
        var map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Aset").openPopup();
    });

    // Kalkulator
    function hitungKPR() {
        let harga = parseFloat(document.getElementById("harga").value.replace(/\D/g, '')) || 0;
        let pinjaman = harga * 0.9;
        let bunga = 0.105 / 12;
        let bulan = parseInt(document.getElementById("tenor").value) * 12;
        let cicilan = (pinjaman * bunga) / (1 - Math.pow(1 + bunga, -bulan));
        document.getElementById("cicilan").innerText = "Rp " + new Intl.NumberFormat("id-ID").format(Math.round(cicilan));
    }
    document.getElementById("tenor").addEventListener("change", hitungKPR);
    hitungKPR();

    // Floating Buttons Scroll Logic
    window.addEventListener('scroll', () => {
        const btns = document.getElementById('mobileButtons');
        const footer = document.querySelector('footer');
        if(!btns || !footer) return;
        const footerTop = footer.getBoundingClientRect().top;
        if (footerTop < window.innerHeight) {
            btns.classList.add('translate-y-full', 'opacity-0');
        } else {
            btns.classList.remove('translate-y-full', 'opacity-0');
        }
    });

    // Thumbnail Gallery
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.addEventListener('click', () => {
            document.getElementById('mainImage').src = thumb.getAttribute('data-img');
        });
    });
</script>