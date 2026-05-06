<header class="bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-lg fixed top-0 left-0 w-full z-50">
    <div class="container mx-auto flex justify-between items-center px-6 py-4">
        <!-- Logo -->
        <a href="home" class="flex items-center space-x-4 group">
            <img src="img/logooye.png" alt="BKK Logo" class="h-14 md:h-16 transition-transform transform group-hover:scale-105 duration-300">
        </a>

        <!-- Hamburger Menu (Mobile) -->
        <button id="menu-toggle" class="md:hidden text-white hover:text-blue-300 focus:outline-none transition-colors duration-300">
            <!-- Hamburger Icon -->
            <svg id="icon-menu" class="w-8 h-8 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
            <!-- Close (X) Icon -->
            <svg id="icon-close" class="w-8 h-8 hidden transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Navigation (Desktop) -->
        <nav id="menu" class="hidden md:flex space-x-8 items-center font-medium">
            <a href="home" class="hover:text-blue-300 transition-colors duration-300 border-b-2 border-transparent hover:border-blue-300 py-1">Home</a>
            <a href="asset" class="hover:text-blue-300 transition-colors duration-300 border-b-2 border-transparent hover:border-blue-300 py-1">Asset</a>
            
            <!-- Dropdown -->
            <div class="relative group">
                <button id="dropdownInfoButton" class="flex items-center hover:text-blue-300 transition-colors duration-300 py-1 focus:outline-none">
                    Informasi Lelang
                    <svg id="desktopInfoIcon" class="w-4 h-4 ml-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <!-- Dropdown Menu Desktop -->
                <div id="dropdownInfo" class="absolute left-0 mt-4 w-48 bg-white text-blue-900 rounded-lg shadow-xl hidden flex-col z-20 border border-gray-100 overflow-hidden transform origin-top transition-all duration-300">
                    <a href="prosedur" class="block px-5 py-3 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200">Prosedur</a>
                    <a href="faq" class="block px-5 py-3 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 border-t border-gray-100">FAQ</a>
                    <a href="#" class="block px-5 py-3 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 border-t border-gray-100">Kontak</a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Mobile Menu (Diubah warnanya jadi Putih) -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200 absolute w-full shadow-2xl transition-all duration-300">
        <div class="flex flex-col px-6 py-5 space-y-4">
            <!-- Teks diubah jadi abu-abu tua (text-gray-800) -->
            <a href="home" class="block text-gray-800 font-medium hover:text-blue-600 hover:translate-x-2 transition-all duration-300">Home</a>
            <a href="asset" class="block text-gray-800 font-medium hover:text-blue-600 hover:translate-x-2 transition-all duration-300">Info Asset</a>
            
            <div class="border-t border-gray-200 pt-4">
                <button id="mobileInfoToggle" class="w-full flex justify-between items-center text-left text-gray-800 font-medium hover:text-blue-600 transition-colors duration-300 focus:outline-none">
                    <span>Informasi Lelang</span>
                    <!-- Icon panah diubah warnanya -->
                    <svg id="mobileInfoIcon" class="w-4 h-4 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div id="mobile-info" class="hidden flex-col pl-4 mt-3 space-y-3 border-l-2 border-blue-500 ml-1">
                    <!-- Sub-menu pakai abu-abu menengah (text-gray-600) -->
                    <a href="prosedur" class="block text-gray-600 hover:text-blue-600 hover:translate-x-2 transition-all duration-300">Prosedur</a>
                    <a href="faq" class="block text-gray-600 hover:text-blue-600 hover:translate-x-2 transition-all duration-300">FAQ</a>
                    <a href="#" class="block text-gray-600 hover:text-blue-600 hover:translate-x-2 transition-all duration-300">Kontak</a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements Mobile Menu
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconMenu = document.getElementById('icon-menu');
        const iconClose = document.getElementById('icon-close');

        // Elements Desktop Dropdown
        const dropdownInfoBtn = document.getElementById('dropdownInfoButton');
        const dropdownInfo = document.getElementById('dropdownInfo');
        const desktopInfoIcon = document.getElementById('desktopInfoIcon');

        // Elements Mobile Dropdown
        const mobileInfoToggle = document.getElementById('mobileInfoToggle');
        const mobileInfo = document.getElementById('mobile-info');
        const mobileInfoIcon = document.getElementById('mobileInfoIcon');

        // Toggle mobile menu (Hamburger to X)
        menuToggle.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
            iconMenu.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });

        // Toggle Desktop Dropdown
        if (dropdownInfoBtn) {
            dropdownInfoBtn.addEventListener('click', function (e) {
                e.stopPropagation(); // Mencegah event merambat ke window
                dropdownInfo.classList.toggle('hidden');
                desktopInfoIcon.classList.toggle('rotate-180');
            });
        }

        // Close Desktop Dropdown kalau klik di luar area
        document.addEventListener('click', function(e) {
            if (dropdownInfo && !dropdownInfoBtn.contains(e.target) && !dropdownInfo.contains(e.target)) {
                dropdownInfo.classList.add('hidden');
                desktopInfoIcon.classList.remove('rotate-180');
            }
        });

        // Toggle Mobile Dropdown
        if (mobileInfoToggle) {
            mobileInfoToggle.addEventListener('click', function () {
                mobileInfo.classList.toggle('hidden');
                mobileInfoIcon.classList.toggle('rotate-180');
            });
        }
    });
</script>