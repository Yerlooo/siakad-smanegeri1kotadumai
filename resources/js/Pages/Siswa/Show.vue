<template>
    <Head title="SIAKAD SMANSA" />

    <AppLayout page-title="Detail Siswa">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Siswa</h1>
                    <p class="text-gray-600">Informasi lengkap data siswa</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('siswa.index')" 
                          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali</span>
                    </Link>
                    <Link v-if="canModify" :href="route('siswa.edit', siswa.id)" 
                          class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit</span>
                    </Link>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-blue-600 px-6 py-8">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <img v-if="siswa.foto" 
                                 :src="siswa.foto" 
                                 :alt="siswa.nama_lengkap"
                                 class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg">
                            <div v-else 
                                 class="h-24 w-24 rounded-full bg-white flex items-center justify-center border-4 border-white shadow-lg">
                                <span class="text-3xl font-bold text-green-600">
                                    {{ siswa.nama_lengkap.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                        </div>
                        <div class="text-white">
                            <h2 class="text-3xl font-bold">{{ siswa.nama_lengkap }}</h2>
                            <p class="text-xl opacity-90">NIS: {{ siswa.nis }}</p>
                            <div class="mt-2">
                                <span class="inline-flex px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-medium">
                                    {{ getStatusText(siswa.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Informasi Personal -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                Informasi Personal
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nama Lengkap:</span>
                                    <span class="font-medium text-gray-900">{{ siswa.nama_lengkap }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">NIS:</span>
                                    <span class="font-medium text-gray-900">{{ siswa.nis }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">NISN:</span>
                                    <span class="font-medium text-gray-900">{{ siswa.nisn || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jenis Kelamin:</span>
                                    <span class="font-medium text-gray-900">
                                        {{ siswa.jenis_kelamin === 'L' || siswa.jenis_kelamin === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tempat, Tgl Lahir:</span>
                                    <span class="font-medium text-gray-900">{{ siswa.tempat_lahir }}, {{ formatDate(siswa.tanggal_lahir) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Agama:</span>
                                    <span class="font-medium text-gray-900">{{ siswa.agama || '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kontak -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                Informasi Kontak
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium text-gray-900">{{ siswa.email || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">No. Telepon:</span>
                                    <span class="font-medium text-gray-900">{{ siswa.no_telepon || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Alamat:</span>
                                    <span class="font-medium text-gray-900">{{ siswa.alamat || '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Akademik -->
                    <div class="mt-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Informasi Akademik
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-sm text-blue-600 font-medium">Kelas</div>
                                <div class="text-lg font-bold text-blue-900">
                                    {{ siswa.kelas?.nama_kelas || 'Belum ada kelas' }}
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-sm text-green-600 font-medium">Status</div>
                                <div class="text-lg font-bold text-green-900">
                                    {{ getStatusText(siswa.status) }}
                                </div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-sm text-purple-600 font-medium">Terdaftar</div>
                                <div class="text-lg font-bold text-purple-900">
                                    {{ formatDate(siswa.created_at) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Orang Tua -->
                    <div class="mt-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Informasi Orang Tua/Wali
                        </h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h4 class="font-medium text-gray-700">Ayah</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Nama:</span>
                                        <span class="font-medium text-gray-900">{{ siswa.nama_ayah || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Pekerjaan:</span>
                                        <span class="font-medium text-gray-900">{{ siswa.pekerjaan_ayah || '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <h4 class="font-medium text-gray-700">Ibu</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Nama:</span>
                                        <span class="font-medium text-gray-900">{{ siswa.nama_ibu || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Pekerjaan:</span>
                                        <span class="font-medium text-gray-900">{{ siswa.pekerjaan_ibu || '-' }}</span>
                                    </div>
                                </div>
                            </div>
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
import { computed } from 'vue'

const props = defineProps({
    siswa: Object,
    auth: Object // pastikan auth dikirim dari backend
})

// Hak akses: hanya admin/tata usaha/kepala sekolah yang bisa edit
const canModify = computed(() => {
    const allowedRoles = ['admin', 'tu', 'kepala_sekolah']
    const userRole = props?.auth?.user?.role
    return allowedRoles.includes(userRole)
})

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
</script>
