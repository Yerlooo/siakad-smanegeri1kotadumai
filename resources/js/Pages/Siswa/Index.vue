<template>
    <Head title="Data Siswa" />

    <AppLayout page-title="Data Siswa">
        <div class="space-y-6">
            <!-- Header dengan tombol tambah -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Data Siswa</h1>
                    <p class="text-gray-600">Kelola data siswa sekolah</p>
                    <!-- Total Siswa Info -->
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ statistics?.hasFilter ? 'Hasil Filter' : 'Total' }}: {{ totalSiswa }} siswa
                            <span v-if="statistics?.hasFilter" class="ml-2 text-gray-400">
                                (dari {{ statistics?.globalTotalSiswa }} total siswa)
                            </span>
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                            </svg>
                            Kelas: {{ totalKelas }} kelas
                        </span>
                        <span v-if="siswaAktif" class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Aktif: {{ siswaAktif }} siswa
                        </span>
                    </div>
                </div>
                <Link v-if="canModify" :href="route('siswa.create')" 
                      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Siswa</span>
                </Link>
            </div>

            <!-- Search dan Filter -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-4 lg:space-y-0 lg:space-x-4">
                    <div class="flex-1">
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Cari nama siswa, NIS, atau email..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    <select 
                        v-model="statusFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="lulus">Lulus</option>
                        <option value="pindah">Pindah</option>
                        <option value="keluar">Keluar</option>
                    </select>
                    <select 
                        v-model="kelasFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        @change="handleKelasFilterChange"
                    >
                        <option value="">Semua Kelas</option>
                        <option v-for="kelas in uniqueKelas" :key="kelas.id" :value="kelas.id">
                            {{ kelas.nama_kelas }}
                        </option>
                    </select>
                    <button 
                        @click="resetFilters"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center space-x-2"
                        title="Reset Filter"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Reset</span>
                        <span v-if="search || statusFilter || kelasFilter" class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full">
                            {{ (search ? 1 : 0) + (statusFilter ? 1 : 0) + (kelasFilter ? 1 : 0) }}
                        </span>
                    </button>
                </div>
                
                <!-- Info hasil filter -->
                <div v-if="search || statusFilter || kelasFilter" class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex flex-wrap items-center justify-between">
                        <p class="text-sm text-gray-600">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mr-2">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 10.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                                </svg>
                                Filter Aktif
                            </span>
                            Menampilkan <span class="font-medium text-blue-600">{{ filteredTotal }}</span> siswa
                            <span class="text-gray-400 block sm:inline">
                                (halaman {{ siswa.current_page }} dari {{ siswa.last_page }})
                            </span>
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                            <span v-if="search" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                                "{{ search }}"
                            </span>
                            <span v-if="statusFilter" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ getStatusText(statusFilter) }}
                            </span>
                            <span v-if="kelasFilter" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                {{ getSelectedKelasName() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Siswa -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" :class="statistics?.hasFilter ? 'border-blue-300 bg-blue-50' : ''">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="statistics?.hasFilter ? 'bg-blue-200' : 'bg-blue-100'">
                                <svg class="w-5 h-5" :class="statistics?.hasFilter ? 'text-blue-700' : 'text-blue-600'" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">
                                {{ statistics?.hasFilter ? 'Hasil Filter' : 'Total Siswa' }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900">{{ totalSiswa }}</p>
                            <p v-if="statistics?.hasFilter" class="text-xs text-gray-500 mt-1">
                                dari {{ statistics?.globalTotalSiswa }} total siswa
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Siswa Aktif -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Siswa Aktif</p>
                            <p class="text-2xl font-bold text-green-600">{{ siswaAktif }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Kelas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Kelas</p>
                            <p class="text-2xl font-bold text-purple-600">{{ totalKelas }}</p>
                        </div>
                    </div>
                </div>

                <!-- Rata-rata Siswa per Kelas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Rata-rata/Kelas</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ Math.round(totalSiswa / totalKelas) || 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Breakdown Status Siswa -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" :class="statistics?.hasFilter ? 'border-blue-300 bg-blue-50' : ''">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ statistics?.hasFilter ? 'Breakdown Status (Hasil Filter)' : 'Breakdown Status Siswa' }}
                    </h3>
                    <div class="flex items-center space-x-2">
                        <span v-if="statistics?.hasFilter" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 10.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                            </svg>
                            Filter Aktif
                        </span>
                        <button 
                            @click="showStatistics = !showStatistics"
                            class="flex items-center text-sm text-gray-500 hover:text-gray-700"
                        >
                            <span>{{ showStatistics ? 'Sembunyikan' : 'Tampilkan' }}</span>
                            <svg 
                                class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': showStatistics }"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div v-show="showStatistics" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="text-2xl font-bold text-green-600">{{ siswaAktif }}</div>
                        <div class="text-sm text-green-600 font-medium">Aktif</div>
                        <div class="text-xs text-gray-500 mt-1">{{ totalSiswa > 0 ? Math.round((siswaAktif / totalSiswa) * 100) : 0 }}% dari total</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="text-2xl font-bold text-blue-600">{{ siswaLulus }}</div>
                        <div class="text-sm text-blue-600 font-medium">Lulus</div>
                        <div class="text-xs text-gray-500 mt-1">{{ totalSiswa > 0 ? Math.round((siswaLulus / totalSiswa) * 100) : 0 }}% dari total</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="text-2xl font-bold text-yellow-600">{{ siswaPindah }}</div>
                        <div class="text-sm text-yellow-600 font-medium">Pindah</div>
                        <div class="text-xs text-gray-500 mt-1">{{ totalSiswa > 0 ? Math.round((siswaPindah / totalSiswa) * 100) : 0 }}% dari total</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
                        <div class="text-2xl font-bold text-red-600">{{ siswaKeluar }}</div>
                        <div class="text-sm text-red-600 font-medium">Keluar</div>
                        <div class="text-xs text-gray-500 mt-1">{{ totalSiswa > 0 ? Math.round((siswaKeluar / totalSiswa) * 100) : 0 }}% dari total</div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Siswa -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Siswa
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NIS
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kelas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kontak
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in siswa.data" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img v-if="item.foto" 
                                                 :src="item.foto" 
                                                 :alt="item.nama_lengkap"
                                                 class="h-10 w-10 rounded-full object-cover">
                                            <div v-else 
                                                 class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600">
                                                    {{ item.nama_lengkap.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ item.nama_lengkap }}</div>
                                            <div class="text-sm text-gray-500">{{ item.tempat_lahir }}, {{ formatDate(item.tanggal_lahir) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ item.nis }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span v-if="item.kelas" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ item.kelas.nama_kelas }}
                                    </span>
                                    <span v-else class="text-gray-400">Belum ada kelas</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ item.no_telepon || '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ item.email || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusBadgeClass(item.status)"
                                          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ getStatusText(item.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <Link :href="route('siswa.show', item.id)"
                                              class="text-blue-600 hover:text-blue-900 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </Link>
                                        <Link v-if="canModify" :href="route('siswa.edit', item.id)"
                                              class="text-yellow-600 hover:text-yellow-900 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </Link>
                                        <button v-if="canModify" @click="confirmDelete(item)"
                                                class="text-red-600 hover:text-red-900 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="siswa.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <!-- Info Pagination -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-700">
                            Menampilkan 
                            <span class="font-medium">{{ siswa.from || 1 }}</span>
                            sampai 
                            <span class="font-medium">{{ siswa.to || siswa.data.length }}</span>
                            dari 
                            <span class="font-medium">{{ totalSiswa }}</span>
                            siswa
                        </div>
                        <div class="text-sm text-gray-500">
                            Halaman {{ siswa.current_page || 1 }} dari {{ siswa.last_page || 1 }}
                        </div>
                    </div>
                    <Pagination :links="siswa.links" />
                </div>

                <!-- Empty State -->
                <div v-if="siswa.data.length === 0" class="text-center py-12">
                    <div class="text-6xl mb-4">👨‍🎓</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data siswa</h3>
                    <p class="text-gray-500 mb-4">Mulai dengan menambahkan data siswa pertama</p>
                    <Link :href="route('siswa.create')" 
                          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Tambah Siswa</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal 
            :show="showDeleteModal" 
            @close="showDeleteModal = false"
            @confirm="deleteSiswa"
            title="Hapus Data Siswa"
            :message="`Apakah Anda yakin ingin menghapus data siswa ${siswaToDelete?.nama_lengkap}? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Hapus"
            confirm-class="bg-red-600 hover:bg-red-700"
        />
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    siswa: Object,
    allKelas: Array,
    statistics: Object,
    filters: Object
})

// Inisialisasi filter dari props backend
const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')
const kelasFilter = ref(props.filters?.kelas_id || '')
const showDeleteModal = ref(false)
const siswaToDelete = ref(null)
const showStatistics = ref(true) // Control untuk menampilkan/menyembunyikan statistik detail

// Debug data saat component dimount
onMounted(() => {
    console.log('=== DEBUG SISWA INDEX ===')
    console.log('Props siswa:', props.siswa)
    console.log('Props allKelas:', props.allKelas)
    console.log('Props statistics:', props.statistics)
    console.log('Pagination data siswa:', props.siswa?.data?.length || 0)
    console.log('Total siswa dari backend:', props.statistics?.totalSiswa || 'tidak ada')
    console.log('Total siswa dari pagination:', props.siswa?.total || 'tidak ada')
    console.log('Total kelas dari props:', props.allKelas?.length || 0)
    console.log('Unique kelas computed:', uniqueKelas.value)
    console.log('========================')
})

// Computed properties untuk statistik
const totalSiswa = computed(() => {
    // Gunakan data dari backend jika tersedia, fallback ke pagination data
    return props.statistics?.totalSiswa || props.siswa?.total || props.siswa?.data?.length || 0
})

const totalKelas = computed(() => {
    return uniqueKelas.value.length
})

const siswaAktif = computed(() => {
    // Gunakan data dari backend jika tersedia, fallback ke perhitungan lokal
    if (props.statistics?.siswaAktif !== undefined) {
        return props.statistics.siswaAktif
    }
    // Fallback: hitung dari data pagination saat ini (tidak akurat untuk total keseluruhan)
    if (!props.siswa?.data) return 0
    return props.siswa.data.filter(siswa => siswa.status === 'aktif').length
})

const siswaLulus = computed(() => {
    // Gunakan data dari backend jika tersedia, fallback ke perhitungan lokal
    if (props.statistics?.siswaLulus !== undefined) {
        return props.statistics.siswaLulus
    }
    // Fallback: hitung dari data pagination saat ini (tidak akurat untuk total keseluruhan)
    if (!props.siswa?.data) return 0
    return props.siswa.data.filter(siswa => siswa.status === 'lulus').length
})

const siswaPindah = computed(() => {
    // Gunakan data dari backend jika tersedia, fallback ke perhitungan lokal
    if (props.statistics?.siswaPindah !== undefined) {
        return props.statistics.siswaPindah
    }
    // Fallback: hitung dari data pagination saat ini (tidak akurat untuk total keseluruhan)
    if (!props.siswa?.data) return 0
    return props.siswa.data.filter(siswa => siswa.status === 'pindah').length
})

const siswaKeluar = computed(() => {
    // Gunakan data dari backend jika tersedia, fallback ke perhitungan lokal
    if (props.statistics?.siswaKeluar !== undefined) {
        return props.statistics.siswaKeluar
    }
    // Fallback: hitung dari data pagination saat ini (tidak akurat untuk total keseluruhan)
    if (!props.siswa?.data) return 0
    return props.siswa.data.filter(siswa => siswa.status === 'keluar').length
})

const siswaPerKelas = computed(() => {
    if (!props.siswa?.data) return {}
    
    const kelasCount = {}
    props.siswa.data.forEach(siswa => {
        if (siswa.kelas) {
            const kelasNama = siswa.kelas.nama_kelas
            kelasCount[kelasNama] = (kelasCount[kelasNama] || 0) + 1
        }
    })
    
    return kelasCount
})

const uniqueKelas = computed(() => {
    // Prioritas 1: Gunakan allKelas dari backend jika tersedia
    if (props.allKelas && Array.isArray(props.allKelas) && props.allKelas.length > 0) {
        return props.allKelas.sort((a, b) => {
            // Custom sort untuk kelas (X.2, X.3, XI.1, XI.2, XI.3, XII.1, XII.2, XII.3)
            const getKelasOrder = (nama) => {
                if (nama.startsWith('X.')) return 10 + parseInt(nama.split('.')[1] || '0')
                if (nama.startsWith('XI.')) return 20 + parseInt(nama.split('.')[1] || '0')
                if (nama.startsWith('XII.')) return 30 + parseInt(nama.split('.')[1] || '0')
                return 999 // fallback untuk nama kelas lain
            }
            return getKelasOrder(a.nama_kelas) - getKelasOrder(b.nama_kelas)
        })
    }
    
    // Prioritas 2: Fallback ke kelas yang ada di data siswa
    const kelasMap = new Map()
    
    // Pastikan props.siswa dan props.siswa.data ada
    if (props.siswa && props.siswa.data && Array.isArray(props.siswa.data)) {
        props.siswa.data.forEach(siswa => {
            if (siswa.kelas && siswa.kelas.id && siswa.kelas.nama_kelas) {
                kelasMap.set(siswa.kelas.id, {
                    id: siswa.kelas.id,
                    nama_kelas: siswa.kelas.nama_kelas
                })
            }
        })
    }
    
    // Sort kelas berdasarkan nama untuk tampilan yang lebih rapi
    const kelasFromSiswa = Array.from(kelasMap.values()).sort((a, b) => {
        const getKelasOrder = (nama) => {
            if (nama.startsWith('X.')) return 10 + parseInt(nama.split('.')[1] || '0')
            if (nama.startsWith('XI.')) return 20 + parseInt(nama.split('.')[1] || '0')
            if (nama.startsWith('XII.')) return 30 + parseInt(nama.split('.')[1] || '0')
            return 999 // fallback untuk nama kelas lain
        }
        return getKelasOrder(a.nama_kelas) - getKelasOrder(b.nama_kelas)
    })
    
    return kelasFromSiswa
})

const filteredSiswa = computed(() => {
    // Karena filtering sekarang dilakukan di backend, 
    // kita hanya perlu return data yang sudah difilter dari backend
    return props.siswa?.data || []
})

// Informasi hasil filter dari backend
const filteredTotal = computed(() => {
    return props.statistics?.filteredTotal || props.siswa?.total || 0
})

const getStatusBadgeClass = (status) => {
    const classes = {
        'aktif': 'bg-green-100 text-green-800',
        'lulus': 'bg-blue-100 text-blue-800',
        'pindah': 'bg-yellow-100 text-yellow-800',
        'keluar': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
    const texts = {
        'aktif': 'Aktif',
        'lulus': 'Lulus',
        'pindah': 'Pindah',
        'keluar': 'Keluar'
    }
    return texts[status] || status
}

const getSelectedKelasName = () => {
    if (!kelasFilter.value) return ''
    
    const selectedKelas = uniqueKelas.value.find(k => 
        k.id.toString() === kelasFilter.value.toString()
    )
    
    return selectedKelas?.nama_kelas || `ID: ${kelasFilter.value}`
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

const confirmDelete = (siswa) => {
    siswaToDelete.value = siswa
    showDeleteModal.value = true
}

const deleteSiswa = () => {
    router.delete(route('siswa.destroy', siswaToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
            siswaToDelete.value = null
        }
    })
}

// Fungsi untuk handle perubahan filter kelas
const handleKelasFilterChange = () => {
    applyFilters()
}

// Fungsi untuk reset semua filter
const resetFilters = () => {
    search.value = ''
    statusFilter.value = ''
    kelasFilter.value = ''
    applyFilters()
}

// Fungsi untuk menerapkan filter ke backend
const applyFilters = () => {
    router.get(route('siswa.index'), {
        search: search.value || null,
        status: statusFilter.value || null,
        kelas_id: kelasFilter.value || null,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

// Watch untuk menerapkan filter saat nilai berubah (dengan debounce untuk search)
let searchTimeout = null
watch([search], ([newSearch]) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        applyFilters()
    }, 500) // 500ms debounce untuk search
})

watch([statusFilter, kelasFilter], () => {
    applyFilters()
})
</script>
