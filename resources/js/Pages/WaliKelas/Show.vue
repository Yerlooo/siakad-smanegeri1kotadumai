<template>
    <Head title="Detail Wali Kelas" />

    <AppLayout page-title="Detail Wali Kelas">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Wali Kelas</h1>
                    <p class="text-gray-600">Informasi lengkap wali kelas</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('wali-kelas.index')" 
                          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali</span>
                    </Link>
                    <button @click="openAssignModal" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Ubah Wali Kelas</span>
                    </button>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center shadow-lg">
                                <span class="text-2xl font-bold text-indigo-600">
                                    {{ kelas.nama_kelas.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                        </div>
                        <div class="text-white">
                            <h2 class="text-3xl font-bold">{{ kelas.nama_kelas }}</h2>
                            <p class="text-xl opacity-90">{{ kelas.tingkat }} {{ kelas.jurusan }}</p>
                            <div class="mt-2">
                                <span v-if="kelas.wali_kelas" class="inline-flex px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-medium">
                                    Wali Kelas: {{ kelas.wali_kelas.nama_lengkap || kelas.wali_kelas.name || 'Nama tidak tersedia' }}
                                </span>
                                <span v-else class="inline-flex px-3 py-1 bg-yellow-500 bg-opacity-80 rounded-full text-sm font-medium">
                                    Belum Ada Wali Kelas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <!-- Informasi Kelas -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
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
                                    <span class="font-medium text-gray-900">{{ kelas.jurusan || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Kapasitas:</span>
                                    <span class="font-medium text-gray-900">{{ kelas.kapasitas || '-' }} siswa</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ruang Kelas:</span>
                                    <span class="font-medium text-gray-900">{{ kelas.ruang_kelas || '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                Informasi Wali Kelas
                            </h3>
                            <div v-if="kelas.wali_kelas" class="bg-indigo-50 p-4 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-lg font-medium text-indigo-600">
                                                {{ (kelas.wali_kelas.nama_lengkap || kelas.wali_kelas.name || 'G').charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-indigo-900">{{ kelas.wali_kelas.nama_lengkap || kelas.wali_kelas.name || 'Nama tidak tersedia' }}</p>
                                        <p class="text-sm text-indigo-600">NIP: {{ kelas.wali_kelas.nip || '-' }}</p>
                                        <p class="text-sm text-indigo-600">{{ kelas.wali_kelas.email || '-' }}</p>
                                        <p class="text-sm text-indigo-600">{{ kelas.wali_kelas.no_telepon || '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="bg-yellow-50 p-4 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-yellow-800">Belum Ada Wali Kelas</p>
                                        <p class="text-sm text-yellow-600">Silakan tugaskan guru sebagai wali kelas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik -->
                    <div class="space-y-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Statistik Kelas
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-sm text-blue-600 font-medium">Total Siswa</div>
                                <div class="text-2xl font-bold text-blue-900">{{ siswa?.length || 0 }}</div>
                                <div class="text-xs text-blue-600">dari {{ kelas.kapasitas || '∞' }} kapasitas</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-sm text-green-600 font-medium">Siswa Aktif</div>
                                <div class="text-2xl font-bold text-green-900">
                                    {{ siswa?.filter(s => s.status === 'aktif').length || 0 }}
                                </div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-sm text-purple-600 font-medium">Laki-laki</div>
                                <div class="text-2xl font-bold text-purple-900">
                                    {{ siswa?.filter(s => s.jenis_kelamin === 'Laki-laki' || s.jenis_kelamin === 'L').length || 0 }}
                                </div>
                            </div>
                            <div class="bg-pink-50 p-4 rounded-lg">
                                <div class="text-sm text-pink-600 font-medium">Perempuan</div>
                                <div class="text-2xl font-bold text-pink-900">
                                    {{ siswa?.filter(s => s.jenis_kelamin === 'Perempuan' || s.jenis_kelamin === 'P').length || 0 }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Siswa -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            Daftar Siswa di Kelas
                        </h3>
                        
                        <div v-if="siswa?.length > 0" class="bg-gray-50 rounded-lg overflow-hidden">
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
                                        <tr v-for="(siswaItem, index) in siswa" :key="siswaItem.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ siswaItem.nis }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ siswaItem.nama_lengkap }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ getGenderDisplay(siswaItem.jenis_kelamin) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="getStatusClass(siswaItem.status)" class="inline-flex px-2 text-xs font-semibold rounded-full">
                                                    {{ getStatusText(siswaItem.status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-yellow-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            <p class="text-lg font-medium text-yellow-800">Belum ada siswa di kelas ini</p>
                            <p class="text-sm text-yellow-600 mt-1">Siswa dapat ditambahkan melalui menu Data Siswa</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Modal -->
        <div v-if="showAssignModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAssignModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="assignWaliKelas">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ kelas.wali_kelas ? 'Ubah' : 'Tugaskan' }} Wali Kelas untuk {{ kelas.nama_kelas }}
                            </h3>
                            <div>
                                <label for="wali_kelas_id" class="block text-sm font-medium text-gray-700">Pilih Guru</label>
                                <select
                                    id="wali_kelas_id"
                                    v-model="assignForm.wali_kelas_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Pilih Guru</option>
                                    <option v-for="guruItem in guru" :key="guruItem.id" :value="guruItem.id">
                                        {{ guruItem.nama_lengkap || guruItem.name || 'Nama tidak tersedia' }} ({{ guruItem.nip || guruItem.email }})
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="submit"
                                :disabled="!assignForm.wali_kelas_id"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            >
                                {{ kelas.wali_kelas ? 'Ubah' : 'Tugaskan' }}
                            </button>
                            <button
                                type="button"
                                @click="closeAssignModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    kelas: Object,
    siswa: {
        type: Array,
        default: () => []
    },
    guru: {
        type: Array,
        default: () => []
    }
})

const showAssignModal = ref(false)
const assignForm = ref({
    wali_kelas_id: props.kelas.wali_kelas?.id || ''
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

const getStatusClass = (status) => {
    const classes = {
        'aktif': 'bg-green-100 text-green-800',
        'lulus': 'bg-blue-100 text-blue-800',
        'pindah': 'bg-yellow-100 text-yellow-800',
        'keluar': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getGenderDisplay = (jenis_kelamin) => {
    if (jenis_kelamin === 'L' || jenis_kelamin === 'Laki-laki') {
        return 'Laki-laki'
    } else if (jenis_kelamin === 'P' || jenis_kelamin === 'Perempuan') {
        return 'Perempuan'
    }
    return jenis_kelamin || '-'
}

const openAssignModal = () => {
    assignForm.value.wali_kelas_id = props.kelas.wali_kelas?.id || ''
    showAssignModal.value = true
}

const closeAssignModal = () => {
    showAssignModal.value = false
    assignForm.value.wali_kelas_id = ''
}

const assignWaliKelas = () => {
    router.post(route('wali-kelas.assign', props.kelas.id), {
        wali_kelas_id: assignForm.value.wali_kelas_id
    }, {
        onSuccess: () => {
            closeAssignModal()
        }
    })
}
</script>
