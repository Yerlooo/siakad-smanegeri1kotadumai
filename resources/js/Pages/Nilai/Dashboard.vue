<template>
    <Head title="SIAKAD SMANSA" />
    <AppLayout title="Dashboard Input Nilai">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    📊 Dashboard Input Nilai
                </h2>
                <button @click="refreshStatusData" 
                        :disabled="isRefreshing"
                        class="flex items-center space-x-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                    <svg :class="{'animate-spin': isRefreshing}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="text-sm">{{ isRefreshing ? 'Memuat...' : 'Refresh' }}</span>
                </button>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 sm:p-6 text-white">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-blue-400 bg-opacity-30">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-base sm:text-lg font-semibold">Total Mata Pelajaran</h3>
                                <p class="text-xl sm:text-2xl font-bold">{{ reactiveProgressNilai.length }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 sm:p-6 text-white">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-green-400 bg-opacity-30">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-base sm:text-lg font-semibold">Total Kelas Diajar</h3>
                                <p class="text-xl sm:text-2xl font-bold">{{ totalKelas }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 sm:p-6 text-white sm:col-span-2 lg:col-span-1">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-purple-400 bg-opacity-30">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z"/>
                                </svg>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-base sm:text-lg font-semibold">Jenis Penilaian</h3>
                                <p class="text-xl sm:text-2xl font-bold">{{ totalJenisNilaiCustom }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Utama Pengaturan Jenis Nilai -->
                <div class="mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="mb-4 sm:mb-0">
                                <h3 class="text-lg sm:text-xl font-semibold mb-2">⚙️ Pengaturan Jenis Nilai</h3>
                                <p class="text-blue-100 text-sm sm:text-base">
                                    Atur jenis penilaian yang akan digunakan untuk semua mata pelajaran dan kelas yang Anda ajar
                                </p>
                                <div class="mt-2 text-blue-100 text-xs sm:text-sm">
                                    Total jenis nilai saat ini: <span class="font-semibold">{{ totalJenisNilaiCustom }}</span>
                                    <span v-if="totalBobot !== 100" class="ml-2 px-2 py-1 bg-yellow-500 text-white rounded-full text-xs">
                                        Bobot: {{ totalBobot }}%
                                    </span>
                                    <span v-else class="ml-2 px-2 py-1 bg-green-500 text-white rounded-full text-xs">
                                        ✓ Bobot: 100%
                                    </span>
                                </div>
                            </div>
                            <button @click="openMainSettingsModal"
                                    class="w-full sm:w-auto px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors flex items-center justify-center space-x-2 shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Atur Jenis Nilai</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Progress per Mata Pelajaran -->
                <div class="space-y-6">
                    <!-- Info untuk guru yang belum punya jadwal -->
                    <div v-if="reactiveProgressNilai.length === 0" class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-blue-800 mb-2">Selamat Datang di Dashboard Input Nilai!</h3>
                                <div class="text-sm text-blue-700">
                                    <p class="mb-3">Sistem ini memungkinkan Anda untuk mengelola nilai siswa dengan mudah dan efisien.</p>
                                    
                                    <div v-if="reactiveProgressNilai.length === 0" class="bg-white bg-opacity-50 rounded-lg p-4">
                                        <p class="font-medium mb-2">📋 Untuk memulai, pastikan:</p>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Anda sudah login sebagai guru atau kepala tata usaha</li>
                                            <li>Ada jadwal pelajaran yang di-assign ke akun Anda</li>
                                            <li>Tahun ajaran 2024/2025 semester ganjil sudah aktif</li>
                                        </ul>
                                        <p class="mt-3 text-xs text-blue-600">💡 Hubungi admin/tata usaha jika Anda tidak melihat mata pelajaran yang seharusnya Anda ajar.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-for="progress in reactiveProgressNilai" :key="progress.mata_pelajaran.id" 
                         class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-200">
                        <div class="p-6">
                            <!-- Header Mata Pelajaran -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <span class="text-blue-600 font-bold text-lg">
                                            {{ progress.mata_pelajaran.kode_mapel }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-xl font-semibold text-gray-900">
                                            {{ progress.mata_pelajaran.nama_mapel }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ progress.total_kelas }} kelas • {{ progress.mata_pelajaran.jam_pelajaran }} JP
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-sm text-gray-600">Progress Keseluruhan</div>
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ Math.round(getOverallProgress(progress.kelas_detail)) }}%
                                    </div>
                                </div>
                            </div>

                            <!-- Kelas Cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                                <div v-for="kelas in progress.kelas_detail" :key="kelas.kelas.id"
                                     class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-900 text-sm sm:text-base">{{ kelas.kelas.nama_kelas }}</h4>
                                        <span class="px-2 py-1 text-xs rounded-full" 
                                              :class="getProgressBadgeClass(kelas.progress_persen)">
                                            {{ kelas.progress_persen }}%
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="flex justify-between text-xs sm:text-sm text-gray-600 mb-1">
                                            <span>{{ getCompletedJenisNilaiCount(kelas, getJenisNilaiForMataPelajaranKelas(progress.mata_pelajaran.id, kelas.kelas.id).length) }} dari {{ getJenisNilaiForMataPelajaranKelas(progress.mata_pelajaran.id, kelas.kelas.id).length }} jenis nilai selesai</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                 :style="{ width: kelas.progress_persen + '%' }"></div>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="space-y-2 mb-3">
                                        <!-- Pesan ketika belum ada jenis nilai custom yang diatur -->
                                        <div v-if="getJenisNilaiForMataPelajaranKelas(progress.mata_pelajaran.id, kelas.kelas.id).length === 0" 
                                             class="text-center py-3 px-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <div class="text-xs text-yellow-700">
                                                <svg class="w-4 h-4 mx-auto mb-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                <p class="font-medium">Belum ada jenis nilai</p>
                                                <p class="text-xs">Atur jenis nilai terlebih dahulu</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Mobile: Stack vertically, Desktop: Grid -->
                                        <div v-else class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2">
                                            <button v-for="jenis in getJenisNilaiForMataPelajaranKelas(progress.mata_pelajaran.id, kelas.kelas.id).slice(0, 3)" :key="jenis.id"
                                                    @click="inputNilai(progress.mata_pelajaran.id, kelas.kelas.id, jenis.id)"
                                                    :title="getStatusTooltip(kelas, jenis.id)"
                                                    :class="getButtonClass(kelas, jenis.id)"
                                                    class="w-full text-xs px-2 py-2 sm:py-1 rounded-lg transition-colors text-center relative border">
                                                <div class="flex items-center justify-center space-x-1">
                                                    <span>{{ getJenisNilaiIcon(jenis.nama) }}</span>
                                                    <span class="font-medium">{{ jenis.nama.split(' ')[0] }}</span>
                                                </div>
                                                <!-- Status Indicator -->
                                                <span v-if="getStatusIndicator(kelas, jenis.id)" 
                                                      :class="getStatusIndicator(kelas, jenis.id).class"
                                                      class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-xs flex items-center justify-center shadow-sm">
                                                    {{ getStatusIndicator(kelas, jenis.id).icon }}
                                                </span>
                                            </button>
                                        </div>
                                        
                                        <!-- Show more indicator if there are more jenis nilai -->
                                        <div v-if="getJenisNilaiForMataPelajaranKelas(progress.mata_pelajaran.id, kelas.kelas.id).length > 3" class="text-xs text-gray-500 text-center">
                                            +{{ getJenisNilaiForMataPelajaranKelas(progress.mata_pelajaran.id, kelas.kelas.id).length - 3 }} jenis nilai lainnya
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
                                        <button @click="lihatDetail(progress.mata_pelajaran.id, kelas.kelas.id)"
                                                class="flex-1 inline-flex items-center justify-center text-xs px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 active:bg-gray-300 transition-colors font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="hidden sm:inline">Lihat </span>Detail
                                        </button>
                                        <button @click="inputCustom(progress.mata_pelajaran.id, kelas.kelas.id)"
                                                :disabled="getJenisNilaiForMataPelajaranKelas(progress.mata_pelajaran.id, kelas.kelas.id).length === 0"
                                                :class="getJenisNilaiForMataPelajaranKelas(progress.mata_pelajaran.id, kelas.kelas.id).length === 0 ? 
                                                    'bg-gray-400 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600 active:bg-green-700'"
                                                class="flex-1 inline-flex items-center justify-center text-xs px-3 py-2 text-white rounded-lg transition-colors font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            {{ getJenisNilaiForMataPelajaranKelas(progress.mata_pelajaran.id, kelas.kelas.id).length === 0 ? 'Atur Jenis Nilai Dulu' : 'Input Nilai' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="reactiveProgressNilai.length === 0" class="text-center py-8 sm:py-12">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2 px-4">Belum Ada Mata Pelajaran</h3>
                    <p class="text-sm sm:text-base text-gray-600 px-4 max-w-md mx-auto">Anda belum memiliki jadwal mengajar untuk semester ini. Silakan hubungi admin untuk mendapatkan jadwal.</p>
                </div>

                <!-- Modal untuk Input Custom -->
                <div v-if="showInputModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 py-6 sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="closeInputModal"></div>
                        
                        <!-- Modal panel -->
                        <div class="relative bg-white rounded-xl shadow-2xl transform transition-all w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
                            <!-- Header dengan close button -->
                            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                                <div>
                                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Pilih Jenis Nilai untuk Input</h3>
                                    <p class="text-sm text-gray-600 mt-1">Pilih jenis penilaian yang akan diinput untuk kelas yang dipilih</p>
                                </div>
                                <button @click="closeInputModal" 
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-full transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Scrollable content -->
                            <div class="overflow-y-auto max-h-[calc(90vh-140px)] p-4 sm:p-6">
                                <!-- Pesan ketika belum ada jenis nilai custom -->
                                <div v-if="allJenisNilai.length === 0" class="text-center py-12">
                                    <div class="w-16 h-16 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Jenis Nilai</h3>
                                    <p class="text-gray-600 mb-4">Anda belum mengatur jenis nilai untuk semua mata pelajaran yang Anda ajar.</p>
                                    <p class="text-sm text-gray-500">Silakan tutup modal ini dan klik "Atur Jenis Nilai" terlebih dahulu.</p>
                                </div>
                                
                                <!-- Grid responsive untuk cards -->
                                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                                    <div v-for="jenis in allJenisNilai" :key="jenis.id"
                                         @click="selectJenisNilai(jenis.id)"
                                         :class="getModalJenisClass(jenis.id)"
                                         class="group p-4 border-2 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 active:scale-95 relative">
                                        <!-- Status Badge -->
                                        <div v-if="getModalStatusBadge(jenis.id)" 
                                             :class="getModalStatusBadge(jenis.id).class"
                                             class="absolute -top-2 -right-2 px-2 py-1 rounded-full text-xs font-medium shadow-lg">
                                            {{ getModalStatusBadge(jenis.id).text }}
                                        </div>
                                        
                                        <div class="flex flex-col space-y-3">
                                            <!-- Header dengan icon dan nama -->
                                            <div class="flex items-center space-x-3">
                                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                                    <span class="text-white font-bold text-lg">
                                                        {{ getJenisNilaiIcon(jenis.nama) }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-semibold text-gray-900 text-base group-hover:text-blue-700 truncate">
                                                        {{ jenis.nama }}
                                                    </h4>
                                                    <p class="text-xs text-gray-500 truncate">Jenis Penilaian</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Info grid -->
                                            <div class="space-y-2">
                                                <!-- Bobot -->
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm text-gray-600">Bobot:</span>
                                                    <span class="font-semibold text-blue-600">{{ jenis.bobot }}%</span>
                                                </div>
                                                
                                                <!-- Status aktif/nonaktif -->
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-gray-500">Status:</span>
                                                    <span :class="[
                                                        'px-2 py-1 rounded-full text-xs font-medium',
                                                        jenis.status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
                                                    ]">
                                                        {{ jenis.status ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Progress Status untuk jenis nilai ini - selalu tampilkan -->
                                                <div class="space-y-2">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs text-gray-500">Progress:</span>
                                                        <span :class="getModalStatusDetail(jenis.id).statusClass"
                                                              class="px-2 py-1 rounded-full text-xs font-medium">
                                                            {{ getModalStatusDetail(jenis.id).statusText }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Progress stats -->
                                                    <div class="text-xs text-gray-600 grid grid-cols-3 gap-1 text-center">
                                                        <div class="bg-green-50 rounded px-1 py-1">
                                                            <div class="font-medium text-green-700">{{ getModalStatusDetail(jenis.id).final_count || 0 }}</div>
                                                            <div class="text-green-600">Final</div>
                                                        </div>
                                                        <div class="bg-yellow-50 rounded px-1 py-1">
                                                            <div class="font-medium text-yellow-700">{{ getModalStatusDetail(jenis.id).draft_count || 0 }}</div>
                                                            <div class="text-yellow-600">Draft</div>
                                                        </div>
                                                        <div class="bg-gray-50 rounded px-1 py-1">
                                                            <div class="font-medium text-gray-700">{{ getModalStatusDetail(jenis.id).belum_dinilai || 0 }}</div>
                                                            <div class="text-gray-600">Belum</div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Progress bar -->
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div :class="getModalStatusDetail(jenis.id).progressClass"
                                                             class="h-2 rounded-full transition-all duration-300"
                                                             :style="{ width: getModalStatusDetail(jenis.id).progress + '%' }"></div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Deskripsi -->
                                                <div v-if="jenis.deskripsi" class="text-xs text-gray-500 italic bg-gray-50 rounded p-2">
                                                    {{ jenis.deskripsi }}
                                                </div>
                                            </div>
                                            
                                            <!-- Action indicator -->
                                            <div class="flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pt-2">
                                                <div class="flex items-center space-x-2 text-blue-600 text-sm font-medium">
                                                    <span>Pilih untuk Input</span>
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Info tambahan -->
                                <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-200">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-blue-900 mb-2">💡 Tips Input Nilai</h4>
                                            <ul class="text-sm text-blue-700 space-y-1">
                                                <li class="flex items-start">
                                                    <span class="text-blue-500 mr-2">•</span>
                                                    <span>Tap salah satu jenis nilai di atas untuk mulai input</span>
                                                </li>
                                                <li class="flex items-start">
                                                    <span class="text-blue-500 mr-2">•</span>
                                                    <span>Bobot menunjukkan kontribusi nilai terhadap nilai akhir</span>
                                                </li>
                                                <li class="flex items-start">
                                                    <span class="text-blue-500 mr-2">•</span>
                                                    <span>Pastikan semua siswa sudah mendapat nilai sebelum finalisasi</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="border-t border-gray-200 p-4 sm:p-6 bg-gray-50">
                                <div class="flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0">
                                    <button @click="closeInputModal"
                                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:px-4 sm:py-2 border border-gray-300 rounded-lg text-base sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                        <svg class="w-5 h-5 mr-2 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal untuk Pengaturan Jenis Nilai -->
                <div v-if="showSettingsModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 py-6 sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="closeSettingsModal"></div>
                        
                        <!-- Modal panel -->
                        <div class="relative bg-white rounded-xl shadow-2xl transform transition-all w-full max-w-5xl mx-4 max-h-[95vh] overflow-hidden">
                            <!-- Header -->
                            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                                <div>
                                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900">⚙️ Pengaturan Jenis Nilai Global</h3>
                                    <p class="text-sm text-gray-600 mt-1">Atur jenis penilaian dan bobot yang akan berlaku untuk <strong>semua mata pelajaran dan kelas</strong> yang Anda ajar</p>
                                </div>
                                <button @click="closeSettingsModal" 
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-full transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Content -->
                            <div class="overflow-y-auto max-h-[calc(95vh-200px)] p-4 sm:p-6">
                                <!-- Warning bobot total -->
                                <div v-if="totalBobot !== 100" class="mb-6 p-4 rounded-lg border-2" 
                                     :class="totalBobot > 100 ? 'border-red-300 bg-red-50' : 'border-yellow-300 bg-yellow-50'">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 mt-0.5" :class="totalBobot > 100 ? 'text-red-400' : 'text-yellow-400'" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="font-medium" :class="totalBobot > 100 ? 'text-red-800' : 'text-yellow-800'">
                                                Total Bobot: {{ totalBobot }}%
                                            </h4>
                                            <p class="text-sm mt-1" :class="totalBobot > 100 ? 'text-red-700' : 'text-yellow-700'">
                                                {{ totalBobot > 100 ? 'Total bobot melebihi 100%. Silakan sesuaikan!' : 'Total bobot belum mencapai 100%. Pastikan total bobot = 100%' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form tambah jenis nilai baru -->
                                <div class="mb-6 p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Tambah Jenis Nilai Baru
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jenis Nilai</label>
                                            <input v-model="newJenisNilai.nama" 
                                                   type="text" 
                                                   placeholder="e.g. Tugas Harian"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Bobot (%)</label>
                                            <input v-model.number="newJenisNilai.bobot" 
                                                   type="number" 
                                                   min="1" 
                                                   max="100"
                                                   placeholder="e.g. 30"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div class="flex items-end">
                                            <button @click="addJenisNilai" 
                                                    :disabled="!newJenisNilai.nama || !newJenisNilai.bobot || isAddingJenisNilai"
                                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                                                <svg v-if="isAddingJenisNilai" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                {{ isAddingJenisNilai ? 'Menambah...' : 'Tambah' }}
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="newJenisNilai.deskripsi !== undefined" class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                                        <textarea v-model="newJenisNilai.deskripsi" 
                                                  placeholder="Deskripsi singkat tentang jenis nilai ini..."
                                                  rows="2"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    </div>
                                </div>

                                <!-- Daftar jenis nilai yang sudah ada -->
                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        Jenis Nilai Saat Ini ({{ customJenisNilai.length }})
                                    </h4>
                                    
                                    <div v-if="customJenisNilai.length === 0" class="text-center py-8 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p>Belum ada jenis nilai yang diatur.</p>
                                        <p class="text-sm mt-1">Tambahkan jenis nilai pertama untuk memulai!</p>
                                    </div>

                                    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <div v-for="(jenis, index) in customJenisNilai" :key="jenis.id || index"
                                             class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-white">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-100 text-blue-600">
                                                        <span class="text-lg">📋</span>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-semibold text-gray-900">{{ jenis.nama }}</h5>
                                                        <p class="text-sm text-gray-600">Jenis Penilaian</p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-lg font-bold text-blue-600">{{ jenis.bobot }}%</div>
                                                    <div class="text-xs text-gray-500">Bobot</div>
                                                </div>
                                            </div>
                                            
                                            <div v-if="editingIndex === index" class="space-y-3">
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama</label>
                                                        <input v-model="editingJenis.nama" 
                                                               type="text"
                                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Bobot (%)</label>
                                                        <input v-model.number="editingJenis.bobot" 
                                                               type="number" 
                                                               min="1" 
                                                               max="100"
                                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button @click="saveEdit(index)" 
                                                            :disabled="isSavingEdit"
                                                            class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 disabled:bg-gray-400">
                                                        {{ isSavingEdit ? 'Menyimpan...' : 'Simpan' }}
                                                    </button>
                                                    <button @click="cancelEdit" 
                                                            class="flex-1 px-3 py-2 bg-gray-500 text-white text-sm rounded-lg hover:bg-gray-600">
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div v-else class="flex items-center justify-between">
                                                <div v-if="jenis.deskripsi" class="text-sm text-gray-600 italic">
                                                    {{ jenis.deskripsi }}
                                                </div>
                                                <div class="flex space-x-2 ml-auto">
                                                    <button @click="editJenis(index)" 
                                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                    <button @click="deleteJenis(index)" 
                                                            :disabled="isDeletingJenis"
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="border-t border-gray-200 p-4 sm:p-6 bg-gray-50">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                                    <div class="text-sm text-gray-600">
                                        Total Bobot: <span class="font-semibold" :class="totalBobot === 100 ? 'text-green-600' : totalBobot > 100 ? 'text-red-600' : 'text-yellow-600'">{{ totalBobot }}%</span>
                                    </div>
                                    <div class="flex space-x-3">
                                        <button @click="closeSettingsModal"
                                                class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            Tutup
                                        </button>
                                        <button v-if="totalBobot === 100" 
                                                @click="saveAllSettings"
                                                class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                            Konfirmasi Pengaturan
                                        </button>
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
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    progressNilai: Array,
    jenisNilai: Array
})

// Create reactive copy of progressNilai for updates
const reactiveProgressNilai = ref([])

const showInputModal = ref(false)
const selectedMataPelajaran = ref(null)
const selectedKelas = ref(null)

// State untuk pengaturan jenis nilai
const showSettingsModal = ref(false)
const customJenisNilai = ref([])
const newJenisNilai = ref({
    nama: '',
    bobot: null,
    deskripsi: ''
})

// State untuk editing
const editingIndex = ref(-1)
const editingJenis = ref({})

// Loading states
const isAddingJenisNilai = ref(false)
const isSavingEdit = ref(false)
const isDeletingJenis = ref(false)
const isRefreshing = ref(false)

const totalKelas = computed(() => {
    return reactiveProgressNilai.value.reduce((total, progress) => total + progress.total_kelas, 0)
})

const totalJenisNilaiCustom = computed(() => {
    return customJenisNilai.value.length
})

const totalBobot = computed(() => {
    return customJenisNilai.value.reduce((total, jenis) => {
        const bobot = Number(jenis.bobot) || 0;
        return total + bobot;
    }, 0);
})

// Computed property untuk mendapatkan jenis nilai custom untuk modal yang sedang aktif
const allJenisNilai = computed(() => {
    // Tampilkan semua jenis nilai custom yang dibuat oleh guru yang sedang login
    // (berlaku untuk semua mata pelajaran dan kelas yang diajar)
    return customJenisNilai.value;
})

// Function untuk mendapatkan jenis nilai untuk mata pelajaran dan kelas tertentu
const getJenisNilaiForMataPelajaranKelas = (mataPelajaranId, kelasId) => {
    // Tampilkan semua jenis nilai custom guru (berlaku untuk semua mata pelajaran dan kelas)
    return customJenisNilai.value;
}

const getOverallProgress = (kelasDetail) => {
    if (kelasDetail.length === 0) return 0
    const totalProgress = kelasDetail.reduce((sum, kelas) => sum + kelas.progress_persen, 0)
    return totalProgress / kelasDetail.length
}

const getProgressBadgeClass = (progress) => {
    if (progress >= 80) return 'bg-green-100 text-green-800'
    if (progress >= 50) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}

const getJenisNilaiIcon = (namaJenis) => {
    const nama = namaJenis.toLowerCase()
    if (nama.includes('tugas') || nama.includes('harian')) return '📝'
    if (nama.includes('uts') || nama.includes('tengah')) return '📊'
    if (nama.includes('uas') || nama.includes('akhir')) return '🎯'
    if (nama.includes('quiz') || nama.includes('kuis')) return '❓'
    if (nama.includes('praktik') || nama.includes('praktek')) return '🔬'
    if (nama.includes('project') || nama.includes('proyek')) return '🚀'
    return '📋'
}

const getJenisNilaiProgress = (jenisNilaiId) => {
    // This would typically come from the backend data
    // For now, return null to hide progress bar
    return null
}

// Function to get completed jenis nilai count for a class
const getCompletedJenisNilaiCount = (kelas, totalJenisNilai) => {
    if (!kelas.status_jenis_nilai || kelas.status_jenis_nilai.length === 0) {
        // Fallback: estimate based on progress percentage
        return Math.floor((kelas.progress_persen / 100) * totalJenisNilai)
    }
    
    // Count jenis nilai that are fully completed (all students have final grades)
    return kelas.status_jenis_nilai.filter(status => status.is_all_final).length
}

const getStatusIndicator = (kelas, jenisNilaiId) => {
    // Jika tidak ada status_jenis_nilai atau kosong, gunakan fallback
    if (!kelas.status_jenis_nilai || kelas.status_jenis_nilai.length === 0) {
        // Gunakan progress_persen sebagai fallback sementara
        if (kelas.progress_persen && kelas.progress_persen > 0) {
            if (kelas.progress_persen >= 100) {
                return { icon: '✓', class: 'bg-green-500 text-white' }
            } else {
                return { icon: '~', class: 'bg-yellow-500 text-white' }
            }
        }
        return null;
    }
    
    const status = kelas.status_jenis_nilai.find(s => s.jenis_nilai_id === jenisNilaiId)
    if (!status) return null;
    
    if (status.is_all_final) {
        return { icon: '✓', class: 'bg-green-500 text-white' }
    } else if (status.total_dinilai > 0) {
        return { icon: '~', class: 'bg-yellow-500 text-white' }
    }
    
    return null
}

const getButtonClass = (kelas, jenisNilaiId) => {
    if (!kelas.status_jenis_nilai || kelas.status_jenis_nilai.length === 0) {
        // Gunakan progress_persen sebagai fallback
        if (kelas.progress_persen && kelas.progress_persen > 0) {
            if (kelas.progress_persen >= 100) {
                return 'bg-green-50 text-green-700 hover:bg-green-100 border-green-200'
            } else {
                return 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100 border-yellow-200'
            }
        }
        return 'bg-blue-50 text-blue-600 hover:bg-blue-100'
    }
    
    const status = kelas.status_jenis_nilai.find(s => s.jenis_nilai_id === jenisNilaiId)
    if (!status) {
        return 'bg-blue-50 text-blue-600 hover:bg-blue-100'
    }
    
    if (status.is_all_final) {
        return 'bg-green-50 text-green-700 hover:bg-green-100 border-green-200'
    } else if (status.total_dinilai > 0) {
        return 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100 border-yellow-200'
    } else {
        return 'bg-blue-50 text-blue-600 hover:bg-blue-100'
    }
}

const getStatusTooltip = (kelas, jenisNilaiId) => {
    if (!kelas.status_jenis_nilai || kelas.status_jenis_nilai.length === 0) {
        const jenis = customJenisNilai.value.find(j => j.id === jenisNilaiId)
        return `Input ${jenis?.nama || 'Nilai'}`
    }
    
    const status = kelas.status_jenis_nilai.find(s => s.jenis_nilai_id === jenisNilaiId)
    if (!status) {
        const jenis = customJenisNilai.value.find(j => j.id === jenisNilaiId)
        return `Input ${jenis?.nama || 'Nilai'}`
    }
    
    if (status.is_all_final) {
        return `${status.jenis_nilai_nama}: Semua Final (${status.final_count}/${status.total_siswa})`
    } else if (status.total_dinilai > 0) {
        return `${status.jenis_nilai_nama}: ${status.final_count} Final, ${status.draft_count} Draft, ${status.belum_dinilai} Belum`
    } else {
        return `${status.jenis_nilai_nama}: Belum ada nilai (${status.belum_dinilai}/${status.total_siswa})`
    }
}

// Methods untuk modal
const getModalJenisClass = (jenisNilaiId) => {
    if (!selectedKelas.value) {
        return 'border-gray-200'
    }
    
    const kelasDetail = getSelectedKelasDetail()
    if (!kelasDetail || !kelasDetail.status_jenis_nilai) {
        return 'border-gray-200'
    }
    
    const status = kelasDetail.status_jenis_nilai.find(s => s.jenis_nilai_id === jenisNilaiId)
    if (!status) {
        return 'border-gray-200'
    }
    
    if (status.is_all_final) {
        return 'border-green-300 bg-green-50'
    } else if (status.total_dinilai > 0) {
        return 'border-yellow-300 bg-yellow-50'
    } else {
        return 'border-gray-200'
    }
}

const getModalStatusBadge = (jenisNilaiId) => {
    if (!selectedKelas.value || !selectedMataPelajaran.value) {
        return { 
            text: '📋 Siap Input', 
            class: 'bg-blue-500 text-white' 
        }
    }
    
    const kelasDetail = getSelectedKelasDetail()
    if (!kelasDetail) {
        return { 
            text: '❌ Tidak Ditemukan', 
            class: 'bg-gray-400 text-white' 
        }
    }
    
    const status = kelasDetail.status_jenis_nilai?.find(s => s.jenis_nilai_id === jenisNilaiId)
    if (!status) {
        // Jika tidak ada status untuk jenis nilai ini di kelas yang dipilih, tampilkan status default
        return { 
            text: '○ Belum', 
            class: 'bg-gray-400 text-white' 
        }
    }
    
    if (status.is_all_final) {
        return { 
            text: '✓ Selesai', 
            class: 'bg-green-500 text-white' 
        }
    } else if (status.total_dinilai > 0) {
        return { 
            text: '⚠ Sebagian', 
            class: 'bg-yellow-500 text-white' 
        }
    } else {
        return { 
            text: '○ Belum', 
            class: 'bg-gray-400 text-white' 
        }
    }
}

const getModalStatusDetail = (jenisNilaiId) => {
    // Jika tidak ada kelas atau mata pelajaran yang dipilih
    if (!selectedKelas.value || !selectedMataPelajaran.value) {
        return {
            jenis_nilai_id: jenisNilaiId,
            total_siswa: 0,
            total_dinilai: 0,
            final_count: 0,
            draft_count: 0,
            belum_dinilai: 0,
            is_all_final: false,
            progress: 0,
            statusText: 'Pilih untuk Input',
            statusClass: 'bg-blue-100 text-blue-800',
            progressClass: 'bg-blue-400'
        }
    }
    
    const kelasDetail = getSelectedKelasDetail()
    
    if (!kelasDetail) {
        return {
            jenis_nilai_id: jenisNilaiId,
            total_siswa: 0,
            total_dinilai: 0,
            final_count: 0,
            draft_count: 0,
            belum_dinilai: 0,
            is_all_final: false,
            progress: 0,
            statusText: 'Data Tidak Ditemukan',
            statusClass: 'bg-gray-100 text-gray-800',
            progressClass: 'bg-gray-400'
        }
    }
    
    const status = kelasDetail.status_jenis_nilai?.find(s => s.jenis_nilai_id === jenisNilaiId)
    
    if (!status) {
        // Jika tidak ada status untuk jenis nilai ini di kelas yang dipilih, buat status default
        return {
            jenis_nilai_id: jenisNilaiId,
            total_siswa: kelasDetail.total_siswa || 0,
            total_dinilai: 0,
            final_count: 0,
            draft_count: 0,
            belum_dinilai: kelasDetail.total_siswa || 0,
            is_all_final: false,
            progress: 0,
            statusText: 'Belum Dinilai',
            statusClass: 'bg-gray-100 text-gray-800',
            progressClass: 'bg-gray-400'
        }
    }
    
    const progress = status.total_siswa > 0 ? (status.total_dinilai / status.total_siswa) * 100 : 0
    
    let statusText, statusClass, progressClass
    
    if (status.is_all_final) {
        statusText = 'Semua Final'
        statusClass = 'bg-green-100 text-green-800'
        progressClass = 'bg-green-500'
    } else if (status.total_dinilai > 0) {
        statusText = 'Sebagian Dinilai'
        statusClass = 'bg-yellow-100 text-yellow-800'
        progressClass = 'bg-yellow-500'
    } else {
        statusText = 'Belum Dinilai'
        statusClass = 'bg-gray-100 text-gray-800'
        progressClass = 'bg-gray-400'
    }
    
    const result = {
        ...status,
        progress: Math.round(progress),
        statusText,
        statusClass,
        progressClass
    };
    
    return result;
}

const getSelectedKelasDetail = () => {
    if (!selectedMataPelajaran.value || !selectedKelas.value) return null
    
    const mataPelajaran = reactiveProgressNilai.value.find(p => p.mata_pelajaran.id === selectedMataPelajaran.value)
    if (!mataPelajaran) return null
    
    const kelasDetail = mataPelajaran.kelas_detail.find(k => k.kelas.id === selectedKelas.value)
    return kelasDetail || null
}

const inputNilai = (mataPelajaranId, kelasId, jenisNilaiId) => {
    router.get(route('nilai-siswa.create'), {
        mata_pelajaran_id: mataPelajaranId,
        kelas_id: kelasId,
        jenis_nilai_id: jenisNilaiId
    })
}

const lihatDetail = (mataPelajaranId, kelasId) => {
    router.get(route('nilai-siswa.show'), {
        mata_pelajaran_id: mataPelajaranId,
        kelas_id: kelasId
    })
}

const inputCustom = (mataPelajaranId, kelasId) => {
    selectedMataPelajaran.value = mataPelajaranId
    selectedKelas.value = kelasId
    
    // Load custom jenis nilai untuk modal input agar data terbaru
    loadCustomJenisNilai()
    
    showInputModal.value = true
}

const selectJenisNilai = (jenisNilaiId) => {
    inputNilai(selectedMataPelajaran.value, selectedKelas.value, jenisNilaiId)
    closeInputModal()
}

const closeInputModal = () => {
    showInputModal.value = false
    selectedMataPelajaran.value = null
    selectedKelas.value = null
}

// Methods untuk pengaturan jenis nilai
const openSettingsModal = (mataPelajaranId, kelasId) => {
    selectedMataPelajaran.value = mataPelajaranId
    selectedKelas.value = kelasId
    loadCustomJenisNilai()  // Load semua jenis nilai guru, tidak spesifik mata pelajaran/kelas
    showSettingsModal.value = true
}

// Method untuk membuka modal pengaturan utama (tanpa parameter mata pelajaran/kelas)
const openMainSettingsModal = () => {
    selectedMataPelajaran.value = null
    selectedKelas.value = null
    loadCustomJenisNilai()  // Load semua jenis nilai guru
    showSettingsModal.value = true
}

const closeSettingsModal = () => {
    showSettingsModal.value = false
    selectedMataPelajaran.value = null
    selectedKelas.value = null
    // Tidak perlu reset customJenisNilai karena sekarang global untuk guru
    resetNewJenisNilai()
    cancelEdit()
}

const loadCustomJenisNilai = async () => {
    try {
        // Load jenis nilai custom milik guru yang sedang login (tanpa filter mata pelajaran/kelas)
        const response = await axios.get('/api/jenis-nilai');
        
        // Perbaiki nama field dari API response dan pastikan bobot adalah number
        const jenisNilaiData = response.data.jenisNilai || [];
        
        // Filter hanya jenis nilai custom (yang punya guru_id, bukan global)
        const customOnly = jenisNilaiData.filter(jenis => jenis.guru_id !== null);
        
        customJenisNilai.value = customOnly.map(jenis => ({
            ...jenis,
            bobot: Number(jenis.bobot) || 0  // Pastikan bobot adalah number
        }));
        
    } catch (error) {
        console.error('Error loading jenis nilai:', error);
        // Fallback ke data props jika API gagal
        if (props.jenisNilai && props.jenisNilai.length > 0) {
            customJenisNilai.value = props.jenisNilai.map(jenis => ({
                ...jenis,
                bobot: Number(jenis.bobot) || 0
            }));
        } else {
            customJenisNilai.value = [];
        }
    }
}

// Method untuk memuat semua custom jenis nilai guru yang sedang login
const loadAllCustomJenisNilai = async () => {
    try {
        // Load semua jenis nilai custom milik guru yang sedang login
        const response = await axios.get('/api/jenis-nilai');
        
        const jenisNilaiData = response.data.jenisNilai || [];
        const customOnly = jenisNilaiData.filter(jenis => jenis.guru_id !== null);
        
        customJenisNilai.value = customOnly.map(jenis => ({
            ...jenis,
            bobot: Number(jenis.bobot) || 0
        }));
        
    } catch (error) {
        console.error('Error loading all custom jenis nilai:', error);
        // Fallback ke data props jika API gagal
        if (props.jenisNilai && props.jenisNilai.length > 0) {
            customJenisNilai.value = props.jenisNilai.map(jenis => ({
                ...jenis,
                bobot: Number(jenis.bobot) || 0
            }));
        } else {
            customJenisNilai.value = [];
        }
    }
}

const resetNewJenisNilai = () => {
    newJenisNilai.value = {
        nama: '',
        bobot: null,
        deskripsi: ''
    }
}

const addJenisNilai = async () => {
    if (!newJenisNilai.value.nama || !newJenisNilai.value.bobot) return
    
    // Validasi bobot
    const bobotNumber = Number(newJenisNilai.value.bobot);
    if (isNaN(bobotNumber) || bobotNumber <= 0 || bobotNumber > 100) {
        alert('Bobot harus berupa angka antara 1-100');
        return;
    }
    
    isAddingJenisNilai.value = true
    
    try {
        const jenisData = {
            nama: newJenisNilai.value.nama,
            bobot: bobotNumber,  // Pastikan mengirim sebagai number
            deskripsi: newJenisNilai.value.deskripsi
            // Hapus mata_pelajaran_id dan kelas_id karena sekarang jenis nilai berlaku untuk semua
        };
        
        const response = await axios.post('/api/jenis-nilai', jenisData);
        
        if (response.data.success) {
            // Tambah ke local state - pastikan menggunakan field yang benar
            const newJenisNilaiData = response.data.jenis_nilai || response.data.jenisNilai;
            // Pastikan bobot adalah number
            const jenisToAdd = {
                ...newJenisNilaiData,
                bobot: Number(newJenisNilaiData.bobot) || 0
            };
            customJenisNilai.value.push(jenisToAdd);
            resetNewJenisNilai();
            
            // Reload all custom jenis nilai untuk memastikan sinkronisasi dengan dashboard
            await loadAllCustomJenisNilai();
        } else {
            throw new Error(response.data.message || 'Gagal menambah jenis nilai');
        }
        
    } catch (error) {
        console.error('Error adding jenis nilai:', error);
        
        const message = error.response?.data?.message || 
                       error.response?.data?.error ||
                       error.message || 
                       'Gagal menambah jenis nilai';
        alert(`Error: ${message}`);
    } finally {
        isAddingJenisNilai.value = false;
    }
}

const editJenis = (index) => {
    editingIndex.value = index
    editingJenis.value = { 
        ...customJenisNilai.value[index],
        bobot: Number(customJenisNilai.value[index].bobot) || 0  // Pastikan bobot adalah number
    }
}

const saveEdit = async (index) => {
    if (!editingJenis.value.nama || !editingJenis.value.bobot) return
    
    // Validasi bobot
    const bobotNumber = Number(editingJenis.value.bobot);
    if (isNaN(bobotNumber) || bobotNumber <= 0 || bobotNumber > 100) {
        alert('Bobot harus berupa angka antara 1-100');
        return;
    }
    
    isSavingEdit.value = true
    
    try {
        // Pastikan bobot adalah number sebelum dikirim
        const dataToSend = {
            ...editingJenis.value,
            bobot: bobotNumber
        };
        
        const response = await axios.put(`/api/jenis-nilai/${editingJenis.value.id}`, dataToSend);
        
        if (response.data.success) {
            // Pastikan data yang disimpan ke state juga memiliki bobot sebagai number
            const updatedJenis = {
                ...response.data.jenis_nilai,
                bobot: Number(response.data.jenis_nilai.bobot) || 0
            };
            customJenisNilai.value[index] = updatedJenis;
            cancelEdit();
        } else {
            throw new Error(response.data.message || 'Gagal menyimpan perubahan');
        }
        
    } catch (error) {
        console.error('Error saving edit:', error);
        const message = error.response?.data?.message || error.message || 'Gagal menyimpan perubahan';
        alert(message);
    } finally {
        isSavingEdit.value = false;
    }
}

const cancelEdit = () => {
    editingIndex.value = -1
    editingJenis.value = {}
}

const deleteJenis = async (index) => {
    const jenisNilai = customJenisNilai.value[index];
    
    if (!confirm(`Apakah Anda yakin ingin menghapus jenis nilai "${jenisNilai.nama}"?`)) {
        return;
    }
    
    isDeletingJenis.value = true;
    
    try {
        const response = await axios.delete(`/api/jenis-nilai/${jenisNilai.id}`);
        
        if (response.status === 200 && response.data.success) {
            // Hapus dari array lokal setelah berhasil di backend
            customJenisNilai.value.splice(index, 1);
            
            // Reload all custom jenis nilai untuk memastikan sinkronisasi dengan dashboard
            await loadAllCustomJenisNilai();
            
            // Tampilkan pesan sukses
            alert('Jenis nilai berhasil dihapus');
            
        } else {
            throw new Error(response.data.message || 'Gagal menghapus jenis nilai');
        }
        
    } catch (error) {
        console.error('Error deleting jenis nilai:', error);
        
        const message = error.response?.data?.message || 
                       error.response?.data?.error || 
                       error.message || 
                       'Gagal menghapus jenis nilai';
        alert(`Error: ${message}`);
        
        // Reload data jika terjadi error untuk memastikan konsistensi
        await loadCustomJenisNilai();
        
    } finally {
        isDeletingJenis.value = false;
    }
}

const saveAllSettings = async () => {
    if (totalBobot.value !== 100) {
        alert('Total bobot harus 100% sebelum dapat disimpan!')
        return
    }
    
    // Karena sekarang menggunakan sistem teacher-global, 
    // jenis nilai sudah tersimpan otomatis saat ditambah/diubah
    alert('Pengaturan jenis nilai berhasil disimpan dan akan berlaku untuk semua mata pelajaran yang Anda ajar!');
    closeSettingsModal();
    
    // Refresh data dashboard untuk memperbarui tampilan
    window.location.reload();
}

const getSelectedMataPelajaranName = () => {
    if (!selectedMataPelajaran.value) return ''
    const mataPelajaran = reactiveProgressNilai.value.find(p => p.mata_pelajaran.id === selectedMataPelajaran.value)
    return mataPelajaran ? mataPelajaran.mata_pelajaran.nama_mapel : ''
}

// Function to load status nilai for all classes
const loadAllStatusNilai = async () => {
    try {
        const response = await axios.get('/api/status-nilai-dashboard');
        
        if (response.data.success) {
            // Update reactiveProgressNilai dengan status data yang baru
            const statusData = response.data.data;
            
            // Merge status data ke dalam reactiveProgressNilai yang sudah ada
            reactiveProgressNilai.value.forEach((progress, progressIndex) => {
                const matchingStatus = statusData.find(status => 
                    status.mata_pelajaran_id === progress.mata_pelajaran.id
                );
                
                if (matchingStatus) {
                    progress.kelas_detail.forEach((kelasDetail, kelasIndex) => {
                        const matchingKelas = matchingStatus.kelas_detail.find(kelas => 
                            kelas.kelas_id === kelasDetail.kelas.id
                        );
                        
                        if (matchingKelas) {
                            // Update status_jenis_nilai dengan data terbaru dari API
                            kelasDetail.status_jenis_nilai = matchingKelas.status_jenis_nilai;
                            kelasDetail.progress_persen = matchingKelas.progress_persen;
                            kelasDetail.nilai_selesai = matchingKelas.nilai_selesai;
                        }
                    });
                }
            });
        }
        
    } catch (error) {
        console.error('Error loading status nilai:', error);
        // Fallback: tetap gunakan data yang ada dari props
    }
}

// Function to refresh status data manually
const refreshStatusData = async () => {
    isRefreshing.value = true;
    try {
        await loadAllStatusNilai();
    } catch (error) {
        console.error('Error during refresh:', error);
        alert('Gagal memuat data terbaru. Silakan coba lagi.');
    } finally {
        isRefreshing.value = false;
    }
}

// Load semua custom jenis nilai saat component dimount
onMounted(() => {
    // Initialize reactive progress data with deep copy of props
    reactiveProgressNilai.value = JSON.parse(JSON.stringify(props.progressNilai || []));
    
    // Muat data jenis nilai dari props yang dikirim dari backend
    if (props.jenisNilai && props.jenisNilai.length > 0) {
        customJenisNilai.value = props.jenisNilai.map(jenis => ({
            ...jenis,
            bobot: Number(jenis.bobot) || 0
        }));
    } else {
        // Jika tidak ada data dari props, coba load dari API
        loadAllCustomJenisNilai();
    }
    
    // Load status nilai untuk setiap kelas
    loadAllStatusNilai();
})
</script>

<style scoped>
.transition-all {
    transition: all 0.3s ease;
}
</style>
