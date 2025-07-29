<template>
    <AppLayout title="Nilai Saya">
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        📊 Nilai Saya
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ siswa.nama_lengkap }} - {{ siswa.kelas.nama_kelas }} - {{ filter.semester }} {{ filter.tahun_ajaran }}
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
                    <div class="text-center sm:text-right">
                        <div class="text-sm text-gray-600">Rata-rata</div>
                        <div class="text-lg font-bold text-blue-600">{{ statistik.rata_rata }}</div>
                    </div>
                    <div class="text-center sm:text-right">
                        <div class="text-sm text-gray-600">Tuntas</div>
                        <div class="text-lg font-bold text-green-600">{{ statistik.mapel_tuntas }}/{{ statistik.total_mapel }}</div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Filter -->
                <div class="bg-white shadow rounded-lg p-4 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-4 sm:space-y-0">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                            <select v-model="selectedSemester" 
                                    @change="updateFilter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                            <select v-model="selectedTahunAjaran" 
                                    @change="updateFilter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option v-for="tahun in filter.tahun_ajaran_options" 
                                        :key="tahun" :value="tahun">
                                    {{ tahun }}
                                </option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button @click="exportPdf" 
                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                                📄 Export PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistik Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="text-2xl font-bold text-blue-600">{{ statistik.total_mapel }}</div>
                        <div class="text-sm text-gray-600">Total Mata Pelajaran</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="text-2xl font-bold text-green-600">{{ statistik.mapel_tuntas }}</div>
                        <div class="text-sm text-gray-600">Mata Pelajaran Tuntas</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="text-2xl font-bold text-red-600">{{ statistik.mapel_belum_tuntas }}</div>
                        <div class="text-sm text-gray-600">Belum Tuntas</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="text-2xl font-bold text-purple-600">{{ statistik.rata_rata }}</div>
                        <div class="text-sm text-gray-600">Rata-rata Nilai</div>
                    </div>
                </div>

                <!-- Tabel Nilai - Desktop -->
                <div class="hidden md:block bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                        Mata Pelajaran
                                    </th>
                                    <th rowspan="2" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                        KKM
                                    </th>
                                    <th :colspan="jenisNilai.length" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                        Jenis Nilai
                                    </th>
                                    <th rowspan="2" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                        Nilai Akhir
                                    </th>
                                    <th rowspan="2" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                        Predikat
                                    </th>
                                    <th rowspan="2" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                                <tr>
                                    <th v-for="jenis in jenisNilai" :key="jenis.id"
                                        class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                        {{ jenis.nama }}
                                        <br>
                                        <span class="text-xs text-gray-400">({{ jenis.bobot }}%)</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="ringkasan in ringkasanNilai" :key="ringkasan.mata_pelajaran.id"
                                    :class="ringkasan.status === 'Belum Tuntas' ? 'bg-red-50' : ''">
                                    <!-- Mata Pelajaran -->
                                    <td class="px-6 py-4 whitespace-nowrap border-r">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ ringkasan.mata_pelajaran.nama_mapel }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ ringkasan.mata_pelajaran.kode_mapel }}
                                        </div>
                                    </td>
                                    
                                    <!-- KKM -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center border-r">
                                        <span class="text-sm font-medium text-blue-600">
                                            {{ ringkasan.kkm }}
                                        </span>
                                    </td>
                                    
                                    <!-- Nilai per Jenis -->
                                    <td v-for="jenis in jenisNilai" :key="jenis.id"
                                        class="px-3 py-4 whitespace-nowrap text-center border-r">
                                        <div v-if="ringkasan.nilai_detail[jenis.id]">
                                            <div class="text-sm font-medium"
                                                 :class="ringkasan.nilai_detail[jenis.id].nilai >= ringkasan.kkm 
                                                         ? 'text-green-600' : 'text-red-600'">
                                                {{ ringkasan.nilai_detail[jenis.id].nilai }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ formatDate(ringkasan.nilai_detail[jenis.id].tanggal_input) }}
                                            </div>
                                        </div>
                                        <div v-else class="text-sm text-gray-400">-</div>
                                    </td>
                                    
                                    <!-- Nilai Akhir -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center border-r">
                                        <div class="text-lg font-bold"
                                             :class="ringkasan.nilai_akhir >= ringkasan.kkm 
                                                     ? 'text-green-600' : 'text-red-600'">
                                            {{ ringkasan.nilai_akhir }}
                                        </div>
                                    </td>
                                    
                                    <!-- Predikat -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center border-r">
                                        <span :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getPredikatClass(ringkasan.nilai_akhir, ringkasan.kkm)
                                        ]">
                                            {{ ringkasan.predikat.huruf }}
                                        </span>
                                        <div class="text-xs text-gray-600 mt-1">
                                            {{ ringkasan.predikat.predikat }}
                                        </div>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            ringkasan.status === 'Tuntas' 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-red-100 text-red-800'
                                        ]">
                                            {{ ringkasan.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-4">
                    <div v-for="ringkasan in ringkasanNilai" :key="ringkasan.mata_pelajaran.id"
                         :class="[
                             'bg-white border rounded-lg p-4 shadow-sm',
                             ringkasan.status === 'Belum Tuntas' ? 'border-red-200 bg-red-50' : 'border-gray-200'
                         ]">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ ringkasan.mata_pelajaran.nama_mapel }}</h4>
                                <p class="text-sm text-gray-500">{{ ringkasan.mata_pelajaran.kode_mapel }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold" 
                                     :class="ringkasan.nilai_akhir >= ringkasan.kkm ? 'text-green-600' : 'text-red-600'">
                                    {{ ringkasan.nilai_akhir }}
                                </div>
                                <div class="text-xs text-gray-500">KKM: {{ ringkasan.kkm }}</div>
                            </div>
                        </div>
                        
                        <!-- Nilai Detail -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div v-for="jenis in jenisNilai" :key="jenis.id" class="text-center">
                                <div class="text-xs text-gray-600 mb-1">{{ jenis.nama }} ({{ jenis.bobot }}%)</div>
                                <div v-if="ringkasan.nilai_detail[jenis.id]" 
                                     :class="[
                                         'text-sm font-medium',
                                         ringkasan.nilai_detail[jenis.id].nilai >= ringkasan.kkm 
                                             ? 'text-green-600' : 'text-red-600'
                                     ]">
                                    {{ ringkasan.nilai_detail[jenis.id].nilai }}
                                </div>
                                <div v-else class="text-sm text-gray-400">-</div>
                            </div>
                        </div>
                        
                        <!-- Status & Predikat -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                            <span :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                getPredikatClass(ringkasan.nilai_akhir, ringkasan.kkm)
                            ]">
                                {{ ringkasan.predikat.huruf }} - {{ ringkasan.predikat.predikat }}
                            </span>
                            <span :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                ringkasan.status === 'Tuntas' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800'
                            ]">
                                {{ ringkasan.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="ringkasanNilai.length === 0" class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">📊</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Nilai</h3>
                    <p class="text-gray-600">
                        Nilai untuk semester {{ filter.semester }} tahun ajaran {{ filter.tahun_ajaran }} belum tersedia.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    siswa: Object,
    jenisNilai: Array,
    ringkasanNilai: Array,
    statistik: Object,
    filter: Object
})

const selectedSemester = ref(props.filter.semester)
const selectedTahunAjaran = ref(props.filter.tahun_ajaran)

const updateFilter = () => {
    router.get(route('nilai-saya.index'), {
        semester: selectedSemester.value,
        tahun_ajaran: selectedTahunAjaran.value
    }, {
        preserveState: true,
        replace: true
    })
}

const exportPdf = () => {
    window.open(route('nilai-saya.export-pdf', {
        semester: selectedSemester.value,
        tahun_ajaran: selectedTahunAjaran.value
    }), '_blank')
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

const getPredikatClass = (nilai, kkm) => {
    if (nilai >= 90) return 'bg-blue-100 text-blue-800'
    if (nilai >= 80) return 'bg-green-100 text-green-800'
    if (nilai >= kkm) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}
</script>
