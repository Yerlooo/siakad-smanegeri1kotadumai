<template>
  <AppLayout title="Absensi Siswa">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        📋 Absensi Siswa
      </h2>
    </template>

    <div class="py-6 sm:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-lg">
          <div class="p-4 sm:p-6 text-gray-900">
            
            <!-- Filter Section -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
              <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter Data
              </h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Tanggal
                  </label>
                  <input 
                    type="date" 
                    v-model="filters.tanggal"
                    @change="applyFilters"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  >
                </div>
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

            <!-- Success/Error Modal Pop-up -->
            <div v-if="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center px-4">
              <div class="relative mx-auto p-6 border border-gray-200 w-full max-w-md shadow-2xl rounded-xl bg-white">
                <div class="text-center">
                  <!-- Success Icon -->
                  <div v-if="modalType === 'success'" class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                  
                  <!-- Error Icon -->
                  <div v-if="modalType === 'error'" class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </div>
                  
                  <h3 class="text-lg leading-6 font-semibold text-gray-900 mb-3">
                    {{ modalType === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan!' }}
                  </h3>
                  <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                    {{ modalMessage }}
                  </p>
                  <div class="flex justify-center">
                    <button 
                      @click="closeModal"
                      :class="modalType === 'success' ? 'bg-green-500 hover:bg-green-600 focus:ring-green-500' : 'bg-red-500 hover:bg-red-600 focus:ring-red-500'"
                      class="px-6 py-3 text-white text-base font-medium rounded-lg w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200"
                    >
                      OK
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Navigation -->
            <div class="mb-6 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
              <Link 
                :href="route('absensi.rekap-siswa')"
                class="inline-flex items-center justify-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 active:bg-green-800 transition-colors duration-200 font-medium"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Rekap Siswa
              </Link>
              <Link 
                v-if="isTataUsaha || isKepalaSekolah"
                :href="route('absensi.monitoring')"
                class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors duration-200 font-medium"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Monitoring Real-Time
              </Link>
              <Link 
                v-if="isKepalaSekolah"
                :href="route('absensi.laporan')"
                class="inline-flex items-center justify-center bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 active:bg-purple-800 transition-colors duration-200 font-medium"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Laporan Absensi
              </Link>
            </div>

            <!-- Absensi Data -->
            <div v-if="!formData || formData.length === 0" class="text-center py-12">
              <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Data Absensi</h3>
              <div class="text-gray-600 text-base">
                Tidak ada data untuk tanggal <span class="font-medium">{{ formatTanggal(filters.tanggal) }}</span>
              </div>
              <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">
                Silakan pilih tanggal, kelas, dan mata pelajaran yang sesuai untuk melihat data absensi
              </p>
            </div>

            <div v-else class="space-y-6">
              <div v-for="(data, index) in formData" :key="index" class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-4 border-b border-gray-200">
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <h4 class="font-semibold text-lg text-gray-900 mb-1">
                        {{ data.jadwal?.mata_pelajaran?.nama_mapel || 'Mata Pelajaran' }}
                      </h4>
                      <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="font-medium">{{ data.jadwal?.kelas?.nama_kelas || 'Kelas' }}</span>
                      </div>
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-600">
                        <div class="flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                          </svg>
                          <span>{{ data.jadwal.guru?.name || data.jadwal.guru?.nama_guru || 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          <span>{{ data.jadwal.hari || 'N/A' }} {{ data.jadwal.jam_mulai || 'N/A' }} - {{ data.jadwal.jam_selesai || 'N/A' }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-xs text-gray-500">Total Siswa</div>
                      <div class="text-2xl font-bold text-blue-600">{{ data.siswa.length }}</div>
                    </div>
                  </div>
                </div>

                <form @submit.prevent="submitAbsensi(data)">
                  <!-- Desktop Table View -->
                  <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Keluar</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(siswaData, siswaIndex) in data.siswa" :key="`${siswaData.siswa.id}-${siswaData.statusForm}`" 
                            :class="getRowClass(siswaData.statusForm)">
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ siswaIndex + 1 }}</td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ siswaData.siswa.nama_lengkap }}</div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ siswaData.siswa.nisn }}</td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <select 
                              v-model="siswaData.statusForm"
                              @change="handleStatusChange(siswaData)"
                              :key="`status-${siswaData.siswa.id}-${siswaData.statusForm}`"
                              class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                              <option v-for="(label, value) in statusOptions" :key="value" :value="value">{{ label }}</option>
                            </select>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <input 
                              type="time" 
                              v-model="siswaData.jamMasukForm"
                              :disabled="siswaData.statusForm !== 'hadir'"
                              :key="`jam-masuk-${siswaData.siswa.id}`"
                              class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                            >
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <input 
                              type="time" 
                              v-model="siswaData.jamKeluarForm"
                              :disabled="siswaData.statusForm !== 'hadir'"
                              :key="`jam-keluar-${siswaData.siswa.id}`"
                              class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                            >
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <input 
                              type="text" 
                              v-model="siswaData.keteranganForm"
                              placeholder="Keterangan..."
                              class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full"
                            >
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- Mobile Card View -->
                  <div class="lg:hidden">
                    <div v-for="(siswaData, siswaIndex) in data.siswa" :key="`mobile-${siswaData.siswa.id}-${siswaData.statusForm}`" 
                         :class="getRowClass(siswaData.statusForm)"
                         class="border-b border-gray-200 p-4 last:border-b-0">
                      
                      <!-- Student Header -->
                      <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                          <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-sm">{{ siswaIndex + 1 }}</span>
                          </div>
                          <div>
                            <h5 class="font-medium text-gray-900">{{ siswaData.siswa.nama_lengkap }}</h5>
                            <p class="text-sm text-gray-500">NISN: {{ siswaData.siswa.nisn }}</p>
                          </div>
                        </div>
                        <!-- Status Badge -->
                        <div :class="getStatusBadgeClass(siswaData.statusForm)" 
                             class="px-3 py-1 rounded-full text-xs font-medium">
                          {{ statusOptions[siswaData.statusForm] }}
                        </div>
                      </div>

                      <!-- Form Fields -->
                      <div class="space-y-4">
                        <!-- Status Selection -->
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-2">Status Kehadiran</label>
                          <select 
                            v-model="siswaData.statusForm"
                            @change="handleStatusChange(siswaData)"
                            :key="`status-mobile-${siswaData.siswa.id}-${siswaData.statusForm}`"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          >
                            <option v-for="(label, value) in statusOptions" :key="value" :value="value">{{ label }}</option>
                          </select>
                        </div>

                        <!-- Time Fields (only visible when present) -->
                        <div v-if="siswaData.statusForm === 'hadir'" class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                              </svg>
                              Jam Masuk
                            </label>
                            <input 
                              type="time" 
                              v-model="siswaData.jamMasukForm"
                              :key="`jam-masuk-mobile-${siswaData.siswa.id}`"
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l4 4m0 0l-4 4m4-4H3"/>
                              </svg>
                              Jam Keluar
                            </label>
                            <input 
                              type="time" 
                              v-model="siswaData.jamKeluarForm"
                              :key="`jam-keluar-mobile-${siswaData.siswa.id}`"
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                          </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1.586z"/>
                            </svg>
                            Keterangan
                          </label>
                          <input 
                            type="text" 
                            v-model="siswaData.keteranganForm"
                            placeholder="Tambahkan keterangan jika diperlukan..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          >
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Footer dengan tombol submit -->
                  <div class="bg-gray-50 px-4 py-4 border-t border-gray-200">
                    <div class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                      <div class="text-sm text-gray-600 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Total Siswa: <span class="font-medium">{{ data.siswa.length }}</span>
                      </div>
                      <button 
                        type="submit" 
                        :disabled="processing"
                        class="w-full sm:w-auto bg-blue-600 text-white px-6 py-3 sm:py-2 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center font-medium"
                      >
                        <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        {{ processing ? 'Menyimpan...' : 'Simpan Absensi' }}
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  absensiData: Array,
  tanggal: String,
  kelasList: Array,
  mataPelajaranList: Array,
  selectedKelas: String,
  selectedMataPelajaran: String,
  statusOptions: Object,
  isKepalaSekolah: Boolean,
  isTataUsaha: Boolean
})

