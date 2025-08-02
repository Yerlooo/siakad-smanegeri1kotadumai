<template>
  <AppLayout title="Monitoring Absensi Real-Time">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        📊 Monitoring Absensi Real-Time
      </h2>
    </template>

    <div class="py-1 sm:py-2 lg:py-4">
      <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Header dengan Auto Refresh -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-6 mb-4 sm:mb-6">
          <div class="flex flex-col space-y-3 sm:space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
            <div>
              <h3 class="text-base sm:text-lg font-medium text-gray-900">Monitoring Absensi</h3>
              <p class="text-xs sm:text-sm text-gray-600 mt-1">
                Data diperbarui otomatis setiap 30 detik
              </p>
            </div>
            
            <!-- Mobile: Stack vertically -->
            <div class="flex flex-col space-y-2 sm:space-y-3 lg:space-y-0 lg:flex-row lg:items-center lg:space-x-4">
              <!-- Last Updated -->
              <div class="text-xs sm:text-sm text-gray-500 order-3 lg:order-1">
                <div class="flex items-center space-x-1 sm:space-x-2">
                  <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="hidden sm:inline">Update terakhir:</span>
                  <span class="sm:hidden">Update:</span>
                  <span>{{ lastUpdated || 'Memuat...' }} WIB</span>
                </div>
              </div>
              
              <!-- Buttons Container -->
              <div class="flex space-x-2 sm:space-x-3 order-1 lg:order-2">
                <!-- Auto Refresh Toggle -->
                <button 
                  @click="toggleAutoRefresh"
                  :class="autoRefresh ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                  class="flex-1 sm:flex-none inline-flex items-center justify-center px-2 sm:px-3 py-2 border border-transparent text-xs sm:text-sm leading-4 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                >
                  <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" :class="{ 'animate-spin': autoRefresh }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  <span class="hidden sm:inline">{{ autoRefresh ? 'Auto ON' : 'Auto OFF' }}</span>
                  <span class="sm:hidden">{{ autoRefresh ? 'ON' : 'OFF' }}</span>
                </button>
                
                <!-- Manual Refresh -->
                <button 
                  @click="refreshData"
                  :disabled="loading"
                  class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-2 sm:px-4 py-2 rounded-lg flex items-center justify-center space-x-1 sm:space-x-2 transition-colors text-xs sm:text-sm"
                >
                  <svg class="w-3 h-3 sm:w-4 sm:h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  <span>Refresh</span>
                </button>
                
                <!-- Rekap Absensi Button -->
                <Link 
                  :href="route('absensi.rekap')"
                  v-if="canAccess(['guru', 'kepala_tatausaha', 'tata_usaha'])"
                  class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white px-2 sm:px-4 py-2 rounded-lg flex items-center justify-center space-x-1 sm:space-x-2 transition-colors text-xs sm:text-sm"
                >
                  <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                  </svg>
                  <span class="hidden sm:inline">Rekap Absensi</span>
                  <span class="sm:hidden">Rekap</span>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-6 mb-4 sm:mb-6">
          <h3 class="text-sm sm:text-base font-medium text-gray-900 mb-3 sm:mb-4 flex items-center">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filter Monitoring
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <div>
              <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Tanggal</label>
              <input 
                type="date" 
                v-model="filters.tanggal"
                @change="applyFilters"
                class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Kelas</label>
              <select 
                v-model="filters.kelas_id"
                @change="applyFilters"
                class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Semua Kelas</option>
                <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
                  {{ kelas.nama_kelas }}
                </option>
              </select>
            </div>
            <div class="flex items-end sm:col-span-2 lg:col-span-1">
              <button 
                @click="applyFilters"
                class="w-full bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center text-sm"
              >
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
              </button>
            </div>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
              </div>
              <div class="ml-2 sm:ml-4 min-w-0 flex-1">
                <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Mapel</p>
                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ summary.totalMataPelajaran }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="ml-2 sm:ml-4 min-w-0 flex-1">
                <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Sudah Input</p>
                <p class="text-lg sm:text-2xl font-bold text-green-600">{{ summary.sudahInput }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="ml-2 sm:ml-4 min-w-0 flex-1">
                <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Belum Input</p>
                <p class="text-lg sm:text-2xl font-bold text-yellow-600">{{ summary.belumInput }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 col-span-2 lg:col-span-1">
            <div class="flex items-center">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <div class="ml-2 sm:ml-4 min-w-0 flex-1">
                <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Rata-rata Kehadiran</p>
                <p class="text-lg sm:text-2xl font-bold text-purple-600">{{ summary.rataRataKehadiran }}%</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Global Toggle Button -->
        <div class="mb-4 sm:mb-6">
          <button 
            @click="toggleAllDetails"
            class="inline-flex items-center px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105 active:scale-95"
          >
            <svg 
              class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2 transition-transform duration-300" 
              :class="{ 'rotate-180': showAllDetails }"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            {{ showAllDetails ? 'Sembunyikan Semua Detail' : 'Tampilkan Semua Detail' }}
          </button>
        </div>

        <!-- Monitoring Data -->
        <div v-if="monitoringData.length === 0" class="text-center py-8 sm:py-12 bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3 sm:mb-4">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
          </div>
          <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Tidak Ada Data</h3>
          <p class="text-sm sm:text-base text-gray-600 px-4">Tidak ada jadwal mata pelajaran untuk tanggal yang dipilih</p>
        </div>

        <div v-else class="space-y-4 sm:space-y-6">
          <div v-for="data in monitoringData" :key="data.jadwal_id" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-3 sm:px-4 lg:px-6 py-3 sm:py-4 border-b border-gray-200">
              <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between space-y-2 sm:space-y-0">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <h4 class="font-semibold text-base sm:text-lg text-gray-900 mb-1 truncate">{{ data.mata_pelajaran }}</h4>
                    <!-- Toggle Button -->
                    <button 
                      @click="toggleItemExpanded(data.jadwal_id)"
                      class="ml-2 p-1 sm:p-2 rounded-lg hover:bg-white/50 transition-all duration-200 flex-shrink-0 hover:scale-110 active:scale-95"
                    >
                      <svg 
                        class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600 transition-transform duration-300 ease-in-out" 
                        :class="{ 'rotate-180': isExpanded(data.jadwal_id) }"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                      </svg>
                    </button>
                  </div>
                  <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-4 text-xs sm:text-sm text-gray-600">
                    <div class="flex items-center">
                      <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                      <span class="font-medium">{{ data.kelas }}</span>
                    </div>
                    <div class="flex items-center">
                      <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                      <span class="truncate">{{ data.guru }}</span>
                    </div>
                    <div class="flex items-center">
                      <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <span>{{ data.jam }}</span>
                    </div>
                  </div>
                </div>
                <div class="text-left sm:text-right">
                  <div class="text-xs sm:text-sm text-gray-500">Kehadiran</div>
                  <div class="text-xl sm:text-2xl font-bold text-blue-600">{{ data.persentase_kehadiran }}%</div>
                  <div v-if="data.last_input" class="text-xs text-gray-500 mt-1">
                    Input: {{ data.last_input }} WIB
                  </div>
                </div>
              </div>
            </div>

            <!-- Collapsible Content -->
            <div 
              class="dropdown-container"
              :class="{ 'is-expanded': isExpanded(data.jadwal_id) }"
            >
              <div class="dropdown-content">
                <!-- Statistics -->
                <div class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 bg-gray-50 border-b border-gray-200">
                  <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 sm:gap-4">
                    <div class="text-center">
                      <div class="text-base sm:text-xl font-bold text-gray-900">{{ data.statistik.total_siswa }}</div>
                      <div class="text-xs text-gray-500">Total</div>
                    </div>
                    <div class="text-center">
                      <div class="text-base sm:text-xl font-bold text-green-600">{{ data.statistik.hadir }}</div>
                      <div class="text-xs text-gray-500">Hadir</div>
                    </div>
                    <div class="text-center">
                      <div class="text-base sm:text-xl font-bold text-yellow-600">{{ data.statistik.sakit }}</div>
                      <div class="text-xs text-gray-500">Sakit</div>
                    </div>
                    <div class="text-center">
                      <div class="text-base sm:text-xl font-bold text-blue-600">{{ data.statistik.izin }}</div>
                      <div class="text-xs text-gray-500">Izin</div>
                    </div>
                    <div class="text-center">
                      <div class="text-base sm:text-xl font-bold text-red-600">{{ data.statistik.alpha }}</div>
                      <div class="text-xs text-gray-500">Alpha</div>
                    </div>
                  </div>
                </div>

                <!-- Progress Bar -->
                <div class="px-3 sm:px-4 lg:px-6 py-3 bg-white border-b border-gray-200">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-xs sm:text-sm font-medium text-gray-700">Progress Input Absensi</span>
                    <span class="text-xs sm:text-sm text-gray-500">
                      {{ data.statistik.total_siswa - data.statistik.belum_absen }}/{{ data.statistik.total_siswa }}
                    </span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-blue-600 h-2 rounded-full transition-all duration-500 ease-out"
                      :style="{ width: getProgressPercentage(data.statistik) + '%' }"
                    ></div>
                  </div>
                </div>

                <!-- Siswa Belum Absen -->
                <div v-if="data.statistik.belum_absen > 0" class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                  <h5 class="font-medium text-sm sm:text-base text-gray-900 mb-2 sm:mb-3 flex items-center">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Siswa Belum Absen ({{ data.statistik.belum_absen }})
                  </h5>
                  <div class="student-grid">
                    <div 
                      v-for="(siswa, index) in data.siswa_belum_absen" 
                      :key="siswa.id" 
                      class="student-card"
                      :style="{ animationDelay: (index * 0.05) + 's' }"
                    >
                      <div class="w-6 h-6 sm:w-8 sm:h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0">
                        <span class="text-yellow-600 font-medium text-xs sm:text-sm">{{ siswa.nama_lengkap.charAt(0) }}</span>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ siswa.nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">{{ siswa.nis }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Detail Siswa Berdasarkan Status (untuk Kepala Tata Usaha dan Tata Usaha) -->
                <div v-if="data.statistik.total_siswa > data.statistik.belum_absen" class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 bg-gray-50 border-t border-gray-200">
                  <h5 class="font-medium text-sm sm:text-base text-gray-900 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Detail Absensi Siswa
                  </h5>
                  
                  <!-- Tab Navigation -->
                  <div class="flex flex-wrap gap-2 mb-4">
                    <button 
                      v-for="(count, status) in { hadir: data.statistik.hadir, sakit: data.statistik.sakit, izin: data.statistik.izin, alpha: data.statistik.alpha }"
                      :key="status"
                      v-show="count > 0"
                      @click="setActiveTab(data.jadwal_id, status)"
                      :class="[
                        'px-3 py-2 text-xs sm:text-sm font-medium rounded-lg transition-all duration-200',
                        getActiveTab(data.jadwal_id) === status 
                          ? getStatusButtonClass(status, true)
                          : getStatusButtonClass(status, false)
                      ]"
                    >
                      {{ getStatusLabel(status) }} ({{ count }})
                    </button>
                  </div>

                  <!-- Tab Content -->
                  <div class="space-y-2">
                    <div 
                      v-for="(siswaList, status) in data.siswa_by_status"
                      :key="status"
                      v-show="getActiveTab(data.jadwal_id) === status && siswaList.length > 0"
                      class="student-grid"
                    >
                      <div 
                        v-for="(siswa, index) in siswaList" 
                        :key="siswa.id" 
                        class="student-detail-card"
                        :style="{ animationDelay: (index * 0.05) + 's' }"
                      >
                        <div :class="['w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0', getStatusIconClass(status)]">
                          <span :class="['font-medium text-xs sm:text-sm', getStatusTextClass(status)]">{{ siswa.nama_lengkap.charAt(0) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ siswa.nama_lengkap }}</p>
                          <p class="text-xs text-gray-500">{{ siswa.nis }}</p>
                          <div v-if="status === 'hadir' && (siswa.jam_masuk || siswa.jam_keluar)" class="text-xs text-gray-400 mt-1">
                            <span v-if="siswa.jam_masuk">Masuk: {{ siswa.jam_masuk }}</span>
                            <span v-if="siswa.jam_keluar" class="ml-2">Keluar: {{ siswa.jam_keluar }}</span>
                          </div>
                          <div v-if="siswa.keterangan && siswa.keterangan.trim()" class="text-xs text-gray-400 mt-1 italic">
                            "{{ siswa.keterangan }}"
                          </div>
                        </div>
                        <div :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusBadgeClass(status)]">
                          {{ getStatusLabel(status) }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Completed State - tampilkan hanya jika SEMUA siswa sudah absen -->
                <div v-if="data.statistik.belum_absen === 0" class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 bg-green-50 border-l-4 border-green-400">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs sm:text-sm font-medium text-green-800">
                      Absensi sudah lengkap untuk mata pelajaran ini
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Summary when collapsed -->
            <div 
              v-show="!isExpanded(data.jadwal_id)"
              class="summary-section px-3 sm:px-4 lg:px-6 py-2 sm:py-3 bg-gray-50 border-b border-gray-200"
            >
              <div class="flex items-center justify-between text-xs sm:text-sm text-gray-600">
                <span>
                  Progress: {{ data.statistik.total_siswa - data.statistik.belum_absen }}/{{ data.statistik.total_siswa }} siswa
                </span>
                <span v-if="data.statistik.belum_absen > 0" class="text-yellow-600 font-medium">
                  {{ data.statistik.belum_absen }} belum absen
                </span>
                <span v-else class="text-green-600 font-medium">
                  ✓ Lengkap
                </span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                <div 
                  class="bg-blue-600 h-1.5 rounded-full transition-all duration-500 ease-out"
                  :style="{ width: getProgressPercentage(data.statistik) + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  monitoringData: Array,
  kelasList: Array,
  filters: Object
})

