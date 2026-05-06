<?php
    include("views/header.php");
    include("views/navbar.php");
?>

    <style>
        .sticky-menu {
            transition: all 0.3s ease-in-out;
        }
        @media (max-width: 768px) {
            #stickyMenu {
                display: none; /* Sembunyikan menu di mobile */
            }
            #rightSection {
                order: -1; /* Pindahkan bagian kanan ke atas pada mobile */
            }
            #actionButtons {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background: white;
                z-index: 100;
                box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
                padding: 10px;
                display: flex;
                justify-content: space-around;
            }
        }
    </style>

    <!-- Kontainer Utama -->
    <div class="max-w-6xl mx-auto mt-32 flex gap-6 flex-col md:flex-row">

        <!-- Bagian Kiri -->
        <div class="w-full md:w-2/3">
            <!-- Foto -->
            <div class="md:col-span-2">
                <!-- Foto Utama -->
                <div id="fotoSection" class="bg-white shadow-md rounded-lg overflow-hidden">
                    <img id="mainImage" src="img/lelang/001oke.jpg" class="w-full h-[350px] object-cover">
                </div>

                <!-- Galeri Thumbnail -->
                <div class="mt-4 flex gap-2">
                    <img onclick="changeImage(this)" src="img/lelang/001oke.jpg" class="w-24 h-16 object-cover cursor-pointer border-2 border-gray-300 hover:border-blue-500">
                    <img onclick="changeImage(this)" src="img/lelang/04.jpg" class="w-24 h-16 object-cover cursor-pointer border-2 border-gray-300 hover:border-blue-500">
                    <img onclick="changeImage(this)" src="img/lelang/001oke.jpg" class="w-24 h-16 object-cover cursor-pointer border-2 border-gray-300 hover:border-blue-500">
                    <div class="w-24 h-16 bg-gray-300 flex items-center justify-center text-gray-600">+3</div>
                </div>

                <!-- Informasi Tambahan -->
                <p class="text-gray-600 text-sm mt-2">Dipublikasikan pada 04 November 2024 | 10:58 WIB dikelola oleh KC Cilegon</p>
            </div>

            <!-- Menu Sticky -->
            <div id="stickyMenu" class="sticky-menu bg-white p-4 shadow-md mt-4 rounded-md flex justify-around z-20">
                <a href="#deskripsi" class="text-blue-500 font-semibold">Deskripsi</a>
                <a href="#spesifikasi" class="text-blue-500 font-semibold">Spesifikasi</a>
                <a href="#lokasi" class="text-blue-500 font-semibold">Lokasi</a>
                <a href="#skema" class="text-blue-500 font-semibold">Skema</a>
                <!-- <a href="#pembelian" class="text-blue-500 font-semibold">Pembelian</a> -->
                <a href="#kalkulator" class="text-blue-500 font-semibold">Kalkulator</a>
            </div>

            <!-- Konten -->
            <div id="deskripsi" class="mt-6 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-bold">Deskripsi Aset</h2>
                <p class="text-gray-600 mt-2">RUMAH TINGGAL DI DAERAH PCI CILEGON</p>
            </div>

            <!-- Spesifikasi -->
            <div id="spesifikasi" class="mt-6 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-bold">Spesifikasi Aset</h2>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-500 p-2 rounded-full">LT</span>
                        <p class="text-gray-600">136 m²</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-500 p-2 rounded-full">LB</span>
                        <p class="text-gray-600">136 m²</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-500 p-2 rounded-full">2</span>
                        <p class="text-gray-600">Lantai</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-500 p-2 rounded-full">🛏</span>
                        <p class="text-gray-600">4 Kamar</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-500 p-2 rounded-full">🚿</span>
                        <p class="text-gray-600">2 Kamar Mandi</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-500 p-2 rounded-full">🚘</span>
                        <p class="text-gray-600">1 Carport</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-500 p-2 rounded-full">🔌</span>
                        <p class="text-gray-600">1300 kWh</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-500 p-2 rounded-full">SHM</span>
                        <p class="text-gray-600">Dokumen SHM</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-500 p-2 rounded-full">📅</span>
                        <p class="text-gray-600">Dibangun Tahun 2012</p>
                    </div>
                </div>
            </div>
            <!-- Lokasi -->
            <!-- Lokasi -->
            <div id="lokasi" class="mt-6 bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-bold">Lokasi Aset</h2>
                    <div class="mt-4">
                        <div id="map" class="w-full h-72 rounded-lg shadow-md border border-gray-300"></div>
                        <p class="text-gray-600 mt-2">KOMPLEK PCI BLOK C 62 NO 12A /14 RT 03 RW 06, BANTEN, SERANG KAB., KRAMATWATU, 42161</p>
                        <a href="https://www.google.com/maps" target="_blank" class="text-blue-500 hover:underline">View on GoogleMaps</a>
                    </div>
            </div>

            <div id="skema" class="bg-white p-6 mt-4 shadow-md rounded-md">
                <h2 class="text-lg font-semibold">Skema Pembelian</h2>
                <p class="text-gray-600">Tersedia skema cicilan dan tunai...</p>
            </div>

            <div id="pembelian" class="bg-white p-6 mt-4 shadow-md rounded-md">
                <h2 class="text-lg font-semibold">Pembelian</h2>
                <p class="text-gray-600">Silakan hubungi kontak kami...</p>
            </div>

            <div id="kalkulator" class="bg-white p-6 mt-4 shadow-md rounded-md">
                <h2 class="text-lg font-semibold">Kalkulator</h2>
                <p class="text-gray-600">Hitung cicilan Anda di sini...</p>
            </div>
        </div>

        <!-- Bagian Kanan (Info Properti) -->
        <div id="rightSection" class="w-full md:w-1/3 bg-white p-4 shadow-md rounded-lg">
            <span class="text-green-500 text-sm font-semibold">Lelang</span>
            <h2 class="text-lg font-bold mt-1">RUMAH TINGGAL DI PCI CILEGON</h2>
            <p class="text-blue-600 text-xl font-bold mt-2">Rp 475.000.000</p>

            <!-- Spesifikasi -->
            <div class="flex gap-4 mt-3">
                <div class="bg-gray-100 px-3 py-1 rounded-md">
                    <p class="text-sm font-semibold">LT</p>
                    <p class="text-sm">136 m²</p>
                </div>
                <div class="bg-gray-100 px-3 py-1 rounded-md">
                    <p class="text-sm font-semibold">LB</p>
                    <p class="text-sm">136 m²</p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div id="actionButtons">
                <button class="w-full bg-blue-600 text-white py-2 rounded-lg mt-4 hover:bg-blue-700">
                    Ajukan KPR
                </button>
                <button class="w-full border border-gray-400 text-gray-600 py-2 rounded-lg mt-2 hover:bg-gray-200">
                    Hubungi Kami
                </button>
            </div>

            <p class="text-gray-500 text-xs mt-2">Jam operasional: Senin - Jumat, 07:30 - 16:30 WIB</p>

            <!-- Menu Simpan, Bagikan, Brosur -->
            <div class="flex justify-between mt-4">
                <button class="flex items-center gap-1 text-gray-600 hover:text-blue-500">
                    <span>💾</span> Simpan
                </button>
                <button class="flex items-center gap-1 text-gray-600 hover:text-blue-500">
                    <span>🔗</span> Bagikan
                </button>
                <button class="flex items-center gap-1 text-gray-600 hover:text-blue-500">
                    <span>📄</span> Brosur
                </button>
            </div>
        </div>
    </div>

    <script>
        function changeImage(element) {
            document.getElementById('mainImage').src = element.src;
        }

        const stickyMenu = document.getElementById("stickyMenu");
        const fotoSection = document.getElementById("fotoSection");
        const navbarHeight = document.querySelector("nav").offsetHeight;
        const rightSection = document.getElementById("rightSection");
        const actionButtons = document.getElementById("actionButtons");

        // window.addEventListener("scroll", () => {
        //     const fotoBottom = fotoSection.getBoundingClientRect().bottom;
            
        //     if (fotoBottom <= navbarHeight) {
        //         stickyMenu.classList.add("fixed", "top-[60px]", "left-0", "w-full", "shadow-lg");
        //         stickyMenu.classList.remove("mt-4");
        //     } else {
        //         stickyMenu.classList.remove("fixed", "top-[60px]", "left-0", "w-full", "shadow-lg");
        //         stickyMenu.classList.add("mt-4");
        //     }

        //     if (window.innerWidth <= 768) {
        //         const footer = document.querySelector("footer");
        //         const footerTop = footer.getBoundingClientRect().top;
        //         const viewportHeight = window.innerHeight;

        //         if (footerTop > viewportHeight) {
        //             actionButtons.style.position = "fixed";
        //         } else {
        //             actionButtons.style.position = "static";
        //         }
        //     }
        // });
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const rightSection = document.getElementById("rightSection");
        const fotoSection = document.getElementById("fotoSection");
        const navbarHeight = document.querySelector("nav").offsetHeight;
        const footer = document.querySelector("footer");

        function handleScroll() {
            const rightSectionHeight = rightSection.offsetHeight;
            const rightSectionTop = rightSection.getBoundingClientRect().top;
            const footerTop = footer.getBoundingClientRect().top;
            const viewportHeight = window.innerHeight;
            
            if (window.innerWidth > 768) { // Untuk tampilan desktop
                if (footerTop > viewportHeight) {
                    rightSection.style.position = "fixed";
                    rightSection.style.top = navbarHeight + "px";
                    rightSection.style.width = "30%";
                } else {
                    rightSection.style.position = "static";
                }
            } else { // Untuk tampilan mobile
                rightSection.style.position = "relative";
                rightSection.style.width = "100%";
            }
        }

        window.addEventListener("scroll", handleScroll);
        window.addEventListener("resize", handleScroll);
        handleScroll();
    });
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([-6.0092058, 106.0517302], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([-6.0092058, 106.0517302]).addTo(map)
        .bindPopup("<b>Lokasi Aset</b><br>KOMPLEK PCI BLOK C 62 NO 12A /14 RT 03 RW 06")
        .openPopup();

    map.on('click', function(e) {
        alert("Anda mengklik lokasi di peta: " + e.latlng.toString());
    });
</script>


    

<?php
    include("views/script.php");
    include("views/footer.php");
?>