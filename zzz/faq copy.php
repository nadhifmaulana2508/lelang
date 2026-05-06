<?php
    include("includes/header.php");
    include("includes/navbar.php");
?>

<div class="w-full pt-20"> <!-- Menambahkan padding atas agar tidak tertutup navbar -->
    <!-- Header FAQ Full Width -->
    <div class="text-center bg-gradient-to-b from-blue-500 to-blue-300 py-10 shadow-lg w-full relative z-10">
        <h2 class="text-4xl font-bold text-white">FAQ</h2>
        <p class="text-lg text-white mt-2">Ada yang bisa kami bantu?</p>
        <div class="mt-4 flex justify-center">
            <div class="relative w-full max-w-md">
                <input type="text" placeholder="Ketik info yang Anda cari..." 
                    class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="absolute right-4 top-3 text-gray-400">🔍</span>
            </div>
        </div>
    </div>

    <!-- Container Konten FAQ -->
    <div class="max-w-7xl mx-auto p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kategori Pertanyaan -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">Kategori Bantuan</h3>
            <button onclick="toggleFAQ('motor-mobil')" class="flex items-center bg-blue-500 text-white p-4 rounded-lg shadow-md hover:bg-blue-600 transition w-full">
                <img src="icon-motor-mobil.png" alt="Motor & Mobil" class="w-8 h-8 mr-3">
                <span class="text-lg font-semibold">Layanan Motor & Mobil Eks BCA Multifinance</span>
            </button>
            <div id="faq-motor-mobil" class="hidden mt-2"></div>
            <button onclick="toggleFAQ('bcamf')" class="flex items-center bg-blue-500 text-white p-4 rounded-lg shadow-md hover:bg-blue-600 transition w-full mt-4">
                <img src="icon-bcamf.png" alt="BCAMF" class="w-8 h-8 mr-3">
                <span class="text-lg font-semibold">Penggabungan BCAMF</span>
            </button>
            <div id="faq-bcamf" class="hidden mt-2"></div>
        </div>

        <!-- Pertanyaan Populer (Konten Dinamis) -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-lg" id="faq-content">
            <h3 class="text-2xl font-semibold text-gray-800">Pertanyaan</h3>
            <div id="faq-display" class="mt-4 border-t pt-4">
                <p class="text-gray-600">Silakan pilih kategori untuk melihat pertanyaan.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFAQ(category) {
        let content = getFAQContent(category);
        let isMobile = window.innerWidth < 768;

        if (isMobile) {
            document.querySelectorAll("[id^='faq-']").forEach(el => el.classList.add("hidden"));
            let mobileTarget = document.getElementById(`faq-${category}`);
            mobileTarget.classList.toggle("hidden");
            mobileTarget.innerHTML = mobileTarget.classList.contains("hidden") ? "" : content;
        } else {
            let desktopTarget = document.getElementById("faq-display");
            desktopTarget.innerHTML = content;
        }
    }

    function getFAQContent(category) {
        if (category === "motor-mobil") {
            return `
                <details class="mb-4 border-b pb-2 open">
                    <summary class="cursor-pointer font-medium text-blue-600 flex justify-between items-center">
                        Apakah ada nomor rekening khusus untuk proses pelunasan dipercepat (ET)?
                        <span>▼</span>
                    </summary>
                    <p class="mt-2 text-gray-600">Ya, ada rekening khusus untuk pelunasan dipercepat. Silakan hubungi CS untuk informasi lebih lanjut.</p>
                </details>`;
        } else if (category === "bcamf") {
            return `
                <details class="mb-4 border-b pb-2 open">
                    <summary class="cursor-pointer font-medium text-blue-600 flex justify-between items-center">
                        Apa dampaknya jika setelah tenor saya sudah berakhir?
                        <span>▼</span>
                    </summary>
                    <p class="mt-2 text-gray-600">Setelah tenor berakhir, Anda akan menerima surat keterangan lunas dan dapat mengambil BPKB kendaraan Anda.</p>
                </details>`;
        }
        return "<p class='text-gray-600'>Data tidak ditemukan.</p>";
    }

    function checkScreenSize() {
        let isMobile = window.innerWidth < 768;
        let faqContent = document.getElementById("faq-content");

        if (isMobile) {
            faqContent.style.display = "none";
        } else {
            faqContent.style.display = "block";
            let faqDisplay = document.getElementById("faq-display");
            if (faqDisplay.innerHTML.trim() === "" || faqDisplay.innerHTML.includes("Silakan pilih kategori")) {
                faqDisplay.innerHTML = getFAQContent("motor-mobil");
            }
        }
    }

    window.addEventListener("load", () => {
        checkScreenSize();
        if (window.innerWidth >= 768) {
            document.getElementById("faq-display").innerHTML = getFAQContent("motor-mobil");
        }
    });

    window.addEventListener("resize", checkScreenSize);
</script>

<?php
    include("includes/script.php");
    include("includes/footer.php");
?>