<template>
    <Head title="Data Siswa" />

    <AppLayout page-title="Data Siswa">
        <div class="space-y-6">
            <!-- Header dengan tombol tambah -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Data Siswa</h1>
                    <p class="text-gray-600">Kelola data siswa sekolah</p>
                </div>
                <Link :href="route('siswa.create')" 
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
                    >
                        <option value="">Semua Kelas</option>
                        <option v-for="kelas in uniqueKelas" :key="kelas.id" :value="kelas.id">
                            {{ kelas.nama_kelas }}
                        </option>
                    </select>
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
                            <tr v-for="item in filteredSiswa" :key="item.id" class="hover:bg-gray-50">
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
                                        <Link :href="route('siswa.edit', item.id)"
                                              class="text-yellow-600 hover:text-yellow-900 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </Link>
                                        <button @click="confirmDelete(item)"
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
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    siswa: Object
})

const search = ref('')
const statusFilter = ref('')
const kelasFilter = ref('')
const showDeleteModal = ref(false)
const siswaToDelete = ref(null)

const uniqueKelas = computed(() => {
    const kelasMap = new Map()
    props.siswa.data.forEach(siswa => {
        if (siswa.kelas) {
            kelasMap.set(siswa.kelas.id, siswa.kelas)
        }
    })
    return Array.from(kelasMap.values())
})

const filteredSiswa = computed(() => {
    return props.siswa.data.filter(item => {
        const matchesSearch = search.value === '' || 
            item.nama_lengkap.toLowerCase().includes(search.value.toLowerCase()) ||
            item.nis.toLowerCase().includes(search.value.toLowerCase()) ||
            item.email?.toLowerCase().includes(search.value.toLowerCase())
        
        const matchesStatus = statusFilter.value === '' || item.status === statusFilter.value
        const matchesKelas = kelasFilter.value === '' || item.kelas?.id == kelasFilter.value
        
        return matchesSearch && matchesStatus && matchesKelas
    })
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
</script>
