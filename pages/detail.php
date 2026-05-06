<?php
// ==========================================
// 1. DYNAMIC DATABASE CONNECTION (Anti-Block)
// ==========================================
$db_path = __DIR__ . '/api/config/database.php';
if (!file_exists($db_path)) {
    $db_path = __DIR__ . '/../api/config/database.php';
}

if (file_exists($db_path)) {
    require_once $db_path;
} else {
    die("<div class='mt-32 text-center text-red-500 font-bold'>Error: File koneksi database tidak ditemukan!</div>");
}

function formatRupiah($angka, $prefix = 'Rp') {
    if (!is_numeric($angka)) return $prefix . ' 0';
    return $prefix . ' ' . number_format($angka, 0, ',', '.');
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<div class='mt-32 text-center text-red-500 font-bold text-xl'>ID tidak ditemukan di URL</div>";
    exit;
}

try {
    // Update View Count Otomatis via PHP
    $stmtView = $pdo->prepare("UPDATE dummy_asset SET view_count = IFNULL(view_count, 0) + 1 WHERE id = ?");
    $stmtView->execute([$id]);

    // Ambil Detail Data Asset
    $stmt = $pdo->prepare("SELECT * FROM dummy_asset WHERE id = ?");
    $stmt->execute([$id]);
    $asset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$asset) {
        echo "<div class='mt-32 text-center text-red-500 font-bold text-xl'>Aset tidak ditemukan.</div>";
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='mt-32 text-center text-red-500 font-bold text-xl'>Error Database: " . $e->getMessage() . "</div>";
    exit;
}

