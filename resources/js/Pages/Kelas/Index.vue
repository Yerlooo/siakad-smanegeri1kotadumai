<template>
    <Head title="Data Kelas" />

    <AppLayout page-title="Data Kelas">
        <div class="space-y-6">
            <!-- Header dengan tombol tambah -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Data Kelas</h1>
                    <p class="text-gray-600">Kelola data kelas sekolah</p>
                </div>
                <Link v-if="canModify" :href="route('kelas.create')" 
                      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Kelas</span>
                </Link>
            </div>

            <!-- Search dan Filter -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-4 lg:space-y-0 lg:space-x-4">
                    <div class="flex-1">
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Cari nama kelas..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    <select 
                        v-model="tingkatFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Semua Tingkat</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>
                    <select 
                        v-model="jurusanFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Semua Jurusan</option>
                        <option value="IPA">IPA</option>
                        <option value="IPS">IPS</option>
                        <option value="Bahasa">Bahasa</option>
                    </select>
                </div>
            </div>

            <!-- Grid Data Kelas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="kelasItem in filteredKelas" 
                     :key="kelasItem.id" 
                     class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Header Kelas -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ kelasItem.nama_kelas }}</h3>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Tingkat {{ kelasItem.tingkat }}
                                    </span>
                                    <span v-if="kelasItem.jurusan" 
                                          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ kelasItem.jurusan }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-2xl">🏫</div>
                        </div>

                        <!-- Wali Kelas -->
                        <div class="mb-4">
                            <div class="text-sm text-gray-600 mb-1">Wali Kelas:</div>
                            <div v-if="kelasItem.wali_kelas" class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ kelasItem.wali_kelas.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ kelasItem.wali_kelas.name }}</span>
                            </div>
                            <div v-else class="text-sm text-gray-400">Belum ada wali kelas</div>
                        </div>

                        <!-- Statistik -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ kelasItem.siswa_count }}</div>
                                <div class="text-xs text-gray-600">Siswa</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ kelasItem.kapasitas }}</div>
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
                                <div class="bg-blue-600 h-2 rounded-full" 
                                     :style="{ width: Math.min((kelasItem.siswa_count / kelasItem.kapasitas) * 100, 100) + '%' }">
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center">
                            <Link :href="route('kelas.show', kelasItem.id)"
                                  class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Detail
                            </Link>
                            <div v-if="canModify" class="flex space-x-2">
                                <Link :href="route('kelas.edit', kelasItem.id)"
                                      class="text-yellow-600 hover:text-yellow-900 p-1 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </Link>
                                <button @click="confirmDelete(kelasItem)"
                                        class="text-red-600 hover:text-red-900 p-1 rounded">
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
                      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2">
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
