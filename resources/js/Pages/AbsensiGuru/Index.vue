<template>
    <AppLayout title="Absensi Guru">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Laporan Ketidakhadiran Guru
                </h2>
                <Link 
                    :href="route('absensi-guru.create')" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    + Laporkan Ketidakhadiran
                </Link>
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
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Riwayat Laporan Ketidakhadiran
                        </h3>

                        <!-- Tabel Data Absensi -->
                        <div v-if="absensiGuru.data.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
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
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="absensi in absensiGuru.data" :key="absensi.id">
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
                                            <span :class="getStatusBadgeClass(absensi.status_laporan)" 
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ getStatusText(absensi.status_laporan) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <Link :href="route('absensi-guru.show', absensi.id)" 
                                                      class="text-blue-600 hover:text-blue-900">
                                                    Detail
                                                </Link>
                                                <Link v-if="absensi.status_laporan === 'dilaporkan'" 
                                                      :href="route('absensi-guru.edit', absensi.id)" 
                                                      class="text-yellow-600 hover:text-yellow-900">
                                                    Edit
                                                </Link>
                                                <button v-if="absensi.status_laporan === 'dilaporkan'" 
                                                        @click="deleteAbsensi(absensi.id)" 
                                                        class="text-red-600 hover:text-red-900">
                                                    Hapus
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
                                Belum ada pengajuan absensi
                            </div>
                            <Link :href="route('absensi-guru.create')" 
                                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Ajukan Ketidakhadiran Pertama
                            </Link>
                        </div>

                        <!-- Pagination -->
                        <div v-if="absensiGuru.data.length > 0" class="mt-6">
                            <Pagination :links="absensiGuru.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'

defineProps({
    absensiGuru: Object,
    user: Object
})

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

const getStatusText = (status) => {
    const statusMap = {
        'dilaporkan': 'Telah Dilaporkan',
        'diterima': 'Diterima'
    }
    return statusMap[status] || status
}

const getStatusBadgeClass = (status) => {
    const classMap = {
        'dilaporkan': 'bg-blue-100 text-blue-800',
        'diterima': 'bg-green-100 text-green-800'
    }
    return classMap[status] || 'bg-gray-100 text-gray-800'
}

const deleteAbsensi = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus laporan ketidakhadiran ini?')) {
        router.delete(route('absensi-guru.destroy', id))
    }
}
</script>
