<?php
// ==========================================
// RENDER SIDEBAR (Bisa Buka Tutup + Menu Lengkap + URL Bersih)
// ==========================================
function renderSidebar($page, $is_superadmin, $user_kode) {
    // Custom CSS Sidebar
    echo '<style>
        .sidebar-bg { background-color: #041020; } /* Warna dark biru pekat sesuai gambar */
        .text-sidebar { color: #8a9bb2; }
        .text-sidebar-active { color: #ffffff; }
        .bg-sidebar-active { background-color: #1e293b; border-left: 4px solid #fbbf24; }
    </style>';

    ?>
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="w-64 sidebar-bg flex flex-col h-full fixed md:relative z-40 transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out shrink-0 shadow-xl">
        
        <div class="h-20 flex items-center px-6 mb-4 mt-2 justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-400 rounded-xl flex items-center justify-center font-extrabold text-gray-900 text-xl mr-3 shadow-[0_0_15px_rgba(251,191,36,0.4)]">C</div>
                <div>
                    <h1 class="text-white font-extrabold text-lg leading-tight tracking-wide">CESSIE Portal</h1>
                    <p class="text-[10px] text-sidebar font-medium">Asset & Debtor Offering</p>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 no-scrollbar">
            <p class="text-[10px] uppercase font-bold text-gray-500 mb-2 px-2 tracking-wider mt-2">Main Menu</p>
            <nav class="space-y-1 mb-6">
                <a href="dashboard" class="flex items-center px-4 py-3 <?= $page === 'dashboard' ? 'bg-sidebar-active text-sidebar-active' : 'text-sidebar hover:text-white hover:bg-white/5' ?> rounded-xl font-semibold transition-all">
                    <i class="fas fa-th-large w-6 <?= $page === 'dashboard' ? 'text-white' : '' ?>"></i> Dashboard
                </a>
                
                <a href="calon_cessie" class="flex items-center px-4 py-3 <?= in_array($page, ['calon_cessie', 'form_cessie']) ? 'bg-sidebar-active text-sidebar-active' : 'text-sidebar hover:text-white hover:bg-white/5' ?> rounded-xl font-semibold transition-all">
                    <i class="fas fa-user-friends w-6 <?= in_array($page, ['calon_cessie', 'form_cessie']) ? 'text-white' : '' ?>"></i> Calon Cessie
                </a>

                <a href="agunan" class="flex items-center px-4 py-3 <?= $page === 'agunan' ? 'bg-sidebar-active text-sidebar-active' : 'text-sidebar hover:text-white hover:bg-white/5' ?> rounded-xl font-semibold transition-all">
                    <i class="fas fa-images w-6 <?= $page === 'agunan' ? 'text-white' : '' ?>"></i> Data Agunan
                </a>

                <a href="data" class="flex items-center px-4 py-3 <?= in_array($page, ['data', 'form', 'view']) ? 'bg-sidebar-active text-sidebar-active' : 'text-sidebar hover:text-white hover:bg-white/5' ?> rounded-xl font-semibold transition-all">
                    <i class="fas fa-home w-6 <?= in_array($page, ['data', 'form', 'view']) ? 'text-white' : '' ?>"></i> Data Lelang
                </a>
                
                <a href="#" class="flex items-center px-4 py-3 text-sidebar hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all">
                    <i class="fas fa-images w-6"></i> Galeri Foto
                </a>
                
                <a href="#" class="flex items-center px-4 py-3 text-sidebar hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all">
                    <i class="fas fa-file-contract w-6"></i> Dokumen
                </a>
                
                <a href="pengajuan" class="flex items-center px-4 py-3 <?= $page === 'pengajuan' ? 'bg-sidebar-active text-sidebar-active' : 'text-sidebar hover:text-white hover:bg-white/5' ?> rounded-xl font-semibold transition-all">
                    <i class="fas fa-briefcase w-6 <?= $page === 'pengajuan' ? 'text-white' : '' ?>"></i> Investor/Pembeli
                </a>
                
                <a href="#" class="flex items-center px-4 py-3 text-sidebar hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all">
                    <i class="fas fa-chart-line w-6"></i> Monitoring Penawaran
                </a>
            </nav>

            <p class="text-[10px] uppercase font-bold text-gray-500 mb-2 px-2 tracking-wider">System</p>
            <nav class="space-y-1">
                <a href="#" class="flex items-center px-4 py-3 text-sidebar hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all">
                    <i class="fas fa-user-cog w-6"></i> User & Role
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-sidebar hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all">
                    <i class="fas fa-history w-6"></i> Audit Trail
                </a>
                <a href="?action=logout" class="flex items-center px-4 py-3 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-xl font-semibold transition-all mt-4">
                    <i class="fas fa-sign-out-alt w-6"></i> Logout
                </a>
            </nav>
        </div>

        <div class="p-4 mt-auto">
            <div class="bg-slate-800/40 rounded-2xl p-4 border border-slate-700/50">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs text-gray-300 font-medium">Portfolio Readiness</p>
                </div>
                <div class="flex items-end justify-between mb-3">
                    <h4 class="text-3xl font-extrabold text-white">78%</h4>
                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 text-xs font-extrabold rounded-full cursor-pointer hover:bg-yellow-200">Review</span>
                </div>
                <div class="w-full bg-slate-700/50 h-2 rounded-full overflow-hidden mb-2">
                    <div class="bg-gradient-to-r from-blue-500 to-yellow-400 h-2 rounded-full" style="width: 78%"></div>
                </div>
                <p class="text-[10px] text-gray-400">34 aset siap ditawarkan kepada...</p>
            </div>
        </div>
    </aside>
    <?php
}

// ==========================================
// RENDER HEADER ATAS (Ada Tombol Hamburger-nya)
// ==========================================
function renderHeader() {
    ?>
    <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-4 md:px-8 shrink-0 relative z-10">
        
        <div class="flex items-center gap-4 flex-1">
            
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-blue-600 focus:outline-none p-2.5 rounded-xl bg-slate-50 border border-gray-200 transition-colors shadow-sm">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="relative w-full max-w-md hidden md:block">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Cari debitur, agunan, cabang..." class="w-full bg-gray-50 border border-gray-200 text-sm rounded-full pl-11 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
            </div>
        </div>

        <div class="flex items-center gap-3 md:gap-5">
            <button class="relative text-gray-500 hover:text-blue-600 transition-colors">
                <i class="far fa-bell text-xl"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full border-2 border-white">3</span>
            </button>
            
            <div class="hidden md:flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-full border border-gray-200">
                <i class="fas fa-shield-alt text-slate-600 text-sm"></i>
                <span class="text-xs font-bold text-slate-700">Internal Bank</span>
            </div>

            <div class="h-8 w-px bg-gray-200 hidden md:block"></div>

            <div class="flex items-center gap-3 cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center">
                    AD
                </div>
                <div class="hidden md:block text-right">
                    <p class="text-sm font-bold text-gray-800 leading-tight">Admin Cessie</p>
                    <p class="text-[10px] text-gray-500">Divisi Kredit</p>
                </div>
            </div>
        </div>
    </header>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            // Logika untuk HP (Buka/Tutup panel & Overlay)
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            
            // Logika untuk PC/Laptop (Menggeser layout utama ke kiri)
            sidebar.classList.toggle('md:-ml-64');
        }
    </script>
    <?php
}
?>