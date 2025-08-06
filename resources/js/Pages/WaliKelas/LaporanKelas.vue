<template>
    <AppLayout :page-title="`Laporan Kelas ${kelas.nama_kelas}`">
        <!-- Loading Overlay -->
        <div v-if="isLoading" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl flex items-center space-x-4">
                <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div>
                    <div class="text-lg font-semibold text-gray-900">{{ loadingMessage }}</div>
                    <div class="text-sm text-gray-600">Mohon tunggu sebentar...</div>
                </div>
            </div>
        </div>

        <!-- Modern Header with Gradient -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Laporan Kelas {{ kelas.nama_kelas }}</h1>
                    <p class="text-indigo-100">Laporan komprehensif akademik dan kehadiran siswa</p>
                </div>
                <div class="flex items-center space-x-4">
                    <Link :href="route('wali-kelas.dashboard')" 
                          class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">Periode:</span> {{ getCurrentPeriod() }}
                    </div>
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">Total Siswa:</span> {{ statistik.total_siswa }}
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    <button @click="exportToPDF" 
                            :disabled="isLoading"
                            :class="['px-4 py-2 rounded-lg text-sm transition-colors flex items-center space-x-2', 
                                     isLoading ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700', 
                                     'text-white']">
                        <svg v-if="loadingType === 'pdf'" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>{{ loadingType === 'pdf' ? 'Generating PDF...' : 'Export PDF' }}</span>
                    </button>
                    <button @click="exportToExcel" 
                            :disabled="isLoading"
                            :class="['px-4 py-2 rounded-lg text-sm transition-colors flex items-center space-x-2', 
                                     isLoading ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700', 
                                     'text-white']">
                        <svg v-if="loadingType === 'excel'" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>{{ loadingType === 'excel' ? 'Generating Excel...' : 'Export Excel' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ statistik.total_siswa }}</div>
                        <div class="text-blue-100 text-sm">Total Siswa</div>
                        <div class="text-xs text-blue-200 mt-1">
                            {{ statistik.siswa_laki }} ♂ • {{ statistik.siswa_perempuan }} ♀
                        </div>
                    </div>
                    <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ statistik.rata_rata_kelas }}</div>
                        <div class="text-green-100 text-sm">Rata-rata Kelas</div>
                        <div class="text-xs text-green-200 mt-1">Semua mata pelajaran</div>
                    </div>
                    <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ statistik.kehadiran_rata }}</div>
                        <div class="text-yellow-100 text-sm">Rata-rata Kehadiran</div>
                        <div class="text-xs text-yellow-200 mt-1">Persentase hadir</div>
                    </div>
                    <svg class="w-8 h-8 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ statistik.prestasi_count }}</div>
                        <div class="text-purple-100 text-sm">Siswa Berprestasi</div>
                        <div class="text-xs text-purple-200 mt-1">Nilai ≥ 85</div>
                    </div>
                    <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Performance Chart and Quick Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Performance Overview -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Distribusi Nilai Kelas
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ getGradeDistribution().excellent }}</div>
                        <div class="text-sm text-green-600">Sangat Baik</div>
                        <div class="text-xs text-gray-500">85-100</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ getGradeDistribution().good }}</div>
                        <div class="text-sm text-blue-600">Baik</div>
                        <div class="text-xs text-gray-500">75-84</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ getGradeDistribution().average }}</div>
                        <div class="text-sm text-yellow-600">Cukup</div>
                        <div class="text-xs text-gray-500">65-74</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">{{ getGradeDistribution().needsImprovement }}</div>
                        <div class="text-sm text-red-600">Perlu Perbaikan</div>
                        <div class="text-xs text-gray-500">&lt; 65</div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Insights -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Wawasan Cepat
                </h3>
                <div class="space-y-4">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <div class="text-sm font-medium text-blue-900">Siswa Terbaik</div>
                        <div class="text-xs text-blue-700">{{ getTopStudent() }}</div>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <div class="text-sm font-medium text-yellow-900">Perlu Perhatian</div>
                        <div class="text-xs text-yellow-700">{{ getNeedsAttentionCount() }} siswa</div>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <div class="text-sm font-medium text-green-900">Kehadiran Tertinggi</div>
                        <div class="text-xs text-green-700">{{ statistik.kehadiran_tertinggi }}%</div>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <div class="text-sm font-medium text-red-900">Kehadiran Terendah</div>
                        <div class="text-xs text-red-700">{{ statistik.kehadiran_terendah }}%</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modern Ranking Table -->
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    Ranking Akademik Siswa
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peringkat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(ranking, index) in rankingSiswa" :key="ranking.siswa.id"
                            :class="getRankingRowClass(index)" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                         :class="getRankingBadgeClass(index)">
                                        <span class="text-sm font-bold text-white">{{ index + 1 }}</span>
                                    </div>
                                    <span v-if="index < 3" class="ml-2 text-lg">
                                        {{ getRankingIcon(index) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ ranking.siswa.nama_lengkap.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ ranking.siswa.nama_lengkap }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ ranking.siswa.nisn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-bold px-3 py-1 rounded-full" 
                                          :class="getGradeBadgeClass(ranking.rata_rata)">
                                        {{ ranking.rata_rata }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="h-2 rounded-full" 
                                             :class="getAttendanceBarClass(ranking.kehadiran)"
                                             :style="{ width: ranking.kehadiran + '%' }"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ ranking.kehadiran }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      :class="getStatusBadgeClass(ranking.status)">
                                    {{ ranking.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="viewStudentDetail(ranking.siswa)" 
                                        :disabled="isLoading"
                                        :class="['mr-3 transition-colors', 
                                                 isLoading ? 'text-gray-400 cursor-not-allowed' : 'text-indigo-600 hover:text-indigo-900']">
                                    <svg v-if="loadingType === 'detail' && loadingStudentId === ranking.siswa.id" 
                                         class="w-4 h-4 inline animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button @click="printStudentReport(ranking.siswa)" 
                                        :disabled="isLoading"
                                        :class="['transition-colors', 
                                                 isLoading ? 'text-gray-400 cursor-not-allowed' : 'text-gray-600 hover:text-gray-900']">
                                    <svg v-if="loadingType === 'print' && loadingStudentId === ranking.siswa.id" 
                                         class="w-4 h-4 inline animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Analisis per Mata Pelajaran -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Analisis per Mata Pelajaran</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div v-for="mapel in analisisMapel" :key="mapel.mata_pelajaran" 
                                 class="border rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3">{{ mapel.mata_pelajaran }}</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Rata-rata</span>
                                        <span class="text-sm font-medium" :class="getGradeColor(mapel.rata_rata)">
                                            {{ mapel.rata_rata }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Tertinggi</span>
                                        <span class="text-sm font-medium text-green-600">{{ mapel.nilai_tertinggi }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Terendah</span>
                                        <span class="text-sm font-medium text-red-600">{{ mapel.nilai_terendah }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Total Nilai</span>
                                        <span class="text-sm font-medium text-gray-900">{{ mapel.jumlah_nilai }}</span>
                                    </div>
                                </div>
                                
                                <!-- Progress bar -->
                                <div class="mt-3">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Progress Penilaian</span>
                                        <span>{{ Math.round((mapel.jumlah_nilai / statistik.total_siswa) * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             :style="{ width: Math.round((mapel.jumlah_nilai / statistik.total_siswa) * 100) + '%' }">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik Kehadiran -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Kehadiran Kelas</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ absensiStats.hadir }}</div>
                                <div class="text-sm text-gray-500">Hadir</div>
                                <div class="text-xs text-gray-400">
                                    {{ getAttendancePercentage('hadir') }}%
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ absensiStats.sakit }}</div>
                                <div class="text-sm text-gray-500">Sakit</div>
                                <div class="text-xs text-gray-400">
                                    {{ getAttendancePercentage('sakit') }}%
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ absensiStats.izin }}</div>
                                <div class="text-sm text-gray-500">Izin</div>
                                <div class="text-xs text-gray-400">
                                    {{ getAttendancePercentage('izin') }}%
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ absensiStats.alpha }}</div>
                                <div class="text-sm text-gray-500">Alpha</div>
                                <div class="text-xs text-gray-400">
                                    {{ getAttendancePercentage('alpha') }}%
                                </div>
                            </div>
                        </div>
                        
                        <!-- Attendance Chart -->
                        <div class="mt-6">
                            <div class="flex justify-between text-sm text-gray-500 mb-2">
                                <span>Distribusi Kehadiran</span>
                                <span>Total: {{ totalAttendance }} records</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4 flex overflow-hidden">
                                <div class="bg-green-500 h-full" 
                                     :style="{ width: getAttendancePercentage('hadir') + '%' }"
                                     :title="`Hadir: ${getAttendancePercentage('hadir')}%`">
                                </div>
                                <div class="bg-yellow-500 h-full" 
                                     :style="{ width: getAttendancePercentage('sakit') + '%' }"
                                     :title="`Sakit: ${getAttendancePercentage('sakit')}%`">
                                </div>
                                <div class="bg-blue-500 h-full" 
                                     :style="{ width: getAttendancePercentage('izin') + '%' }"
                                     :title="`Izin: ${getAttendancePercentage('izin')}%`">
                                </div>
                                <div class="bg-red-500 h-full" 
                                     :style="{ width: getAttendancePercentage('alpha') + '%' }"
                                     :title="`Alpha: ${getAttendancePercentage('alpha')}%`">
                                </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Detail Modal -->
        <div v-if="showStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4" 
             @click="closeStudentModal">
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
                        <button @click="closeStudentModal" 
                                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div v-if="selectedStudent" class="p-6">
                    <!-- Student Profile -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold">{{ selectedStudent.nama_lengkap.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold">{{ selectedStudent.nama_lengkap }}</h2>
                                <p class="text-blue-100">{{ selectedStudent.kelas?.nama_kelas }} • NISN: {{ selectedStudent.nisn }}</p>
                                <div class="mt-2 flex items-center space-x-4">
                                    <span class="text-sm bg-white bg-opacity-20 px-2 py-1 rounded">
                                        Ranking: #{{ getStudentRanking(selectedStudent) }}
                                    </span>
                                    <span class="text-sm bg-white bg-opacity-20 px-2 py-1 rounded">
                                        Rata-rata: {{ getStudentAverage(selectedStudent) }}
                                    </span>
                                </div>
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
                                    <span class="text-sm text-gray-900 text-right">{{ selectedStudent.nama_lengkap }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-600">NISN</span>
                                    <span class="text-sm text-gray-900">{{ selectedStudent.nisn }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-600">Kelas</span>
                                    <span class="text-sm text-gray-900">{{ selectedStudent.kelas?.nama_kelas }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-600">Jenis Kelamin</span>
                                    <span class="text-sm text-gray-900">{{ selectedStudent.jenis_kelamin }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-600">Tanggal Lahir</span>
                                    <span class="text-sm text-gray-900">{{ selectedStudent.tanggal_lahir || '-' }}</span>
                                </div>
                                <div class="border-t pt-3">
                                    <span class="text-sm font-medium text-gray-600">Alamat</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ selectedStudent.alamat || 'Belum diisi' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Academic Performance -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Performa Akademik
                            </h4>
                            <div class="space-y-4">
                                <div class="bg-white p-4 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700">Rata-rata Nilai</span>
                                        <span class="text-lg font-bold" :class="getGradeColor(getStudentAverage(selectedStudent))">
                                            {{ getStudentAverage(selectedStudent) }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full" 
                                             :class="getGradeBarClass(getStudentAverage(selectedStudent))"
                                             :style="{ width: (getStudentAverage(selectedStudent) || 0) + '%' }"></div>
                                    </div>
                                </div>
                                
                                <div class="bg-white p-4 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700">Kehadiran</span>
                                        <span class="text-lg font-bold" :class="getAttendanceColor(getStudentAttendance(selectedStudent))">
                                            {{ getStudentAttendance(selectedStudent) }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full" 
                                             :class="getAttendanceBarClass(getStudentAttendance(selectedStudent))"
                                             :style="{ width: getStudentAttendance(selectedStudent) + '%' }"></div>
                                    </div>
                                </div>

                                <div class="bg-white p-4 rounded-lg">
                                    <div class="text-sm font-medium text-gray-700 mb-2">Status Akademik</div>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                                          :class="getStatusBadgeClass(getStudentStatus(selectedStudent))">
                                        {{ getStudentStatus(selectedStudent) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button @click="printStudentReport(selectedStudent)" 
                                :disabled="isLoading"
                                :class="['flex-1 px-4 py-2 rounded-lg transition-colors flex items-center justify-center',
                                         isLoading ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700',
                                         'text-white']">
                            <svg v-if="loadingType === 'print'" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            {{ loadingType === 'print' ? 'Generating Report...' : 'Print Laporan Siswa' }}
                        </button>
                        <Link :href="route('wali-kelas.edit-siswa', selectedStudent.id)"
                              class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Data Siswa
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template><script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    kelas: Object,
    statistik: Object,
    rankingSiswa: Array,
    analisisMapel: Array,
    absensiStats: Object
});

const showStudentModal = ref(false);
const selectedStudent = ref(null);

// Loading states
const isLoading = ref(false);
const loadingType = ref('');
const loadingMessage = ref('');
const loadingStudentId = ref(null);

const totalAttendance = computed(() => {
    return Object.values(props.absensiStats).reduce((sum, count) => sum + count, 0);
});

const getAttendancePercentage = (type) => {
    if (totalAttendance.value === 0) return 0;
    return Math.round((props.absensiStats[type] / totalAttendance.value) * 100);
};

const getRankingRowClass = (index) => {
    if (index === 0) return 'bg-yellow-50';
    if (index === 1) return 'bg-gray-50';
    if (index === 2) return 'bg-orange-50';
    return '';
};

const getRankingIcon = (index) => {
    const icons = ['🥇', '🥈', '🥉'];
    return icons[index] || '';
};

const getGradeColor = (nilai) => {
    if (nilai >= 85) return 'text-green-600';
    if (nilai >= 75) return 'text-blue-600';
    if (nilai >= 65) return 'text-yellow-600';
    return 'text-red-600';
};

const getStatusColor = (nilai) => {
    if (nilai >= 85) return 'bg-green-100 text-green-800';
    if (nilai >= 75) return 'bg-blue-100 text-blue-800';
    if (nilai >= 65) return 'bg-yellow-100 text-yellow-800';
    return 'bg-red-100 text-red-800';
};

const getStatusText = (nilai) => {
    if (nilai >= 85) return 'Berprestasi';
    if (nilai >= 75) return 'Baik';
    if (nilai >= 65) return 'Cukup';
    return 'Perlu Perhatian';
};

// Additional functions needed by template
const getRankingBadgeClass = (index) => {
    if (index === 0) return 'bg-yellow-500';
    if (index === 1) return 'bg-gray-500';
    if (index === 2) return 'bg-orange-500';
    return 'bg-blue-500';
};

const getGradeBadgeClass = (nilai) => {
    if (nilai >= 85) return 'bg-green-100 text-green-800';
    if (nilai >= 75) return 'bg-blue-100 text-blue-800';
    if (nilai >= 65) return 'bg-yellow-100 text-yellow-800';
    return 'bg-red-100 text-red-800';
};

const getAttendanceBarClass = (percentage) => {
    if (percentage >= 90) return 'bg-green-500';
    if (percentage >= 80) return 'bg-blue-500';
    if (percentage >= 70) return 'bg-yellow-500';
    return 'bg-red-500';
};

const getStatusBadgeClass = (status) => {
    const statusMap = {
        'Berprestasi': 'bg-green-100 text-green-800',
        'Baik': 'bg-blue-100 text-blue-800',
        'Cukup': 'bg-yellow-100 text-yellow-800',
        'Perlu Perhatian': 'bg-red-100 text-red-800'
    };
    return statusMap[status] || 'bg-gray-100 text-gray-800';
};

const getGradeDistribution = () => {
    // Mock data - should be calculated from actual student data
    return {
        excellent: props.rankingSiswa.filter(r => r.rata_rata >= 85).length,
        good: props.rankingSiswa.filter(r => r.rata_rata >= 75 && r.rata_rata < 85).length,
        average: props.rankingSiswa.filter(r => r.rata_rata >= 65 && r.rata_rata < 75).length,
        needsImprovement: props.rankingSiswa.filter(r => r.rata_rata < 65).length
    };
};

const getCurrentPeriod = () => {
    const now = new Date();
    const month = now.getMonth() + 1;
    const year = now.getFullYear();
    const semester = month >= 1 && month <= 6 ? 'Genap' : 'Ganjil';
    return `Semester ${semester} ${year}`;
};

const getTopStudent = () => {
    if (props.rankingSiswa.length > 0) {
        return props.rankingSiswa[0].siswa.nama_lengkap;
    }
    return 'Belum ada data';
};

const getNeedsAttentionCount = () => {
    return props.rankingSiswa.filter(r => r.rata_rata < 70).length;
};

const viewStudentDetail = async (siswa) => {
    try {
        loadingStudentId.value = siswa.id;
        isLoading.value = true;
        loadingType.value = 'detail';
        loadingMessage.value = 'Memuat detail siswa...';
        
        // Small delay to show loading state
        await new Promise(resolve => setTimeout(resolve, 300));
        
        selectedStudent.value = siswa;
        showStudentModal.value = true;
    } catch (error) {
        console.error('Error loading student detail:', error);
    } finally {
        isLoading.value = false;
        loadingStudentId.value = null;
        loadingType.value = null;
        loadingMessage.value = '';
    }
};

const closeStudentModal = () => {
    showStudentModal.value = false;
    selectedStudent.value = null;
};

const printStudentReport = async (siswa) => {
    try {
        loadingStudentId.value = siswa.id;
        isLoading.value = true;
        loadingType.value = 'print';
        loadingMessage.value = 'Menyiapkan laporan siswa...';
        
        // Generate individual student report PDF
        window.open(route('wali-kelas.student-report-pdf', { kelasId: props.kelas.id, siswaId: siswa.id }), '_blank');
        
        // Small delay to show loading state
        await new Promise(resolve => setTimeout(resolve, 1000));
        
    } catch (error) {
        console.error('Error generating student report:', error);
    } finally {
        isLoading.value = false;
        loadingStudentId.value = null;
        loadingType.value = null;
        loadingMessage.value = '';
    }
};

// Helper functions for student modal
const getStudentRanking = (siswa) => {
    const index = props.rankingSiswa.findIndex(r => r.siswa.id === siswa.id);
    return index !== -1 ? index + 1 : '-';
};

const getStudentAverage = (siswa) => {
    const studentRanking = props.rankingSiswa.find(r => r.siswa.id === siswa.id);
    return studentRanking ? studentRanking.rata_rata : 0;
};

const getStudentAttendance = (siswa) => {
    const studentRanking = props.rankingSiswa.find(r => r.siswa.id === siswa.id);
    return studentRanking ? studentRanking.kehadiran : 0;
};

const getStudentStatus = (siswa) => {
    const studentRanking = props.rankingSiswa.find(r => r.siswa.id === siswa.id);
    return studentRanking ? studentRanking.status : 'Unknown';
};

const getGradeBarClass = (nilai) => {
    if (nilai >= 85) return 'bg-green-500';
    if (nilai >= 75) return 'bg-blue-500';
    if (nilai >= 65) return 'bg-yellow-500';
    return 'bg-red-500';
};

const getAttendanceColor = (percentage) => {
    if (percentage >= 90) return 'text-green-600';
    if (percentage >= 80) return 'text-blue-600';
    if (percentage >= 70) return 'text-yellow-600';
    return 'text-red-600';
};

const getStudentAttendancePercentage = (siswa) => {
    const studentRanking = props.rankingSiswa.find(r => r.siswa.id === siswa.id);
    return studentRanking ? studentRanking.kehadiran : 'N/A';
};

const exportToPDF = async () => {
    try {
        isLoading.value = true;
        loadingType.value = 'pdf';
        loadingMessage.value = 'Mengekspor laporan ke PDF...';
        
        // Export class report to PDF
        window.open(route('wali-kelas.export-pdf', props.kelas.id), '_blank');
        
        // Wait a bit to show loading state
        await new Promise(resolve => setTimeout(resolve, 1500));
        
    } catch (error) {
        console.error('Error exporting to PDF:', error);
    } finally {
        isLoading.value = false;
        loadingType.value = null;
        loadingMessage.value = '';
    }
};

const exportToExcel = async () => {
    try {
        isLoading.value = true;
        loadingType.value = 'excel';
        loadingMessage.value = 'Mengekspor laporan ke Excel...';
        
        // Export class report to Excel
        window.location.href = route('wali-kelas.export-excel', props.kelas.id);
        
        // Wait a bit to show loading state
        await new Promise(resolve => setTimeout(resolve, 1500));
        
    } catch (error) {
        console.error('Error exporting to Excel:', error);
    } finally {
        isLoading.value = false;
        loadingType.value = null;
        loadingMessage.value = '';
    }
};
</script>
