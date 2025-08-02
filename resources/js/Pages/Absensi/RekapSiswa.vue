<template>
  <Head title="SIAKAD SMANSA" />
  <AppLayout title="Rekap Absensi Siswa">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        📊 Rekap Absensi Siswa
      </h2>
    </template>

    <div class="py-6 sm:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-lg">
          <div class="p-4 sm:p-6 text-gray-900">
            
            <!-- Navigation -->
            <div class="mb-6 flex flex-col space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
              <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
                <Link 
                  :href="route('absensi.index')"
                  class="inline-flex items-center justify-center bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 active:bg-gray-800 transition-colors duration-200 font-medium"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                  </svg>
                  <span class="hidden sm:inline">Kembali ke </span>Absensi
                </Link>
                <Link 
                  v-if="isKepalaSekolah"
                  :href="route('absensi.laporan')"
                  class="inline-flex items-center justify-center bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 active:bg-purple-800 transition-colors duration-200 font-medium"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  Laporan Absensi
                </Link>
              </div>
              
              <!-- Export Buttons -->
              <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
                <button 
                  @click="exportExcel"
                  :disabled="exporting"
                  class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 active:bg-green-800 transition-colors duration-200 flex items-center justify-center disabled:opacity-50 font-medium"
                >
                  <svg v-if="exporting === 'excel'" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <span class="hidden sm:inline">{{ exporting === 'excel' ? 'Mengexport...' : 'Export ' }}</span>Excel
                </button>
                
                <button 
                  @click="exportPdf"
                  :disabled="exporting"
                  class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 active:bg-red-800 transition-colors duration-200 flex items-center justify-center disabled:opacity-50 font-medium"
                >
                  <svg v-if="exporting === 'pdf'" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                  <span class="hidden sm:inline">{{ exporting === 'pdf' ? 'Mengexport...' : 'Export ' }}</span>PDF
                </button>
              </div>
            </div>

            <!-- Filter Section -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
              <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter Data
              </h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Kelas
                  </label>
                  <select 
                    v-model="filters.kelas_id"
                    @change="applyFilters"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  >
                    <option value="">Semua Kelas</option>
                    <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
                      {{ kelas.nama_kelas }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Mata Pelajaran
                  </label>
                  <select 
                    v-model="filters.mata_pelajaran_id"
                    @change="applyFilters"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  >
                    <option value="">Semua Mata Pelajaran</option>
                    <option v-for="mapel in mataPelajaranList" :key="mapel.id" :value="mapel.id">
                      {{ mapel.nama_mapel }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Bulan
                  </label>
                  <select 
                    v-model="filters.bulan"
                    @change="applyFilters"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  >
                    <option v-for="(nama, value) in bulanOptions" :key="value" :value="value">
                      {{ nama }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Tahun
                  </label>
                  <select 
                    v-model="filters.tahun"
                    @change="applyFilters"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  >
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                  </select>
                </div>
                <div class="flex items-end">
                  <button 
                    @click="applyFilters"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors duration-200 flex items-center justify-center font-medium"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span class="hidden sm:inline">Filter</span>
                    <span class="sm:hidden">Cari</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Summary Cards -->
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="bg-gradient-to-r from-green-100 to-green-200 p-4 rounded-lg border border-green-300">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                    </svg>
                  </div>
                  <div class="ml-3">
                    <div class="text-green-800 text-sm font-medium">Total Siswa</div>
                    <div class="text-green-900 text-xl sm:text-2xl font-bold">{{ rekapData.length }}</div>
                  </div>
                </div>
              </div>
              
              <div class="bg-gradient-to-r from-blue-100 to-blue-200 p-4 rounded-lg border border-blue-300">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <div class="ml-3">
                    <div class="text-blue-800 text-sm font-medium">Rata-rata Kehadiran</div>
                    <div class="text-blue-900 text-xl sm:text-2xl font-bold">{{ averageAttendance }}%</div>
                  </div>
                </div>
              </div>
              
              <div class="bg-gradient-to-r from-yellow-100 to-yellow-200 p-4 rounded-lg border border-yellow-300">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <div class="ml-3">
                    <div class="text-yellow-800 text-sm font-medium">Periode</div>
                    <div class="text-yellow-900 text-base sm:text-lg font-bold">{{ namaBulan }} {{ tahun }}</div>
                  </div>
                </div>
              </div>
              
              <div class="bg-gradient-to-r from-purple-100 to-purple-200 p-4 rounded-lg border border-purple-300">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                      <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <div class="ml-3">
                    <div class="text-purple-800 text-sm font-medium">Total Absensi</div>
                    <div class="text-purple-900 text-xl sm:text-2xl font-bold">{{ totalAbsensi }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Rekap Table -->
            <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-lg">
              <!-- Desktop Table -->
              <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sakit</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Izin</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Alpha</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">% Hadir</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(data, index) in rekapData" :key="data.siswa.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ data.siswa.nama_lengkap }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ data.siswa.kelas?.nama_kelas || 'N/A' }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ data.siswa.nisn }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          {{ data.statistik.hadir }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                          {{ data.statistik.sakit }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          {{ data.statistik.izin }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                          {{ data.statistik.alpha }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">{{ data.statistik.total }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getPercentageClass(data.statistik.persentase_hadir)">
                          {{ data.statistik.persentase_hadir }}%
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <button @click="showDetail(data)" class="text-indigo-600 hover:text-indigo-900">Detail</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Mobile Card View -->
              <div class="lg:hidden">
                <!-- Info header untuk mobile -->
                <div class="p-3 bg-gray-50 border-b border-gray-200">
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">{{ rekapData.length }} siswa</span>
                    <span class="text-gray-500">Periode: {{ namaBulan }} {{ tahun }}</span>
                  </div>
                </div>

                <!-- Empty state -->
                <div v-if="rekapData.length === 0" class="text-center py-12">
                  <div class="text-6xl mb-4">📊</div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data</h3>
                  <p class="text-gray-500">Tidak ada data rekap absensi untuk filter yang dipilih</p>
                </div>

                <!-- Mobile Cards -->
                <div v-for="(data, index) in rekapData" :key="data.siswa.id" 
                     class="border-b border-gray-200 p-4 hover:bg-gray-50 transition-colors last:border-b-0">
                  
                  <!-- Student Header -->
                  <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                      <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-sm">{{ index + 1 }}</span>
                      </div>
                      <div class="min-w-0 flex-1">
                        <h5 class="font-medium text-gray-900 truncate">{{ data.siswa.nama_lengkap }}</h5>
                        <div class="flex items-center space-x-3 text-sm text-gray-500">
                          <span>{{ data.siswa.kelas?.nama_kelas || 'N/A' }}</span>
                          <span>•</span>
                          <span>{{ data.siswa.nisn }}</span>
                        </div>
                      </div>
                    </div>
                    <!-- Percentage Badge -->
                    <div :class="getPercentageClass(data.statistik.persentase_hadir)" 
                         class="px-3 py-1 rounded-full text-xs font-medium">
                      {{ data.statistik.persentase_hadir }}%
                    </div>
                  </div>

                  <!-- Statistics Grid -->
                  <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                      <div class="text-green-600 text-xs font-medium mb-1">Hadir</div>
                      <div class="text-green-900 text-lg font-bold">{{ data.statistik.hadir }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3 text-center">
                      <div class="text-yellow-600 text-xs font-medium mb-1">Sakit</div>
                      <div class="text-yellow-900 text-lg font-bold">{{ data.statistik.sakit }}</div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                      <div class="text-blue-600 text-xs font-medium mb-1">Izin</div>
                      <div class="text-blue-900 text-lg font-bold">{{ data.statistik.izin }}</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-3 text-center">
                      <div class="text-red-600 text-xs font-medium mb-1">Alpha</div>
                      <div class="text-red-900 text-lg font-bold">{{ data.statistik.alpha }}</div>
                    </div>
                  </div>

                  <!-- Total and Actions -->
                  <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                      Total: <span class="font-medium text-gray-900">{{ data.statistik.total }}</span> absensi
                    </div>
                    <button @click="showDetail(data)" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors">
                      Lihat Detail
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Detail Modal -->
            <div v-if="showDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
              <div class="relative top-4 mx-auto p-0 border w-full max-w-6xl sm:max-w-4xl md:max-w-5xl shadow-lg rounded-md bg-white sm:top-10 sm:p-5">
                <div class="sm:mt-3">
                  <!-- Modal Header -->
                  <div class="flex justify-between items-center p-4 sm:p-0 sm:mb-4 bg-white border-b sm:border-b-0">
                    <div>
                      <h3 class="text-lg font-medium text-gray-900">
                        Detail Absensi
                      </h3>
                      <p class="text-sm text-gray-500 mt-1">
                        {{ selectedSiswa?.nama_lengkap }} - {{ selectedSiswa?.kelas?.nama_kelas }}
                      </p>
                    </div>
                    <button @click="closeDetail" 
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  
                  <!-- Content with max height and scroll -->
                  <div class="max-h-[calc(100vh-120px)] sm:max-h-96 overflow-y-auto p-4 sm:p-0">
                    <!-- Desktop Table -->
                    <div class="hidden md:block">
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                          </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                          <tr v-for="absensi in selectedAbsensi" :key="absensi.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                              {{ formatTanggal(absensi.tanggal) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                              {{ absensi.mata_pelajaran?.nama_mapel || 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="getStatusClass(absensi.status)">
                                {{ formatStatus(absensi.status) }}
                              </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                              {{ formatWaktu(absensi.jam_masuk) }} - {{ formatWaktu(absensi.jam_keluar) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                              {{ absensi.keterangan || '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                              <button @click="confirmDelete(absensi)"
                                      class="text-red-600 hover:text-red-900 transition duration-200 p-1 hover:bg-red-50 rounded"
                                      title="Hapus data absensi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-3">
                      <div v-for="absensi in selectedAbsensi" :key="absensi.id" 
                           class="bg-white border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        
                        <!-- Card Header -->
                        <div class="flex items-start justify-between mb-3">
                          <div class="min-w-0 flex-1">
                            <h6 class="font-medium text-gray-900 text-sm truncate">
                              {{ absensi.mata_pelajaran?.nama_mapel || 'N/A' }}
                            </h6>
                            <p class="text-xs text-gray-500 mt-1">
                              {{ formatTanggal(absensi.tanggal) }}
                            </p>
                          </div>
                          <div class="flex items-center space-x-2 ml-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :class="getStatusClass(absensi.status)">
                              {{ formatStatus(absensi.status) }}
                            </span>
                            <button @click="confirmDelete(absensi)"
                                    class="text-red-600 hover:text-red-900 transition-colors p-1 hover:bg-red-50 rounded">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                              </svg>
                            </button>
                          </div>
                        </div>

                        <!-- Card Details -->
                        <div class="space-y-2 text-sm">
                          <div class="flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ formatWaktu(absensi.jam_masuk) }} - {{ formatWaktu(absensi.jam_keluar) }}
                          </div>
                          <div v-if="absensi.keterangan" class="flex items-start text-gray-600">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            <span class="text-xs">{{ absensi.keterangan }}</span>
                          </div>
                        </div>
                      </div>

                      <!-- Empty state for mobile -->
                      <div v-if="selectedAbsensi.length === 0" class="text-center py-8">
                        <div class="text-4xl mb-2">📋</div>
                        <p class="text-gray-500 text-sm">Tidak ada detail absensi</p>
                      </div>
                    </div>
                  </div>

                  <!-- Mobile Footer (outside scroll area) -->
                  <div class="p-4 bg-gray-50 sm:hidden">
                    <button @click="closeDetail" 
                            class="w-full py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors">
                      Tutup
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Confirm Delete Modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
              <div class="relative mx-auto border w-full max-w-md shadow-lg rounded-lg bg-white">
                <!-- Modal Header -->
                <div class="p-6 pb-4">
                  <!-- Warning Icon -->
                  <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                  </div>
                  
                  <div class="text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                      Konfirmasi Hapus
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">
                      Apakah Anda yakin ingin menghapus data absensi ini? Tindakan ini tidak dapat dibatalkan.
                    </p>
                  </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0">
                  <button @click="cancelDelete"
                          class="w-full sm:w-auto px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Batal
                  </button>
                  <button @click="deleteAbsensi"
                          :disabled="deleting"
                          class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                    <svg v-if="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ deleting ? 'Menghapus...' : 'Hapus' }}
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  rekapData: Array,
  kelasList: Array, // Sudah terfilter dari backend untuk guru
  mataPelajaranList: Array, // Sudah terfilter dari backend untuk guru
  selectedKelas: String,
  selectedMataPelajaran: String,
  bulan: Number,
  tahun: Number,
  namaBulan: String,
  isKepalaSekolah: Boolean
})

const showDetailModal = ref(false)
const selectedSiswa = ref(null)
const selectedAbsensi = ref([])

// State untuk delete modal
const showDeleteModal = ref(false)
const selectedAbsensiToDelete = ref(null)
const deleting = ref(false)

// State untuk export
const exporting = ref(false)

const filters = reactive({
  kelas_id: props.selectedKelas || '',
  mata_pelajaran_id: props.selectedMataPelajaran || '',
  bulan: Number(props.bulan),
  tahun: Number(props.tahun)
})

const bulanOptions = {
  1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April',
  5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus',
  9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
}

const averageAttendance = computed(() => {
  if (props.rekapData.length === 0) return 0
  const total = props.rekapData.reduce((sum, data) => sum + data.statistik.persentase_hadir, 0)
  return Math.round(total / props.rekapData.length)
})

const totalAbsensi = computed(() => {
  return props.rekapData.reduce((sum, data) => sum + data.statistik.total, 0)
})

const applyFilters = () => {
  // Pastikan bulan dan tahun bertipe number sebelum dikirim
  filters.bulan = Number(filters.bulan)
  filters.tahun = Number(filters.tahun)
  router.get(route('absensi.rekap-siswa'), filters, {
    preserveState: true,
    preserveScroll: true
  })
}

const getPercentageClass = (percentage) => {
  if (percentage >= 90) return 'bg-green-100 text-green-800'
  if (percentage >= 75) return 'bg-yellow-100 text-yellow-800'
  if (percentage >= 60) return 'bg-orange-100 text-orange-800'
  return 'bg-red-100 text-red-800'
}

const getStatusClass = (status) => {
  const classes = {
    'hadir': 'bg-green-100 text-green-800',
    'sakit': 'bg-yellow-100 text-yellow-800',
    'izin': 'bg-blue-100 text-blue-800',
    'alpha': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status) => {
  const statusMap = {
    'hadir': 'Hadir',
    'sakit': 'Sakit',
    'izin': 'Izin',
    'alpha': 'Alpha'
  }
  return statusMap[status] || status
}

const showDetail = (data) => {
  selectedSiswa.value = data.siswa
  selectedAbsensi.value = data.absensi
  showDetailModal.value = true
}

const closeDetail = () => {
  showDetailModal.value = false
  selectedSiswa.value = null
  selectedAbsensi.value = []
}

const formatTanggal = (tanggal) => {
  return new Date(tanggal).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatWaktu = (waktu) => {
  if (!waktu) return '-'
  try {
    return new Date(waktu).toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    })
  } catch (e) {
    return '-'
  }
}

const confirmDelete = (absensi) => {
  selectedAbsensiToDelete.value = absensi
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  selectedAbsensiToDelete.value = null
}

const deleteAbsensi = () => {
  if (!selectedAbsensiToDelete.value) return
  
  deleting.value = true
  const absensiToDelete = selectedAbsensiToDelete.value
  
  router.delete(route('absensi.destroy', absensiToDelete.id), {
    onSuccess: () => {
      deleting.value = false
      showDeleteModal.value = false
      selectedAbsensiToDelete.value = null
      
      // Refresh data setelah hapus
      applyFilters()
      
      // Juga refresh detail modal jika masih terbuka
      if (showDetailModal.value && selectedSiswa.value) {
        // Filter out deleted record from selectedAbsensi
        selectedAbsensi.value = selectedAbsensi.value.filter(
          item => item.id !== absensiToDelete.id
        )
      }
    },
    onError: (errors) => {
      deleting.value = false
      // Tampilkan error message (bisa ditambahkan modal error jika perlu)
      alert('Terjadi kesalahan saat menghapus data absensi.')
    }
  })
}

const exportExcel = () => {
  exporting.value = 'excel'
  
  // Buat URL dengan query parameters yang sama dengan filter current
  const queryParams = new URLSearchParams({
    ...filters,
    format: 'excel'
  }).toString()
  
  // Buat temporary link untuk download
  const downloadUrl = `${route('absensi.rekap-siswa')}?${queryParams}`
  
  // Download file
  window.open(downloadUrl, '_blank')
  
  // Reset state setelah delay
  setTimeout(() => {
    exporting.value = false
  }, 2000)
}

const exportPdf = () => {
  exporting.value = 'pdf'
  
  // Buat URL dengan query parameters yang sama dengan filter current
  const queryParams = new URLSearchParams({
    ...filters,
    format: 'pdf'
  }).toString()
  
  // Buat temporary link untuk download
  const downloadUrl = `${route('absensi.rekap-siswa')}?${queryParams}`
  
  // Download file
  window.open(downloadUrl, '_blank')
  
  // Reset state setelah delay
  setTimeout(() => {
    exporting.value = false
  }, 2000)
}

// Check for absensi updates dan refresh data jika perlu
onMounted(() => {
  const lastUpdate = localStorage.getItem('absensi_updated')
  const pageLoadTime = sessionStorage.getItem('rekap_page_load_time')
  
  // Jika ada update absensi setelah halaman ini di-load, refresh data
  if (lastUpdate && (!pageLoadTime || parseInt(lastUpdate) > parseInt(pageLoadTime))) {
    applyFilters()
    // Clear flag setelah refresh
    localStorage.removeItem('absensi_updated')
  }
  
  // Set waktu load halaman ini
  sessionStorage.setItem('rekap_page_load_time', Date.now().toString())
})
</script>
