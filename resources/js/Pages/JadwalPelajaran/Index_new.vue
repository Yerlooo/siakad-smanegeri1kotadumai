<template>
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
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ userRole === 'murid' ? 'Jadwal Pelajaran Saya' : 'Jadwal Pelajaran' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ userRole === 'murid' 
                            ? 'Lihat jadwal pelajaran kelas Anda' 
                            : (permissions && permissions.canCreate ? 'Kelola jadwal pelajaran sekolah' : 'Lihat jadwal pelajaran sekolah') 
                        }}
                    </p>
                </div>
                <Link v-if="permissions && permissions.canCreate"
                      :href="route('jadwal-pelajaran.create')" 
                      class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Jadwal
                </Link>
                <!-- Info role untuk guru -->
                <div v-if="permissions && !permissions.canCreate && userRole !== 'murid'" class="mt-4 sm:mt-0 text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Mode Baca Saja
                    </span>
                </div>
                <!-- Info khusus untuk murid -->
                <div v-if="userRole === 'murid'" class="mt-4 sm:mt-0 text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                        Jadwal Kelas Anda
                    </span>
                </div>
            </div>

            <!-- Filters - Hanya untuk role selain murid -->
            <div v-if="userRole !== 'murid'" class="bg-white p-4 rounded-lg shadow">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hari</label>
                        <select v-model="filters.hari" @change="filterJadwal" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                        <label class="block text-sm font-medium text-gray-700">Kelas</label>
                        <select v-model="filters.kelas_id" @change="filterJadwal"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kelas</option>
                            <option v-for="kelas in availableKelas" :key="kelas.id" :value="kelas.id">
                                {{ kelas.nama_kelas }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cari Ruangan</label>
                        <input v-model="filters.ruangan" @input="filterJadwal"
                               type="text" placeholder="Cari berdasarkan ruangan..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ruangan
                                </th>
                                <th v-if="userRole !== 'murid'" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="jadwalPelajaran.data.length === 0">
                                <td :colspan="userRole === 'murid' ? 5 : 6" class="px-6 py-4 text-center text-gray-500">
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ jadwal.ruangan || '-' }}
                                </td>
                                <td v-if="userRole !== 'murid'" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <!-- Lihat Detail - Semua role bisa akses -->
                                        <Link :href="route('jadwal-pelajaran.show', jadwal.id)"
                                              class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors"
                                              title="Lihat Detail">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 6 1 6 0z"/>
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

                <!-- Pagination -->
                <div v-if="jadwalPelajaran.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200">
                    <Pagination :links="jadwalPelajaran.links" />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal 
            :show="showDeleteModal" 
            @close="showDeleteModal = false; selectedJadwal = null"
            @confirm="deleteJadwal">
            <template #title>
                Hapus Jadwal Pelajaran
            </template>

            <template #content>
                Apakah Anda yakin ingin menghapus jadwal pelajaran <strong>{{ selectedJadwal?.mata_pelajaran?.nama_mapel }}</strong> 
                untuk kelas <strong>{{ selectedJadwal?.kelas?.nama_kelas }}</strong>?
                Tindakan ini tidak dapat dibatalkan.
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
    ruangan: ''
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