const processing = ref(false)
const showModal = ref(false)
const modalType = ref('') // 'success' or 'error'
const modalMessage = ref('')
const formData = ref([]) // Local reactive copy of absensiData

const filters = reactive({
  tanggal: props.tanggal,
  kelas_id: props.selectedKelas || '',
  mata_pelajaran_id: props.selectedMataPelajaran || ''
})

// Initialize form data
onMounted(() => {
  initializeFormData()
  
  // Add keyboard listener for modal
  document.addEventListener('keydown', handleKeydown)
})

// Cleanup event listener
onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeydown)
})

const handleKeydown = (e) => {
  if (e.key === 'Escape' && showModal.value) {
    closeModal()
  }
}

const initializeFormData = () => {
  if (!props.absensiData || props.absensiData.length === 0) {
    formData.value = []
    return
  }
  
  // Create a deep reactive copy of absensiData
  formData.value = JSON.parse(JSON.stringify(props.absensiData)).map(data => {
    if (data.siswa && Array.isArray(data.siswa)) {
      data.siswa = data.siswa.map(siswaData => {
        // Pastikan siswaData.siswa exists
        if (!siswaData.siswa) {
          return siswaData
        }

        // Set default values atau dari data yang sudah ada
        if (siswaData.absensi) {
          siswaData.statusForm = siswaData.absensi.status || 'alpha'
          
          // Handle jam masuk dengan lebih aman
          if (siswaData.absensi.jam_masuk) {
            try {
              const jamMasuk = new Date(siswaData.absensi.jam_masuk)
              siswaData.jamMasukForm = jamMasuk.toTimeString().slice(0, 5)
            } catch (e) {
              siswaData.jamMasukForm = ''
            }
          } else {
            siswaData.jamMasukForm = ''
          }
          
          // Handle jam keluar dengan lebih aman
          if (siswaData.absensi.jam_keluar) {
            try {
              const jamKeluar = new Date(siswaData.absensi.jam_keluar)
              siswaData.jamKeluarForm = jamKeluar.toTimeString().slice(0, 5)
            } catch (e) {
              siswaData.jamKeluarForm = ''
            }
          } else {
            siswaData.jamKeluarForm = ''
          }
          
          siswaData.keteranganForm = siswaData.absensi.keterangan || ''
        } else {
          // Set default values jika belum ada data absensi
          siswaData.statusForm = 'alpha'
          siswaData.jamMasukForm = ''
          siswaData.jamKeluarForm = ''
          siswaData.keteranganForm = ''
        }
        
        return siswaData
      })
    }
    return data
  })
}

