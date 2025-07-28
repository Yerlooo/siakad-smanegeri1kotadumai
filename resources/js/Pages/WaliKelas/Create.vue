<template>
    <Head title="Assign Wali Kelas" />

    <AppLayout page-title="Assign Wali Kelas">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Assign Wali Kelas</h1>
                    <p class="text-gray-600">Tugaskan guru sebagai wali kelas untuk beberapa kelas sekaligus</p>
                </div>
                <Link :href="route('wali-kelas.index')" 
                      class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali</span>
                </Link>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Form Assign Wali Kelas</h2>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Summary Card -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-blue-800 font-medium">Informasi Assign</p>
                                <p class="text-xs text-blue-600 mt-1">
                                    {{ assignments.filter(a => a.kelas_id && a.wali_kelas_id).length }} dari {{ assignments.length }} assignment siap disimpan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Validation Errors -->
                    <div v-if="hasConflicts" class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="h-5 w-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-red-800 font-medium">Ada Konflik dalam Assignment</p>
                                <p class="text-xs text-red-600 mt-1">
                                    Pastikan tidak ada kelas atau guru yang dipilih lebih dari sekali
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment List -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Assignment Wali Kelas
                        </h3>
                        
                        <div class="space-y-4">
                            <div v-for="(assignment, index) in assignments" :key="index" 
                                 class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Kelas Info -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Kelas
                                        </label>
                                        <select
                                            v-model="assignment.kelas_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': isKelasAssigned(assignment.kelas_id, index) }"
                                            required
                                            @change="updateKelasInfo(index)"
                                        >
                                            <option value="">Pilih Kelas</option>
                                            <option v-for="kelasItem in availableKelas" :key="kelasItem.id" :value="kelasItem.id">
                                                {{ kelasItem.nama_kelas }} ({{ kelasItem.tingkat }} {{ kelasItem.jurusan }})
                                                {{ kelasItem.wali_kelas ? ' - Sudah ada wali kelas' : '' }}
                                            </option>
                                        </select>
                                        
                                        <!-- Kelas Current Status -->
                                        <div v-if="assignment.current_wali" class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs">
                                            <span class="text-yellow-800">
                                                Wali kelas saat ini: {{ assignment.current_wali }}
                                            </span>
                                        </div>
                                        
                                        <!-- Duplicate Kelas Warning -->
                                        <div v-if="isKelasAssigned(assignment.kelas_id, index)" class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-xs">
                                            <span class="text-red-800">
                                                Kelas ini sudah dipilih di assignment lain
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Guru Assignment -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Wali Kelas
                                        </label>
                                        <select
                                            v-model="assignment.wali_kelas_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': isGuruAssigned(assignment.wali_kelas_id) }"
                                            required
                                        >
                                            <option value="">Pilih Guru</option>
                                            <option v-for="guruItem in guru" :key="guruItem.id" :value="guruItem.id">
                                                {{ guruItem.nama_lengkap }} ({{ guruItem.nip }})
                                                {{ isGuruAssigned(guruItem.id) ? ' - Sudah ditugaskan' : '' }}
                                            </option>
                                        </select>
                                        
                                        <!-- Duplicate Guru Warning -->
                                        <div v-if="isGuruAssigned(assignment.wali_kelas_id)" class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-xs">
                                            <span class="text-red-800">
                                                Guru ini sudah ditugaskan di assignment lain
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remove Assignment -->
                                <div v-if="assignments.length > 1" class="mt-3 flex justify-end">
                                    <button type="button" @click="removeAssignment(index)"
                                            class="text-red-600 hover:text-red-900 text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus Assignment
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Add Assignment Button -->
                        <button type="button" @click="addAssignment"
                                class="w-full border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 hover:bg-blue-50 transition-colors">
                            <svg class="w-6 h-6 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span class="text-gray-600">Tambah Assignment Baru</span>
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6">
                        <Link :href="route('wali-kelas.index')" 
                              class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                            Batal
                        </Link>
                        <button type="submit" 
                                :disabled="form.processing || !isValidForm"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors disabled:opacity-50">
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Assignment ({{ assignments.filter(a => a.kelas_id && a.wali_kelas_id).length }})</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Information Card -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Catatan Penting</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Satu guru hanya bisa menjadi wali kelas untuk satu kelas</li>
                                <li>Jika kelas sudah memiliki wali kelas, assignment baru akan mengganti yang lama</li>
                                <li>Pastikan guru yang dipilih memiliki waktu yang cukup untuk mengelola kelas</li>
                                <li>Assignment akan langsung aktif setelah disimpan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    kelas: Array,
    guru: Array
})

const assignments = ref([
    { kelas_id: '', wali_kelas_id: '', current_wali: null }
])

const form = ref({
    processing: false
})

const availableKelas = computed(() => {
    return props.kelas || []
})

const addAssignment = () => {
    assignments.value.push({ 
        kelas_id: '', 
        wali_kelas_id: '', 
        current_wali: null 
    })
}

const removeAssignment = (index) => {
    if (assignments.value.length > 1) {
        assignments.value.splice(index, 1)
    }
}

const updateKelasInfo = (index) => {
    const kelasId = assignments.value[index].kelas_id
    const kelas = props.kelas.find(k => k.id == kelasId)
    
    if (kelas && kelas.wali_kelas) {
        assignments.value[index].current_wali = kelas.wali_kelas.nama_lengkap
    } else {
        assignments.value[index].current_wali = null
    }
}

const isGuruAssigned = (guruId) => {
    return assignments.value.filter(a => a.wali_kelas_id == guruId).length > 1
}

const isKelasAssigned = (kelasId, currentIndex) => {
    return assignments.value.some((a, index) => 
        a.kelas_id == kelasId && index !== currentIndex
    )
}

const hasConflicts = computed(() => {
    // Check for duplicate kelas
    const kelasIds = assignments.value.map(a => a.kelas_id).filter(Boolean)
    const uniqueKelasIds = [...new Set(kelasIds)]
    
    // Check for duplicate guru
    const guruIds = assignments.value.map(a => a.wali_kelas_id).filter(Boolean)
    const uniqueGuruIds = [...new Set(guruIds)]
    
    return kelasIds.length !== uniqueKelasIds.length || 
           guruIds.length !== uniqueGuruIds.length
})

const isValidForm = computed(() => {
    const validAssignments = assignments.value.filter(a => a.kelas_id && a.wali_kelas_id)
    return validAssignments.length > 0 && !hasConflicts.value
})

const submit = async () => {
    form.value.processing = true
    
    try {
        // Filter valid assignments only
        const validAssignments = assignments.value.filter(a => a.kelas_id && a.wali_kelas_id)
        
        // Process each valid assignment sequentially
        for (const assignment of validAssignments) {
            await new Promise((resolve, reject) => {
                useForm({
                    wali_kelas_id: assignment.wali_kelas_id
                }).post(route('wali-kelas.assign', assignment.kelas_id), {
                    preserveScroll: true,
                    onSuccess: () => resolve(),
                    onError: () => reject()
                })
            })
        }
        
        // Redirect to index page after all assignments are processed
        window.location.href = route('wali-kelas.index')
        
    } catch (error) {
        console.error('Error processing assignments:', error)
    } finally {
        form.value.processing = false
    }
}
</script>
