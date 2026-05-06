<header class="bg-blue-900 text-white shadow-md fixed top-0 left-0 w-full z-50">
    <div class="container mx-auto flex justify-between items-center px-6 py-4">
        <!-- Logo -->
        <div class="flex items-center space-x-4">
            <img src="img/logooye.png" alt="BKK Logo" class="h-16">
        </div>

        <!-- Hamburger Menu (Mobile) -->
        <button id="menu-toggle" class="md:hidden text-white focus:outline-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        <!-- Navigation -->
        <nav id="menu" class="hidden md:flex space-x-6">
            <a href="home" class="hover:text-blue-300">Home</a>
            <a href="asset" class="hover:text-blue-300">Asset</a>
            <!-- <div class="relative">
                <button id="dropdownAssetsButton" class="flex items-center hover:text-blue-300">Aset Lelang
                    <svg class="w-2.5 h-2.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <div id="dropdownAssets" class="absolute left-0 mt-2 w-40 bg-blue-800 text-white hidden flex-col z-10">
                    <a href="aset_tanah" class="block px-4 py-2 hover:bg-blue-700">Tanah</a>
                    <a href="aset_bangunan" class="block px-4 py-2 hover:bg-blue-700">Bangunan</a>
                    <a href="aset_kendaraan" class="block px-4 py-2 hover:bg-blue-700">Kendaraan</a>
                </div>
            </div> -->
            <div class="relative">
                <button id="dropdownInfoButton" class="flex items-center hover:text-blue-300">Informasi Lelang
                    <svg class="w-2.5 h-2.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <div id="dropdownInfo" class="absolute left-0 mt-2 w-40 bg-blue-800 text-white hidden flex-col z-10">
                    <a href="prosedur" class="block px-4 py-2 hover:bg-blue-700">Prosedur</a>
                    <a href="faq" class="block px-4 py-2 hover:bg-blue-700">FAQ</a>
                    <a href="#" class="block px-4 py-2 hover:bg-blue-700">Kontak</a>
                </div>
            </div>
           
        </nav>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-blue-800 text-white px-6 py-4 space-y-4">
        <a href="index" class="block hover:text-blue-300">Home</a>
        <a href="asset" class="block hover:text-blue-300">Info Asset</a>
        
        <!-- <div>
            <button class="w-full text-left hover:text-blue-300" onclick="toggleDropdown('mobile-assets')">Aset Lelang</button>
            <div id="mobile-assets" class="hidden flex flex-col pl-4">
                <a href="aset_tanah" class="block hover:text-blue-300">Tanah</a>
                <a href="aset_bangunan" class="block hover:text-blue-300">Bangunan</a>
                <a href="aset_kendaraan" class="block hover:text-blue-300">Kendaraan</a>
            </div>
        </div> -->
        <div>
            <button class="w-full text-left hover:text-blue-300" onclick="toggleDropdown('mobile-info')">Informasi Lelang</button>
            <div id="mobile-info" class="hidden flex flex-col pl-4">
                <a href="prosedur" class="block hover:text-blue-300">Prosedur</a>
                <a href="faq" class="block hover:text-blue-300">FAQ</a>
                <a href="#" class="block hover:text-blue-300">Kontak</a>
            </div>
        </div>
        
    </div>
</header>

<script>
    document.getElementById('menu-toggle').addEventListener('click', function () {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    function toggleDropdown(id) {
        let menu = document.getElementById(id);
        menu.classList.toggle('hidden');
    }

    function closeOtherDropdowns(openDropdownId) {
        ['dropdownAssets', 'dropdownInfo'].forEach(id => {
            if (id !== openDropdownId) {
                document.getElementById(id).classList.add('hidden');
            }
        });
    }

    document.getElementById('dropdownAssetsButton').addEventListener('click', function () {
        closeOtherDropdowns('dropdownAssets');
        document.getElementById('dropdownAssets').classList.toggle('hidden');
    });

    document.getElementById('dropdownInfoButton').addEventListener('click', function () {
        closeOtherDropdowns('dropdownInfo');
        document.getElementById('dropdownInfo').classList.toggle('hidden');
    });
</script>