const page = usePage()
const loading = ref(false)

// Role access helper
const canAccess = (roles) => {
  const userRole = page.props.auth.user.role?.name
  return roles.includes(userRole)
}
const autoRefresh = ref(true)
const lastUpdated = ref('')
const refreshInterval = ref(null)
const currentData = ref(props.monitoringData || [])
const expandedItems = ref(new Set()) // Track which items are expanded
const showAllDetails = ref(false) // Global toggle for all details
const activeTabs = ref(new Map()) // Track active tab for each jadwal item

const filters = reactive({
  tanggal: props.filters?.tanggal || new Date().toISOString().split('T')[0],
  kelas_id: props.filters?.kelas_id || ''
})

// Computed properties
const monitoringData = computed(() => currentData.value)

const summary = computed(() => {
  const total = monitoringData.value.length
  const sudahInput = monitoringData.value.filter(data => data.statistik.belum_absen === 0).length
  const belumInput = total - sudahInput
  
  let totalKehadiran = 0
  let totalSiswa = 0
  
  monitoringData.value.forEach(data => {
    totalKehadiran += data.statistik.hadir
    totalSiswa += data.statistik.total_siswa
  })
  
  const rataRataKehadiran = totalSiswa > 0 ? Math.round((totalKehadiran / totalSiswa) * 100) : 0
  
  return {
    totalMataPelajaran: total,
    sudahInput,
    belumInput,
    rataRataKehadiran
  }
})

