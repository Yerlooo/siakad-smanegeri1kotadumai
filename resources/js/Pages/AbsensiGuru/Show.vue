<template>
    <AppLayout title="Detail Absensi Guru">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Pengajuan Absensi
                </h2>
                <div class="flex space-x-2">
                    <Link v-if="absensiGuru.status_laporan === 'dilaporkan'" 
                          :href="route('absensi-guru.edit', absensiGuru.id)" 
                          class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </Link>
                    <Link :href="route('absensi-guru.index')" 
                          class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white">
                        <!-- Status Badge -->
                        <div class="mb-6 flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    Laporan Ketidakhadiran
                                </h3>
                                <span :class="getStatusBadgeClass(absensiGuru.status_laporan)" 
                                      class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full">
                                    {{ getStatusText(absensiGuru.status_laporan) }}
                                </span>
                            </div>
                            <div class="text-right text-sm text-gray-500">
                                <div>Dilaporkan: {{ formatDateTime(absensiGuru.created_at) }}</div>
                                <div v-if="absensiGuru.updated_at !== absensiGuru.created_at">
                                    Diperbarui: {{ formatDateTime(absensiGuru.updated_at) }}
                                </div>
                            </div>
                        </div>

                        <!-- Detail Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Guru</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ absensiGuru.user.name }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ formatDate(absensiGuru.tanggal) }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jenis Ketidakhadiran</label>
                                    <div class="mt-1">
                                        <span :class="getJenisBadgeClass(absensiGuru.jenis_ketidakhadiran)" 
                                              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ getJenisText(absensiGuru.jenis_ketidakhadiran) }}
                                        </span>
                                    </div>
                                </div>

                                <div v-if="absensiGuru.status_laporan === 'diterima' && absensiGuru.penerima">
                                    <label class="block text-sm font-medium text-gray-700">Diterima Oleh</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ absensiGuru.penerima.name }}</div>
                                </div>

                                <div v-if="absensiGuru.tanggal_diterima">
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Diterima</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ formatDateTime(absensiGuru.tanggal_diterima) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Alasan -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan</label>
                            <div class="bg-gray-50 rounded-md p-4">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ absensiGuru.alasan }}</p>
                            </div>
                        </div>

                        <!-- Keterangan Tambahan -->
                        <div v-if="absensiGuru.keterangan" class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan Tambahan</label>
                            <div class="bg-gray-50 rounded-md p-4">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ absensiGuru.keterangan }}</p>
                            </div>
                        </div>

                        <!-- Catatan Admin -->
                        <div v-if="absensiGuru.catatan_admin" class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan dari Tata Usaha</label>
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <p class="text-sm text-blue-900 whitespace-pre-wrap">{{ absensiGuru.catatan_admin }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div v-if="absensiGuru.status_laporan === 'dilaporkan'" class="mt-8 flex justify-end space-x-3">
                            <button @click="deleteAbsensi" 
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Hapus Pengajuan
                            </button>
                            <Link :href="route('absensi-guru.edit', absensiGuru.id)" 
                                  class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Edit Pengajuan
                            </Link>
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

defineProps({
    absensiGuru: Object
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

const formatDateTime = (dateTimeString) => {
    const date = new Date(dateTimeString)
    return date.toLocaleString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
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
        'izin': 'bg-yellow-100 text-yellow-800',
        'dinas': 'bg-blue-100 text-blue-800',
        'cuti': 'bg-green-100 text-green-800'
    }
    return classMap[jenis] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
    const statusMap = {
        'dilaporkan': 'Dilaporkan',
        'diterima': 'Diterima'
    }
    return statusMap[status] || status
}

const getStatusBadgeClass = (status) => {
    const classMap = {
        'dilaporkan': 'bg-yellow-100 text-yellow-800',
        'diterima': 'bg-green-100 text-green-800'
    }
    return classMap[status] || 'bg-gray-100 text-gray-800'
}

const deleteAbsensi = () => {
    if (confirm('Apakah Anda yakin ingin menghapus pengajuan absensi ini?')) {
        router.delete(route('absensi-guru.destroy', absensiGuru.id))
    }
}
</script>