watch(() => props.absensiData, (newData) => {
  initializeFormData()
}, { deep: true, immediate: true })

const applyFilters = () => {
  // Pastikan value bertipe string sesuai kebutuhan backend
  filters.kelas_id = filters.kelas_id ? String(filters.kelas_id) : ''
  filters.mata_pelajaran_id = filters.mata_pelajaran_id ? String(filters.mata_pelajaran_id) : ''
  router.get(route('absensi.index'), filters, {
    preserveState: true,
    preserveScroll: true
  })
}

const handleStatusChange = async (siswaData) => {
  // Clear jam masuk dan keluar jika status bukan hadir
  if (siswaData.statusForm !== 'hadir') {
    siswaData.jamMasukForm = ''
    siswaData.jamKeluarForm = ''
  } else {
    // Set default time jika hadir dan belum ada
    if (!siswaData.jamMasukForm) {
      const now = new Date()
      siswaData.jamMasukForm = now.toTimeString().slice(0, 5)
    }
    if (!siswaData.jamKeluarForm) {
      const now = new Date()
      // Set jam keluar 1 jam setelah jam masuk sebagai default
      const defaultEnd = new Date(now.getTime() + 60 * 60 * 1000)
      siswaData.jamKeluarForm = defaultEnd.toTimeString().slice(0, 5)
    }
  }
  
  // Force reactivity update
  await nextTick()
}

