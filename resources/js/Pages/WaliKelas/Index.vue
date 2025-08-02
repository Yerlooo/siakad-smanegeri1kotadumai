<template>
    <Head title="SIAKAD SMANSA" />
    <AppLayout page-title="Wali Kelas">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div>
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">Manajemen Wali Kelas</h2>
                    <p class="mt-1 text-sm text-gray-600">Kelola penugasan wali kelas untuk setiap kelas</p>
                </div>
                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
                    <button @click="openGlobalAssignModal" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Assign Wali Kelas
                    </button>
                    <button @click="refreshData" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kelas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tingkat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Wali Kelas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Siswa
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="kelas.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada data kelas
                                </td>
                            </tr>
                            <tr v-for="kelasItem in kelas" :key="kelasItem.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ kelasItem.nama_kelas }}</div>
                                    <div class="text-sm text-gray-500">{{ kelasItem.jurusan || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Kelas {{ kelasItem.tingkat }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="kelasItem.wali_kelas" class="text-sm">
                                        <div class="font-medium text-gray-900">{{ kelasItem.wali_kelas.name }}</div>
                                        <div class="text-gray-500">{{ kelasItem.wali_kelas.email }}</div>
                                    </div>
                                    <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Belum Ditugaskan
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ kelasItem.siswa_count || 0 }} siswa
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <!-- View Details -->
                                        <button @click="viewDetails(kelasItem)"
                                                class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded-md hover:bg-blue-50 transition-colors"
                                                title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        
                                        <!-- Assign/Change Wali Kelas -->
                                        <button @click="openQuickAssignModal(kelasItem)"
                                                class="text-green-600 hover:text-green-900 px-2 py-1 rounded-md hover:bg-green-50 transition-colors"
                                                :title="kelasItem.wali_kelas ? 'Ubah Wali Kelas' : 'Tugaskan Wali Kelas'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                        
                                        <!-- Remove Wali Kelas -->
                                        <button v-if="kelasItem.wali_kelas" 
                                                @click="removeWaliKelas(kelasItem)"
                                                class="text-red-600 hover:text-red-900 px-2 py-1 rounded-md hover:bg-red-50 transition-colors"
                                                title="Hapus Wali Kelas">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden">
                    <!-- Info jumlah data untuk mobile -->
                    <div class="p-3 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600">
                                {{ kelas.length }} kelas terdaftar
                            </span>
                            <span class="text-gray-500">
                                Manajemen Wali Kelas
                            </span>
                        </div>
                    </div>

                    <!-- Empty State for Mobile -->
                    <div v-if="kelas.length === 0" class="text-center py-12">
                        <div class="text-6xl mb-4">🏫</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data kelas</h3>
                        <p class="text-gray-500 mb-4">Mulai dengan menambahkan data kelas pertama</p>
                    </div>

                    <!-- Mobile Cards -->
                    <div v-for="kelasItem in kelas" :key="kelasItem.id" class="border-b border-gray-200 p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-3">
                            <!-- Class Icon -->
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 pr-2">
                                        <h3 class="text-sm font-medium text-gray-900 leading-tight">
                                            {{ kelasItem.nama_kelas }}
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ kelasItem.jurusan || 'Kelas ' + kelasItem.tingkat }}
                                        </p>
                                        
                                        <!-- Info Grid -->
                                        <div class="mt-2 space-y-2 text-xs">
                                            <!-- Row 1: Tingkat dan Jumlah Siswa -->
                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-gray-600">Tingkat:</span>
                                                    <span class="text-gray-900 mt-0.5">Kelas {{ kelasItem.tingkat }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-gray-600">Siswa:</span>
                                                    <span class="text-gray-900 mt-0.5">{{ kelasItem.siswa_count || 0 }} siswa</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Row 2: Wali Kelas -->
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-600">Wali Kelas:</span>
                                                <div v-if="kelasItem.wali_kelas" class="mt-0.5">
                                                    <div class="font-medium text-gray-900">{{ kelasItem.wali_kelas.name }}</div>
                                                    <div class="text-gray-500 break-all">{{ kelasItem.wali_kelas.email }}</div>
                                                </div>
                                                <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mt-0.5 self-start">
                                                    Belum Ditugaskan
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Menu -->
                                    <div class="flex-shrink-0">
                                        <div class="flex flex-col items-center space-y-1">
                                            <!-- View Details -->
                                            <button @click="viewDetails(kelasItem)"
                                                    class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-colors"
                                                    title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            
                                            <!-- Assign/Change Wali Kelas -->
                                            <button @click="openQuickAssignModal(kelasItem)"
                                                    class="p-2 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-full transition-colors"
                                                    :title="kelasItem.wali_kelas ? 'Ubah Wali Kelas' : 'Tugaskan Wali Kelas'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </button>
                                            
                                            <!-- Remove Wali Kelas -->
                                            <button v-if="kelasItem.wali_kelas" 
                                                    @click="removeWaliKelas(kelasItem)"
                                                    class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-full transition-colors"
                                                    title="Hapus Wali Kelas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Wali Kelas Modal -->
        <div v-if="showAssignModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAssignModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full mx-4 max-w-sm sm:max-w-lg">
                    <form @submit.prevent="assignWaliKelas">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                        {{ isQuickAssign ? 'Tugaskan Wali Kelas' : 'Pilih Kelas dan Wali Kelas' }}
                                    </h3>
                                    
                                    <!-- Kelas Selection (only for global assign) -->
                                    <div v-if="!isQuickAssign" class="mb-4">
                                        <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            Pilih Kelas
                                        </label>
                                        <select
                                            id="kelas_id"
                                            v-model="selectedKelas"
                                            @change="onKelasChange"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                                            required
                                        >
                                            <option value="">Pilih Kelas...</option>
                                            <option v-for="kelasItem in kelas" :key="kelasItem.id" :value="kelasItem">
                                                {{ kelasItem.nama_kelas }} ({{ kelasItem.tingkat }})
                                                {{ kelasItem.wali_kelas ? '- ' + kelasItem.wali_kelas.name : '' }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Kelas Info (for quick assign) -->
                                    <div v-if="isQuickAssign && selectedKelas" class="mb-4 p-3 bg-gray-50 rounded-md">
                                        <div class="text-sm font-medium text-gray-700">Kelas:</div>
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ selectedKelas.nama_kelas }} ({{ selectedKelas.tingkat }})
                                        </div>
                                        <div v-if="selectedKelas.wali_kelas" class="text-sm text-gray-600 mt-1">
                                            Wali Kelas saat ini: {{ selectedKelas.wali_kelas.name }}
                                        </div>
                                    </div>

                                    <div>
                                        <label for="wali_kelas_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            Pilih Guru sebagai Wali Kelas
                                        </label>
                                        <select
                                            id="wali_kelas_id"
                                            v-model="assignForm.wali_kelas_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                                            required
                                        >
                                            <option value="">Pilih Guru</option>
                                            <option v-for="guruItem in guru" :key="guruItem.id" :value="guruItem.id">
                                                {{ guruItem.name }} ({{ guruItem.email }})
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col space-y-3 sm:flex-row-reverse sm:space-y-0">
                            <button
                                type="submit"
                                :disabled="!assignForm.wali_kelas_id"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors"
                            >
                                Tugaskan
                            </button>
                            <button
                                type="button"
                                @click="closeAssignModal"
                                class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm transition-colors"
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
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    kelas: Array,
    guru: Array
})

