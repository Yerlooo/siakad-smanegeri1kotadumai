<template>
    <Head title="SIAKAD SMANSA" />
    <AppLayout page-title="Jadwal Pelajaran">
        <div class="space-y-6">
            <!-- Flash Messages -->
            <div v-if="$page.props.flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $page.props.flash.success }}</span>
            </div>
            
            <div v-if="$page.props.flash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $page.props.flash.error }}</span>
            </div>
        
            <!-- Header -->
            <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div class="flex-1">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
                        {{ userRole === 'murid' ? 'Jadwal Pelajaran Saya' : 'Jadwal Pelajaran' }}
                    </h2>
                    <p class="mt-1 text-sm sm:text-base text-gray-600">
                        {{ userRole === 'murid' 
                            ? 'Lihat jadwal pelajaran kelas Anda' 
                            : (permissions && permissions.canCreate ? 'Kelola jadwal pelajaran sekolah' : 'Lihat jadwal pelajaran sekolah') 
                        }}
                    </p>
                </div>
                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3 sm:items-center">
                    <Link v-if="permissions && permissions.canCreate"
                          :href="route('jadwal-pelajaran.create')" 
                          class="inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden sm:inline">Tambah </span>Jadwal
                    </Link>
                    <!-- Info role untuk guru -->
                    <div v-if="permissions && !permissions.canCreate && userRole !== 'murid'">
                        <span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span class="hidden sm:inline">Mode </span>Baca Saja
                        </span>
                    </div>
                    <!-- Info khusus untuk murid -->
                    <div v-if="userRole === 'murid'">
                        <span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                            <span class="hidden sm:inline">Jadwal </span>Kelas Anda
                        </span>
                    </div>
                </div>
            </div>

            <!-- Filters - Hanya untuk role selain murid -->
            <div v-if="userRole !== 'murid'" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex flex-col space-y-4 lg:flex-row lg:items-end lg:space-y-0 lg:space-x-4">
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                            <select v-model="filters.hari" @change="filterJadwal" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Semua Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                            <select v-model="filters.kelas_id" @change="filterJadwal"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Semua Kelas</option>
                                <option v-for="kelas in availableKelas" :key="kelas.id" :value="kelas.id">
                                    {{ kelas.nama_kelas }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Mata Pelajaran</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input v-model="filters.mapel" @input="filterJadwal"
                                       type="text" placeholder="Cari berdasarkan mata pelajaran..."
                                       class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                <div v-if="filters.mapel" 
                                     @click="filters.mapel = ''; filterJadwal()"
                                     class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button 
                            @click="filters.hari = ''; filters.kelas_id = ''; filters.mapel = ''; filterJadwal()"
                            class="w-full sm:w-auto px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center space-x-2 transition-colors"
                            title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="hidden sm:inline">Reset</span>
                            <span v-if="filters.hari || filters.kelas_id || filters.mapel" class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full ml-1">
                                {{ (filters.hari ? 1 : 0) + (filters.kelas_id ? 1 : 0) + (filters.mapel ? 1 : 0) }}
                            </span>
                        </button>
                    </div>
                </div>
                
                <!-- Filter Info -->
                <div v-if="filters.hari || filters.kelas_id || filters.mapel" class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex flex-wrap items-center justify-between">
                        <p class="text-sm text-gray-600">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 mr-2">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 10.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                                </svg>
                                Filter Aktif
                            </span>
                            Menampilkan hasil filter
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                            <span v-if="filters.hari" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ filters.hari }}
                            </span>
                            <span v-if="filters.kelas_id" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                {{ availableKelas.find(k => k.id == filters.kelas_id)?.nama_kelas || 'Kelas' }}
                            </span>
                            <span v-if="filters.mapel" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                                "{{ filters.mapel }}"
                            </span>
                        </div>
                    </div>
                </div>
            </div>

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
                                    Kelas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Guru
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hari & Jam
                                </th>
                                <th v-if="userRole !== 'murid'" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="jadwalPelajaran.data.length === 0">
                                <td :colspan="userRole === 'murid' ? 4 : 5" class="px-6 py-4 text-center text-gray-500">
                                    {{ userRole === 'murid' ? 'Belum ada jadwal pelajaran untuk kelas Anda' : 'Belum ada data jadwal pelajaran' }}
                                </td>
                            </tr>
                            <tr v-for="jadwal in jadwalPelajaran.data" :key="jadwal.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ jadwal.mata_pelajaran?.nama_mapel }}</div>
                                    <div class="text-sm text-gray-500">{{ jadwal.mata_pelajaran?.kode_mapel }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ jadwal.kelas?.nama_kelas }}</div>
                                    <div class="text-sm text-gray-500">{{ jadwal.kelas?.tingkat }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ jadwal.guru?.name }}</div>
                                    <div class="text-sm text-gray-500">{{ jadwal.guru?.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ jadwal.hari }}</div>
                                    <div class="text-sm text-gray-500">{{ formatTime(jadwal.jam_mulai) }} - {{ formatTime(jadwal.jam_selesai) }}</div>
                                </td>
                                <td v-if="userRole !== 'murid'" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <!-- Lihat Detail - Semua role bisa akses -->
                                        <Link :href="route('jadwal-pelajaran.show', jadwal.id)"
                                              class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors"
                                              title="Lihat Detail">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat
                                        </Link>
                                        
                                        <!-- Edit - Hanya untuk kepala tata usaha dan tata usaha -->
                                        <Link v-if="permissions && permissions.canEdit"
                                              :href="route('jadwal-pelajaran.edit', jadwal.id)"
                                              class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded hover:bg-yellow-200 transition-colors"
                                              title="Edit Jadwal">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </Link>
                                        
                                        <!-- Hapus - Hanya untuk kepala tata usaha dan tata usaha -->
                                        <button v-if="permissions && permissions.canDelete"
                                                @click="confirmDelete(jadwal)"
                                                class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded hover:bg-red-200 transition-colors"
                                                title="Hapus Jadwal">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                        
                                        <!-- Info untuk guru yang tidak bisa edit/hapus -->
                                        <div v-if="permissions && !permissions.canEdit && !permissions.canDelete" 
                                             class="text-gray-400 text-xs italic px-2">
                                            Baca Saja
                                        </div>
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
                                {{ jadwalPelajaran.data.length }} jadwal
                                <span v-if="userRole === 'murid'">kelas Anda</span>
                            </span>
                            <span class="text-gray-500">
                                Total {{ jadwalPelajaran.total }} data
                            </span>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="jadwalPelajaran.data.length === 0" class="text-center py-12">
                        <div class="text-6xl mb-4">📅</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            {{ userRole === 'murid' ? 'Belum ada jadwal pelajaran' : 'Belum ada data jadwal' }}
                        </h3>
                        <p class="text-gray-500 mb-4">
                            {{ userRole === 'murid' 
                                ? 'Jadwal pelajaran untuk kelas Anda belum tersedia' 
                                : (permissions && permissions.canCreate ? 'Mulai dengan menambahkan jadwal pelajaran pertama' : 'Tidak ada jadwal pelajaran tersedia') 
                            }}
                        </p>
                        <Link v-if="permissions && permissions.canCreate" :href="route('jadwal-pelajaran.create')" 
                              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Tambah Jadwal</span>
                        </Link>
                    </div>

                    <!-- Mobile Cards -->
                    <div v-for="jadwal in jadwalPelajaran.data" :key="jadwal.id" class="border-b border-gray-200 p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-3">
                            <!-- Schedule Icon -->
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 pr-2">
                                        <h3 class="text-sm font-medium text-gray-900 leading-tight">
                                            {{ jadwal.mata_pelajaran?.nama_mapel }}
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ jadwal.mata_pelajaran?.kode_mapel }}
                                        </p>
                                        
                                        <!-- Info Grid -->
                                        <div class="mt-2 space-y-2 text-xs">
                                            <!-- Row 1: Kelas dan Hari -->
                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                                    </svg>
                                                    <span class="font-medium text-gray-900">{{ jadwal.kelas?.nama_kelas }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="font-medium text-gray-900">{{ jadwal.hari }}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Row 2: Jam Pelajaran -->
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-medium text-gray-600">{{ formatTime(jadwal.jam_mulai) }} - {{ formatTime(jadwal.jam_selesai) }}</span>
                                            </div>
                                            
                                            <!-- Row 3: Guru -->
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                                </svg>
                                                <div class="min-w-0 flex-1">
                                                    <div class="font-medium text-gray-900 truncate">{{ jadwal.guru?.name }}</div>
                                                    <div class="text-gray-500 truncate">{{ jadwal.guru?.email }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Menu -->
                                    <div v-if="userRole !== 'murid'" class="flex-shrink-0">
                                        <div class="flex flex-col items-center space-y-1">
                                            <Link :href="route('jadwal-pelajaran.show', jadwal.id)"
                                                  class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-colors"
                                                  title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </Link>
                                            <Link v-if="permissions && permissions.canEdit" :href="route('jadwal-pelajaran.edit', jadwal.id)"
                                                  class="p-2 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded-full transition-colors"
                                                  title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </Link>
                                            <button v-if="permissions && permissions.canDelete" @click="confirmDelete(jadwal)"
                                                    class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-full transition-colors"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                            <div v-if="permissions && !permissions.canEdit && !permissions.canDelete" 
                                                 class="text-gray-400 text-xs italic text-center px-1">
                                                Baca Saja
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="jadwalPelajaran.last_page > 1" class="bg-white px-3 py-3 border-t border-gray-200 sm:px-4">
                    <Pagination :links="jadwalPelajaran.links" />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal 
            :show="showDeleteModal" 
            @close="showDeleteModal = false; selectedJadwal = null"
            @confirm="deleteJadwal"
            confirmText="Ya, Hapus"
            cancelText="Batal">
            <template #title>
                Hapus Jadwal Pelajaran
            </template>

            <template #content>
                <div class="space-y-3">
                    <p class="text-gray-700">
                        Apakah Anda yakin ingin menghapus jadwal pelajaran ini?
                    </p>
                    
                    <!-- Schedule Info Card -->
                    <div v-if="selectedJadwal" class="bg-gray-50 rounded-lg p-4 border-l-4 border-red-400">
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium text-gray-900">{{ selectedJadwal?.mata_pelajaran?.nama_mapel }}</span>
                                <span class="text-gray-500 ml-1">({{ selectedJadwal?.mata_pelajaran?.kode_mapel }})</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                <span class="font-medium text-gray-900">{{ selectedJadwal?.kelas?.nama_kelas }}</span>
                                <span class="text-gray-500 ml-1">- {{ selectedJadwal?.kelas?.tingkat }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium text-gray-900">{{ selectedJadwal?.hari }}</span>
                                <span class="text-gray-500 ml-2">{{ formatTime(selectedJadwal?.jam_mulai) }} - {{ formatTime(selectedJadwal?.jam_selesai) }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                                <span class="font-medium text-gray-900">{{ selectedJadwal?.guru?.name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-2 text-amber-700 bg-amber-50 p-3 rounded-lg">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm">
                            <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Semua data terkait jadwal ini akan dihapus secara permanen.
                        </div>
                    </div>
                </div>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router, usePage, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    jadwalPelajaran: Object,
    availableKelas: {
        type: Array,
        default: () => []
    },
    userRole: String,
    permissions: Object
})

const page = usePage()
const showDeleteModal = ref(false)
const selectedJadwal = ref(null)
const filters = ref({
    hari: '',
    kelas_id: '',
    mapel: ''
})

const formatTime = (timeString) => {
    if (!timeString) return '-'
    // Jika timeString sudah dalam format HH:MM, return as is
    if (timeString.includes(':') && timeString.length <= 5) {
        return timeString
    }
    // Jika dalam format datetime, extract time part
    try {
        const date = new Date(timeString)
        return date.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        })
    } catch {
        return timeString
    }
}

const filterJadwal = () => {
    const params = {}
    Object.keys(filters.value).forEach(key => {
        if (filters.value[key]) {
            params[key] = filters.value[key]
        }
    })
    router.get(route('jadwal-pelajaran.index'), params, {
        preserveState: true,
        preserveScroll: true
    })
}

const confirmDelete = (jadwal) => {
    selectedJadwal.value = jadwal
    showDeleteModal.value = true
}

const deleteJadwal = () => {
    router.delete(route('jadwal-pelajaran.destroy', selectedJadwal.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
            selectedJadwal.value = null
        }
    })
}
</script>
