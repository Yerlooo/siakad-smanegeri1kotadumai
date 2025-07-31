<template>
    <Head title="Detail Kelas" />

    <AppLayout page-title="Detail Kelas">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Kelas</h1>
                    <p class="text-gray-600">Informasi lengkap data kelas</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('kelas.index')" 
                          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali</span>
                    </Link>
                    <Link v-if="$page.props.auth.user.role?.name !== 'murid'" :href="route('kelas.edit', kelas.id)" 
                          class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit</span>
                    </Link>
                </div>
            </div>

            <!-- Kelas Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-8">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center shadow-lg">
                                <span class="text-2xl font-bold text-purple-600">
                                    {{ kelas.tingkat }}
                                </span>
                            </div>
                        </div>
                        <div class="text-white">
                            <h2 class="text-3xl font-bold">{{ kelas.nama_kelas }}</h2>
                            <p class="text-xl opacity-90">{{ kelas.tingkat }} {{ kelas.jurusan }}</p>
                            <div class="mt-2">
                                <span class="inline-flex px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-medium">
                                    {{ getStatusText(kelas.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Informasi Kelas -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                Informasi Kelas
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nama Kelas:</span>
                                    <span class="font-medium text-gray-900">{{ kelas.nama_kelas }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tingkat:</span>
                                    <span class="font-medium text-gray-900">{{ kelas.tingkat }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jurusan:</span>
                                    <span class="font-medium text-gray-900">{{ kelas.jurusan }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Kapasitas:</span>
                                    <span class="font-medium text-gray-900">{{ kelas.kapasitas || '-' }} siswa</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ruang Kelas:</span>
                                    <span class="font-medium text-gray-900">{{ kelas.ruang_kelas || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-medium text-gray-900">{{ getStatusText(kelas.status) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Wali Kelas -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                Wali Kelas
                            </h3>
                            <div v-if="kelas.wali_kelas" class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-lg font-medium text-purple-600">
                                                {{ kelas.wali_kelas.name ? kelas.wali_kelas.name.charAt(0).toUpperCase() : '?' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ kelas.wali_kelas.name }}</p>
                                        <p class="text-sm text-gray-600">NIP: {{ kelas.wali_kelas.nip }}</p>
                                        <p class="text-sm text-gray-600">{{ kelas.wali_kelas.mata_pelajaran || 'Mata Pelajaran tidak tersedia' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-yellow-800 text-sm">Belum ada wali kelas yang ditugaskan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div v-if="kelas.keterangan" class="mt-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Keterangan
                        </h3>
                        <p class="text-gray-700 leading-relaxed">{{ kelas.keterangan }}</p>
                    </div>

                    <!-- Statistik -->
                    <div class="mt-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Statistik Kelas
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-sm text-blue-600 font-medium">Jumlah Siswa</div>
                                <div class="text-2xl font-bold text-blue-900">
                                    {{ siswas.length }}
                                </div>
                                <div class="text-xs text-blue-600">
                                    dari {{ kelas.kapasitas || '∞' }} kapasitas
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-sm text-green-600 font-medium">Siswa Aktif</div>
                                <div class="text-2xl font-bold text-green-900">
                                    {{ siswas.filter(s => s.status === 'aktif').length }}
                                </div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-sm text-purple-600 font-medium">Persentase</div>
                                <div class="text-2xl font-bold text-purple-900">
                                    {{ kelas.kapasitas ? Math.round((siswas.length / kelas.kapasitas) * 100) : 0 }}%
                                </div>
                                <div class="text-xs text-purple-600">kapasitas terisi</div>
                            </div>
                            <div class="bg-orange-50 p-4 rounded-lg">
                                <div class="text-sm text-orange-600 font-medium">Dibuat</div>
                                <div class="text-lg font-bold text-orange-900">
                                    {{ formatDate(kelas.created_at) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Siswa -->
                    <div class="mt-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Siswa</h3>
                            <div class="text-sm text-gray-600">
                                Total: {{ siswas.length }} siswa
                            </div>
                        </div>
                        
                        <div v-if="siswas.length > 0" class="bg-gray-50 rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(siswa, index) in siswas" :key="siswa.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ siswa.nis }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ siswa.nama_lengkap }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ siswa.jenis_kelamin === 'L' || siswa.jenis_kelamin === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="getStatusClass(siswa.status)" 
                                                      class="inline-flex px-2 text-xs font-semibold rounded-full">
                                                    {{ getStatusText(siswa.status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                            <div class="text-yellow-600">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <p class="text-lg font-medium text-yellow-800">Belum ada siswa di kelas ini</p>
                                <p class="text-sm text-yellow-600 mt-1">Siswa dapat ditambahkan melalui menu Data Siswa</p>
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

const props = defineProps({
    kelas: Object,
    siswas: Array
})

const getStatusText = (status) => {
    const texts = {
        'aktif': 'Aktif',
        'nonaktif': 'Non-Aktif',
        'lulus': 'Lulus',
        'pindah': 'Pindah',
        'keluar': 'Keluar'
    }
    return texts[status] || status
}

const getStatusClass = (status) => {
    const classes = {
        'aktif': 'bg-green-100 text-green-800',
        'nonaktif': 'bg-gray-100 text-gray-800',
        'lulus': 'bg-blue-100 text-blue-800',
        'pindah': 'bg-yellow-100 text-yellow-800',
        'keluar': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}
</script>