const showAssignModal = ref(false)
const selectedKelas = ref(null)
const isQuickAssign = ref(false)
const assignForm = ref({
    wali_kelas_id: ''
})

const openQuickAssignModal = (kelasItem) => {
    selectedKelas.value = kelasItem
    assignForm.value.wali_kelas_id = kelasItem.wali_kelas?.id || ''
    isQuickAssign.value = true
    showAssignModal.value = true
}

const openAssignModal = (kelasItem) => {
    selectedKelas.value = kelasItem
    assignForm.value.wali_kelas_id = kelasItem.wali_kelas?.id || ''
    isQuickAssign.value = false
    showAssignModal.value = true
}

const openGlobalAssignModal = () => {
    // Open modal with first available kelas without wali kelas
    const availableKelas = props.kelas.find(k => !k.wali_kelas)
    if (availableKelas) {
        selectedKelas.value = availableKelas
        assignForm.value.wali_kelas_id = ''
    } else if (props.kelas.length > 0) {
        selectedKelas.value = props.kelas[0]
        assignForm.value.wali_kelas_id = props.kelas[0].wali_kelas?.id || ''
    }
    isQuickAssign.value = false
    showAssignModal.value = true
}

const onKelasChange = () => {
    if (selectedKelas.value) {
        assignForm.value.wali_kelas_id = selectedKelas.value.wali_kelas?.id || ''
    }
}

const closeAssignModal = () => {
    showAssignModal.value = false
    selectedKelas.value = null
    isQuickAssign.value = false
    assignForm.value.wali_kelas_id = ''
}

const assignWaliKelas = () => {
    router.post(route('wali-kelas.assign', selectedKelas.value.id), {
        wali_kelas_id: assignForm.value.wali_kelas_id
    }, {
        onSuccess: () => {
            closeAssignModal()
        }
    })
}

const viewDetails = (kelasItem) => {
    // Navigate to wali kelas detail page
    router.visit(route('wali-kelas.show', kelasItem.id))
}

const removeWaliKelas = (kelasItem) => {
    if (confirm(`Apakah Anda yakin ingin menghapus wali kelas dari ${kelasItem.nama_kelas}?`)) {
        router.post(route('wali-kelas.assign', kelasItem.id), {
            wali_kelas_id: null
        }, {
            onSuccess: () => {
                // Success message will be handled by flash messages
            }
        })
    }
}

const refreshData = () => {
    router.reload({ only: ['kelas', 'guru'] })
}
</script>
