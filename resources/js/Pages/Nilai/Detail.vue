<template>
    <AppLayout title="Detail Nilai Siswa">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        📋 Detail Nilai - {{ mataPelajaran.nama_mapel }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Kelas: {{ kelas.nama_kelas }} • {{ kelas.tingkat }} {{ kelas.jurusan }}
                    </p>
                </div>
                <Link :href="route('nilai-siswa.index')" 
                      class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Siswa</p>
                                <p class="text-2xl font-bold text-gray-900">{{ siswaList.length }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Nilai Lengkap</p>
                                <p class="text-2xl font-bold text-gray-900">{{ siswaLengkap }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100">
                                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Progress</p>
                                <p class="text-2xl font-bold text-gray-900">{{ progressPersen }}%</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Rata-rata</p>
                                <p class="text-2xl font-bold text-gray-900">{{ rataRata.toFixed(1) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter dan Action Buttons -->
                <div class="bg-white rounded-lg shadow mb-6 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <!-- Filter Jenis Nilai -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Jenis Nilai</label>
                                <select v-model="selectedJenisNilai" @change="filterData"
                                        class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Semua Jenis Nilai</option>
                                    <option v-for="jenis in jenisNilai" :key="jenis.id" :value="jenis.id">
                                        {{ jenis.nama }}
                                    </option>
                                </select>
                            </div>

                            <!-- Filter Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select v-model="selectedStatus" @change="filterData"
                                        class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Semua Status</option>
                                    <option value="final">Final</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <!-- Export Buttons -->
                            <div class="flex items-center space-x-2">
                                <button @click="exportExcel" 
                                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Export Excel
                                </button>

                                <button @click="exportPdf" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Export PDF
                                </button>
                            </div>

                            <!-- Input Nilai Button -->
                            <Link :href="route('nilai-siswa.create', { 
                                    mata_pelajaran_id: mataPelajaran.id,
                                    kelas_id: kelas.id 
                                })"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Input Nilai
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Table Detail Nilai -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Nilai Siswa</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Daftar nilai siswa untuk semua jenis penilaian
                        </p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NIS / Nama Siswa
                                    </th>
                                    <th v-for="jenis in jenisNilai" :key="jenis.id"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ jenis.nama }}
                                        <br>
                                        <span class="text-gray-400 font-normal">({{ jenis.bobot }}%)</span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rata-rata
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(siswa, index) in filteredSiswa" :key="siswa.id" 
                                    class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ siswa.nama_lengkap }}</div>
                                        <div class="text-sm text-gray-500">{{ siswa.nis }}</div>
                                    </td>
                                    <td v-for="jenis in jenisNilai" :key="jenis.id"
                                        class="px-6 py-4 whitespace-nowrap text-center">
                                        <span v-if="getNilaiSiswa(siswa.id, jenis.id)" 
                                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="getNilaiClass(getNilaiSiswa(siswa.id, jenis.id))">
                                            {{ getNilaiSiswa(siswa.id, jenis.id).nilai }}
                                            <span v-if="getNilaiSiswa(siswa.id, jenis.id).status === 'draft'" 
                                                  class="ml-1 text-gray-400">*</span>
                                        </span>
                                        <span v-else class="text-gray-400 text-sm">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span v-if="getRataRataSiswa(siswa.id) > 0" 
                                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="getRataRataClass(getRataRataSiswa(siswa.id))">
                                            {{ getRataRataSiswa(siswa.id).toFixed(1) }}
                                        </span>
                                        <span v-else class="text-gray-400 text-sm">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="getStatusClass(getStatusSiswa(siswa.id))">
                                            {{ getStatusLabel(getStatusSiswa(siswa.id)) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="filteredSiswa.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data nilai</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada nilai yang diinput untuk filter yang dipilih.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    mataPelajaran: Object,
    kelas: Object,
    siswaList: Array,
    nilaiSiswa: Array,
    jenisNilai: Array
})

const selectedJenisNilai = ref('')
const selectedStatus = ref('')
const filteredSiswa = ref([])

// Computed values
const siswaLengkap = computed(() => {
    return props.siswaList.filter(siswa => {
        return props.jenisNilai.every(jenis => {
            return props.nilaiSiswa.some(nilai => 
                nilai.siswa_id === siswa.id && 
                nilai.jenis_nilai_id === jenis.id &&
                nilai.status === 'final'
            )
        })
    }).length
})

