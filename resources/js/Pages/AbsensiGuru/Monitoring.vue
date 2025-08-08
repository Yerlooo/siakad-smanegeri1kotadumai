<template>
    <AppLayout title="Monitoring Absensi Guru">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Monitoring Absensi Guru
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Alert Success -->
                <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Alert Error -->
                <div v-if="$page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Filter Section -->
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Filter Pencarian
                        </h3>
                        
                        <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Status Filter -->
                            <div>
                                <label for="status_laporan" class="block text-sm font-medium text-gray-700 mb-1">Status Laporan</label>
                                <select v-model="filterForm.status_laporan" 
                                        id="status_laporan"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua Status</option>
                                    <option value="dilaporkan">Telah Dilaporkan</option>
                                    <option value="diterima">Diterima</option>
                                </select>
                            </div>

                            <!-- Jenis Ketidakhadiran Filter -->
                            <div>
                                <label for="jenis_ketidakhadiran" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                                <select v-model="filterForm.jenis_ketidakhadiran" 
                                        id="jenis_ketidakhadiran"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua Jenis</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="izin">Izin</option>
                                    <option value="dinas">Dinas Luar Kota</option>
                                    <option value="cuti">Cuti</option>
                                </select>
                            </div>

                            <!-- Guru Filter -->
                            <div>
                                <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-1">Guru</label>
                                <select v-model="filterForm.guru_id" 
                                        id="guru_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua Guru</option>
                                    <option v-for="guru in daftarGuru" :key="guru.id" :value="guru.id">
                                        {{ guru.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Bulan/Tahun Filter -->
                            <div>
                                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan/Tahun</label>
                                <div class="flex space-x-2">
                                    <select v-model="filterForm.bulan" 
                                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Bulan</option>
                                        <option v-for="(nama, nilai) in bulanOptions" :key="nilai" :value="nilai">
                                            {{ nama }}
                                        </option>
                                    </select>
                                    <select v-model="filterForm.tahun" 
                                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Tahun</option>
                                        <option v-for="tahun in tahunOptions" :key="tahun" :value="tahun">
                                            {{ tahun }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Filter Buttons -->
                            <div class="md:col-span-2 lg:col-span-4 flex space-x-3">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Terapkan Filter
                                </button>
                                <button type="button" 
                                        @click="resetFilters"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Data -->
                    <div class="p-6">
                        <div v-if="absensiGuru.data.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Guru
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Alasan
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status Laporan
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="absensi in absensiGuru.data" :key="absensi.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ absensi.user.name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(absensi.tanggal) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getJenisBadgeClass(absensi.jenis_ketidakhadiran)" 
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ getJenisText(absensi.jenis_ketidakhadiran) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="max-w-xs truncate" :title="absensi.alasan">
                                                {{ absensi.alasan }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusLaporanBadgeClass(absensi.status_laporan)" 
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ getStatusLaporanText(absensi.status_laporan) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button @click="viewDetail(absensi)" 
                                                        class="text-blue-600 hover:text-blue-900">
                                                    Detail
                                                </button>
                                                <button v-if="absensi.status_laporan === 'dilaporkan'" 
                                                        @click="markAsReceived(absensi)" 
                                                        class="text-green-600 hover:text-green-900">
                                                    Tandai Diterima
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-8">
                            <div class="text-gray-500 text-lg mb-4">
                                Tidak ada data absensi guru
                            </div>
                            <p class="text-gray-400 text-sm">
                                Belum ada laporan ketidakhadiran dari guru atau sesuaikan filter pencarian
                            </p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="absensiGuru.data.length > 0" class="mt-6">
                            <Pagination :links="absensiGuru.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div v-if="showDetailModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeDetailModal"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Laporan Ketidakhadiran</h3>
                        
                        <div v-if="selectedAbsensi" class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Guru</label>
                                <div class="mt-1 text-sm text-gray-900">{{ selectedAbsensi.user.name }}</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <div class="mt-1 text-sm text-gray-900">{{ formatDate(selectedAbsensi.tanggal) }}</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Ketidakhadiran</label>
                                <div class="mt-1">
                                    <span :class="getJenisBadgeClass(selectedAbsensi.jenis_ketidakhadiran)" 
                                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ getJenisText(selectedAbsensi.jenis_ketidakhadiran) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alasan</label>
                                <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded">
                                    {{ selectedAbsensi.alasan }}
                                </div>
                            </div>
                            
                            <div v-if="selectedAbsensi.keterangan">
                                <label class="block text-sm font-medium text-gray-700">Keterangan Tambahan</label>
                                <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded">
                                    {{ selectedAbsensi.keterangan }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status Laporan</label>
                                <div class="mt-1">
                                    <span :class="getStatusLaporanBadgeClass(selectedAbsensi.status_laporan)" 
                                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ getStatusLaporanText(selectedAbsensi.status_laporan) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div v-if="selectedAbsensi.status_laporan === 'diterima' && selectedAbsensi.penerima">
                                <label class="block text-sm font-medium text-gray-700">Diterima Oleh</label>
                                <div class="mt-1 text-sm text-gray-900">{{ selectedAbsensi.penerima.name }}</div>
                            </div>
                            
                            <div v-if="selectedAbsensi.tanggal_diterima">
                                <label class="block text-sm font-medium text-gray-700">Tanggal Diterima</label>
                                <div class="mt-1 text-sm text-gray-900">{{ formatDate(selectedAbsensi.tanggal_diterima) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="closeDetailModal" 
                                class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, reactive } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    absensiGuru: Object,
    daftarGuru: Array,
    filters: Object
})

const showDetailModal = ref(false)
const selectedAbsensi = ref(null)

const filterForm = reactive({
    status_laporan: props.filters?.status_laporan || '',
    jenis_ketidakhadiran: props.filters?.jenis_ketidakhadiran || '',
    bulan: props.filters?.bulan || '',
    tahun: props.filters?.tahun || '',
    guru_id: props.filters?.guru_id || ''
})

const bulanOptions = {
    '1': 'Januari', '2': 'Februari', '3': 'Maret', '4': 'April',
    '5': 'Mei', '6': 'Juni', '7': 'Juli', '8': 'Agustus',
    '9': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
}

const tahunOptions = [
    new Date().getFullYear(),
    new Date().getFullYear() - 1,
    new Date().getFullYear() - 2
]

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const getJenisText = (jenis) => {
    const jenisMap = {
        'sakit': 'Sakit',
        'izin': 'Izin',
        'dinas': 'Dinas Luar Kota',
        'cuti': 'Cuti'
    }
    return jenisMap[jenis] || jenis
}

const getJenisBadgeClass = (jenis) => {
    const classMap = {
        'sakit': 'bg-red-100 text-red-800',
        'izin': 'bg-blue-100 text-blue-800',
        'dinas': 'bg-purple-100 text-purple-800',
        'cuti': 'bg-green-100 text-green-800'
    }
    return classMap[jenis] || 'bg-gray-100 text-gray-800'
}

const getStatusLaporanText = (status) => {
    const statusMap = {
        'dilaporkan': 'Telah Dilaporkan',
        'diterima': 'Diterima'
    }
    return statusMap[status] || status
}

const getStatusLaporanBadgeClass = (status) => {
    const classMap = {
        'dilaporkan': 'bg-blue-100 text-blue-800',
        'diterima': 'bg-green-100 text-green-800'
    }
    return classMap[status] || 'bg-gray-100 text-gray-800'
}

const applyFilters = () => {
    router.get(route('absensi-guru.monitoring'), filterForm)
}

const resetFilters = () => {
    Object.keys(filterForm).forEach(key => {
        filterForm[key] = ''
    })
    router.get(route('absensi-guru.monitoring'))
}

const viewDetail = (absensi) => {
    selectedAbsensi.value = absensi
    showDetailModal.value = true
}

const closeDetailModal = () => {
    showDetailModal.value = false
    selectedAbsensi.value = null
}

const markAsReceived = (absensi) => {
    if (confirm('Apakah Anda yakin ingin menandai laporan ini sebagai diterima?')) {
        router.put(route('absensi-guru.update-status', absensi.id), {}, {
            onSuccess: () => {
                // Refresh page data
            }
        })
    }
}
</script>