// Methods
const toggleItemExpanded = (jadwalId) => {
  if (expandedItems.value.has(jadwalId)) {
    expandedItems.value.delete(jadwalId)
  } else {
    expandedItems.value.add(jadwalId)
  }
}

const toggleAllDetails = () => {
  showAllDetails.value = !showAllDetails.value
  if (showAllDetails.value) {
    // Expand all items
    monitoringData.value.forEach(data => {
      expandedItems.value.add(data.jadwal_id)
    })
  } else {
    // Collapse all items
    expandedItems.value.clear()
  }
}

const isExpanded = (jadwalId) => {
  return expandedItems.value.has(jadwalId)
}

const refreshData = async () => {
  loading.value = true
  
  try {
    const params = new URLSearchParams({
      tanggal: filters.tanggal,
      ...(filters.kelas_id && { kelas_id: filters.kelas_id })
    })
    
    const response = await fetch(`${route('absensi.monitoring.api')}?${params}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    })
    
    if (response.ok) {
      const result = await response.json()
      currentData.value = result.data
      // Update waktu dengan timezone Indonesia
      const now = new Date()
      lastUpdated.value = now.toLocaleTimeString('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour12: false
      })
    }
  } catch (error) {
    console.error('Error refreshing data:', error)
  } finally {
    loading.value = false
  }
}

const toggleAutoRefresh = () => {
  autoRefresh.value = !autoRefresh.value
  
  if (autoRefresh.value) {
    startAutoRefresh()
  } else {
    stopAutoRefresh()
  }
}

const startAutoRefresh = () => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
  }
  
  refreshInterval.value = setInterval(() => {
    if (autoRefresh.value) {
      refreshData()
    }
  }, 30000) // 30 seconds
}

const stopAutoRefresh = () => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
    refreshInterval.value = null
  }
}

const applyFilters = () => {
  router.get(route('absensi.monitoring'), filters, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      currentData.value = page.props.monitoringData
    }
  })
}

const getProgressPercentage = (statistik) => {
  if (statistik.total_siswa === 0) return 0
  return Math.round(((statistik.total_siswa - statistik.belum_absen) / statistik.total_siswa) * 100)
}

// Methods for handling detail tabs
const setActiveTab = (jadwalId, status) => {
  activeTabs.value.set(jadwalId, status)
}

const getActiveTab = (jadwalId) => {
  if (!activeTabs.value.has(jadwalId)) {
    // Set default active tab to the first status with data
    const defaultStatuses = ['hadir', 'sakit', 'izin', 'alpha']
    const dataItem = monitoringData.value.find(item => item.jadwal_id === jadwalId)
    if (dataItem) {
      for (const status of defaultStatuses) {
        if (dataItem.statistik[status] > 0) {
          activeTabs.value.set(jadwalId, status)
          break
        }
      }
    }
  }
  return activeTabs.value.get(jadwalId) || 'hadir'
}

const getStatusLabel = (status) => {
  const labels = {
    hadir: 'Hadir',
    sakit: 'Sakit', 
    izin: 'Izin',
    alpha: 'Alpha'
  }
  return labels[status] || status
}

const getStatusButtonClass = (status, isActive) => {
  const baseClasses = 'border'
  if (isActive) {
    const activeClasses = {
      hadir: 'bg-green-600 text-white border-green-600',
      sakit: 'bg-yellow-600 text-white border-yellow-600',
      izin: 'bg-blue-600 text-white border-blue-600',
      alpha: 'bg-red-600 text-white border-red-600'
    }
    return `${baseClasses} ${activeClasses[status]}`
  } else {
    const inactiveClasses = {
      hadir: 'bg-white text-green-600 border-green-200 hover:bg-green-50',
      sakit: 'bg-white text-yellow-600 border-yellow-200 hover:bg-yellow-50',
      izin: 'bg-white text-blue-600 border-blue-200 hover:bg-blue-50',
      alpha: 'bg-white text-red-600 border-red-200 hover:bg-red-50'
    }
    return `${baseClasses} ${inactiveClasses[status]}`
  }
}

const getStatusIconClass = (status) => {
  const classes = {
    hadir: 'bg-green-100',
    sakit: 'bg-yellow-100',
    izin: 'bg-blue-100',
    alpha: 'bg-red-100'
  }
  return classes[status] || 'bg-gray-100'
}

const getStatusTextClass = (status) => {
  const classes = {
    hadir: 'text-green-600',
    sakit: 'text-yellow-600',
    izin: 'text-blue-600',
    alpha: 'text-red-600'
  }
  return classes[status] || 'text-gray-600'
}

const getStatusBadgeClass = (status) => {
  const classes = {
    hadir: 'bg-green-100 text-green-800',
    sakit: 'bg-yellow-100 text-yellow-800',
    izin: 'bg-blue-100 text-blue-800',
    alpha: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Lifecycle
onMounted(() => {
  // Set initial last updated time dengan timezone Indonesia
  const now = new Date()
  lastUpdated.value = now.toLocaleTimeString('id-ID', {
    timeZone: 'Asia/Jakarta',
    hour12: false
  })
  
  // Start auto refresh if enabled
  if (autoRefresh.value) {
    startAutoRefresh()
  }
})

onBeforeUnmount(() => {
  stopAutoRefresh()
})
</script>

<style scoped>
/* Smooth dropdown container with height-based animation */
.dropdown-container {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1),
              opacity 0.3s ease-in-out;
  opacity: 0;
}

.dropdown-container.is-expanded {
  max-height: 2000px; /* Large enough to accommodate content */
  opacity: 1;
}

.dropdown-content {
  /* Prevent layout shifting during animation */
  will-change: transform;
  backface-visibility: hidden;
}

/* Smooth summary section transition */
.summary-section {
  transition: opacity 0.2s ease-in-out,
              transform 0.2s ease-in-out;
  transform: translateY(0);
  opacity: 1;
}

/* Student grid with staggered animation */
.student-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 0.5rem;
}

@media (min-width: 640px) {
  .student-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
  }
}

@media (min-width: 1024px) {
  .student-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
  }
}

.student-card {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  background-color: #fefce8;
  border: 1px solid #fde047;
  border-radius: 0.5rem;
  animation: slideInFromBottom 0.3s ease-out forwards;
  opacity: 0;
  transform: translateY(20px);
}

.student-detail-card {
  display: flex;
  align-items: center;
  padding: 0.75rem;
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  animation: slideInFromBottom 0.3s ease-out forwards;
  opacity: 0;
  transform: translateY(20px);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease-in-out;
}

.student-detail-card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

@media (min-width: 640px) {
  .student-card {
    padding: 0.75rem;
  }
  
  .student-detail-card {
    padding: 1rem;
  }
}

@keyframes slideInFromBottom {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Arrow rotation with smooth transition */
.rotate-180 {
  transform: rotate(180deg);
}

/* Enhanced button animations */
button {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform, box-shadow;
}

button:hover {
  transform: translateY(-1px) scale(1.02);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

button:active {
  transform: translateY(0) scale(0.98);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Progress bar with smooth animation */
.bg-blue-600 {
  transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Card hover effects with better performance */
.bg-white.rounded-lg.shadow-sm {
  transition: transform 0.2s ease-out,
              box-shadow 0.2s ease-out;
  will-change: transform, box-shadow;
}

.bg-white.rounded-lg.shadow-sm:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Global toggle button enhancement */
.inline-flex.items-center {
  position: relative;
  overflow: hidden;
}

.inline-flex.items-center::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  transition: left 0.5s;
}

.inline-flex.items-center:hover::before {
  left: 100%;
}

/* Reduce motion for users who prefer it */
@media (prefers-reduced-motion: reduce) {
  .dropdown-container {
    transition: opacity 0.2s ease-in-out;
  }
  
  .student-card {
    animation: none;
    opacity: 1;
    transform: none;
  }
  
  button:hover {
    transform: none;
    box-shadow: none;
  }
  
  .bg-white.rounded-lg.shadow-sm:hover {
    transform: none;
  }
}

/* Mobile optimizations */
@media (max-width: 640px) {
  .dropdown-container.is-expanded {
    max-height: 1500px; /* Smaller max-height for mobile */
  }
  
  .student-card {
    padding: 0.5rem;
    animation-duration: 0.2s;
  }
  
  button:hover {
    transform: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }
  
  .bg-white.rounded-lg.shadow-sm:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }
}

/* Loading states */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

/* Spinner animation */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Focus states for accessibility */
button:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

button:focus:not(:focus-visible) {
  outline: none;
}

/* Smooth scrolling for better UX */
html {
  scroll-behavior: smooth;
}

/* Prevent layout shift during animations */
* {
  box-sizing: border-box;
}

/* Ensure smooth performance */
.dropdown-container,
.student-card,
button {
  contain: layout style paint;
}
</style>