const progressPersen = computed(() => {
    if (props.siswaList.length === 0) return 0
    return Math.round((siswaLengkap.value / props.siswaList.length) * 100)
})

const rataRata = computed(() => {
    const allNilai = props.nilaiSiswa.filter(nilai => nilai.status === 'final')
    if (allNilai.length === 0) return 0
    
    const total = allNilai.reduce((sum, nilai) => sum + parseFloat(nilai.nilai), 0)
    return total / allNilai.length
})

// Methods
const filterData = () => {
    let filtered = [...props.siswaList]
    
    if (selectedJenisNilai.value) {
        filtered = filtered.filter(siswa => {
            return props.nilaiSiswa.some(nilai => 
                nilai.siswa_id === siswa.id && 
                nilai.jenis_nilai_id == selectedJenisNilai.value
            )
        })
    }
    
    if (selectedStatus.value) {
        filtered = filtered.filter(siswa => {
            return props.nilaiSiswa.some(nilai => 
                nilai.siswa_id === siswa.id && 
                nilai.status === selectedStatus.value
            )
        })
    }
    
    filteredSiswa.value = filtered
}

const getNilaiSiswa = (siswaId, jenisNilaiId) => {
    return props.nilaiSiswa.find(nilai => 
        nilai.siswa_id === siswaId && nilai.jenis_nilai_id === jenisNilaiId
    )
}

const getRataRataSiswa = (siswaId) => {
    const nilaiSiswaIni = props.nilaiSiswa.filter(nilai => 
        nilai.siswa_id === siswaId && nilai.status === 'final'
    )
    
    if (nilaiSiswaIni.length === 0) return 0
    
    // Hitung rata-rata berbobot
    let totalNilai = 0
    let totalBobot = 0
    
    nilaiSiswaIni.forEach(nilai => {
        const jenis = props.jenisNilai.find(j => j.id === nilai.jenis_nilai_id)
        if (jenis) {
            totalNilai += parseFloat(nilai.nilai) * (jenis.bobot / 100)
            totalBobot += jenis.bobot / 100
        }
    })
    
    return totalBobot > 0 ? totalNilai / totalBobot : 0
}

const getStatusSiswa = (siswaId) => {
    const nilaiSiswaIni = props.nilaiSiswa.filter(nilai => nilai.siswa_id === siswaId)
    
    if (nilaiSiswaIni.length === 0) return 'belum'
    
    const hasAllFinal = props.jenisNilai.every(jenis => {
        return nilaiSiswaIni.some(nilai => 
            nilai.jenis_nilai_id === jenis.id && nilai.status === 'final'
        )
    })
    
    return hasAllFinal ? 'lengkap' : 'sebagian'
}

const getNilaiClass = (nilaiObj) => {
    if (!nilaiObj) return 'bg-gray-100 text-gray-800'
    
    const nilai = parseFloat(nilaiObj.nilai)
    if (nilai >= 85) return 'bg-green-100 text-green-800'
    if (nilai >= 70) return 'bg-blue-100 text-blue-800'
    if (nilai >= 55) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}

const getRataRataClass = (rata) => {
    if (rata >= 85) return 'bg-green-100 text-green-800'
    if (rata >= 70) return 'bg-blue-100 text-blue-800'
    if (rata >= 55) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}

const getStatusClass = (status) => {
    switch(status) {
        case 'lengkap': return 'bg-green-100 text-green-800'
        case 'sebagian': return 'bg-yellow-100 text-yellow-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const getStatusLabel = (status) => {
    switch(status) {
        case 'lengkap': return 'Lengkap'
        case 'sebagian': return 'Sebagian'
        default: return 'Belum Ada'
    }
}

const exportExcel = () => {
    const url = route('nilai-siswa.export.excel', {
        mata_pelajaran_id: props.mataPelajaran.id,
        kelas_id: props.kelas.id
    })
    window.open(url, '_blank')
}

const exportPdf = () => {
    const url = route('nilai-siswa.export.pdf', {
        mata_pelajaran_id: props.mataPelajaran.id,
        kelas_id: props.kelas.id
    })
    window.open(url, '_blank')
}

// Initialize
onMounted(() => {
    filteredSiswa.value = [...props.siswaList]
})
</script>

<style scoped>
.transition-colors {
    transition: background-color 0.2s ease;
}
</style>
