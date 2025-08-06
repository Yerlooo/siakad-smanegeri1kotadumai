<template>
    <AppLayout page-title="Monitoring Siswa">
        <!-- Loading Overlay -->
        <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-700 font-medium">{{ loadingMessage }}</p>
            </div>
        </div>

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Monitoring Siswa - Wali Kelas
                </h2>
                <div class="text-sm text-gray-500">
                    {{ siswaData.length }} siswa ditemukan
                </div>
            </div>
        </template>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-blue-100 truncate">Total Siswa</dt>
                                <dd class="text-2xl font-bold">{{ siswaData.length }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-green-100 truncate">Berprestasi</dt>
                                <dd class="text-2xl font-bold">{{ getPrestasiCount() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-yellow-100 truncate">Perlu Perhatian</dt>
                                <dd class="text-2xl font-bold">{{ getPerluPerhatianCount() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-purple-100 truncate">Rata-rata Kelas</dt>
                                <dd class="text-2xl font-bold">{{ getAverageGrade() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter & Pencarian
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kelas</label>
                        <select v-model="selectedKelas" @change="filterByKelas" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kelas</option>
                            <option v-for="kelas in kelasAsWali" :key="kelas.id" :value="kelas.id">
                                {{ kelas.nama_kelas }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                        <select v-model="selectedStatus" @change="applyFilters" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="berprestasi">Berprestasi</option>
                            <option value="normal">Normal</option>
                            <option value="perlu_perhatian">Perlu Perhatian</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <div class="relative">
                            <input v-model="searchQuery" @input="applyFilters" 
                                   type="text" placeholder="Cari nama atau NISN..." 
                                   class="w-full pl-10 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Grid -->
        <div class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">

                <div v-for="data in filteredSiswaData" :key="data.siswa.id" 
                     class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <!-- Student Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                    <span class="text-white font-bold text-sm">
                                        {{ data.siswa.nama_lengkap.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ data.siswa.nama_lengkap }}</h3>
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ data.siswa.kelas.nama_kelas }}
                                        <span class="mx-1">•</span>
                                        {{ data.siswa.nisn }}
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shadow-sm"
                                  :class="getStatusColor(data.status_akademik)">
                                {{ data.status_akademik.status }}
                            </span>
                        </div>

                        <!-- Academic Performance -->
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Performa Akademik
                            </h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Rata-rata Nilai</span>
                                    <div class="flex items-center">
                                        <span class="text-sm font-semibold mr-2" :class="getGradeColor(data.rata_rata_nilai)">
                                            {{ data.rata_rata_nilai }}
                                        </span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full" 
                                                 :class="getGradeBarColor(data.rata_rata_nilai)"
                                                 :style="`width: ${(data.rata_rata_nilai/100)*100}%`"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Kehadiran</span>
                                    <div class="flex items-center">
                                        <span class="text-sm font-semibold mr-2 text-green-600">{{ data.persentase_kehadiran }}%</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" 
                                                 :style="`width: ${data.persentase_kehadiran}%`"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Stats -->
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Statistik Kehadiran
                            </h4>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="text-center p-2 bg-green-50 rounded-lg">
                                    <div class="text-xs text-green-600 mb-1">Hadir</div>
                                    <div class="text-sm font-bold text-green-700">{{ data.absensi_stats.hadir }}</div>
                                </div>
                                <div class="text-center p-2 bg-yellow-50 rounded-lg">
                                    <div class="text-xs text-yellow-600 mb-1">Sakit</div>
                                    <div class="text-sm font-bold text-yellow-700">{{ data.absensi_stats.sakit }}</div>
                                </div>
                                <div class="text-center p-2 bg-blue-50 rounded-lg">
                                    <div class="text-xs text-blue-600 mb-1">Izin</div>
                                    <div class="text-sm font-bold text-blue-700">{{ data.absensi_stats.izin }}</div>
                                </div>
                                <div class="text-center p-2 bg-red-50 rounded-lg">
                                    <div class="text-xs text-red-600 mb-1">Alpha</div>
                                    <div class="text-sm font-bold text-red-700">{{ data.absensi_stats.alpha }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Grades -->
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                Nilai Terbaru
                            </h4>
                            <div class="space-y-1 max-h-20 overflow-y-auto">
                                <div v-for="nilai in data.nilai_terbaru.slice(0, 3)" :key="nilai.id" 
                                     class="flex justify-between items-center text-xs p-2 bg-white rounded border">
                                    <span class="text-gray-600 truncate flex-1">{{ nilai.mata_pelajaran.nama_mapel }}</span>
                                    <span class="font-bold ml-2 px-2 py-1 rounded text-white text-xs" 
                                          :class="getGradeBadgeColor(nilai.nilai)">
                                        {{ nilai.nilai }}
                                    </span>
                                </div>
                                <div v-if="data.nilai_terbaru.length === 0" class="text-xs text-gray-400 text-center py-2">
                                    Belum ada nilai
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2 pt-4 border-t border-gray-100">
                            <Link :href="route('wali-kelas.edit-siswa', data.siswa.id)" 
                                  class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-3 rounded-lg text-center transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Data
                            </Link>
                            <button @click="showDetails(data.siswa)" 
                                    :disabled="isLoading && loadingStudentId === data.siswa.id"
                                    class="flex-1 bg-gray-600 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm py-2 px-3 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <svg v-if="isLoading && loadingStudentId === data.siswa.id" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Detail
                            </button>
                        </div>
                    </div>
                </div>

            <!-- Empty State -->
            <div v-if="filteredSiswaData.length === 0" class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-12 text-center">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada siswa yang sesuai</h3>
                    <p class="text-gray-500 mb-4">Ubah filter atau kriteria pencarian untuk melihat data siswa.</p>
                    <button @click="resetFilters" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Reset Filter
                    </button>
                </div>
            </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div v-if="showDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4" 
             @click="closeDetailModal">
            <div class="relative w-full max-w-4xl max-h-full overflow-y-auto bg-white shadow-2xl rounded-lg" 
                 @click.stop>
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Detail Siswa
                        </h3>
                        <button @click="closeDetailModal" 
                                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div v-if="selectedSiswa" class="p-6">
                    <!-- Student Profile -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold">{{ selectedSiswa.nama_lengkap.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold">{{ selectedSiswa.nama_lengkap }}</h2>
                                <p class="text-blue-100">{{ selectedSiswa.kelas.nama_kelas }} • NISN: {{ selectedSiswa.nisn }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informasi Pribadi
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-600">Nama Lengkap</span>
                                    <span class="text-sm text-gray-900 text-right">{{ selectedSiswa.nama_lengkap }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-600">NISN</span>
                                    <span class="text-sm text-gray-900">{{ selectedSiswa.nisn }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-600">Kelas</span>
                                    <span class="text-sm text-gray-900">{{ selectedSiswa.kelas.nama_kelas }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-600">Jenis Kelamin</span>
                                    <span class="text-sm text-gray-900">{{ selectedSiswa.jenis_kelamin }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-600">Tanggal Lahir</span>
                                    <span class="text-sm text-gray-900">{{ selectedSiswa.tanggal_lahir || '-' }}</span>
                                </div>
                                <div class="border-t pt-3">
                                    <span class="text-sm font-medium text-gray-600">Alamat</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ selectedSiswa.alamat || 'Belum diisi' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Parent Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Data Orang Tua
                            </h4>
                            <div class="space-y-4">
                                <div class="border-b pb-3">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Data Ayah</h5>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Nama</span>
                                            <span class="text-sm text-gray-900">{{ selectedSiswa.nama_ayah || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Pekerjaan</span>
                                            <span class="text-sm text-gray-900">{{ selectedSiswa.pekerjaan_ayah || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Data Ibu</h5>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Nama</span>
                                            <span class="text-sm text-gray-900">{{ selectedSiswa.nama_ibu || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Pekerjaan</span>
                                            <span class="text-sm text-gray-900">{{ selectedSiswa.pekerjaan_ibu || '-' }}</span>
                                        </div>
                                    </div>
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
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    siswaData: Array,
    kelasAsWali: Array,
    selectedKelas: [String, Number]
});

const searchQuery = ref('');
const selectedKelas = ref(props.selectedKelas || '');
const selectedStatus = ref('');
const showDetailModal = ref(false);
const selectedSiswa = ref(null);

// Loading states
const isLoading = ref(false);
const loadingMessage = ref('');
const loadingStudentId = ref(null);

// Statistics computed properties
const statistics = computed(() => {
    const total = props.siswaData.length;
    const berprestasi = props.siswaData.filter(siswa => {
        // Assuming a student is "berprestasi" if average grade >= 80
        const avgGrade = calculateAverageGrade(siswa);
        return avgGrade >= 80;
    }).length;
    
    const perluPerhatian = props.siswaData.filter(siswa => {
        // Assuming a student needs attention if average grade < 70 or attendance < 80%
        const avgGrade = calculateAverageGrade(siswa);
        const attendance = calculateAttendance(siswa);
        return avgGrade < 70 || attendance < 80;
    }).length;
    
    const rataRataKelas = props.siswaData.length > 0 
        ? (props.siswaData.reduce((sum, siswa) => sum + calculateAverageGrade(siswa), 0) / props.siswaData.length).toFixed(1)
        : 0;

    return {
        total,
        berprestasi,
        perluPerhatian,
        rataRataKelas
    };
});

// Filtered students based on search and filters
const filteredSiswaData = computed(() => {
    let filtered = props.siswaData;

    // Search filter
    if (searchQuery.value) {
        filtered = filtered.filter(data => 
            data.siswa.nama_lengkap.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            data.siswa.nisn.includes(searchQuery.value)
        );
    }

    // Class filter - if selectedKelas is changed
    if (selectedKelas.value) {
        filtered = filtered.filter(data => data.siswa.kelas.id == selectedKelas.value);
    }

    // Status filter
    if (selectedStatus.value) {
        filtered = filtered.filter(data => {
            const avgGrade = data.rata_rata_nilai || calculateAverageGrade(data.siswa);
            const attendance = data.persentase_kehadiran || calculateAttendance(data.siswa);
            
            switch (selectedStatus.value) {
                case 'berprestasi':
                    return avgGrade >= 80;
                case 'perlu_perhatian':
                    return avgGrade < 70 || attendance < 80;
                case 'normal':
                    return avgGrade >= 70 && avgGrade < 80 && attendance >= 80;
                default:
                    return true;
            }
        });
    }

    return filtered;
});

const filterByKelas = () => {
    router.get(route('wali-kelas.monitoring-siswa'), {
        kelas_id: selectedKelas.value
    }, {
        preserveState: true,
        replace: true
    });
};

// Helper functions
const calculateAverageGrade = (siswa) => {
    // Mock calculation - replace with actual grade calculation
    if (siswa.nilai_siswa && siswa.nilai_siswa.length > 0) {
        const total = siswa.nilai_siswa.reduce((sum, nilai) => sum + parseFloat(nilai.nilai || 0), 0);
        return Math.round(total / siswa.nilai_siswa.length);
    }
    return Math.floor(Math.random() * 40) + 60; // Mock data
};

const calculateAttendance = (siswa) => {
    // Mock calculation - replace with actual attendance calculation
    if (siswa.absensi && siswa.absensi.length > 0) {
        const hadir = siswa.absensi.filter(abs => abs.status === 'hadir').length;
        return Math.round((hadir / siswa.absensi.length) * 100);
    }
    return Math.floor(Math.random() * 30) + 70; // Mock data
};

const getStatusBadge = (siswa) => {
    const avgGrade = calculateAverageGrade(siswa);
    const attendance = calculateAttendance(siswa);
    
    if (avgGrade >= 80) {
        return { text: 'Berprestasi', class: 'bg-green-100 text-green-800' };
    } else if (avgGrade < 70 || attendance < 80) {
        return { text: 'Perlu Perhatian', class: 'bg-red-100 text-red-800' };
    } else {
        return { text: 'Normal', class: 'bg-blue-100 text-blue-800' };
    }
};

const getStatusColor = (status) => {
    const colors = {
        'green': 'bg-green-100 text-green-800',
        'blue': 'bg-blue-100 text-blue-800',
        'yellow': 'bg-yellow-100 text-yellow-800',
        'red': 'bg-red-100 text-red-800'
    };
    return colors[status.color] || 'bg-gray-100 text-gray-800';
};

const getGradeColor = (nilai) => {
    if (nilai >= 85) return 'text-green-600';
    if (nilai >= 75) return 'text-blue-600';
    if (nilai >= 65) return 'text-yellow-600';
    return 'text-red-600';
};

const getGradeBadgeColor = (nilai) => {
    if (nilai >= 85) return 'bg-green-500';
    if (nilai >= 75) return 'bg-blue-500';
    if (nilai >= 65) return 'bg-yellow-500';
    return 'bg-red-500';
};

const showDetail = (siswa) => {
    selectedSiswa.value = siswa;
    showDetailModal.value = true;
};

const showDetails = async (siswa) => {
    try {
        loadingStudentId.value = siswa.id;
        isLoading.value = true;
        loadingMessage.value = 'Memuat detail siswa...';
        
        // Small delay to show loading state
        await new Promise(resolve => setTimeout(resolve, 300));
        
        selectedSiswa.value = siswa;
        showDetailModal.value = true;
    } catch (error) {
        console.error('Error loading student details:', error);
    } finally {
        isLoading.value = false;
        loadingStudentId.value = null;
        loadingMessage.value = '';
    }
};

const closeDetailModal = () => {
    showDetailModal.value = false;
    selectedSiswa.value = null;
};

const resetFilters = () => {
    searchQuery.value = '';
    selectedKelas.value = '';
    selectedStatus.value = '';
};

const startConsultation = async (siswa) => {
    try {
        isLoading.value = true;
        loadingMessage.value = 'Memulai konsultasi...';
        
        // Navigate to consultation page
        router.visit(`/wali-kelas/konsultasi-siswa?siswa=${siswa.id}`);
    } catch (error) {
        console.error('Error starting consultation:', error);
        isLoading.value = false;
        loadingMessage.value = '';
    }
};

const viewDetailedReport = async (siswa) => {
    try {
        isLoading.value = true;
        loadingMessage.value = 'Membuka laporan detail...';
        
        // Navigate to detailed report page
        router.visit(`/wali-kelas/laporan-kelas?siswa=${siswa.id}`);
    } catch (error) {
        console.error('Error opening detailed report:', error);
        isLoading.value = false;
        loadingMessage.value = '';
    }
};

// Additional functions for template
const getPrestasiCount = () => {
    return props.siswaData.filter(data => {
        const avgGrade = data.rata_rata_nilai || calculateAverageGrade(data.siswa);
        return avgGrade >= 80;
    }).length;
};

const getPerluPerhatianCount = () => {
    return props.siswaData.filter(data => {
        const avgGrade = data.rata_rata_nilai || calculateAverageGrade(data.siswa);
        const attendance = data.persentase_kehadiran || calculateAttendance(data.siswa);
        return avgGrade < 70 || attendance < 80;
    }).length;
};

const getAverageGrade = () => {
    if (props.siswaData.length === 0) return 0;
    const total = props.siswaData.reduce((sum, data) => {
        return sum + (data.rata_rata_nilai || calculateAverageGrade(data.siswa));
    }, 0);
    return Math.round(total / props.siswaData.length);
};

const getGradeBarColor = (nilai) => {
    if (nilai >= 85) return 'bg-green-500';
    if (nilai >= 75) return 'bg-blue-500';
    if (nilai >= 65) return 'bg-yellow-500';
    return 'bg-red-500';
};

const applyFilters = () => {
    // This will be handled by computed property filteredSiswaData
    console.log('Applying filters...');
};
</script>
