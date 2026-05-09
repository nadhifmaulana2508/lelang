<?php
// ==========================================
// 1. DATABASE CONNECTION
// ==========================================
$db_path = __DIR__ . '/api/config/database.php';
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../api/config/database.php'; }
if (file_exists($db_path)) { require_once $db_path; } else { die("DB Error"); }

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

$id = $_GET['id'] ?? null;
if (!$id) { exit("ID Kosong"); }

try {
    $pdo->prepare("UPDATE dummy_asset SET view_count = IFNULL(view_count, 0) + 1 WHERE id = ?")->execute([$id]);
    $stmt = $pdo->prepare("SELECT * FROM dummy_asset WHERE id = ?");
    $stmt->execute([$id]);
    $asset = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$asset) { exit("Aset tidak ditemukan"); }
} catch (PDOException $e) { exit("Error DB"); }
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    html { scroll-behavior: smooth; }
    body { background-color: #fcfcfc; margin: 0; padding-bottom: 90px; }
    .scroll-target { scroll-margin-top: 140px; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    #map { width: 100%; height: 300px; border-radius: 20px; z-index: 10; border: 1px solid #f1f5f9; }
    
    /* Hover Effect Gambar */
    .thumbnail:hover { border-color: #3b82f6; transform: translateY(-2px); }
    
    /* Tombol Mobile Floating */
    #mobileButtons { transition: transform 0.4s ease, opacity 0.3s ease; z-index: 90; }
    .hide-floating-btn { transform: translateY(120%); opacity: 0; }

    /* Fix Tinggi Tombol & Input SAMA PERSIS */
    .btn-custom, .input-custom {
        height: 46px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px !important;
        font-weight: 800 !important;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    @media (max-width: 768px) { #map { height: 220px; } .scroll-target { scroll-margin-top: 125px; } }
</style>

<div class="max-w-6xl mx-auto mt-20 lg:mt-32 px-4 mb-16">
    <div class="grid grid-cols-1 lg:grid-cols-[65%_35%] gap-6 items-start">
        
        <div class="space-y-5">
            <div id="fotoSection">
                <div class="bg-white shadow-sm rounded-[2.5rem] overflow-hidden border border-gray-100">
                    <img id="mainImage" src="<?= BASE_URL ?>/img/agunan/<?= $asset['foto1']; ?>" class="w-full h-[250px] sm:h-[420px] object-cover" onerror="this.src='<?= BASE_URL ?>/img/agunan/byl.jpg';">
                </div>
                <div class="flex overflow-x-auto space-x-2 py-3 no-scrollbar">
                    <?php for ($i = 1; $i <= 4; $i++): if (!empty($asset["foto$i"])): ?>
                        <img src="<?= BASE_URL ?>/img/agunan/<?= $asset["foto$i"]; ?>" class="thumbnail w-16 h-12 sm:w-24 sm:h-18 object-cover rounded-xl cursor-pointer border-2 border-transparent transition-all shrink-0" data-img="<?= BASE_URL ?>/img/agunan/<?= $asset["foto$i"]; ?>">
                    <?php endif; endfor; ?>
                </div>
            </div>

            <div id="stickyMenu" class="sticky top-[75px] md:top-[90px] bg-white/90 backdrop-blur-md py-2.5 px-2 md:px-6 shadow-md border border-gray-100 mt-2 rounded-[2rem] flex justify-around md:justify-start gap-1 md:gap-10 z-30">
                <a href="#deskripsi" class="flex flex-col items-center gap-1 text-gray-400 hover:text-blue-600 transition-all group">
                    <i class="fas fa-info-circle text-lg"></i><span class="text-[9px] font-bold uppercase">Info</span>
                </a>
                <a href="#spesifikasi" class="flex flex-col items-center gap-1 text-gray-400 hover:text-blue-600 transition-all group">
                    <i class="fas fa-layer-group text-lg"></i><span class="text-[9px] font-bold uppercase">Spek</span>
                </a>
                <a href="#lokasi" class="flex flex-col items-center gap-1 text-gray-400 hover:text-blue-600 transition-all group">
                    <i class="fas fa-map-marked-alt text-lg"></i><span class="text-[9px] font-bold uppercase">Lokasi</span>
                </a>
                <a href="#pembelian" class="flex flex-col items-center gap-1 text-gray-400 hover:text-blue-600 transition-all group">
                    <i class="fas fa-hand-holding-usd text-lg"></i><span class="text-[9px] font-bold uppercase">Skema</span>
                </a>
                <a href="#kalkulator" class="flex flex-col items-center gap-1 text-gray-400 hover:text-blue-600 transition-all group">
                    <i class="fas fa-calculator text-lg"></i><span class="text-[9px] font-bold uppercase">Cicilan</span>
                </a>
            </div>

            <div id="deskripsi" class="scroll-target bg-white p-6 md:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h2 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-4 italic">Deskripsi Unit</h2>
                <p class="text-gray-600 text-sm md:text-base leading-relaxed"><?= nl2br(htmlspecialchars($asset['deskripsi'])) ?></p>
            </div>

            <div id="spesifikasi" class="scroll-target bg-white p-6 md:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h2 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-6 italic">Spesifikasi</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50"><p class="text-[8px] font-bold text-blue-400 uppercase mb-1 tracking-tighter">Luas Tanah</p><p class="text-lg font-black text-blue-900"><?= $asset['luas_tanah'] ?>m²</p></div>
                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50"><p class="text-[8px] font-bold text-blue-400 uppercase mb-1 tracking-tighter">Bangunan</p><p class="text-lg font-black text-blue-900"><?= $asset['luas_bangunan'] ?>m²</p></div>
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100"><p class="text-[8px] font-bold text-gray-400 uppercase mb-1 tracking-tighter">Lantai</p><p class="text-lg font-black text-gray-800"><?= $asset['lantai'] ?></p></div>
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100"><p class="text-[8px] font-bold text-gray-400 uppercase mb-1 tracking-tighter">Surat</p><p class="text-xs font-black text-gray-800 uppercase tracking-tighter"><?= $asset['jenis_surat'] ?></p></div>
                </div>
            </div>

            <div id="lokasi" class="scroll-target bg-white p-6 md:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-[10px] font-black text-blue-500 uppercase tracking-widest italic">Lokasi & GPS</h2>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $asset['latitude'] ?>,<?= $asset['longitude'] ?>" target="_blank" class="bg-blue-600 text-white text-[10px] font-black px-4 py-2 rounded-full shadow-md"><i class="fas fa-directions mr-2"></i>PETUNJUK ARAH</a>
                </div>
                <p class="text-gray-700 mb-5 text-xs md:text-sm font-medium leading-relaxed italic opacity-80"><i class="fas fa-map-pin text-red-500 mr-2"></i> <?= htmlspecialchars($asset['alamat_asset']) ?></p>
                <div id="map"></div>
            </div>

            <div id="pembelian" class="scroll-target bg-white p-6 md:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm text-center">
                <h2 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-8 italic">Skema Kepemilikan</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
                    <div class="bg-gray-50 p-5 rounded-[2rem] border border-gray-100 hover:bg-blue-50/30 transition-colors">
                        <div class="w-10 h-10 bg-blue-500 text-white rounded-xl flex items-center justify-center mb-3"><i class="fas fa-gavel"></i></div>
                        <h4 class="font-black text-blue-900 text-[11px] uppercase tracking-tighter">Lelang DJKN</h4>
                        <p class="text-[10px] text-gray-500 leading-relaxed mt-1 italic">Via lelang.go.id, transparan & legalitas negara.</p>
                    </div>
                    <div class="bg-gray-50 p-5 rounded-[2rem] border border-gray-100 hover:bg-orange-50/30 transition-colors">
                        <div class="w-10 h-10 bg-orange-500 text-white rounded-xl flex items-center justify-center mb-3"><i class="fas fa-file-contract"></i></div>
                        <h4 class="font-black text-orange-900 text-[11px] uppercase tracking-tighter">Cessie</h4>
                        <p class="text-[10px] text-gray-500 leading-relaxed mt-1 italic">Pengalihan hak tagih, harga investasi terbaik.</p>
                    </div>
                    <div class="bg-gray-50 p-5 rounded-[2rem] border border-gray-100 hover:bg-green-50/30 transition-colors">
                        <div class="w-10 h-10 bg-green-500 text-white rounded-xl flex items-center justify-center mb-3"><i class="fas fa-home"></i></div>
                        <h4 class="font-black text-green-900 text-[11px] uppercase tracking-tighter">KPR BKK</h4>
                        <p class="text-[10px] text-gray-500 leading-relaxed mt-1 italic">Proses cepat, bunga kompetitif di BKK Jateng.</p>
                    </div>
                </div>
            </div>

            <div id="kalkulator" class="scroll-target bg-white p-6 md:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h2 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-6 italic">Simulasi Cicilan</h2>
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-5 rounded-[2rem] mb-5 border border-gray-100">
                    <div class="col-span-2"><label class="text-[9px] font-black text-gray-400 uppercase mb-1 ml-2">Harga Aset</label><input id="harga" type="text" value="<?= number_format($asset['harga_jual'], 0, ',', '.'); ?>" readonly class="w-full bg-white border-none rounded-2xl p-4 font-black text-blue-600 text-base shadow-inner"></div>
                    <div><label class="text-[9px] font-black text-gray-400 uppercase mb-1 ml-2">DP (%)</label><input id="dp" type="number" value="10" class="w-full bg-white border-gray-100 rounded-2xl p-4 font-bold text-sm shadow-inner focus:ring-2 focus:ring-blue-500 outline-none"></div>
                    <div><label class="text-[9px] font-black text-gray-400 uppercase mb-1 ml-2">Tenor</label><select id="tenor" class="w-full bg-white border-gray-100 rounded-2xl p-4 font-bold text-sm shadow-inner cursor-pointer"><option value="5">5 Thn</option><option value="10" selected>10 Thn</option><option value="15">15 Thn</option></select></div>
                </div>
                <div class="text-center p-8 bg-blue-600 rounded-[2.5rem] text-white shadow-xl shadow-blue-100">
                    <p class="text-[9px] font-bold uppercase tracking-widest opacity-70">Estimasi Angsuran / Bulan</p>
                    <p id="cicilan" class="text-3xl md:text-5xl font-black mt-2 tracking-tighter">Rp 0</p>
                </div>
            </div>
        </div>

        <div class="hidden lg:block lg:sticky lg:top-[110px]">
            <div class="bg-white p-8 shadow-2xl shadow-blue-900/10 rounded-[3rem] border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <span class="bg-blue-600 text-white text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-md shadow-blue-200"><?= $asset['proses_penjualan'] ?></span>
                    <span class="text-gray-400 text-[10px] font-bold italic"><i class="fas fa-eye mr-1 text-blue-300"></i><?= $asset['view_count'] ?> views</span>
                </div>
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1"><?= $asset['jenis_surat'] ?></h2>
                <h3 class="text-base font-black text-gray-800 leading-tight mb-4">No. <?= $asset['nomor_surat'] ?></h3>
                <p class="text-blue-600 text-3xl font-black mb-8 tracking-tighter"><?= formatRupiah($asset['harga_jual']) ?></p>
                
                <div class="grid grid-cols-2 gap-3 mb-8 py-5 border-y border-gray-50 text-center">
                    <div class="border-r border-gray-100"><p class="text-[8px] text-gray-400 font-bold uppercase mb-1">Tanah</p><p class="font-black text-gray-800 text-sm"><?= $asset['luas_tanah'] ?>m²</p></div>
                    <div><p class="text-[8px] text-gray-400 font-bold uppercase mb-1">Bangunan</p><p class="font-black text-gray-800 text-sm"><?= $asset['luas_bangunan'] ?>m²</p></div>
                </div>

                <div class="space-y-2.5">
                    <button onclick="bukaModalPengajuan('Pembiayaan BKK Joglo (KPR)')" class="btn-custom w-full bg-blue-600 text-white rounded-2xl shadow-lg hover:shadow-blue-200 hover:-translate-y-0.5 transition-all">AJUKAN KPR</button>
                    <button onclick="bukaModalPengajuan('')" class="btn-custom w-full border-2 border-green-500 text-green-600 rounded-2xl hover:bg-green-50 active:scale-95 transition-all">HUBUNGI WA</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalPengajuan" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/70 backdrop-blur-md px-4 opacity-0 transition-all duration-300">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-sm overflow-hidden transform scale-95 transition-all" id="modalContent">
        <div class="px-8 py-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Form Peminatan</h3>
            <button onclick="tutupModalPengajuan()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50 hover:text-red-500 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <form id="formAset" class="p-8 space-y-3.5 max-h-[75vh] overflow-y-auto no-scrollbar">
            <input type="hidden" id="fIdAset" value="<?= $id ?>">
            <div class="bg-blue-600 p-4 rounded-[1.5rem] text-white shadow-lg shadow-blue-100 mb-1">
                <label class="block text-[8px] font-black text-blue-200 uppercase mb-0.5">Pilihan Unit</label>
                <p class="font-extrabold text-xs tracking-tight"><?= $asset['jenis_surat'] ?> No. <?= $asset['nomor_surat'] ?></p>
            </div>
            
            <div class="space-y-3">
                <div><label class="text-[8px] font-black text-gray-400 uppercase ml-2 mb-1 block">Nama *</label><input type="text" id="fNama" required class="w-full bg-gray-50 border-gray-100 rounded-xl p-3 px-4 text-xs font-bold focus:ring-1 focus:ring-blue-300 outline-none"></div>
                <div><label class="text-[8px] font-black text-gray-400 uppercase ml-2 mb-1 block">WhatsApp *</label><input type="number" id="fWa" required class="w-full bg-gray-50 border-gray-100 rounded-xl p-3 px-4 text-xs font-bold outline-none"></div>
                <div><label class="text-[8px] font-black text-gray-400 uppercase ml-2 mb-1 block">Skema *</label><select id="fSkema" required class="w-full bg-gray-50 border-gray-100 rounded-xl p-3 px-4 text-xs font-bold outline-none cursor-pointer"><option value="Pembiayaan BKK Joglo (KPR)">KPR BKK Joglo</option><option value="Mekanisme Lelang">Lelang DJKN</option><option value="Mekanisme Cessie">Cessie</option></select></div>
                <div><label class="text-[8px] font-black text-gray-400 uppercase ml-2 mb-1 block">Pesan</label><textarea id="fPesan" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-xl p-3 px-4 text-xs resize-none outline-none"></textarea></div>
            </div>

            <div class="pt-3 space-y-2">
                <button type="button" onclick="kirimDB()" id="btnDB" class="btn-custom w-full bg-blue-600 text-white rounded-2xl shadow-lg border-b-4 border-blue-800">SUBMIT DATA</button>
                <button type="button" onclick="kirimWA()" class="btn-custom w-full border-2 border-green-500 text-green-600 rounded-2xl flex items-center justify-center gap-2"> <i class="fab fa-whatsapp text-sm"></i> LANJUT WA</button>
            </div>
        </form>
    </div>
</div>

<div id="mobileButtons" class="fixed inset-x-0 bottom-0 lg:hidden bg-white/95 backdrop-blur-xl p-4 border-t border-gray-100 shadow-[0_-15px_40px_rgba(0,0,0,0.08)]">
    <div class="flex gap-3">
        <button onclick="bukaModalPengajuan('Pembiayaan BKK Joglo (KPR)')" class="btn-custom w-1/2 bg-blue-600 text-white rounded-2xl active:scale-95 transition-all shadow-lg shadow-blue-100">AJUKAN KPR</button>
        <button onclick="bukaModalPengajuan('')" class="btn-custom w-1/2 border-2 border-green-500 text-green-600 rounded-2xl active:scale-95 transition-all">WHATSAPP</button>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Logic Map & Mobile Hidden on Footer
    document.addEventListener("DOMContentLoaded", () => {
        var m = L.map('map').setView([<?= $asset['latitude'] ?>, <?= $asset['longitude'] ?>], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(m);
        L.marker([<?= $asset['latitude'] ?>, <?= $asset['longitude'] ?>]).addTo(m);

        const footer = document.querySelector('footer');
        const mBtn = document.getElementById('mobileButtons');
        if(footer && mBtn) {
            const obs = new IntersectionObserver((es) => {
                es.forEach(e => { if(e.isIntersecting) mBtn.classList.add('hide-floating-btn'); else mBtn.classList.remove('hide-floating-btn'); });
            }, { threshold: 0.1 });
            obs.observe(footer);
        }
    });

    // Form
    function bukaModalPengajuan(s = '') {
        if(s) document.getElementById('fSkema').value = s;
        const m = document.getElementById('modalPengajuan'); m.classList.remove('hidden');
        setTimeout(() => { m.classList.remove('opacity-0'); document.getElementById('modalContent').classList.remove('scale-95'); }, 10);
    }
    function tutupModalPengajuan() {
        const m = document.getElementById('modalPengajuan'); m.classList.add('opacity-0'); document.getElementById('modalContent').classList.add('scale-95');
        setTimeout(() => m.classList.add('hidden'), 300);
    }

    function kirimDB() {
        const f = document.getElementById('formAset'); if(!f.checkValidity()) return f.reportValidity();
        const b = document.getElementById('btnDB'); b.innerHTML = '...'; b.disabled = true;
        fetch("<?= BASE_URL ?>/api/pengajuan/store", {
            method: "POST", headers: {"Content-Type": "application/json"},
            body: JSON.stringify({id_aset: "<?= $id ?>", nama_lengkap: document.getElementById('fNama').value, no_wa: document.getElementById('fWa').value, minat_skema: document.getElementById('fSkema').value, pesan: document.getElementById('fPesan').value})
        }).then(() => {
            tutupModalPengajuan(); f.reset(); b.innerText = 'SUBMIT DATA'; b.disabled = false;
            alert('Terkirim! Petugas kami akan segera menghubungi Anda.');
        });
    }

    function kirimWA() {
        const f = document.getElementById('formAset'); if(!f.checkValidity()) return f.reportValidity();
        const msg = `Halo BKK Jateng, saya berminat pada aset <?= $asset['jenis_surat'] ?> No. <?= $asset['nomor_surat'] ?>.\n\nNama: ${document.getElementById('fNama').value}\nSkema: ${document.getElementById('fSkema').value}`;
        window.open(`https://wa.me/6288228659668?text=${encodeURIComponent(msg)}`, '_blank');
        tutupModalPengajuan(); f.reset();
    }

    // Kalkulator
    function hitung() {
        let h = <?= $asset['harga_jual'] ?>, d = document.getElementById('dp').value/100*h, b = 10.5/100/12, n = document.getElementById('tenor').value*12, c = (b>0)?(h-d)*b/(1-Math.pow(1+b,-n)):(h-d)/n;
        document.getElementById('cicilan').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(c));
    }
    ['dp','tenor'].forEach(id => document.getElementById(id).oninput = hitung); hitung();

    // Gallery
    document.querySelectorAll('.thumbnail').forEach(t => {
        t.onclick = () => { document.getElementById('mainImage').style.opacity = 0.5; setTimeout(() => { document.getElementById('mainImage').src = t.dataset.img; document.getElementById('mainImage').style.opacity = 1; }, 150); }
    });
</script>