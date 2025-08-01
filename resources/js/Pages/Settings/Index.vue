<template>
    <Head title="SIAKAD SMANSA" />
    <AppLayout page-title="Pengaturan">
        <div class="max-w-4xl mx-auto">
            <!-- Flash Messages -->
            <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ $page.props.flash.error }}
            </div>
            
            <!-- Debug Error Message -->
            <div v-if="error" class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                <strong>Debug Error:</strong> {{ error }}
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Pengaturan Sistem</h3>
                    
                    <form @submit.prevent="updateSettings" class="space-y-6">
                        <!-- Informasi Sekolah -->
                        <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Sekolah</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                                    <input 
                                        type="text" 
                                        v-model="form.school_name" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :readonly="!canEdit"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">NPSN</label>
                                    <input 
                                        type="text" 
                                        v-model="form.school_npsn" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :readonly="!canEdit"
                                    >
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                                    <textarea 
                                        v-model="form.school_address" 
                                        rows="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :readonly="!canEdit"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kepala Tata Usaha</label>
                                    <input 
                                        type="text" 
                                        v-model="form.school_principal" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :readonly="!canEdit"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Telepon</label>
                                    <input 
                                        type="text" 
                                        v-model="form.school_phone" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :readonly="!canEdit"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Tahun Ajaran -->
                        <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Tahun Ajaran Aktif</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tahun Ajaran</label>
                                    <select 
                                        v-model="form.academic_year" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :disabled="!canEdit"
                                    >
                                        <option value="2024/2025">2024/2025</option>
                                        <option value="2025/2026">2025/2026</option>
                                        <option value="2026/2027">2026/2027</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Semester</label>
                                    <select 
                                        v-model="form.academic_semester" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :disabled="!canEdit"
                                    >
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Sistem -->
                        <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Statistik Sistem</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="text-center p-3 bg-white rounded-lg">
                                    <div class="text-xl sm:text-2xl font-bold text-blue-600">{{ statistics.total_guru }}</div>
                                    <div class="text-xs sm:text-sm text-gray-600">Total Guru</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded-lg">
                                    <div class="text-xl sm:text-2xl font-bold text-green-600">{{ statistics.total_siswa ?? 0 }}</div>
                                    <div class="text-xs sm:text-sm text-gray-600">Total Siswa</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded-lg">
                                    <div class="text-xl sm:text-2xl font-bold text-purple-600">{{ statistics.total_mata_pelajaran }}</div>
                                    <div class="text-xs sm:text-sm text-gray-600">Mata Pelajaran</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded-lg">
                                    <div class="text-xl sm:text-2xl font-bold text-orange-600">{{ statistics.total_jadwal ?? 0 }}</div>
                                    <div class="text-xs sm:text-sm text-gray-600">Jadwal Aktif</div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div v-if="canEdit" class="flex flex-col sm:flex-row justify-end gap-3">
                            <button 
                                type="button"
                                @click="resetForm"
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-200"
                            >
                                Reset
                            </button>
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 transition duration-200"
                            >
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan Pengaturan</span>
                            </button>
                        </div>
                        <div v-else class="text-center text-gray-600 text-sm">
                            Anda tidak memiliki akses untuk mengubah pengaturan sistem
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    },
    statistics: {
        type: Object,
        default: () => ({
            total_guru: 0,
            total_siswa: 0,
            total_mata_pelajaran: 0,
            total_jadwal: 0
        })
    },
    canEdit: {
        type: Boolean,
        default: true
    },
    error: {
        type: String,
        default: null
    }
})

const form = useForm({
    school_name: props.settings.school_name || '',
    school_npsn: props.settings.school_npsn || '',
    school_address: props.settings.school_address || '',
    school_principal: props.settings.school_principal || '',
    school_phone: props.settings.school_phone || '',
    academic_year: props.settings.academic_year || '2024/2025',
    academic_semester: props.settings.academic_semester || 'Ganjil'
})

const updateSettings = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Reset form errors on success
        },
        onError: (errors) => {
            console.log('Update errors:', errors)
        }
    })
}

const resetForm = () => {
    form.school_name = props.settings.school_name || ''
    form.school_npsn = props.settings.school_npsn || ''
    form.school_address = props.settings.school_address || ''
    form.school_principal = props.settings.school_principal || ''
    form.school_phone = props.settings.school_phone || ''
    form.academic_year = props.settings.academic_year || '2024/2025'
    form.academic_semester = props.settings.academic_semester || 'Ganjil'
    form.clearErrors()
}
</script>