const submitAbsensi = (data) => {
  // Validasi form sebelum submit
  if (!filters.tanggal) {
    showModal.value = true
    modalType.value = 'error'
    modalMessage.value = 'Silakan pilih tanggal terlebih dahulu!'
    return
  }

  // Validasi mata pelajaran dan guru ID
  const mataPelajaranId = data.jadwal?.mata_pelajaran_id || data.jadwal?.mata_pelajaran?.id
  const guruId = data.jadwal?.guru_id || data.jadwal?.guru?.id

  if (!mataPelajaranId) {
    showModal.value = true
    modalType.value = 'error'
    modalMessage.value = 'Data mata pelajaran tidak valid!'
    return
  }

  processing.value = true
  
  // Validasi dan format data absensi
  const validAbsensi = data.siswa.map(siswaData => {
    if (!siswaData.siswa || !siswaData.siswa.id) {
      return null
    }

    const absensiData = {
      siswa_id: siswaData.siswa.id,
      status: siswaData.statusForm || 'alpha',
      jam_masuk: (siswaData.statusForm === 'hadir' && siswaData.jamMasukForm) ? siswaData.jamMasukForm : null,
      jam_keluar: (siswaData.statusForm === 'hadir' && siswaData.jamKeluarForm) ? siswaData.jamKeluarForm : null,
      keterangan: siswaData.keteranganForm || null
    }
    
    return absensiData
  }).filter(Boolean) // Remove null entries

  if (validAbsensi.length === 0) {
    processing.value = false
    showModal.value = true
    modalType.value = 'error'
    modalMessage.value = 'Tidak ada data siswa yang valid untuk disimpan!'
    return
  }

  const formSubmitData = {
    tanggal: filters.tanggal,
    mata_pelajaran_id: mataPelajaranId,
    guru_id: guruId,
    absensi: validAbsensi
  }

  router.post(route('absensi.store'), formSubmitData, {
    onFinish: () => {
      processing.value = false
    },
    onError: (errors) => {
      processing.value = false
      
      let errorMsg = 'Terjadi kesalahan saat menyimpan absensi.'
      
      if (errors.message) {
        errorMsg = errors.message
      } else if (errors.tanggal) {
        errorMsg = 'Format tanggal tidak valid.'
      } else if (errors.mata_pelajaran_id) {
        errorMsg = 'Mata pelajaran tidak valid.'
      } else if (errors.absensi) {
        errorMsg = 'Data absensi tidak valid. Periksa kembali input Anda.'
      } else if (Object.keys(errors).length > 0) {
        errorMsg = 'Periksa kembali data yang diinput.'
      }
      
      showModal.value = true
      modalType.value = 'error'
      modalMessage.value = errorMsg
    },
    onSuccess: (page) => {
      // Set flag bahwa data absensi telah berubah
      localStorage.setItem('absensi_updated', Date.now().toString())
      
      // Reload data after successful submission
      initializeFormData()
      
      // Safe navigation untuk pesan sukses
      const mataPelajaranNama = data.jadwal?.mata_pelajaran?.nama_mapel || 
                               'Mata Pelajaran'
      const kelasNama = data.jadwal?.kelas?.nama_kelas || 
                       'Kelas'
      
      showModal.value = true
      modalType.value = 'success'
      modalMessage.value = `Absensi untuk ${mataPelajaranNama} - ${kelasNama} berhasil disimpan!`
    },
    preserveScroll: true
  })
}

const closeModal = () => {
  showModal.value = false
  modalType.value = ''
  modalMessage.value = ''
}

const getRowClass = (status) => {
  const classes = {
    'hadir': 'bg-green-50',
    'sakit': 'bg-yellow-50', 
    'izin': 'bg-blue-50',
    'alpha': 'bg-red-50'
  }
  return classes[status] || ''
}

const getStatusBadgeClass = (status) => {
  const classes = {
    'hadir': 'bg-green-100 text-green-800',
    'sakit': 'bg-yellow-100 text-yellow-800',
    'izin': 'bg-blue-100 text-blue-800',
    'alpha': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatTanggal = (tanggal) => {
  if (!tanggal) return 'N/A'
  try {
    return new Date(tanggal).toLocaleDateString('id-ID', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (e) {
    return tanggal
  }
}
</script>
