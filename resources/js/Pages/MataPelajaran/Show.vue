<template>
    <Head title="SIAKAD SMANSA" />
    <AppLayout page-title="Detail Mata Pelajaran">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Detail Mata Pelajaran</h3>
                        <div class="flex space-x-2">
                            <Link v-if="canAccess(['kepala_tatausaha', 'tata_usaha', 'guru'])"
                                  :href="route('mata-pelajaran.edit', mataPelajaran.id)" 
                                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </Link>
                            <Link :href="route('mata-pelajaran.index')" 
                                  class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali
                            </Link>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Informasi Dasar -->
                        <div class="lg:col-span-2">
                            <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                                <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Dasar</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Nama Mata Pelajaran</label>
                                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ mataPelajaran.nama_mapel }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Kode</label>
                                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ mataPelajaran.kode_mapel }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Jam Pelajaran/Minggu</label>
                                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ mataPelajaran.jam_pelajaran }} jam</p>
                                    </div>
                                </div>

                                <div v-if="mataPelajaran.deskripsi">
                                    <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ mataPelajaran.deskripsi }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Status & Metadata -->
                        <div class="space-y-6">
                            <!-- Statistik -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-900 mb-3">Statistik</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Total Jadwal</span>
                                        <span class="text-sm font-medium text-gray-900">{{ mataPelajaran.jadwal_pelajaran?.length || 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Total Kelas</span>
                                        <span class="text-sm font-medium text-gray-900">{{ getUniqueKelasCount() }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Total Guru Pengampu</span>
                                        <span class="text-sm font-medium text-gray-900">{{ getUniqueGuruCount() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Metadata -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-900 mb-3">Informasi Sistem</h4>
                                <div class="space-y-2">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Dibuat</label>
                                        <p class="text-sm text-gray-900">{{ formatDate(mataPelajaran.created_at) }}</p>
                                    </div>
                                    <div v-if="mataPelajaran.updated_at !== mataPelajaran.created_at">
                                        <label class="text-sm font-medium text-gray-500">Diperbarui</label>
                                        <p class="text-sm text-gray-900">{{ formatDate(mataPelajaran.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Pelajaran -->
                    <div v-if="mataPelajaran.jadwal_pelajaran && mataPelajaran.jadwal_pelajaran.length > 0" 
                         class="mt-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Pelajaran</h4>
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kelas
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Guru
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Hari
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Jam
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="jadwal in mataPelajaran.jadwal_pelajaran" :key="jadwal.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ jadwal.kelas?.nama_kelas || '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ jadwal.guru?.name || '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ jadwal.hari }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ jadwal.jam_mulai }} - {{ jadwal.jam_selesai }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div v-if="canAccess(['kepala_tatausaha', 'tata_usaha', 'guru'])" 
                         class="flex items-center justify-end space-x-4 pt-6 border-t mt-8">
                        <button
                            @click="confirmDelete"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>
                Hapus Mata Pelajaran
            </template>

            <template #content>
                Apakah Anda yakin ingin menghapus mata pelajaran <strong>{{ mataPelajaran.nama_mapel }}</strong>?
                Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.
            </template>

            <template #footer>
                <button
                    @click="showDeleteModal = false"
                    class="mr-3 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                    Batal
                </button>

                <button
                    @click="deleteMataPelajaran"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                    Hapus
                </button>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    mataPelajaran: Object
})

const page = usePage()
const showDeleteModal = ref(false)

const canAccess = (roles) => {
    const userRole = page.props.auth.user.role?.name
    return roles.includes(userRole)
}

const getUniqueKelasCount = () => {
    if (!props.mataPelajaran.jadwal_pelajaran) return 0
    const uniqueKelas = new Set(props.mataPelajaran.jadwal_pelajaran.map(jadwal => jadwal.kelas?.id).filter(id => id))
    return uniqueKelas.size
}

const getUniqueGuruCount = () => {
    if (!props.mataPelajaran.jadwal_pelajaran) return 0
    const uniqueGuru = new Set(props.mataPelajaran.jadwal_pelajaran.map(jadwal => jadwal.guru?.id).filter(id => id))
    return uniqueGuru.size
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const confirmDelete = () => {
    showDeleteModal.value = true
}

const deleteMataPelajaran = () => {
    router.delete(route('mata-pelajaran.destroy', props.mataPelajaran.id), {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}
</script>
