<template>
    <Head title="SIAKAD SMANSA" />

    <AppLayout page-title="Data Guru">
        <div class="space-y-6">
            <!-- Flash Messages -->
            <div v-if="$page.props.flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $page.props.flash.success }}</span>
            </div>
            
            <div v-if="$page.props.flash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $page.props.flash.error }}</span>
            </div>
        
            <!-- Header dengan tombol tambah -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Data Guru</h1>
                    <p class="text-gray-600">
                        {{ permissions && permissions.canCreate ? 'Kelola data guru dan staff sekolah' : 'Lihat data guru dan staff sekolah' }}
                    </p>
                    <!-- Total Guru Info -->
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Total: {{ totalGuru }} guru
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Aktif: {{ guruAktif }} guru
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    <Link v-if="permissions && permissions.canCreate"
                          :href="route('guru.create')" 
                          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Tambah Guru</span>
                    </Link>
                    <!-- Info role untuk guru -->
                    <div v-if="permissions && !permissions.canCreate" class="flex justify-center sm:justify-end">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Mode Baca Saja
                        </span>
                    </div>
                </div>
            </div>

            <!-- Search dan Filter -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:space-y-0 lg:space-x-4">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                v-model="search"
                                type="text" 
                                placeholder="Cari nama guru, NIP, atau email..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            >
                            <div v-if="search" 
                                 @click="search = ''"
                                 class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters Row -->
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                        <select 
                            v-model="roleFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[120px]"
                        >
                            <option value="">Semua Role</option>
                            <option value="kepala_tatausaha">👑 Kepala Tata Usaha</option>
                            <option value="tata_usaha">📋 Tata Usaha</option>
                            <option value="guru">👨‍🏫 Guru</option>
                        </select>
                        <button 
                            @click="resetFilters"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center space-x-2 transition-colors"
                            title="Reset Filter"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="hidden sm:inline">Reset</span>
                            <span v-if="search || roleFilter" class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full ml-1">
                                {{ (search ? 1 : 0) + (roleFilter ? 1 : 0) }}
                            </span>
                        </button>
                    </div>
                </div>
                
                <!-- Info hasil filter -->
                <div v-if="search || roleFilter" class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex flex-wrap items-center justify-between">
                        <p class="text-sm text-gray-600">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 mr-2">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 10.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                                </svg>
                                Filter Aktif
                            </span>
                            Menampilkan <span class="font-medium text-green-600">{{ filteredGuru.length }}</span> guru
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                            <span v-if="search" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                                "{{ search }}"
                            </span>
                            <span v-if="roleFilter" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ getRoleDisplayName(roleFilter) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Guru -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Guru
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NIP
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
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
                            <tr v-for="item in filteredGuru" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img v-if="item.foto" 
                                                 :src="item.foto" 
                                                 :alt="item.name"
                                                 class="h-10 w-10 rounded-full object-cover">
                                            <div v-else 
                                                 class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600">
                                                    {{ item.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                                            <div class="text-sm text-gray-500">{{ item.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ item.nip || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          :class="getRoleBadgeClass(item.role?.name)">
                                        {{ item.role?.display_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ item.no_telepon || '-' }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-32">{{ item.alamat || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Lihat Detail - Semua role bisa akses -->
                                        <Link :href="route('guru.show', item.id)"
                                              class="text-blue-600 hover:text-blue-900 p-1 rounded"
                                              title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </Link>
                                        
                                        <!-- Edit - Hanya untuk kepala tata usaha dan tata usaha -->
                                        <Link v-if="permissions && permissions.canEdit"
                                              :href="route('guru.edit', item.id)"
                                              class="text-yellow-600 hover:text-yellow-900 p-1 rounded"
                                              title="Edit Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </Link>
                                        
                                        <!-- Hapus - Hanya untuk kepala tata usaha dan tata usaha -->
                                        <button v-if="permissions && permissions.canDelete"
                                                @click="confirmDelete(item)"
                                                class="text-red-600 hover:text-red-900 p-1 rounded"
                                                title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
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
                        <div class="flex flex-col space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">
                                    {{ filteredGuru.length }} dari {{ totalGuru }} guru
                                </span>
                                <span class="text-gray-500">
                                    Hal {{ guru.current_page || 1 }}/{{ guru.last_page || 1 }}
                                </span>
                            </div>
                            <div v-if="guru.current_page && guru.current_page < guru.last_page" class="w-full">
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-green-600 h-1.5 rounded-full transition-all duration-300" 
                                         :style="{ width: (guru.current_page / guru.last_page) * 100 + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-for="item in filteredGuru" :key="item.id" class="border-b border-gray-200 p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex space-x-3">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <img v-if="item.foto" 
                                     :src="item.foto" 
                                     :alt="item.name"
                                     class="h-12 w-12 rounded-full object-cover">
                                <div v-else 
                                     class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-lg font-medium text-gray-600">
                                        {{ item.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Main Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Header Info -->
                                <div class="mb-3">
                                    <h3 class="text-sm font-medium text-gray-900 leading-tight">
                                        {{ item.name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1 break-all">
                                        {{ item.email }}
                                    </p>
                                </div>
                                
                                <!-- Info Grid -->
                                <div class="space-y-2 text-xs">
                                    <!-- NIP -->
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-600">NIP:</span>
                                        <span class="text-gray-900 mt-0.5 break-all">{{ item.nip || '-' }}</span>
                                    </div>
                                    
                                    <!-- Role -->
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-600">Role:</span>
                                        <div class="mt-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                  :class="getRoleBadgeClass(item.role?.name)">
                                                {{ item.role?.display_name }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- No Telepon (jika ada) -->
                                    <div v-if="item.no_telepon" class="flex flex-col">
                                        <span class="font-medium text-gray-600">No. Telepon:</span>
                                        <span class="text-gray-900 mt-0.5">{{ item.no_telepon }}</span>
                                    </div>
                                    
                                    <!-- Alamat (jika ada) -->
                                    <div v-if="item.alamat" class="flex flex-col">
                                        <span class="font-medium text-gray-600">Alamat:</span>
                                        <span class="text-gray-900 mt-0.5 break-all">{{ item.alamat }}</span>
                                    </div>
                                </div>
                                
                                <!-- Status dan Action Row -->
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center space-x-1">
                                        <Link :href="route('guru.show', item.id)"
                                              class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-colors"
                                              title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </Link>
                                        <Link v-if="permissions && permissions.canEdit" :href="route('guru.edit', item.id)"
                                              class="p-2 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded-full transition-colors"
                                              title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </Link>
                                        <button v-if="permissions && permissions.canDelete" @click="confirmDelete(item)"
                                                class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-full transition-colors"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- Info untuk guru yang tidak bisa edit/hapus -->
                                        <div v-if="permissions && !permissions.canEdit && !permissions.canDelete" 
                                             class="text-gray-400 text-xs italic text-center px-2 py-1">
                                            Baca Saja
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="guru.data.length > 0" class="bg-white px-3 py-3 border-t border-gray-200 sm:px-6">
                    <Pagination 
                        :links="guru.links" 
                        :from="guru.from" 
                        :to="guru.to" 
                        :total="guru.total"
                    />
                </div>

                <!-- Empty State -->
                <div v-if="guru.data.length === 0" class="text-center py-12">
                    <div class="text-6xl mb-4">👨‍🏫</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data guru</h3>
                    <p class="text-gray-500 mb-4">
                        {{ permissions && permissions.canCreate ? 'Mulai dengan menambahkan data guru pertama' : 'Belum ada data guru yang tersedia' }}
                    </p>
            <Link v-if="permissions && permissions.canCreate"
                  :href="route('guru.create')" 
                  class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Tambah Guru</span>
            </Link>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal 
            :show="showDeleteModal" 
            @close="showDeleteModal = false"
            @confirm="deleteGuru"
            title="Hapus Data Guru"
            :message="`Apakah Anda yakin ingin menghapus data guru ${guruToDelete?.name}? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Hapus"
            confirm-class="bg-red-600 hover:bg-red-700"
        />
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    guru: Object,
    userRole: String,
    permissions: Object
})

const search = ref('')
const roleFilter = ref('')
const showDeleteModal = ref(false)
const guruToDelete = ref(null)

const filteredGuru = computed(() => {
    return props.guru.data.filter(item => {
        const matchesSearch = search.value === '' || 
            item.name.toLowerCase().includes(search.value.toLowerCase()) ||
            item.email.toLowerCase().includes(search.value.toLowerCase()) ||
            item.nip?.toLowerCase().includes(search.value.toLowerCase())
        
        const matchesRole = roleFilter.value === '' || item.role?.name === roleFilter.value
        
        return matchesSearch && matchesRole
    })
})

const totalGuru = computed(() => {
    return props.guru.data.length
})

const guruAktif = computed(() => {
    return props.guru.data.length // Assuming all displayed guru are active
})

const resetFilters = () => {
    search.value = ''
    roleFilter.value = ''
}

const getRoleBadgeClass = (roleName) => {
    const classes = {
        'kepala_tatausaha': 'bg-purple-100 text-purple-800',
        'tata_usaha': 'bg-blue-100 text-blue-800',
        'guru': 'bg-green-100 text-green-800'
    }
    return classes[roleName] || 'bg-gray-100 text-gray-800'
}

const getRoleDisplayName = (roleName) => {
    const names = {
        'kepala_tatausaha': '👑 Kepala Tata Usaha',
        'tata_usaha': '📋 Tata Usaha',
        'guru': '👨‍🏫 Guru'
    }
    return names[roleName] || roleName
}

const confirmDelete = (guru) => {
    guruToDelete.value = guru
    showDeleteModal.value = true
}

const deleteGuru = () => {
    router.delete(route('guru.destroy', guruToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
            guruToDelete.value = null
        }
    })
}
</script>
