<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out flex flex-col overflow-hidden" 
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <!-- Logo -->
            <div class="flex items-center justify-center bg-green-600 text-white flex-shrink-0" style="height: 73px;">
                <img src="/storage/logo-smansa.png" alt="Logo SMAN 1" class="h-8 w-8 mr-3 rounded-full bg-white object-contain border border-white/20 shadow-sm" />
                <div class="text-white">
                    <h1 class="text-sm font-bold leading-tight">SIAKAD SMAN 1</h1>
                    <p class="text-xs text-green-100 leading-tight">Kota Dumai</p>
                </div>
            </div>
            
            <!-- Navigation - Scrollable area -->
            <div class="flex-1 flex flex-col min-h-0">
                <nav class="flex-1 overflow-y-auto py-4 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100" style="padding-bottom: 100px;">
                    <div class="px-4 space-y-2">
                    <!-- Dashboard -->
                    <SidebarLink 
                        :href="route('dashboard')" 
                        :active="route().current('dashboard')"
                        icon="🏠"
                        active-class="bg-green-600 text-white">
                        Dashboard
                    </SidebarLink>

                    <!-- Wali Kelas Menu - Hanya untuk guru yang di-assign sebagai wali kelas -->
                    <SidebarLink 
                        v-if="$page.props.auth.user?.is_wali_kelas"
                        :href="route('wali-kelas.dashboard')" 
                        :active="route().current('wali-kelas.*')"
                        icon="👩‍🏫"
                        active-class="bg-blue-600 text-white">
                        Wali Kelas
                    </SidebarLink>

                    <!-- Data Master Dropdown -->
                    <div class="mb-2">
                        <button @click="toggleDataMasterDropdown" 
                                :class="{
                                    'bg-green-100 text-green-800 font-medium': isDataMasterActive,
                                    'text-gray-700 hover:bg-gray-100': !isDataMasterActive
                                }"
                                class="w-full flex items-center justify-between px-3 py-2 text-left focus:outline-none focus:bg-gray-100 rounded-lg transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <span class="mr-3">📊</span>
                                <span class="text-sm font-medium">Data Master</span>
                            </div>
                            <svg :class="{ 'rotate-180': dataMasterDropdownOpen }" 
                                 class="w-4 h-4 transition-transform duration-200" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Content -->
                        <transition name="dropdown-slide">
                            <div v-show="dataMasterDropdownOpen" 
                                 class="mt-1 ml-6 space-y-1 bg-gray-50 rounded-lg p-2 border-l-2 border-green-200">
                                <!-- Data Guru -->
                                <SidebarLink 
                                    v-if="canAccess(['kepala_tatausaha', 'tata_usaha', 'guru'])"
                                    :href="route('guru.index')" 
                                    :active="route().current('guru.*')"
                                    icon="👨‍🏫"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-green-100 text-green-800 font-medium">
                                    Data Guru
                                </SidebarLink>
                                
                                <!-- Wali Kelas -->
                                <SidebarLink 
                                    v-if="canAccess(['kepala_tatausaha', 'tata_usaha'])"
                                    :href="route('wali-kelas.index')" 
                                    :active="route().current('wali-kelas.*')"
                                    icon="👩‍🏫"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-green-100 text-green-800 font-medium">
                                    Wali Kelas
                                </SidebarLink>
                                
                                <!-- Data Siswa -->
                                <SidebarLink 
                                    v-if="canAccess(['kepala_tatausaha', 'tata_usaha', 'guru'])"
                                    :href="route('siswa.index')" 
                                    :active="route().current('siswa.*')"
                                    icon="👨‍🎓"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-green-100 text-green-800 font-medium">
                                    Data Siswa
                                </SidebarLink>
                                
                                <!-- Kelas -->
                                <SidebarLink 
                                    :href="route('kelas.index')" 
                                    :active="route().current('kelas.*')"
                                    icon="🏫"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-green-100 text-green-800 font-medium">
                                    Kelas
                                </SidebarLink>
                            </div>
                        </transition>
                    </div>

                    <!-- Data Akademik Dropdown -->
                    <div class="mb-2">
                        <button @click="toggleDataAkademikDropdown" 
                                :class="{
                                    'bg-blue-100 text-blue-800 font-medium': isDataAkademikActive,
                                    'text-gray-700 hover:bg-gray-100': !isDataAkademikActive
                                }"
                                class="w-full flex items-center justify-between px-3 py-2 text-left focus:outline-none focus:bg-gray-100 rounded-lg transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <span class="mr-3">📚</span>
                                <span class="text-sm font-medium">Akademik</span>
                            </div>
                            <svg :class="{ 'rotate-180': dataAkademikDropdownOpen }" 
                                 class="w-4 h-4 transition-transform duration-200" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Content -->
                        <transition name="dropdown-slide">
                            <div v-show="dataAkademikDropdownOpen" 
                                 class="mt-1 ml-6 space-y-1 bg-blue-50 rounded-lg p-2 border-l-2 border-blue-200">
                                <!-- Mata Pelajaran -->
                                <SidebarLink 
                                    v-if="canAccess(['kepala_tatausaha', 'tata_usaha', 'guru'])"
                                    :href="route('mata-pelajaran.index')" 
                                    :active="route().current('mata-pelajaran.*')"
                                    icon="📚"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-blue-100 text-blue-800 font-medium">
                                    Mata Pelajaran
                                </SidebarLink>
                                
                                <!-- Jadwal Pelajaran -->
                                <SidebarLink 
                                    :href="route('jadwal-pelajaran.index')" 
                                    :active="route().current('jadwal-pelajaran.*')"
                                    icon="📅"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-blue-100 text-blue-800 font-medium">
                                    Jadwal Pelajaran
                                </SidebarLink>
                                
                                <!-- Manajemen KKM -->
                                <SidebarLink 
                                    v-if="canAccess(['guru', 'kepala_tatausaha', 'tata_usaha'])"
                                    :href="route('kkm.index')" 
                                    :active="route().current('kkm.*')"
                                    icon="🎯"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-blue-100 text-blue-800 font-medium">
                                    Manajemen KKM
                                </SidebarLink>
                            </div>
                        </transition>
                    </div>

                    <!-- Penilaian Dropdown -->
                    <div class="mb-2" v-if="canAccess(['guru', 'kepala_tatausaha', 'murid'])">
                        <button @click="togglePenilaianDropdown" 
                                :class="{
                                    'bg-green-100 text-green-800 font-medium': isPenilaianActive,
                                    'text-gray-700 hover:bg-gray-100': !isPenilaianActive
                                }"
                                class="w-full flex items-center justify-between px-3 py-2 text-left focus:outline-none focus:bg-gray-100 rounded-lg transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <span class="mr-3">📝</span>
                                <span class="text-sm font-medium">Penilaian</span>
                            </div>
                            <svg :class="{ 'rotate-180': penilaianDropdownOpen }" 
                                 class="w-4 h-4 transition-transform duration-200" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Content -->
                        <transition name="dropdown-slide">
                            <div v-show="penilaianDropdownOpen" 
                                 class="mt-1 ml-6 space-y-1 bg-green-50 rounded-lg p-2 border-l-2 border-green-200">
                                <!-- Input Nilai -->
                                <SidebarLink 
                                    v-if="canAccess(['guru', 'kepala_tatausaha'])"
                                    :href="route('nilai-siswa.index')" 
                                    :active="route().current('nilai-siswa.*')"
                                    icon="📝"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-green-100 text-green-800 font-medium">
                                    Input Nilai
                                </SidebarLink>
                                
                                <!-- Nilai Saya (Murid) -->
                                <SidebarLink 
                                    v-if="canAccess(['murid'])"
                                    :href="route('nilai-saya.index')" 
                                    :active="route().current('nilai-saya.*')"
                                    icon="📊"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-green-100 text-green-800 font-medium">
                                    Nilai Saya
                                </SidebarLink>
                            </div>
                        </transition>
                    </div>

                    <!-- Kehadiran Dropdown -->
                    <div class="mb-2" v-if="canAccess(['guru']) || canAccess(['kepala_tatausaha', 'tata_usaha'])">
                        <button @click="toggleKehadiranDropdown" 
                                :class="{
                                    'bg-orange-100 text-orange-800 font-medium': isKehadiranActive,
                                    'text-gray-700 hover:bg-gray-100': !isKehadiranActive
                                }"
                                class="w-full flex items-center justify-between px-3 py-2 text-left focus:outline-none focus:bg-gray-100 rounded-lg transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <span class="mr-3">📋</span>
                                <span class="text-sm font-medium">Kehadiran</span>
                            </div>
                            <svg :class="{ 'rotate-180': kehadiranDropdownOpen }" 
                                 class="w-4 h-4 transition-transform duration-200" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Content -->
                        <transition name="dropdown-slide">
                            <div v-show="kehadiranDropdownOpen" 
                                 class="mt-1 ml-6 space-y-1 bg-orange-50 rounded-lg p-2 border-l-2 border-orange-200">
                                <!-- Input Absensi Siswa - Hanya untuk Guru -->
                                <SidebarLink 
                                    v-if="canAccess(['guru'])"
                                    :href="route('absensi.index')" 
                                    :active="route().current('absensi.index')"
                                    icon="📋"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-orange-100 text-orange-800 font-medium">
                                    Absensi Siswa
                                </SidebarLink>
                                
                                <!-- Absensi Guru (Ketidakhadiran) - Untuk semua guru -->
                                <SidebarLink 
                                    v-if="canAccess(['guru'])"
                                    :href="route('absensi-guru.index')" 
                                    :active="route().current('absensi-guru.*')"
                                    icon="👨‍🏫"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-orange-100 text-orange-800 font-medium">
                                    Absensi Guru
                                </SidebarLink>
                                
                                <!-- Monitoring Absensi -->
                                <SidebarLink 
                                    v-if="canAccess(['kepala_tatausaha', 'tata_usaha'])"
                                    :href="route('absensi.monitoring')" 
                                    :active="route().current('absensi.monitoring')"
                                    icon="📊"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-orange-100 text-orange-800 font-medium">
                                    Monitoring Absensi Siswa
                                </SidebarLink>
                                
                                <!-- Monitoring Absensi Guru -->
                                <SidebarLink 
                                    v-if="canAccess(['kepala_tatausaha', 'tata_usaha'])"
                                    :href="route('absensi-guru.monitoring')" 
                                    :active="route().current('absensi-guru.monitoring')"
                                    icon="👥"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-orange-100 text-orange-800 font-medium">
                                    Monitoring Absensi Guru
                                </SidebarLink>
                            </div>
                        </transition>
                    </div>

                    <!-- System Dropdown -->
                    <div class="mb-2">
                        <button @click="toggleSystemDropdown" 
                                :class="{
                                    'bg-purple-100 text-purple-800 font-medium': isSystemActive,
                                    'text-gray-700 hover:bg-gray-100': !isSystemActive
                                }"
                                class="w-full flex items-center justify-between px-3 py-2 text-left focus:outline-none focus:bg-gray-100 rounded-lg transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <span class="mr-3">⚙️</span>
                                <span class="text-sm font-medium">System</span>
                            </div>
                            <svg :class="{ 'rotate-180': systemDropdownOpen }" 
                                 class="w-4 h-4 transition-transform duration-200" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Content -->
                        <transition name="dropdown-slide">
                            <div v-show="systemDropdownOpen" 
                                 class="mt-1 ml-6 space-y-1 bg-purple-50 rounded-lg p-2 border-l-2 border-purple-200">
                                <!-- Approval Requests -->
                                <SidebarLink 
                                    v-if="canAccess(['kepala_tatausaha', 'tata_usaha'])"
                                    :href="route('approval-requests.index')" 
                                    :active="route().current('approval-requests.*')"
                                    icon="✅"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-purple-100 text-purple-800 font-medium">
                                    Persetujuan
                                </SidebarLink>
                                
                                <!-- My Approval Requests -->
                                <SidebarLink 
                                    :href="route('my-approval-requests.index')" 
                                    :active="route().current('my-approval-requests.*')"
                                    icon="📄"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-purple-100 text-purple-800 font-medium">
                                    Status Permintaan
                                </SidebarLink>
                                
                                <!-- Settings -->
                                <SidebarLink 
                                    v-if="canAccess(['kepala_tatausaha'])"
                                    :href="route('settings.index')" 
                                    :active="route().current('settings.*')"
                                    icon="⚙️"
                                    class="text-sm pl-2 hover:bg-white transition-colors duration-150"
                                    active-class="bg-purple-100 text-purple-800 font-medium">
                                    Pengaturan
                                </SidebarLink>
                            </div>
                        </transition>
                    </div>
                    
                </div>
                </nav>
            </div>
            
            <!-- User Info & Logout Section (Fixed at bottom) -->
            <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-10">
                <!-- User Profile Info -->
                <div class="px-4 py-2 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <!-- User Avatar -->
                        <div class="flex-shrink-0">
                            <img v-if="getUserPhoto()" 
                                 :src="getUserPhoto()" 
                                 class="w-8 h-8 rounded-full object-cover border-2 border-green-200 shadow-sm" 
                                 :alt="$page.props.auth.user.name">
                            <div v-else 
                                 class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center border-2 border-green-200 shadow-sm">
                                <span class="text-white text-xs font-bold">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                        </div>
                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $page.props.auth.user.name }}
                            </p>
                            <p class="text-xs text-gray-500 truncate flex items-center">
                                <span class="inline-block w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></span>
                                {{ $page.props.auth.user.role?.display_name || 'User' }}
                            </p>
                        </div>
                        <!-- Profile Button -->
                        <button @click="router.visit(canAccess(['murid']) ? route('murid.profile.edit') : route('profile.edit'))"
                                class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Logout Button -->
                <div class="px-4 py-2">
                    <button @click="handleLogout" 
                            :disabled="isLoggingOut"
                            :class="{ 'opacity-50 cursor-not-allowed': isLoggingOut }"
                            class="logout-btn w-full flex items-center justify-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-all duration-200 disabled:hover:bg-red-50 border border-red-200 hover:border-red-300 shadow-sm">
                        <!-- Loading Spinner -->
                        <svg v-if="isLoggingOut" class="animate-spin spinner-fade-in -ml-1 mr-2 h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <!-- Logout Icon -->
                        <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="font-medium text-xs">
                            {{ isLoggingOut ? 'Logging out...' : 'Logout' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Overlay for mobile -->
        <div v-if="sidebarOpen" 
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"></div>
        
        <!-- Main Content -->
        <div class="lg:ml-64">
            <!-- Top Header -->
            <header class="sticky top-0 z-30 bg-green-600 shadow-lg">
                <div class="flex items-center justify-between px-3 sm:px-4" style="height: 73px;">
                    <div class="flex items-center space-x-2 sm:space-x-4 flex-1 min-w-0">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="lg:hidden p-2 rounded-md text-white/80 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <!-- Page Title dengan breadcrumb style -->
                        <div class="flex items-center space-x-2 text-white min-w-0 flex-1">
                            <div class="min-w-0 flex-1">
                                <h1 class="text-sm sm:text-base md:text-lg font-semibold truncate">{{ pageTitle }}</h1>
                                <p class="text-xs text-green-100 truncate">{{ getCurrentDateTime() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right side actions -->
                    <div class="relative flex items-center space-x-1 sm:space-x-2 md:space-x-3 flex-shrink-0">
                        <!-- Quick Actions (only visible on larger screens) -->
                        <div class="hidden md:flex items-center space-x-2">
                            <!-- Quick Search -->
                            <button @click="openSearchModal" 
                                    class="p-2 text-white/80 hover:bg-white/10 rounded-lg transition-colors relative group">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <!-- Tooltip -->
                                <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                                    Pencarian Global (Ctrl+K)
                                </div>
                            </button>
                            
                            <!-- Settings -->
                            <button v-if="canAccess(['kepala_tatausaha'])" 
                                    @click="router.visit(route('settings.index'))"
                                    class="p-2 text-white/80 hover:bg-white/10 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Mobile Quick Search (visible on small screens) -->
                        <button @click="openSearchModal" 
                                class="md:hidden p-2 text-white/80 hover:bg-white/10 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        
                        <!-- Notifications Dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="96">
                                <template #trigger>
                                    <button @click="onNotificationButtonClick" class="relative p-2 text-white/80 hover:bg-white/10 focus:outline-none focus:text-white rounded-lg transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h16z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0" />
                                        </svg>
                                        <!-- Enhanced Notification Badge -->
                                        <span v-if="unreadNotificationsCount > 0" 
                                              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-green-700 transform translate-x-1/2 -translate-y-1/2 bg-yellow-400 rounded-full min-w-[1rem] h-4 sm:min-w-[1.25rem] sm:h-5 animate-pulse border-2 border-white shadow-lg">
                                            <span class="hidden sm:inline">{{ unreadNotificationsCount > 99 ? '99+' : unreadNotificationsCount }}</span>
                                            <span class="sm:hidden">{{ unreadNotificationsCount > 9 ? '9+' : unreadNotificationsCount }}</span>
                                        </span>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="w-80 sm:w-96 max-w-screen-sm">
                                        <!-- Header -->
                                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-sm font-medium text-gray-900">Notifikasi</h3>
                                                <div class="flex space-x-2">
                                                    <Link :href="route('notifications.index')" 
                                                          class="text-xs text-blue-600 hover:text-blue-800 truncate">
                                                        Lihat semua
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Notifications List -->
                                        <div class="max-h-80 sm:max-h-96 overflow-y-auto">
                                            <div v-if="recentNotifications.length === 0" class="px-4 py-6 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h16z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0" />
                                                </svg>
                                                <p class="text-sm text-gray-500">Tidak ada notifikasi</p>
                                            </div>
                                            
                                            <div v-for="notification in recentNotifications" 
                                                 :key="notification.id" 
                                                 @click="handleNotificationClick(notification)"
                                                 class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                                                <div class="flex items-start space-x-3">
                                                    <div :class="getNotificationIconColor(notification.type)" 
                                                         class="flex-shrink-0 w-2 h-2 rounded-full mt-2"></div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">
                                                            {{ getNotificationTitle(notification) }}
                                                        </p>
                                                        <p class="text-sm text-gray-600 line-clamp-2 break-words">
                                                            {{ getNotificationMessage(notification) }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 mt-1 truncate">
                                                            {{ getRelativeTime(notification.created_at) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                        
                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center px-1 sm:px-2 md:px-3 py-1.5 sm:py-2 border border-white/20 text-xs sm:text-sm leading-4 font-medium rounded-lg text-white bg-white/10 hover:bg-white/20 focus:outline-none focus:bg-white/20 transition-all duration-150 backdrop-blur-sm">
                                        <div class="flex items-center">
                                            <!-- Untuk role murid, gunakan foto siswa; untuk role lain gunakan foto user -->
                                            <img v-if="getUserPhoto()" 
                                                 :src="getUserPhoto()" 
                                                 class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 rounded-full mr-1 sm:mr-2 object-cover border-2 border-white/30 shadow-sm flex-shrink-0" 
                                                 :alt="$page.props.auth.user.name">
                                            <div v-else 
                                                 class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 rounded-full bg-white/20 mr-1 sm:mr-2 flex items-center justify-center border-2 border-white/30 flex-shrink-0">
                                                <span class="text-xs sm:text-sm font-semibold text-white">
                                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                            <div class="text-left hidden sm:block min-w-0 flex-1">
                                                <div class="font-semibold text-white text-xs sm:text-sm truncate max-w-24 sm:max-w-32 md:max-w-none">{{ $page.props.auth.user.name }}</div>
                                                <div class="text-xs text-green-100 truncate max-w-24 sm:max-w-32 md:max-w-none hidden md:block">{{ $page.props.auth.user.role?.display_name }}</div>
                                            </div>
                                        </div>
                                        <svg class="ml-0.5 sm:ml-1 md:ml-2 -mr-0.5 h-3 w-3 sm:h-4 sm:w-4 text-white/80 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="bg-white rounded-lg shadow-lg border border-gray-200 w-64 sm:w-auto">
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <div class="flex items-center space-x-3">
                                                <img v-if="getUserPhoto()" 
                                                     :src="getUserPhoto()" 
                                                     class="w-10 h-10 rounded-full object-cover border border-gray-200 flex-shrink-0" 
                                                     :alt="$page.props.auth.user.name">
                                                <div v-else 
                                                     class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-green-600 font-semibold">
                                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="font-medium text-gray-900 truncate">{{ $page.props.auth.user.name }}</div>
                                                    <div class="text-sm text-gray-500 truncate">{{ $page.props.auth.user.role?.display_name }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="py-1">
                                            <DropdownLink :href="canAccess(['murid']) ? route('murid.profile.edit') : route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span class="truncate">Lihat Profile</span>
                                            </DropdownLink>
                                            <button @click="handleLogout" 
                                                    :disabled="isLoggingOut"
                                                    :class="{ 'opacity-50 cursor-not-allowed': isLoggingOut }"
                                                    class="logout-btn flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 disabled:hover:bg-transparent transition-colors">
                                                <!-- Loading Spinner -->
                                                <svg v-if="isLoggingOut" class="animate-spin spinner-fade-in w-4 h-4 mr-3 text-red-600 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <!-- Logout Icon -->
                                                <svg v-else class="w-4 h-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                <span class="truncate">{{ isLoggingOut ? 'Logging out...' : 'Logout' }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="p-3 sm:p-6">
                <slot />
            </main>
        </div>
        
        <!-- Flash Messages -->
        <FlashMessages />
        
        <!-- Logout Loading Overlay -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isLoggingOut" class="fixed inset-0 z-[60] flex items-center justify-center">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                
                <!-- Loading Content -->
                <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-sm mx-4">
                    <div class="text-center">
                        <!-- Animated Logo -->
                        <div class="mx-auto flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg class="animate-spin w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        
                        <!-- Loading Text -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Logging out...</h3>
                        <p class="text-sm text-gray-500">Please wait while we securely log you out</p>
                        
                        <!-- Progress Dots -->
                        <div class="flex justify-center space-x-1 mt-4">
                            <div class="w-2 h-2 bg-green-600 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-green-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-green-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
        
        <!-- Global Search Modal -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showSearchModal" class="fixed inset-0 z-50 flex items-start justify-center pt-16 px-4">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeSearchModal"></div>
                
                <!-- Search Modal -->
                <div class="relative w-full max-w-2xl">
                    <!-- Search Input -->
                    <div class="bg-white rounded-t-xl shadow-2xl border border-gray-200">
                        <div class="flex items-center px-4 py-4 border-b border-gray-100">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input
                                ref="searchInput"
                                v-model="searchQuery"
                                @input="performSearch"
                                @keydown.escape="closeSearchModal"
                                @keydown.enter="selectFirstResult"
                                type="text"
                                placeholder="Cari siswa, guru, kelas, mata pelajaran..."
                                class="flex-1 text-lg outline-none placeholder-gray-400"
                            />
                            <button @click="closeSearchModal" class="ml-2 p-1 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Search Results -->
                        <div class="max-h-96 overflow-y-auto">
                            <!-- Loading State -->
                            <div v-if="searchLoading" class="flex items-center justify-center py-8">
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-green-600 rounded-full animate-bounce"></div>
                                    <div class="w-4 h-4 bg-green-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-4 h-4 bg-green-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                            
                            <!-- No Query State -->
                            <div v-else-if="!searchQuery.trim()" class="px-4 py-8 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">Mulai mengetik untuk mencari...</p>
                                <div class="mt-4 text-xs text-gray-400">
                                    <p>Tekan <kbd class="px-2 py-1 bg-gray-100 rounded text-xs">ESC</kbd> untuk menutup</p>
                                </div>
                            </div>
                            
                            <!-- No Results -->
                            <div v-else-if="searchResults.length === 0 && !searchLoading" class="px-4 py-8 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20a7.962 7.962 0 01-6-2.709M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                                </svg>
                                <p class="text-gray-500">Tidak ada hasil ditemukan untuk "{{ searchQuery }}"</p>
                                <p class="text-gray-400 text-xs mt-2">Coba gunakan kata kunci lain</p>
                            </div>
                            
                            <!-- Search Results -->
                            <div v-else class="py-2">
                                <!-- Group results by type -->
                                <div v-for="(group, type) in groupedResults" :key="type" class="mb-4">
                                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                                        <h3 class="text-sm font-medium text-gray-700 flex items-center">
                                            <span class="mr-2">{{ getGroupIcon(type) }}</span>
                                            {{ getGroupTitle(type) }}
                                            <span class="ml-2 text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">
                                                {{ group.length }}
                                            </span>
                                        </h3>
                                    </div>
                                    <div class="divide-y divide-gray-100">
                                        <button
                                            v-for="(result, index) in group"
                                            :key="`${type}-${index}`"
                                            @click="selectResult(result)"
                                            class="w-full px-4 py-3 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none transition-colors"
                                        >
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div :class="getResultIconClass(result.type)" class="w-8 h-8 rounded-full flex items-center justify-center">
                                                        <span class="text-sm">{{ getResultIcon(result.type) }}</span>
                                                    </div>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-gray-900" v-html="highlightMatch(result.title, searchQuery)"></p>
                                                    <p class="text-xs text-gray-500" v-html="highlightMatch(result.subtitle, searchQuery)"></p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Search Footer -->
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 rounded-b-xl">
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center">
                                        <kbd class="px-2 py-1 bg-white border border-gray-200 rounded text-xs mr-1">↵</kbd>
                                        untuk membuka
                                    </span>
                                    <span class="flex items-center">
                                        <kbd class="px-2 py-1 bg-white border border-gray-200 rounded text-xs mr-1">ESC</kbd>
                                        untuk menutup
                                    </span>
                                </div>
                                <span>Pencarian Global SIAKAD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import SidebarLink from '@/Components/SidebarLink.vue'
import FlashMessages from '@/Components/FlashMessages.vue'

const props = defineProps({
    pageTitle: {
        type: String,
        default: 'Dashboard'
    }
})

const page = usePage()
const sidebarOpen = ref(false)
const dataMasterDropdownOpen = ref(false)
const dataAkademikDropdownOpen = ref(false)
const penilaianDropdownOpen = ref(false)
const kehadiranDropdownOpen = ref(false)
const systemDropdownOpen = ref(false)
const notifications = ref([])
const showSearchModal = ref(false)
const searchQuery = ref('')
const searchResults = ref([])
const searchLoading = ref(false)
const searchTimeout = ref(null)
const searchInput = ref(null)
const isLoggingOut = ref(false) // Loading state untuk logout

const canAccess = (roles) => {
    const userRole = page.props.auth.user.role?.name
    return roles.includes(userRole)
}

const toggleDataMasterDropdown = () => {
    dataMasterDropdownOpen.value = !dataMasterDropdownOpen.value
}

const toggleDataAkademikDropdown = () => {
    dataAkademikDropdownOpen.value = !dataAkademikDropdownOpen.value
}

const togglePenilaianDropdown = () => {
    penilaianDropdownOpen.value = !penilaianDropdownOpen.value
}

const toggleKehadiranDropdown = () => {
    kehadiranDropdownOpen.value = !kehadiranDropdownOpen.value
}

const toggleSystemDropdown = () => {
    systemDropdownOpen.value = !systemDropdownOpen.value
}

// Check if any Data Master submenu is active
const isDataMasterActive = computed(() => {
    return route().current('guru.*') || 
           route().current('wali-kelas.*') || 
           route().current('siswa.*') || 
           route().current('kelas.*')
})

// Check if any Data Akademik submenu is active
const isDataAkademikActive = computed(() => {
    return route().current('mata-pelajaran.*') || 
           route().current('jadwal-pelajaran.*') || 
           route().current('kkm.*')
})

// Check if any Penilaian submenu is active
const isPenilaianActive = computed(() => {
    return route().current('nilai-siswa.*') || 
           route().current('nilai-saya.*')
})

// Check if any Kehadiran submenu is active
const isKehadiranActive = computed(() => {
    return route().current('absensi.*')
})

// Check if any System submenu is active
const isSystemActive = computed(() => {
    return route().current('approval-requests.*') || 
           route().current('my-approval-requests.*') || 
           route().current('settings.*')
})

// Method untuk mendapatkan foto user
const getUserPhoto = () => {
    const userRole = page.props.auth.user.role?.name
    
    // Jika role murid, gunakan foto siswa
    if (userRole === 'murid' && page.props.auth.user.siswa?.foto) {
        return `/storage/${page.props.auth.user.siswa.foto}`
    }
    
    // Untuk role lain, gunakan foto user
    if (page.props.auth.user.foto) {
        return page.props.auth.user.foto
    }
    
    return null
}

// Method untuk mendapatkan tanggal dan waktu saat ini
const getCurrentDateTime = () => {
    const now = new Date()
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }
    return now.toLocaleDateString('id-ID', options)
}

// Notification computed properties
const unreadNotifications = computed(() => {
    return notifications.value.filter(n => !n.read_at)
})

const recentNotifications = computed(() => {
    // Tampilkan hanya notifikasi yang belum dibaca, maksimal 5
    return unreadNotifications.value.slice(0, 5)
})

const unreadNotificationsCount = computed(() => {
    return unreadNotifications.value.length
})

// Notification methods
const getNotificationIconColor = (type) => {
    const colorMap = {
        'App\\Notifications\\ApprovalRequestReceived': 'bg-yellow-400',
        'App\\Notifications\\ApprovalRequestProcessed': 'bg-green-400',
        default: 'bg-blue-400'
    }
    return colorMap[type] || colorMap.default
}

const getNotificationTitle = (notification) => {
    const data = notification.data
    const typeMap = {
        'App\\Notifications\\ApprovalRequestReceived': 'Permintaan Persetujuan Baru',
        'App\\Notifications\\ApprovalRequestProcessed': `Permintaan ${data.status === 'approved' ? 'Disetujui' : 'Ditolak'}`,
    }
    return typeMap[notification.type] || 'Notifikasi'
}

const getNotificationMessage = (notification) => {
    const data = notification.data
    if (notification.type === 'App\\Notifications\\ApprovalRequestReceived') {
        return `${data.requester_name} mengajukan permintaan perubahan nilai ${data.mata_pelajaran} untuk siswa ${data.siswa_name}.`
    } else if (notification.type === 'App\\Notifications\\ApprovalRequestProcessed') {
        return `Permintaan perubahan nilai ${data.mata_pelajaran} untuk siswa ${data.siswa_name} telah ${data.status === 'approved' ? 'disetujui' : 'ditolak'}.`
    }
    return data.message || 'Tidak ada pesan'
}

const getRelativeTime = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffMinutes = Math.ceil(diffTime / (1000 * 60))
    const diffHours = Math.ceil(diffTime / (1000 * 60 * 60))
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    
    if (diffMinutes < 60) {
        return `${diffMinutes} menit yang lalu`
    } else if (diffHours < 24) {
        return `${diffHours} jam yang lalu`
    } else if (diffDays === 1) {
        return 'Kemarin'
    } else if (diffDays <= 7) {
        return `${diffDays} hari yang lalu`
    } else {
        return date.toLocaleDateString('id-ID')
    }
}

const markAllNotificationsAsRead = async () => {
    try {
        // Menggunakan Inertia untuk request yang lebih aman
        router.patch(route('notifications.mark-all-read'), {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                fetchNotifications()
                // Tidak menampilkan pesan apapun ke user
            },
            onError: (errors) => {
                // Hanya log error di console, tidak tampilkan ke user
                console.error('Error marking all notifications as read:', errors)
            }
        })
    } catch (error) {
        // Hanya log error di console, tidak tampilkan ke user
        console.error('Error marking all notifications as read:', error)
    }
}

// Fungsi untuk menangani klik pada logo notifikasi
const onNotificationButtonClick = async () => {
    // Tidak melakukan apapun, hanya membuka dropdown notifikasi
    // Badge akan tetap tampil sampai notifikasi benar-benar ditandai sebagai dibaca di halaman notifikasi
    // ...existing code...
}

const handleNotificationClick = async (notification) => {
    // Navigate to action if available
    if (notification.data.action_url) {
        router.visit(notification.data.action_url)
    }
}

const fetchNotifications = async () => {
    try {
        // Pastikan user sudah terauthentikasi sebelum fetch
        if (!page.props?.auth?.user) {
            console.log('User not authenticated, skipping notifications fetch');
            return;
        }
        
        // Pastikan route helper tersedia dan environment browser
        if (typeof window === 'undefined' || typeof route === 'undefined') {
            console.log('Not in browser environment or route helper not available');
            return;
        }
        if (typeof route !== 'function') {
            console.error('Route helper not available');
            return;
        }
        
        // Coba menggunakan axios jika tersedia, jika tidak gunakan fetch
        let response
        if (window.axios) {
            try {
                response = await window.axios.get(route('notifications.recent'), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                notifications.value = response.data.notifications || []
            } catch (axiosError) {
                console.error('Axios error fetching notifications:', axiosError);
                
                // Jika error 401/403, mungkin session expired
                if (axiosError.response?.status === 401 || axiosError.response?.status === 403) {
                    console.log('Authentication error, redirecting to login');
                    window.location.href = route('login');
                    return;
                }
                
                // Fallback ke fetch API
                throw axiosError;
            }
        } else {
            response = await fetch(route('notifications.recent'), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin' // Penting untuk session
            })
            
            if (response.status === 401 || response.status === 403) {
                console.log('Authentication error, redirecting to login');
                window.location.href = route('login');
                return;
            }
            
            if (response.ok) {
                const data = await response.json()
                notifications.value = data.notifications || []
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
    } catch (error) {
        console.error('Error fetching notifications:', error)
        
        // Jangan reset notifications jika ini adalah network error sementara
        if (error.name === 'TypeError' && error.message.includes('fetch')) {
            console.log('Network error, keeping existing notifications');
        } else {
            // Reset notifications on other errors
            notifications.value = []
        }
    }
}

// Load notifications on mount
onMounted(() => {
    // Open dropdowns if any submenu is active
    if (isDataMasterActive.value) {
        dataMasterDropdownOpen.value = true
    }
    if (isDataAkademikActive.value) {
        dataAkademikDropdownOpen.value = true
    }
    if (isPenilaianActive.value) {
        penilaianDropdownOpen.value = true
    }
    if (isKehadiranActive.value) {
        kehadiranDropdownOpen.value = true
    }
    if (isSystemActive.value) {
        systemDropdownOpen.value = true
    }
    
    // Delay initial fetch untuk menghindari konflik dengan login redirect
    setTimeout(() => {
        fetchNotifications()
        
        // Poll for new notifications every 30 seconds
        setInterval(fetchNotifications, 30000)
    }, 1000) // Delay 1 detik
    
    // Add keyboard shortcut for search (Ctrl+K)
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault()
            openSearchModal()
        }
    })
})

// Search functionality
const openSearchModal = () => {
    showSearchModal.value = true
    searchQuery.value = ''
    searchResults.value = []
    // Focus input setelah modal terbuka
    setTimeout(() => {
        searchInput.value?.focus()
    }, 100)
}

const closeSearchModal = () => {
    showSearchModal.value = false
    searchQuery.value = ''
    searchResults.value = []
    searchLoading.value = false
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
        searchTimeout.value = null
    }
}

const performSearch = async () => {
    const query = searchQuery.value.trim()
    
    if (!query || query.length < 2) {
        searchResults.value = []
        searchLoading.value = false
        return
    }
    
    // Clear existing timeout
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
    }
    
    // Debounce search
    searchTimeout.value = setTimeout(async () => {
        searchLoading.value = true
        
        try {
            // Check if route exists
            if (typeof route !== 'function') {
                throw new Error('Route helper tidak tersedia')
            }
            
            // Menggunakan GET request dengan query parameter
            const searchUrl = `${route('search.global')}?query=${encodeURIComponent(query)}`
            
            const response = await fetch(searchUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            
            // Check if response is ok
            if (!response.ok) {
                const errorText = await response.text()
                throw new Error(`HTTP ${response.status}: ${errorText}`)
            }
            
            const contentType = response.headers.get('content-type')
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response bukan JSON format')
            }
            
            const data = await response.json()
            
            if (data.success) {
                searchResults.value = data.results || []
            } else {
                console.error('Search API error:', data.message || 'Unknown error')
                searchResults.value = []
            }
        } catch (error) {
            console.error('Search error:', error.message || error)
            searchResults.value = []
            
            // Show user-friendly error message
            if (error.message.includes('Route')) {
                console.error('Route helper error - aplikasi mungkin belum dimuat dengan benar')
            } else if (error.message.includes('HTTP 500')) {
                console.error('Server error - silakan coba lagi')
            } else if (error.message.includes('HTTP 404')) {
                console.error('Search endpoint tidak ditemukan')
            }
        } finally {
            searchLoading.value = false
        }
    }, 300) // Debounce 300ms
}

const selectResult = (result) => {
    if (result.url) {
        router.visit(result.url)
        closeSearchModal()
    }
}

const selectFirstResult = () => {
    if (searchResults.value.length > 0) {
        // Get first result from any group
        const firstGroup = Object.values(groupedResults.value)[0]
        if (firstGroup && firstGroup.length > 0) {
            selectResult(firstGroup[0])
        }
    }
}

// Group search results by type
const groupedResults = computed(() => {
    const grouped = {}
    searchResults.value.forEach(result => {
        if (!grouped[result.type]) {
            grouped[result.type] = []
        }
        grouped[result.type].push(result)
    })
    return grouped
})

const getGroupIcon = (type) => {
    const icons = {
        'siswa': '👨‍🎓',
        'guru': '👨‍🏫',
        'kelas': '🏫',
        'mata_pelajaran': '📚',
        'jadwal': '📅',
        'nilai': '📝',
        'absensi': '📋'
    }
    return icons[type] || '📄'
}

const getGroupTitle = (type) => {
    const titles = {
        'siswa': 'Siswa',
        'guru': 'Guru',
        'kelas': 'Kelas',
        'mata_pelajaran': 'Mata Pelajaran',
        'jadwal': 'Jadwal Pelajaran',
        'nilai': 'Data Nilai',
        'absensi': 'Data Absensi'
    }
    return titles[type] || 'Lainnya'
}

const getResultIcon = (type) => {
    return getGroupIcon(type)
}

const getResultIconClass = (type) => {
    const classes = {
        'siswa': 'bg-blue-100 text-blue-600',
        'guru': 'bg-green-100 text-green-600',
        'kelas': 'bg-purple-100 text-purple-600',
        'mata_pelajaran': 'bg-yellow-100 text-yellow-600',
        'jadwal': 'bg-red-100 text-red-600',
        'nilai': 'bg-indigo-100 text-indigo-600',
        'absensi': 'bg-orange-100 text-orange-600'
    }
    return classes[type] || 'bg-gray-100 text-gray-600'
}

const highlightMatch = (text, query) => {
    if (!text || !query) return text
    
    const regex = new RegExp(`(${query})`, 'gi')
    return text.replace(regex, '<mark class="bg-yellow-200 text-yellow-800 px-1 rounded">$1</mark>')
}

// Logout functionality with loading state
const handleLogout = () => {
    isLoggingOut.value = true
    
    router.post(route('logout'), {}, {
        onStart: () => {
            isLoggingOut.value = true
        },
        onFinish: () => {
            // Keep loading state true until redirect completes
            // isLoggingOut.value = false // Don't reset here to maintain loading state
        },
        onError: () => {
            isLoggingOut.value = false
        }
    })
}
</script>

<style scoped>
/* Custom scrollbar styles */
.scrollbar-thin {
    scrollbar-width: thin;
    scrollbar-color: #d1d5db #f3f4f6;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
    transition: background-color 0.2s ease;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Smooth scrolling */
nav {
    scroll-behavior: smooth;
}

/* Fade effect for top and bottom to indicate more content */
nav::before {
    content: '';
    position: sticky;
    top: 0;
    height: 8px;
    background: linear-gradient(to bottom, white, transparent);
    z-index: 1;
    display: block;
}

nav::after {
    content: '';
    position: sticky;
    bottom: 0;
    height: 8px;
    background: linear-gradient(to top, white, transparent);
    z-index: 1;
    display: block;
}

/* Mobile improvements */
@media (max-width: 640px) {
    .sidebar-user-info {
        padding: 12px 16px;
    }
    
    .sidebar-user-info .text-sm {
        font-size: 12px;
    }
    
    .sidebar-user-info .text-xs {
        font-size: 10px;
    }
}

/* Prevent horizontal scroll on small screens */
@media (max-width: 1024px) {
    .lg\:ml-64 {
        margin-left: 0;
    }
}

/* Dropdown Animation */
.dropdown-slide-enter-active,
.dropdown-slide-leave-active {
    transition: all 0.3s ease;
    overflow: hidden;
}

.dropdown-slide-enter-from {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
}

.dropdown-slide-leave-to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
}

.dropdown-slide-enter-to,
.dropdown-slide-leave-from {
    opacity: 1;
    max-height: 300px;
    transform: translateY(0);
}

/* Top bar animations */
@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

header {
    animation: slideDown 0.3s ease-out;
}

/* Notification badge pulse animation */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Glass morphism effect */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Hover effects */
.hover-scale:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

/* Gradient text effect */
.gradient-text {
    background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Search Modal Styles */
.search-modal-backdrop {
    backdrop-filter: blur(8px);
    background: rgba(0, 0, 0, 0.5);
}

/* Keyboard shortcut styling */
kbd {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 2px 6px;
    font-size: 12px;
    font-family: monospace;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Search result highlight */
mark {
    background-color: #fef3c7;
    color: #92400e;
    padding: 1px 2px;
    border-radius: 2px;
}

/* Search loading animation */
@keyframes search-loading {
    0%, 80%, 100% {
        transform: scale(0);
    }
    40% {
        transform: scale(1);
    }
}

.search-loading-dot {
    animation: search-loading 1.4s infinite ease-in-out both;
}

.search-loading-dot:nth-child(1) { animation-delay: -0.32s; }
.search-loading-dot:nth-child(2) { animation-delay: -0.16s; }
.search-loading-dot:nth-child(3) { animation-delay: 0s; }

/* Tooltip styles */
.tooltip {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.3s;
}

.tooltip.show {
    visibility: visible;
    opacity: 1;
}

/* Search modal enter/leave transitions */
.search-modal-enter-active,
.search-modal-leave-active {
    transition: all 0.3s ease;
}

.search-modal-enter-from {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
}

.search-modal-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
}

/* Logout loading animations */
@keyframes logout-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.8;
    }
}

.logout-loading {
    animation: logout-pulse 2s ease-in-out infinite;
}

/* Spinner fade-in animation */
@keyframes spinner-fade-in {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.spinner-fade-in {
    animation: spinner-fade-in 0.3s ease-out;
}

/* Logout button states */
.logout-btn {
    transition: all 0.2s ease;
}

.logout-btn:disabled {
    transform: none !important;
}

.logout-btn:not(:disabled):hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
}

.logout-btn:not(:disabled):active {
    transform: translateY(0);
}

/* Scrollbar styling untuk sidebar */
.scrollbar-thin {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 6px;
    margin: 4px 0;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 6px;
    border: 1px solid #f1f5f9;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.scrollbar-thin::-webkit-scrollbar-thumb:active {
    background: #64748b;
}

/* User profile section styling */
.user-profile-section {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
    /* Compact header on mobile */
    .mobile-header {
        padding: 0.5rem 0.75rem;
    }
    
    /* Ensure dropdowns don't overflow on mobile */
    .dropdown-content {
        max-width: calc(100vw - 2rem);
        width: auto;
    }
    
    /* Better touch targets on mobile */
    .mobile-touch-target {
        min-height: 44px;
        min-width: 44px;
    }
    
    /* Prevent text overflow in tight spaces */
    .mobile-truncate {
        max-width: 100px;
    }
}

/* Better notification badge positioning */
.notification-badge {
    transform: translate(50%, -50%);
    top: -2px;
    right: -2px;
}

/* Smooth transitions for mobile interactions */
.mobile-transition {
    transition: all 0.2s ease-in-out;
}

/* Fix for flexbox shrinking issues */
.flex-shrink-0 {
    flex-shrink: 0;
}
</style>