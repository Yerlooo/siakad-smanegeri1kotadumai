<template>
    <Head title="SIAKAD SMANSA" />

    <AppLayout page-title="Detail Jadwal Pelajaran">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Jadwal Pelajaran</h1>
                    <p class="text-gray-600">Informasi lengkap jadwal pelajaran</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('jadwal-pelajaran.index')" 
                          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali</span>
                    </Link>
                    <Link :href="route('jadwal-pelajaran.edit', jadwal.id)" 
                          class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit</span>
                    </Link>
                </div>
            </div>

            <!-- Jadwal Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-8">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center shadow-lg">
                                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-white">
                            <h2 class="text-3xl font-bold">{{ jadwal.mata_pelajaran?.nama_mapel || 'Mata Pelajaran' }}</h2>
                            <p class="text-xl opacity-90">{{ getHariText(jadwal.hari) }}, {{ jadwal.jam_mulai }} - {{ jadwal.jam_selesai }}</p>
                            <div class="mt-2">
                                <span class="inline-flex px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-medium">
                                    {{ jadwal.kelas?.nama_kelas || 'Kelas tidak tersedia' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Informasi Jadwal -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                Informasi Jadwal
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Hari:</span>
                                    <span class="font-medium text-gray-900">{{ getHariText(jadwal.hari) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jam Mulai:</span>
                                    <span class="font-medium text-gray-900">{{ jadwal.jam_mulai }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jam Selesai:</span>
                                    <span class="font-medium text-gray-900">{{ jadwal.jam_selesai }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Durasi:</span>
                                    <span class="font-medium text-gray-900">{{ getDurasi(jadwal.jam_mulai, jadwal.jam_selesai) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ruangan:</span>
                                    <span class="font-medium text-gray-900">{{ jadwal.ruangan || '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Mata Pelajaran -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                Mata Pelajaran
                            </h3>
                            <div v-if="jadwal.mata_pelajaran" class="bg-blue-50 p-4 rounded-lg">
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Nama:</span>
                                        <span class="text-blue-900 font-semibold">{{ jadwal.mata_pelajaran.nama_mapel }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Kode:</span>
                                        <span class="text-blue-900">{{ jadwal.mata_pelajaran.kode_mapel }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Jam/Minggu:</span>
                                        <span class="text-blue-900">{{ jadwal.mata_pelajaran.jam_pelajaran }} jam</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="bg-red-50 p-4 rounded-lg">
                                <p class="text-red-800 text-sm">Data mata pelajaran tidak tersedia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Kelas -->
                    <div class="mt-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Kelas
                        </h3>
                        <div v-if="jadwal.kelas" class="bg-purple-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <div class="text-purple-600 text-sm font-medium">Nama Kelas</div>
                                    <div class="text-purple-900 font-semibold">{{ jadwal.kelas.nama_kelas }}</div>
                                </div>
                                <div>
                                    <div class="text-purple-600 text-sm font-medium">Tingkat</div>
                                    <div class="text-purple-900 font-semibold">{{ jadwal.kelas.tingkat }}</div>
                                </div>
                                <div>
                                    <div class="text-purple-600 text-sm font-medium">Jurusan</div>
                                    <div class="text-purple-900 font-semibold">{{ jadwal.kelas.jurusan }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-red-50 p-4 rounded-lg">
                            <p class="text-red-800 text-sm">Data kelas tidak tersedia</p>
                        </div>
                    </div>

                    <!-- Informasi Guru -->
                    <div class="mt-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Guru Pengampu
                        </h3>
                        <div v-if="jadwal.guru" class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-green-600">
                                            {{ (jadwal.guru.name || 'U').charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-green-900 font-semibold">{{ jadwal.guru.name || 'Nama tidak tersedia' }}</div>
                                    <div class="text-green-600 text-sm">NIP: {{ jadwal.guru.nip || 'Tidak tersedia' }}</div>
                                    <div class="text-green-600 text-sm">{{ jadwal.guru.mata_pelajaran || 'Mata pelajaran tidak tersedia' }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-red-50 p-4 rounded-lg">
                            <p class="text-red-800 text-sm">Data guru tidak tersedia</p>
                        </div>
                    </div>

                    <!-- Statistik -->
                    <div class="mt-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Informasi Tambahan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-indigo-50 p-4 rounded-lg">
                                <div class="text-sm text-indigo-600 font-medium">Waktu Jadwal</div>
                                <div class="text-lg font-bold text-indigo-900">
                                    {{ getJamPelajaran(jadwal.jam_mulai) }}
                                </div>
                                <div class="text-xs text-indigo-600">jam pelajaran ke-</div>
                            </div>
                            <div class="bg-orange-50 p-4 rounded-lg">
                                <div class="text-sm text-orange-600 font-medium">Dibuat</div>
                                <div class="text-lg font-bold text-orange-900">
                                    {{ formatDate(jadwal.created_at) }}
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600 font-medium">Terakhir Update</div>
                                <div class="text-lg font-bold text-gray-900">
                                    {{ formatDate(jadwal.updated_at) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div v-if="jadwal.keterangan" class="mt-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Keterangan
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700 leading-relaxed">{{ jadwal.keterangan }}</p>
                        </div>
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
    jadwal: Object
})

const getHariText = (hari) => {
    const hariMap = {
        'senin': 'Senin',
        'selasa': 'Selasa',
        'rabu': 'Rabu',
        'kamis': 'Kamis',
        'jumat': 'Jumat',
        'sabtu': 'Sabtu',
        'minggu': 'Minggu'
    }
    return hariMap[hari] || hari
}

const getDurasi = (jamMulai, jamSelesai) => {
    if (!jamMulai || !jamSelesai) return '-'
    
    const start = new Date(`2000-01-01T${jamMulai}:00`)
    const end = new Date(`2000-01-01T${jamSelesai}:00`)
    const diffMs = end - start
    const diffMinutes = Math.floor(diffMs / 60000)
    
    if (diffMinutes >= 60) {
        const hours = Math.floor(diffMinutes / 60)
        const minutes = diffMinutes % 60
        return minutes > 0 ? `${hours} jam ${minutes} menit` : `${hours} jam`
    }
    
    return `${diffMinutes} menit`
}

const getJamPelajaran = (jamMulai) => {
    if (!jamMulai) return '-'
    
    const jadwalJam = {
        '07:00': 1,
        '07:45': 2,
        '08:30': 3,
        '09:15': 4,
        '10:15': 5,
        '11:00': 6,
        '11:45': 7,
        '13:00': 8,
        '13:45': 9,
        '14:30': 10
    }
    
    return jadwalJam[jamMulai] || '-'
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}
</script>