// PROTEKSI STATUS SOLD
if (strtolower($asset['proses_penjualan']) === 'sold') {
    echo "
    <div class='max-w-3xl mx-auto mt-32 mb-20 px-6 text-center min-h-[50vh] flex flex-col items-center justify-center'>
        <div class='w-24 h-24 bg-red-100 text-red-500 rounded-full flex items-center justify-center mb-6 shadow-md'>
            <i class='fas fa-times-circle text-5xl'></i>
        </div>
        <h1 class='text-3xl md:text-4xl font-extrabold text-gray-800 mb-3'>Aset Sudah Terjual</h1>
        <p class='text-gray-500 text-lg mb-8'>Maaf brokuu, aset ini sudah laku terjual.</p>
        <a href='asset' class='bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-xl transition-all shadow-md'>Lihat Aset Lainnya</a>
    </div>";
    exit;
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    html { scroll-behavior: smooth; }
    .scroll-target { scroll-margin-top: 180px; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    #map { width: 100%; height: 400px; border-radius: 16px; z-index: 1; border: 1px solid #f3f4f6; }
    
    /* Tombol Mobile Floating */
    #mobileButtons {
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    }
    .hide-floating-btn {
        transform: translateY(110%);
        opacity: 0;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        #map { height: 250px; }
        .scroll-target { scroll-margin-top: 150px; }
    }
</style>

<div class="max-w-6xl mx-auto mt-24 lg:mt-32 flex flex-col-reverse lg:flex-row gap-6 px-4 mb-24">
    <div class="w-full lg:w-2/3">
        <div id="fotoSection">
            <div class="bg-white shadow-md rounded-3xl overflow-hidden relative group border border-gray-100">
                <div class="absolute inset-0 flex items-center justify-center bg-gray-50 z-0" id="loaderImg">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-blue-500"></i>
                </div>
                <img id="mainImage" src="<?= BASE_URL ?>/img/agunan/<?= $asset['foto1']; ?>" class="w-full h-[250px] sm:h-[400px] md:h-[500px] object-cover relative z-10" onload="document.getElementById('loaderImg').classList.add('hidden')" onerror="this.src='<?= BASE_URL ?>/img/agunan/byl.jpg';">
            </div>
            <div class="flex overflow-x-auto space-x-3 p-2 mt-2 no-scrollbar">
                <?php for ($i = 1; $i <= 4; $i++): if (!empty($asset["foto$i"])): ?>
                    <img src="<?= BASE_URL ?>/img/agunan/<?= $asset["foto$i"]; ?>" class="thumbnail w-20 h-16 sm:w-24 sm:h-20 object-cover rounded-2xl cursor-pointer hover:opacity-80 border-2 border-transparent hover:border-blue-500 transition-all shadow-sm" data-img="<?= BASE_URL ?>/img/agunan/<?= $asset["foto$i"]; ?>">
                <?php endif; endfor; ?>
            </div>
        </div>

        <div id="stickyMenu" class="sticky top-[85px] md:top-[100px] bg-white/90 backdrop-blur-md px-2 md:px-4 py-3 shadow-sm border border-gray-100 mt-6 rounded-3xl flex justify-between md:justify-start overflow-x-auto no-scrollbar gap-2 md:gap-6 z-40">
            <a href="#deskripsi" class="flex flex-col md:flex-row items-center justify-center min-w-[65px] text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-info-circle text-xl md:text-base md:mr-2"></i><span class="text-[10px] md:text-sm mt-1">Deskripsi</span>
            </a>
            <a href="#spesifikasi" class="flex flex-col md:flex-row items-center justify-center min-w-[65px] text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-list-ul text-xl md:text-base md:mr-2"></i><span class="text-[10px] md:text-sm mt-1">Spek</span>
            </a>
            <a href="#lokasi" class="flex flex-col md:flex-row items-center justify-center min-w-[65px] text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-map-marker-alt text-xl md:text-base md:mr-2"></i><span class="text-[10px] md:text-sm mt-1">Lokasi</span>
            </a>
            <a href="#pembelian" class="flex flex-col md:flex-row items-center justify-center min-w-[65px] text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-shopping-cart text-xl md:text-base md:mr-2"></i><span class="text-[10px] md:text-sm mt-1">Beli</span>
            </a>
            <a href="#kalkulator" class="flex flex-col md:flex-row items-center justify-center min-w-[65px] text-gray-500 hover:text-blue-600 font-bold transition-colors">
                <i class="fas fa-calculator text-xl md:text-base md:mr-2"></i><span class="text-[10px] md:text-sm mt-1">Cicilan</span>
            </a>
        </div>

        <div id="deskripsi" class="scroll-target mt-6 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-black text-gray-800 mb-3 tracking-tight">Deskripsi Aset</h2>
            <p class="text-gray-600 leading-relaxed text-sm md:text-base"><?= nl2br(htmlspecialchars($asset['deskripsi'])) ?></p>
        </div>

        <div id="spesifikasi" class="scroll-target mt-6 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-black text-gray-800 mb-5 tracking-tight">Spesifikasi Detail</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Luas Tanah</p>
                    <p class="text-lg font-black text-gray-800"><?= $asset['luas_tanah'] ?> m²</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Bangunan</p>
                    <p class="text-lg font-black text-gray-800"><?= $asset['luas_bangunan'] ?> m²</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Lantai</p>
                    <p class="text-lg font-black text-gray-800"><?= $asset['lantai'] ?></p>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Legalitas</p>
                    <p class="text-sm font-black text-gray-800"><?= $asset['jenis_surat'] ?></p>
                </div>
            </div>
        </div>

        <div id="lokasi" class="scroll-target mt-6 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-black text-gray-800 mb-4 tracking-tight">Lokasi Unit</h2>
            <p class="text-gray-600 mb-4 text-sm font-medium"><i class="fas fa-map-marker-alt text-red-500 mr-2"></i> <?= htmlspecialchars($asset['alamat_asset']) ?></p>
            <div id="map"></div>
        </div>

        <div id="kalkulator" class="scroll-target mt-6 bg-white p-6 rounded-3xl shadow-sm border border-gray-100 mb-10">
            <h2 class="text-xl font-black text-gray-800 mb-6 tracking-tight">Kalkulator KPR</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 bg-gray-50 p-6 rounded-3xl mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Harga Aset</label>
                    <input id="harga" type="text" value="<?= number_format($asset['harga_jual'], 0, ',', '.'); ?>" readonly class="w-full border-none rounded-2xl p-4 text-right bg-white shadow-sm font-bold text-blue-600 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Uang Muka (%)</label>
                    <input id="dp" type="number" value="10" class="w-full border-none rounded-2xl p-4 text-right bg-white shadow-sm font-bold outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Suku Bunga / Thn (%)</label>
                    <input id="bunga" type="number" value="10.5" step="0.1" class="w-full border-none rounded-2xl p-4 text-right bg-white shadow-sm font-bold outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Tenor (Tahun)</label>
                    <select id="tenor" class="w-full border-none rounded-2xl p-4 font-bold bg-white shadow-sm outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="5">5 Tahun</option><option value="10" selected>10 Tahun</option><option value="15">15 Tahun</option>
                    </select>
                </div>
            </div>
            <div class="text-center p-8 bg-blue-600 rounded-[2rem] shadow-xl text-white shadow-blue-100">
                <p class="text-blue-100 text-sm font-medium">Estimasi Cicilan per Bulan:</p>
                <p id="cicilan" class="text-4xl font-black mt-2">Rp 0</p>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-1/3">
        <div class="bg-white p-7 shadow-xl rounded-[2rem] border border-blue-50 lg:sticky lg:top-[110px]">
            <div class="flex justify-between items-center mb-5">
                <?php
                    $stat = $asset['proses_penjualan'];
                    $badge = 'bg-blue-100 text-blue-700';
                    if(strtolower($stat) == 'jual') $badge = 'bg-green-100 text-green-700';
                    if(strtolower($stat) == 'cessie') $badge = 'bg-orange-100 text-orange-700';
                ?>
                <span class="<?= $badge ?> text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-wider"><?= $stat ?></span>
                <span class="text-gray-400 text-xs font-bold"><i class="fas fa-eye mr-1 text-blue-400"></i><?= $asset['view_count'] ?></span>
            </div>
            <h2 class="text-xl font-bold text-gray-800 leading-tight mb-2"><?= $asset['jenis_surat'] ?> No. <?= $asset['nomor_surat'] ?></h2>
            <p class="text-blue-600 text-3xl font-black mb-6"><?= formatRupiah($asset['harga_jual']) ?></p>
            
            <div class="grid grid-cols-2 gap-4 mb-8 py-6 border-y border-gray-50">
                <div class="text-center border-r border-gray-100"><p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Luas Tanah</p><p class="font-black text-gray-800 text-lg"><?= $asset['luas_tanah'] ?>m²</p></div>
                <div class="text-center"><p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Bangunan</p><p class="font-black text-gray-800 text-lg"><?= $asset['luas_bangunan'] ?>m²</p></div>
            </div>

            <div class="hidden lg:block space-y-3">
                <button onclick="bukaModalPengajuan('Pembiayaan BKK Joglo (KPR)')" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all transform active:scale-95">AJUKAN PEMBIAYAAN</button>
                <button onclick="bukaModalPengajuan('')" class="w-full flex items-center justify-center gap-2 border-2 border-green-500 text-green-600 font-black py-4 rounded-2xl hover:bg-green-50 transition-all transform active:scale-95"><i class="fab fa-whatsapp text-xl"></i> HUBUNGI PETUGAS</button>
            </div>
        </div>
    </div>

    <div id="mobileButtons" class="fixed inset-x-0 bottom-0 z-50 lg:hidden bg-white/95 backdrop-blur-md p-4 border-t border-gray-100 shadow-[0_-10px_40px_rgba(0,0,0,0.08)]">
        <div class="flex gap-3">
            <button onclick="bukaModalPengajuan('Pembiayaan BKK Joglo (KPR)')" class="w-1/2 bg-blue-600 text-white font-black py-4 rounded-2xl shadow-md active:scale-95 transition-all">AJUKAN KPR</button>
            <button onclick="bukaModalPengajuan('')" class="w-1/2 border-2 border-green-500 text-green-600 font-black py-4 rounded-2xl flex items-center justify-center gap-2 active:scale-95 transition-all"><i class="fab fa-whatsapp text-lg"></i> HUBUNGI</button>
        </div>
    </div>
</div>

<div id="modalPengajuan" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-md px-4 opacity-0 transition-all duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-all" id="modalContent">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-xl font-black text-gray-800 tracking-tight">Form Pengajuan</h3>
            <button onclick="tutupModalPengajuan()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-red-50 hover:text-red-500 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <form id="formAset" class="p-8 space-y-5">
            <input type="hidden" id="fIdAset" value="<?= $id ?>">
            <div class="bg-blue-50 p-4 rounded-3xl border border-blue-100">
                <label class="block text-[10px] font-black text-blue-400 uppercase mb-1 ml-1">Unit Pilihan</label>
                <p class="font-bold text-blue-900 ml-1"><?= $asset['jenis_surat'] ?> No. <?= $asset['nomor_surat'] ?></p>
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-2">Nama Lengkap *</label>
                <input type="text" id="fNama" required placeholder="Nama Anda" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-blue-500 shadow-sm">
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-2">No. WhatsApp *</label>
                <input type="number" id="fWa" required placeholder="0812xxxx" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-blue-500 shadow-sm">
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-2">Minat Skema *</label>
                <select id="fSkema" required class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-blue-500 shadow-sm">
                    <option value="" disabled selected>Pilih Skema...</option>
                    <option value="Pembiayaan BKK Joglo (KPR)">BKK Joglo (KPR)</option>
                    <option value="Mekanisme Lelang">Mekanisme Lelang</option>
                    <option value="Mekanisme Cessie">Mekanisme Cessie</option>
                    <option value="Hanya Tanya-tanya Dulu">Hanya Tanya-tanya Dulu</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-2">Pesan / Keterangan</label>
                <textarea id="fPesan" rows="3" placeholder="Ketik pesan Anda..." class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-blue-500 shadow-sm resize-none"></textarea>
            </div>
            <div class="pt-4 flex flex-col gap-3">
                <button type="button" onclick="kirimDB()" id="btnDB" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">SUBMIT DATA</button>
                <button type="button" onclick="kirimWA()" class="w-full border-2 border-green-500 text-green-600 font-black py-4 rounded-2xl hover:bg-green-50 transition-all flex items-center justify-center gap-2"><i class="fab fa-whatsapp text-xl"></i> LANJUT WA</button>
            </div>
        </form>
    </div>
</div>

<div id="modalNotif" class="fixed inset-0 z-[110] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-md px-4 opacity-0 transition-all duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-xs p-10 text-center transform scale-95 transition-all" id="modalNotifContent">
        <div id="notifIconWrapper" class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6"><i id="notifIcon" class="fas fa-check text-4xl"></i></div>
        <h3 id="notifTitle" class="text-2xl font-black text-gray-800 mb-2">Berhasil!</h3>
        <p id="notifMessage" class="text-gray-500 text-sm leading-relaxed mb-8"></p>
        <button onclick="tutupModalNotif()" id="notifButton" class="w-full py-4 rounded-2xl text-white font-black transition-all shadow-lg">OK, MENGERTI</button>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Intersection Observer untuk deteksi Footer (Auto-hide button)
    const footer = document.querySelector('footer');
    const mBtn = document.getElementById('mobileButtons');
    if(footer && mBtn) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if(e.isIntersecting) mBtn.classList.add('hide-floating-btn');
                else mBtn.classList.remove('hide-floating-btn');
            });
        }, { threshold: 0.1 });
        observer.observe(footer);
    }

    // Modal Logic
    function bukaModalPengajuan(s = '') {
        if(s) document.getElementById('fSkema').value = s;
        const m = document.getElementById('modalPengajuan');
        m.classList.remove('hidden');
        setTimeout(() => { m.classList.remove('opacity-0'); document.getElementById('modalContent').classList.remove('scale-95'); }, 10);
    }
    function tutupModalPengajuan() {
        const m = document.getElementById('modalPengajuan');
        m.classList.add('opacity-0'); document.getElementById('modalContent').classList.add('scale-95');
        setTimeout(() => m.classList.add('hidden'), 300);
    }

    // Notif Logic
    function tampilNotif(type, title, msg) {
        const m = document.getElementById('modalNotif'), c = document.getElementById('modalNotifContent'), iW = document.getElementById('notifIconWrapper'), i = document.getElementById('notifIcon'), b = document.getElementById('notifButton');
        document.getElementById('notifTitle').innerText = title; document.getElementById('notifMessage').innerText = msg;
        if(type === 'success') {
            iW.className = "w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6"; i.className = "fas fa-check text-4xl";
            b.className = "w-full py-4 rounded-2xl bg-blue-600 text-white font-black shadow-lg";
        } else {
            iW.className = "w-20 h-20 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6"; i.className = "fas fa-times text-4xl";
            b.className = "w-full py-4 rounded-2xl bg-red-500 text-white font-black shadow-lg";
        }
        m.classList.remove('hidden');
        setTimeout(() => { m.classList.remove('opacity-0'); c.classList.remove('scale-95'); }, 10);
    }
    function tutupModalNotif() {
        const m = document.getElementById('modalNotif');
        m.classList.add('opacity-0'); document.getElementById('modalNotifContent').classList.add('scale-95');
        setTimeout(() => m.classList.add('hidden'), 300);
    }

    // Submit Logic (Bypass aaPanel Block)
    function kirimDB() {
        const f = document.getElementById('formAset');
        if(!f.checkValidity()) return f.reportValidity();
        const b = document.getElementById('btnDB'); b.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; b.disabled = true;
        
        fetch("<?= BASE_URL ?>/api/pengajuan/store", {
            method: "POST",
            headers: {"Content-Type": "application/json", "Accept": "application/json"},
            body: JSON.stringify({
                id_aset: "<?= $id ?>",
                nama_lengkap: document.getElementById('fNama').value,
                no_wa: document.getElementById('fWa').value,
                minat_skema: document.getElementById('fSkema').value,
                pesan: document.getElementById('fPesan').value
            })
        })
        .then(async r => {
            if(!r.ok) throw new Error('WAF Blocked');
            return r.json();
        })
        .then(() => {
            tutupModalPengajuan(); f.reset(); b.innerText = 'SUBMIT DATA'; b.disabled = false;
            tampilNotif('success', 'Berhasil!', 'Data pengajuan Anda telah tersimpan.');
        }).catch(() => {
            b.innerText = 'SUBMIT DATA'; b.disabled = false;
            tampilNotif('error', 'Gagal', 'Terjadi gangguan koneksi server.');
        });
    }

    function kirimWA() {
        const f = document.getElementById('formAset');
        if(!f.checkValidity()) return f.reportValidity();
        const msg = `Halo BKK Jateng, saya berminat pada unit <?= $asset['jenis_surat'] ?> <?= $asset['nomor_surat'] ?>.\nNama: ${document.getElementById('fNama').value}\nSkema: ${document.getElementById('fSkema').value}\nKeterangan: ${document.getElementById('fPesan').value}`;
        window.open(`https://wa.me/6288228659668?text=${encodeURIComponent(msg)}`, '_blank');
        tutupModalPengajuan(); f.reset();
    }

    // Kalkulator
    function hitung() {
        let h = <?= $asset['harga_jual'] ?>; let dp = document.getElementById('dp').value / 100 * h;
        let b = document.getElementById('bunga').value / 100 / 12; let n = document.getElementById('tenor').value * 12;
        let c = (b > 0) ? ((h - dp) * b) / (1 - Math.pow(1 + b, -n)) : (h - dp) / n;
        document.getElementById('cicilan').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(c));
    }
    ['dp','bunga','tenor'].forEach(id => document.getElementById(id).oninput = hitung);
    hitung();

    // Map & Gallery
    document.addEventListener("DOMContentLoaded", () => {
        var m = L.map('map').setView([<?= $asset['latitude'] ?>, <?= $asset['longitude'] ?>], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(m);
        L.marker([<?= $asset['latitude'] ?>, <?= $asset['longitude'] ?>]).addTo(m);
        document.querySelectorAll('.thumbnail').forEach(t => {
            t.onclick = () => { document.getElementById('mainImage').style.opacity = 0.5;
            setTimeout(() => { document.getElementById('mainImage').src = t.dataset.img; document.getElementById('mainImage').style.opacity = 1; }, 150); }
        });
    });
</script>