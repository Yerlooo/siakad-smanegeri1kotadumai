<template>
    <Head title="SIAKAD" />

    <AppLayout page-title="Data Kelas">
        <div class="space-y-6">
            <!-- Header dengan tombol tambah -->
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-start sm:space-y-0">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Data Kelas</h1>
                    <p class="text-sm sm:text-base text-gray-600">Kelola data kelas sekolah</p>
                    <!-- Statistik Kelas -->
                    <div class="mt-2 flex flex-col space-y-1 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4 text-xs sm:text-sm text-gray-500">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                            </svg>
                            Total Kelas: {{ props.kelas?.total || props.kelas?.data?.length || 0 }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                            </svg>
                            Total Siswa: {{ totalSiswaAllKelas }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Rata-rata/Kelas: {{ avgSiswaPerKelas }}
                        </span>
                    </div>
                </div>
                <Link v-if="canModify" :href="route('kelas.create')" 
                      class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors text-sm">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Kelas</span>
                </Link>
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
                                placeholder="Cari nama kelas..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
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
                            v-model="tingkatFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[120px]"
                        >
                            <option value="">Semua Tingkat</option>
                            <option value="10">📚 Kelas 10</option>
                            <option value="11">📖 Kelas 11</option>
                            <option value="12">🎓 Kelas 12</option>
                        </select>
                        <select 
                            v-model="jurusanFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[120px]"
                        >
                            <option value="">Semua Jurusan</option>
                            <option value="IPA">🧪 IPA</option>
                            <option value="IPS">🌍 IPS</option>
                            <option value="Bahasa">📝 Bahasa</option>
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
                            <span v-if="search || tingkatFilter || jurusanFilter" class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full ml-1">
                                {{ (search ? 1 : 0) + (tingkatFilter ? 1 : 0) + (jurusanFilter ? 1 : 0) }}
                            </span>
                        </button>
                    </div>
                </div>
                
                <!-- Info hasil filter -->
                <div v-if="search || tingkatFilter || jurusanFilter" class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex flex-wrap items-center justify-between">
                        <p class="text-sm text-gray-600">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mr-2">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 10.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                                </svg>
                                Filter Aktif
                            </span>
                            Menampilkan <span class="font-medium text-blue-600">{{ filteredKelas.length }}</span> kelas
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                            <span v-if="search" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                                "{{ search }}"
                            </span>
                            <span v-if="tingkatFilter" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                Kelas {{ tingkatFilter }}
                            </span>
                            <span v-if="jurusanFilter" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ jurusanFilter }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Data Kelas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div v-for="kelasItem in filteredKelas" 
                     :key="kelasItem.id" 
                     class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-4 sm:p-6">
                        <!-- Header Kelas -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">{{ kelasItem.nama_kelas }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Tingkat {{ kelasItem.tingkat }}
                                    </span>
                                    <span v-if="kelasItem.jurusan" 
                                          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ kelasItem.jurusan }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-xl sm:text-2xl flex-shrink-0 ml-2">🏫</div>
                        </div>

                        <!-- Wali Kelas -->
                        <div class="mb-4">
                            <div class="text-xs sm:text-sm text-gray-600 mb-1">Wali Kelas:</div>
                            <div v-if="kelasItem.wali_kelas" class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ kelasItem.wali_kelas.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <span class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ kelasItem.wali_kelas.name }}</span>
                            </div>
                            <div v-else class="text-xs sm:text-sm text-gray-400">Belum ada wali kelas</div>
                        </div>

                        <!-- Statistik -->
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6">
                            <div class="text-center p-2 sm:p-3 bg-gray-50 rounded-lg">
                                <div class="text-xl sm:text-2xl font-bold text-green-600">{{ kelasItem.siswa_count }}</div>
                                <div class="text-xs text-gray-600">Siswa</div>
                            </div>
                            <div class="text-center p-2 sm:p-3 bg-gray-50 rounded-lg">
                                <div class="text-xl sm:text-2xl font-bold text-green-600">{{ kelasItem.kapasitas }}</div>
                                <div class="text-xs text-gray-600">Kapasitas</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>Terisi</span>
                                <span>{{ Math.round((kelasItem.siswa_count / kelasItem.kapasitas) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                                     :style="{ width: Math.min((kelasItem.siswa_count / kelasItem.kapasitas) * 100, 100) + '%' }">
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col space-y-2 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
                            <Link :href="route('kelas.show', kelasItem.id)"
                                  class="text-blue-600 hover:text-blue-800 text-sm font-medium text-center sm:text-left">
                                Lihat Detail
                            </Link>
                            <div v-if="canModify" class="flex justify-center sm:justify-end space-x-2">
                                <Link v-if="canModify" :href="route('kelas.edit', kelasItem.id)"
                                      class="text-yellow-600 hover:text-yellow-900 p-2 rounded-full hover:bg-yellow-50 transition-colors"
                                      title="Edit Kelas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </Link>
                                <button v-if="canModify" @click="confirmDelete(kelasItem)"
                                        class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50 transition-colors"
                                        title="Hapus Kelas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="kelas.data.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <Pagination :links="kelas.links" />
            </div>

            <!-- Empty State -->
            <div v-if="kelas.data.length === 0" class="text-center py-12">
                <div class="text-6xl mb-4">🏫</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data kelas</h3>
                <p class="text-gray-500 mb-4">{{ canModify ? 'Mulai dengan menambahkan kelas pertama' : 'Tidak ada data kelas tersedia' }}</p>
                <Link v-if="canModify" :href="route('kelas.create')" 
                      class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Kelas</span>
                </Link>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal 
            :show="showDeleteModal" 
            @close="showDeleteModal = false"
            @confirm="deleteKelas"
            title="Hapus Data Kelas"
            :message="`Apakah Anda yakin ingin menghapus kelas ${kelasToDelete?.nama_kelas}? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Hapus"
            confirm-class="bg-red-600 hover:bg-red-700"
        />
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    kelas: Object,
    canModify: Boolean
})

const search = ref('')
const tingkatFilter = ref('')
const jurusanFilter = ref('')
const showDeleteModal = ref(false)
const kelasToDelete = ref(null)

const filteredKelas = computed(() => {
    return props.kelas.data.filter(item => {
        const matchesSearch = search.value === '' || 
            item.nama_kelas.toLowerCase().includes(search.value.toLowerCase())
        
        const matchesTingkat = tingkatFilter.value === '' || item.tingkat === tingkatFilter.value
        const matchesJurusan = jurusanFilter.value === '' || item.jurusan === jurusanFilter.value
        
        return matchesSearch && matchesTingkat && matchesJurusan
    })
})

const totalSiswaAllKelas = computed(() => {
    // Jumlah total siswa dari semua kelas yang ditampilkan
    return filteredKelas.value.reduce((sum, kelas) => sum + (kelas.siswa_count || 0), 0)
})

const avgSiswaPerKelas = computed(() => {
    if (filteredKelas.value.length === 0) return 0
    return Math.round(totalSiswaAllKelas.value / filteredKelas.value.length)
})

const resetFilters = () => {
    search.value = ''
    tingkatFilter.value = ''
    jurusanFilter.value = ''
}

const confirmDelete = (kelas) => {
    kelasToDelete.value = kelas
    showDeleteModal.value = true
}

const deleteKelas = () => {
    router.delete(route('kelas.destroy', kelasToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
            kelasToDelete.value = null
        }
    })
}
</script>
