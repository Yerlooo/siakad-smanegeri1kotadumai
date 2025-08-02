<template>
    <Head title="SIAKAD SMANSA" />
    <AppLayout page-title="Mata Pelajaran">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div class="flex-1">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">Mata Pelajaran</h2>
                    <p class="mt-1 text-sm sm:text-base text-gray-600">Kelola mata pelajaran sekolah</p>
                </div>
                <Link v-if="canModify" :href="route('mata-pelajaran.create')" 
                      class="inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="hidden sm:inline">Tambah </span>Mata Pelajaran
                </Link>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex flex-col space-y-4 lg:flex-row lg:items-end lg:space-y-0 lg:space-x-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Mata Pelajaran</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input v-model="filters.search" @input="filterMataPelajaran" 
                                   type="text" placeholder="Cari berdasarkan nama atau kode..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <div v-if="filters.search" 
                                 @click="filters.search = ''; filterMataPelajaran()"
                                 class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                        <div class="min-w-[140px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam Pelajaran</label>
                            <select v-model="filters.jam_pelajaran" @change="filterMataPelajaran"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Jam</option>
                                <option value="2">2 Jam</option>
                                <option value="3">3 Jam</option>
                                <option value="4">4 Jam</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button 
                                @click="filters.search = ''; filters.jam_pelajaran = ''; filterMataPelajaran()"
                                class="w-full sm:w-auto px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center space-x-2 transition-colors"
                                title="Reset Filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="hidden sm:inline">Reset</span>
                                <span v-if="filters.search || filters.jam_pelajaran" class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full ml-1">
                                    {{ (filters.search ? 1 : 0) + (filters.jam_pelajaran ? 1 : 0) }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Info -->
                <div v-if="filters.search || filters.jam_pelajaran" class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex flex-wrap items-center justify-between">
                        <p class="text-sm text-gray-600">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mr-2">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 10.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                                </svg>
                                Filter Aktif
                            </span>
                            Menampilkan hasil pencarian
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                            <span v-if="filters.search" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                                "{{ filters.search }}"
                            </span>
                            <span v-if="filters.jam_pelajaran" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ filters.jam_pelajaran }} Jam
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-4 sm:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 sm:ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Mata Pelajaran</dt>
                                    <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ mataPelajaran.total }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-4 sm:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 sm:ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Jam/Minggu</dt>
                                    <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ getTotalJam() }} jam</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-4 sm:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 sm:ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Rata-rata Jam</dt>
                                    <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ getAverageJam() }} jam</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
                        <!-- Table and Mobile Cards -->
            <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-lg">
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mata Pelajaran
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jam/Minggu
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Deskripsi
                                </th>
                                <th v-if="canModify" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="mataPelajaran.data.length === 0">
                                <td :colspan="canModify ? 5 : 4" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada data mata pelajaran
                                </td>
                            </tr>
                            <tr v-for="mapel in mataPelajaran.data" :key="mapel.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ mapel.nama_mapel }}</div>
                                    <div v-if="!canModify" class="mt-1">
                                        <Link :href="route('mata-pelajaran.show', mapel.id)"
                                              class="inline-flex items-center text-xs text-blue-600 hover:text-blue-900">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat Detail
                                        </Link>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ mapel.kode_mapel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ mapel.jam_pelajaran }} jam
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate">
                                        {{ mapel.deskripsi || '-' }}
                                    </div>
                                </td>
                                <td v-if="canModify" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <Link :href="route('mata-pelajaran.show', mapel.id)"
                                              class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat
                                        </Link>
                                        <Link v-if="canModify" :href="route('mata-pelajaran.edit', mapel.id)"
                                              class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded hover:bg-yellow-200 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </Link>
                                        <button v-if="canModify" @click="confirmDelete(mapel)"
                                                class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded hover:bg-red-200 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden">
                    <!-- Info jumlah data untuk mobile -->
                    <div class="p-3 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600">
                                {{ mataPelajaran.data.length }} mata pelajaran
                            </span>
                            <span class="text-gray-500">
                                Total {{ mataPelajaran.total }} data
                            </span>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="mataPelajaran.data.length === 0" class="text-center py-12">
                        <div class="text-6xl mb-4">📚</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data mata pelajaran</h3>
                        <p class="text-gray-500 mb-4">{{ canModify ? 'Mulai dengan menambahkan mata pelajaran pertama' : 'Tidak ada mata pelajaran tersedia' }}</p>
                        <Link v-if="canModify" :href="route('mata-pelajaran.create')" 
                              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Tambah Mata Pelajaran</span>
                        </Link>
                    </div>

                    <!-- Mobile Cards -->
                    <div v-for="mapel in mataPelajaran.data" :key="mapel.id" class="border-b border-gray-200 p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-3">
                            <!-- Subject Icon -->
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 pr-2">
                                        <h3 class="text-sm font-medium text-gray-900 leading-tight">
                                            {{ mapel.nama_mapel }}
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Kode: {{ mapel.kode_mapel }}
                                        </p>
                                        
                                        <!-- Info Grid -->
                                        <div class="mt-2 space-y-2 text-xs">
                                            <!-- Row 1: Jam Pelajaran -->
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-medium text-gray-600">{{ mapel.jam_pelajaran }} jam/minggu</span>
                                            </div>
                                            
                                            <!-- Row 2: Deskripsi (jika ada) -->
                                            <div v-if="mapel.deskripsi" class="flex flex-col">
                                                <span class="font-medium text-gray-600">Deskripsi:</span>
                                                <span class="text-gray-900 mt-0.5 line-clamp-2">{{ mapel.deskripsi }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Menu -->
                                    <div class="flex-shrink-0">
                                        <div class="flex flex-col items-center space-y-1">
                                            <Link :href="route('mata-pelajaran.show', mapel.id)"
                                                  class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-colors"
                                                  title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </Link>
                                            <Link v-if="canModify" :href="route('mata-pelajaran.edit', mapel.id)"
                                                  class="p-2 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded-full transition-colors"
                                                  title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </Link>
                                            <button v-if="canModify" @click="confirmDelete(mapel)"
                                                    class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-full transition-colors"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="mataPelajaran.last_page > 1" class="bg-white px-3 py-3 border-t border-gray-200 sm:px-4">
                    <Pagination 
                        :links="mataPelajaran.links" 
                        :from="mataPelajaran.from" 
                        :to="mataPelajaran.to" 
                        :total="mataPelajaran.total"
                    />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal 
            :show="showDeleteModal" 
            @close="showDeleteModal = false"
            @confirm="deleteMataPelajaran"
            title="Hapus Mata Pelajaran"
            :message="`Apakah Anda yakin ingin menghapus mata pelajaran ${selectedMapel?.nama_mapel}? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.`"
            confirm-text="Hapus"
            confirm-class="bg-red-600 hover:bg-red-700"
        />
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    mataPelajaran: Object,
    canModify: Boolean
})

const page = usePage()
const showDeleteModal = ref(false)
const selectedMapel = ref(null)
const filters = ref({
    search: '',
    jam_pelajaran: ''
})

const canAccess = (roles) => {
    const userRole = page.props.auth.user.role?.name
    return roles.includes(userRole)
}

const getTotalJam = () => {
    return props.mataPelajaran.data.reduce((total, mapel) => total + (mapel.jam_pelajaran || 0), 0)
}

const getAverageJam = () => {
    if (props.mataPelajaran.data.length === 0) return 0
    const total = getTotalJam()
    return Math.round((total / props.mataPelajaran.data.length) * 10) / 10
}

const filterMataPelajaran = () => {
    const params = {}
    Object.keys(filters.value).forEach(key => {
        if (filters.value[key]) {
            params[key] = filters.value[key]
        }
    })
    
    router.get(route('mata-pelajaran.index'), params, {
        preserveState: true,
        preserveScroll: true
    })
}

const confirmDelete = (mapel) => {
    selectedMapel.value = mapel
    showDeleteModal.value = true
}

const deleteMataPelajaran = () => {
    router.delete(route('mata-pelajaran.destroy', selectedMapel.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
            selectedMapel.value = null
        }
    })
}
</script>
