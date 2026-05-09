<div class="flex flex-col gap-6">
    <?php include __DIR__ . '/dash_header.php'; ?>
    <?php include __DIR__ . '/dash_stats.php'; ?>
    <?php include __DIR__ . '/dash_charts.php'; ?>
    <?php include __DIR__ . '/dash_priority.php'; ?>
</div>

<script>
// --- HELPER FORMAT UANG ---
function formatMilyar(angka) {
    if (angka >= 1000000000) return 'Rp' + (angka / 1000000000).toFixed(2) + 'M';
    if (angka >= 1000000) return 'Rp' + (angka / 1000000).toFixed(2) + 'Jt';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
}

// --- GLOBAL CHART VARIABLE ---
let portfolioChartInstance = null;

// --- MAIN FUNCTION: LOAD DATA ---
// --- MAIN FUNCTION: LOAD DATA ---
function loadDashboard() {
    const cabang = document.getElementById('dashCabang')?.value || '';
    const status = document.getElementById('dashStatus').value;

    const url = `<?= API_URL ?>/dashboard/stats?kode_kantor=${encodeURIComponent(cabang)}&status=${encodeURIComponent(status)}`;

    fetch(url)
    .then(res => res.json())
    .then(res => {
        // FIX: Ubah res.code menjadi res.status sesuai format JSON helper kamu!
        if(res.status === 200) {
            updateStats(res.data.cards);
            updateStatusList(res.data.status_counts);
            renderChart(res.data.chart);
            renderPriority(res.data.prioritas);
            renderAgunan(res.data.agunan_unggulan);
        } else {
            console.error("Gagal dari API:", res.message);
        }
    })
    .catch(err => console.error("Error Fetch Dashboard:", err));
}

// --- 1. UPDATE STATS CARD ---
function updateStats(cards) {
    document.getElementById('s_debitur').innerText = cards.total_debitur;
    document.getElementById('s_baki').innerText = formatMilyar(cards.total_baki_debet);
    document.getElementById('s_agunan').innerText = formatMilyar(cards.total_agunan);
    document.getElementById('s_siap').innerText = cards.siap_ditawarkan;
    document.getElementById('s_minat').innerText = cards.minat_pembeli;
}

// --- 2. UPDATE LIST STATUS KANAN ---
function updateStatusList(counts) {
    document.getElementById('c_draft').innerText = counts['Draft'] || 0;
    document.getElementById('c_review').innerText = counts['Review Legal'] || 0;
    document.getElementById('c_siap').innerText = counts['Siap Ditawarkan'] || 0;
    document.getElementById('c_minat').innerText = counts['Diminati'] || 0;
    document.getElementById('c_butuh').innerText = counts['Butuh Update'] || 0;
}

// --- 3. RENDER BAR CHART ---
function renderChart(chartData) {
    const ctx = document.getElementById('portfolioChart').getContext('2d');
    
    // Hancurkan chart lama jika ada (Biar bisa update data mulus saat filter)
    if (portfolioChartInstance) { portfolioChartInstance.destroy(); }

    const labels = chartData.map(d => d.status);
    const dataBaki = chartData.map(d => d.baki_debet);
    const dataAgunan = chartData.map(d => d.nilai_agunan);

    portfolioChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Baki Debet',
                    data: dataBaki,
                    backgroundColor: '#041020',
                    borderRadius: 4,
                    barPercentage: 0.6
                },
                {
                    label: 'Nilai Agunan',
                    data: dataAgunan,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                    barPercentage: 0.6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(value) { return 'Rp' + (value/1000000000).toFixed(0) + 'M'; } }
                }
            },
            plugins: {
                legend: { position: 'top', align: 'end', labels: { boxWidth: 10, font: { size: 10 } } }
            }
        }
    });
}

// --- 4. RENDER TABEL PRIORITAS ---
function renderPriority(data) {
    const tbody = document.getElementById('tbody_priority');
    let html = '';
    
    data.forEach(row => {
        const inisial = row.nama_nasabah.substring(0, 2).toUpperCase();
        const cov = row.baki_debet > 0 ? (row.nilai_agunan / row.baki_debet) * 100 : 0;
        
        let stClass = "bg-gray-50 text-gray-600 border-gray-200";
        if(row.status === 'Siap Ditawarkan') stClass = "bg-green-50 text-green-600 border-green-200";
        if(row.status === 'Review Legal') stClass = "bg-yellow-50 text-yellow-600 border-yellow-200";
        if(row.status === 'Diminati') stClass = "bg-blue-50 text-blue-600 border-blue-200";

        html += `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-3 px-2">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-600 font-bold text-[10px] flex items-center justify-center">${inisial}</div>
                    <div><p class="text-xs font-bold text-[#041020]">${row.nama_nasabah}</p><p class="text-[9px] text-gray-500">Kolek: ${row.kolektibilitas}</p></div>
                </div>
            </td>
            <td class="py-3 px-2 text-xs text-gray-600">${row.nama_kantor}</td>
            <td class="py-3 px-2 text-xs font-bold text-[#041020] text-right">${formatMilyar(row.baki_debet)}</td>
            <td class="py-3 px-2 text-xs text-gray-500 text-right">${formatMilyar(row.nilai_agunan)}</td>
            <td class="py-3 px-2 text-xs font-bold text-${cov >= 100 ? 'green' : 'red'}-600 text-center">${cov.toFixed(0)}%</td>
            <td class="py-3 px-2 text-center"><span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-extrabold border ${stClass}">${row.status}</span></td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

// --- 5. RENDER AGUNAN UNGGULAN ---
function renderAgunan(data) {
    const grid = document.getElementById('grid_agunan');
    let html = '';
    
    data.forEach(row => {
        const cov = row.baki_debet > 0 ? (row.nilai_agunan / row.baki_debet) * 100 : 0;
        // Karena foto belum ada di DB, kita pakai background abu-abu aja sementara
        html += `
        <div class="border border-gray-100 rounded-xl overflow-hidden hover:shadow-md transition-all">
            <div class="h-32 bg-gray-200 flex items-center justify-center"><p class="text-xs font-bold text-gray-400 bg-white px-3 py-1 rounded-full">Foto Agunan</p></div>
            <div class="p-4">
                <p class="font-bold text-sm text-[#041020] mb-1 truncate">${row.jenis_surat}</p>
                <p class="text-[10px] text-gray-500 mb-3 truncate"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i>${row.alamat_asset}</p>
                
                <div class="flex justify-between items-center mb-1"><span class="text-[10px] text-gray-500">Nilai Agunan</span><span class="text-xs font-bold text-[#041020]">${formatMilyar(row.harga_jual)}</span></div>
                <div class="flex justify-between items-center mb-1"><span class="text-[10px] text-gray-500">Baki Debet</span><span class="text-xs font-bold text-[#041020]">${formatMilyar(row.baki_debet)}</span></div>
                <div class="flex justify-between items-center"><span class="text-[10px] text-gray-500">Coverage</span><span class="text-xs font-bold text-green-600">${cov.toFixed(0)}%</span></div>
            </div>
        </div>`;
    });
    grid.innerHTML = html;
}

// Panggil Fungsi saat halaman pertama dibuka
document.addEventListener('DOMContentLoaded', loadDashboard);
</script>