<template>
    <Head title="SIAKAD SMANSA" />

    <AppLayout page-title="Detail Guru">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Detail Guru</h1>
                    <p class="text-sm sm:text-base text-gray-600">Informasi lengkap data guru</p>
                </div>
                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
                    <Link :href="route('guru.index')" 
                          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors text-sm">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali</span>
                    </Link>
                    <Link :href="route('guru.edit', guru.id)" 
                          class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors text-sm">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit</span>
                    </Link>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-4 sm:px-6 py-6 sm:py-8">
                    <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-6">
                        <div class="flex-shrink-0 self-center sm:self-auto">
                            <img v-if="guru.profile_photo" 
                                 :src="guru.profile_photo" 
                                 :alt="guru.name"
                                 class="h-20 w-20 sm:h-24 sm:w-24 rounded-full object-cover border-4 border-white shadow-lg">
                            <div v-else 
                                 class="h-20 w-20 sm:h-24 sm:w-24 rounded-full bg-white flex items-center justify-center border-4 border-white shadow-lg">
                                <span class="text-2xl sm:text-3xl font-bold text-blue-600">
                                    {{ guru.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                        </div>
                        <div class="text-white text-center sm:text-left">
                            <h2 class="text-2xl sm:text-3xl font-bold">{{ guru.name }}</h2>
                            <p class="text-lg sm:text-xl opacity-90 break-all">{{ guru.email }}</p>
                            <div class="mt-2">
                                <span class="inline-flex px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-medium">
                                    {{ guru.role?.name === 'guru' ? 'Guru' : 'Staff' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 sm:px-6 py-4 sm:py-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Informasi Personal -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                Informasi Personal
                            </h3>
                            <div class="space-y-3">
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-gray-600 text-sm sm:text-base">Nama Lengkap:</span>
                                    <span class="font-medium text-gray-900 text-sm sm:text-base mt-1 sm:mt-0 break-words">{{ guru.name }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-gray-600 text-sm sm:text-base">Email:</span>
                                    <span class="font-medium text-gray-900 text-sm sm:text-base mt-1 sm:mt-0 break-all">{{ guru.email }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-gray-600 text-sm sm:text-base">No. Telepon:</span>
                                    <span class="font-medium text-gray-900 text-sm sm:text-base mt-1 sm:mt-0">{{ guru.phone || '-' }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-gray-600 text-sm sm:text-base">Alamat:</span>
                                    <span class="font-medium text-gray-900 text-sm sm:text-base mt-1 sm:mt-0 break-words">{{ guru.address || '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kepegawaian -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                Informasi Kepegawaian
                            </h3>
                            <div class="space-y-3">
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-gray-600 text-sm sm:text-base">Status:</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mt-1 sm:mt-0 self-start">
                                        Aktif
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-gray-600 text-sm sm:text-base">Bergabung:</span>
                                    <span class="font-medium text-gray-900 text-sm sm:text-base mt-1 sm:mt-0">{{ formatDate(guru.created_at) }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-gray-600 text-sm sm:text-base">Terakhir Update:</span>
                                    <span class="font-medium text-gray-900 text-sm sm:text-base mt-1 sm:mt-0">{{ formatDate(guru.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mata Pelajaran yang Diampu -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Mata Pelajaran yang Diampu</h3>
                </div>
                <div class="px-4 sm:px-6 py-4">
                    <div v-if="guru.jadwal_pelajaran && guru.jadwal_pelajaran.length > 0" class="space-y-3">
                        <div v-for="jadwal in guru.jadwal_pelajaran" :key="jadwal.id" 
                             class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 bg-gray-50 rounded-lg space-y-2 sm:space-y-0">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm sm:text-base">{{ jadwal.mata_pelajaran?.nama_mapel }}</h4>
                                <p class="text-xs sm:text-sm text-gray-600">Kelas {{ jadwal.kelas?.nama_kelas }} - {{ jadwal.hari }}</p>
                                <p class="text-xs text-gray-500">{{ jadwal.jam_mulai }} - {{ jadwal.jam_selesai }}</p>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 self-start sm:self-center">
                                {{ jadwal.mata_pelajaran?.kode_mapel }}
                            </span>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <div class="text-gray-400 text-lg mb-2">📚</div>
                        <p class="text-gray-500 text-sm sm:text-base">Belum ada mata pelajaran yang diampu</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    guru: Object
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}
</script>
