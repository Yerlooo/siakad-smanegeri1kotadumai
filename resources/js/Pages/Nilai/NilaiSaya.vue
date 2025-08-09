<template>
    <Head title="SIAKAD SMANSA" />
    <AppLayout title="Nilai Saya">
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        📊 Nilai Saya
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ siswa.nama_lengkap }} ({{ siswa.nis }}) - {{ semester.toUpperCase() }} {{ tahunAjaran }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <button @click="exportToPdf" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span>Export PDF</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Statistik Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ statistik.total_mapel || 0 }}</div>
                        <div class="text-sm text-gray-600">Total Mata Pelajaran</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ statistik.rata_rata || 0 }}</div>
                        <div class="text-sm text-gray-600">Rata-rata</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ statistik.nilai_tertinggi || 0 }}</div>
                        <div class="text-sm text-gray-600">Nilai Tertinggi</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ statistik.nilai_terendah || 0 }}</div>
                        <div class="text-sm text-gray-600">Nilai Terendah</div>
                    </div>
                </div>

                <!-- Grafik Visualisasi -->
                <div v-if="Object.keys(nilaiPerMapel).length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Grafik Radar - Nilai Per Mata Pelajaran -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <span class="mr-2">📊</span>
                            Radar Nilai per Mata Pelajaran
                        </h3>
                        <div class="relative h-80 flex items-center justify-center">
                            <canvas ref="radarChart"></canvas>
                        </div>
                    </div>

                    <!-- Grafik Doughnut - Distribusi Predikat -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <span class="mr-2">🎯</span>
                            Distribusi Predikat Nilai
                        </h3>
                        <div class="relative h-80 flex items-center justify-center">
                            <canvas ref="doughnutChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Grafik Bar - Perbandingan Jenis Penilaian -->
                <div v-if="Object.keys(nilaiPerMapel).length > 0" class="bg-white rounded-lg shadow p-6 mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="mr-2">📈</span>
                        Rata-rata Nilai per Jenis Penilaian
                    </h3>
                    <div class="relative h-80">
                        <canvas ref="barChart"></canvas>
                    </div>
                </div>

                <!-- Tabel Nilai per Mata Pelajaran -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Detail Nilai per Mata Pelajaran</h3>
                        <p class="text-sm text-gray-600">Semester {{ semester.charAt(0).toUpperCase() + semester.slice(1) }} - {{ tahunAjaran }}</p>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mata Pelajaran
                                    </th>
                                    <th v-for="jenisNilai in jenisNilaiAktif" :key="jenisNilai.id"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ jenisNilai.nama }}
                                        <div class="text-xs text-gray-400">({{ jenisNilai.bobot }}%)</div>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai Akhir
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Predikat
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(nilaiList, mataPelajaranId) in nilaiPerMapel" :key="mataPelajaranId">
                                    <!-- Nama Mata Pelajaran -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ nilaiList[0].mata_pelajaran.nama_mapel }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ nilaiList[0].mata_pelajaran.kode_mapel }}
                                        </div>
                                    </td>
                                    
                                    <!-- Nilai per Jenis -->
                                    <td v-for="jenisNilai in jenisNilaiAktif" :key="jenisNilai.id"
                                        class="px-6 py-4 whitespace-nowrap text-center">
                                        <span v-if="getNilaiByJenis(nilaiList, jenisNilai.id)"
                                              :class="[
                                                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                  getNilaiClass(getNilaiByJenis(nilaiList, jenisNilai.id).nilai)
                                              ]">
                                            {{ getNilaiByJenis(nilaiList, jenisNilai.id).nilai }}
                                        </span>
                                        <span v-else class="text-gray-400 text-sm">-</span>
                                    </td>
                                    
                                    <!-- Nilai Akhir -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span v-if="nilaiAkhir[mataPelajaranId]"
                                              :class="[
                                                  'inline-flex items-center px-3 py-1 rounded-full text-sm font-bold',
                                                  getNilaiClass(nilaiAkhir[mataPelajaranId])
                                              ]">
                                            {{ nilaiAkhir[mataPelajaranId] }}
                                        </span>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    
                                    <!-- Predikat -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span v-if="nilaiAkhir[mataPelajaranId]"
                                              :class="[
                                                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                  getPredikatClass(nilaiAkhir[mataPelajaranId])
                                              ]">
                                            {{ getPredikat(nilaiAkhir[mataPelajaranId]) }}
                                        </span>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4 p-4">
                        <div v-for="(nilaiList, mataPelajaranId) in nilaiPerMapel" :key="mataPelajaranId"
                             class="bg-white border rounded-lg p-4 shadow-sm">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ nilaiList[0].mata_pelajaran.nama_mapel }}</h4>
                                    <p class="text-sm text-gray-500">{{ nilaiList[0].mata_pelajaran.kode_mapel }}</p>
                                </div>
                                <div v-if="nilaiAkhir[mataPelajaranId]" class="text-right">
                                    <div :class="[
                                        'text-lg font-bold',
                                        nilaiAkhir[mataPelajaranId] >= 75 ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ nilaiAkhir[mataPelajaranId] }}
                                    </div>
                                    <div class="text-xs text-gray-500">Nilai Akhir</div>
                                </div>
                            </div>
                            
                            <!-- Detail Nilai -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div v-for="jenisNilai in jenisNilaiAktif" :key="jenisNilai.id">
                                    <div class="text-xs text-gray-500 mb-1">{{ jenisNilai.nama }} ({{ jenisNilai.bobot }}%)</div>
                                    <div v-if="getNilaiByJenis(nilaiList, jenisNilai.id)"
                                         :class="[
                                             'text-lg font-semibold',
                                             getNilaiByJenis(nilaiList, jenisNilai.id).nilai >= 75 ? 'text-green-600' : 'text-red-600'
                                         ]">
                                        {{ getNilaiByJenis(nilaiList, jenisNilai.id).nilai }}
                                    </div>
                                    <div v-else class="text-gray-400">-</div>
                                </div>
                            </div>
                            
                            <!-- Predikat -->
                            <div v-if="nilaiAkhir[mataPelajaranId]" class="flex justify-center">
                                <span :class="[
                                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                    getPredikatClass(nilaiAkhir[mataPelajaranId])
                                ]">
                                    {{ getPredikat(nilaiAkhir[mataPelajaranId]) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="Object.keys(nilaiPerMapel).length === 0" 
                         class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Nilai</h3>
                        <p class="mt-1 text-sm text-gray-500">Nilai Anda belum tersedia untuk semester ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, onMounted, ref, nextTick } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Chart, registerables } from 'chart.js'

// Register Chart.js components
Chart.register(...registerables)

const props = defineProps({
    siswa: Object,
    nilaiPerMapel: Object,
    jenisNilaiAktif: Array,
    nilaiAkhir: Object,
    statistik: Object,
    semester: String,
    tahunAjaran: String
})

// Refs untuk canvas elements
const radarChart = ref(null)
const doughnutChart = ref(null)
const barChart = ref(null)

// Chart instances
let radarChartInstance = null
let doughnutChartInstance = null
let barChartInstance = null

// Get nilai by jenis
const getNilaiByJenis = (nilaiList, jenisNilaiId) => {
    return nilaiList.find(nilai => nilai.jenis_nilai_id === jenisNilaiId)
}

// Get nilai class for styling
const getNilaiClass = (nilai) => {
    if (nilai >= 90) return 'bg-blue-100 text-blue-800'
    if (nilai >= 80) return 'bg-green-100 text-green-800'
    if (nilai >= 75) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}

// Get predikat
const getPredikat = (nilai) => {
    if (nilai >= 90) return 'A (Sangat Baik)'
    if (nilai >= 80) return 'B+ (Baik)'
    if (nilai >= 75) return 'B (Baik)'
    if (nilai >= 65) return 'C+ (Cukup)'
    if (nilai >= 55) return 'C (Cukup)'
    return 'D (Kurang)'
}

// Get predikat class
const getPredikatClass = (nilai) => {
    if (nilai >= 90) return 'bg-blue-100 text-blue-800'
    if (nilai >= 80) return 'bg-green-100 text-green-800'
    if (nilai >= 75) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}

// Get simple predikat for charts
const getSimplePredikat = (nilai) => {
    if (nilai >= 90) return 'A'
    if (nilai >= 80) return 'B+'
    if (nilai >= 75) return 'B'
    if (nilai >= 65) return 'C+'
    if (nilai >= 55) return 'C'
    return 'D'
}

// Prepare data for radar chart
const getRadarChartData = () => {
    const labels = []
    const data = []
    
    Object.entries(props.nilaiAkhir).forEach(([mataPelajaranId, nilai]) => {
        const nilaiList = props.nilaiPerMapel[mataPelajaranId]
        if (nilaiList && nilaiList.length > 0) {
            labels.push(nilaiList[0].mata_pelajaran.nama_mapel.length > 15 
                ? nilaiList[0].mata_pelajaran.nama_mapel.substring(0, 15) + '...'
                : nilaiList[0].mata_pelajaran.nama_mapel)
            data.push(nilai)
        }
    })
    
    return { labels, data }
}

// Prepare data for doughnut chart (predikat distribution)
const getDoughnutChartData = () => {
    const predikatCount = {}
    
    Object.values(props.nilaiAkhir).forEach(nilai => {
        const predikat = getSimplePredikat(nilai)
        predikatCount[predikat] = (predikatCount[predikat] || 0) + 1
    })
    
    return {
        labels: Object.keys(predikatCount),
        data: Object.values(predikatCount)
    }
}

// Prepare data for bar chart (average by jenis nilai)
const getBarChartData = () => {
    const jenisNilaiAverage = {}
    
    props.jenisNilaiAktif.forEach(jenis => {
        let total = 0
        let count = 0
        
        Object.values(props.nilaiPerMapel).forEach(nilaiList => {
            const nilai = getNilaiByJenis(nilaiList, jenis.id)
            if (nilai) {
                total += nilai.nilai
                count++
            }
        })
        
        jenisNilaiAverage[jenis.nama] = count > 0 ? (total / count).toFixed(1) : 0
    })
    
    return {
        labels: Object.keys(jenisNilaiAverage),
        data: Object.values(jenisNilaiAverage)
    }
}

// Create radar chart
const createRadarChart = () => {
    if (!radarChart.value) return
    
    const { labels, data } = getRadarChartData()
    
    radarChartInstance = new Chart(radarChart.value, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai Akhir',
                data: data,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgb(59, 130, 246)',
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(59, 130, 246)',
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        color: '#6B7280'
                    },
                    grid: {
                        color: '#E5E7EB'
                    },
                    pointLabels: {
                        font: {
                            size: 11
                        },
                        color: '#374151'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            return `Nilai: ${context.parsed.r}`
                        }
                    }
                }
            }
        }
    })
}

// Create doughnut chart
const createDoughnutChart = () => {
    if (!doughnutChart.value) return
    
    const { labels, data } = getDoughnutChartData()
    
    const colors = [
        '#3B82F6', // Blue for A
        '#10B981', // Green for B+
        '#F59E0B', // Yellow for B
        '#F97316', // Orange for C+
        '#EF4444', // Red for C
        '#6B7280'  // Gray for D
    ]
    
    doughnutChartInstance = new Chart(doughnutChart.value, {
        type: 'doughnut',
        data: {
            labels: labels.map(label => `Predikat ${label}`),
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 2,
                borderColor: '#fff',
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0)
                            const percentage = ((context.parsed / total) * 100).toFixed(1)
                            return `${context.label}: ${context.parsed} (${percentage}%)`
                        }
                    }
                }
            }
        }
    })
}

// Create bar chart
const createBarChart = () => {
    if (!barChart.value) return
    
    const { labels, data } = getBarChartData()
    
    barChartInstance = new Chart(barChart.value, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Rata-rata Nilai',
                data: data,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(249, 115, 22, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(249, 115, 22)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        color: '#6B7280'
                    },
                    grid: {
                        color: '#E5E7EB'
                    }
                },
                x: {
                    ticks: {
                        color: '#374151'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            return `Rata-rata: ${context.parsed.y}`
                        }
                    }
                }
            }
        }
    })
}

// Initialize charts
const initializeCharts = async () => {
    await nextTick()
    
    // Only create charts if there's data
    if (Object.keys(props.nilaiPerMapel).length === 0) {
        return
    }
    
    // Destroy existing charts if they exist
    if (radarChartInstance) radarChartInstance.destroy()
    if (doughnutChartInstance) doughnutChartInstance.destroy()
    if (barChartInstance) barChartInstance.destroy()
    
    // Create new charts
    createRadarChart()
    createDoughnutChart()
    createBarChart()
}

// Export to PDF
const exportToPdf = () => {
    window.open(route('nilai-saya.export-pdf'), '_blank')
}

// Initialize charts on component mount
onMounted(() => {
    initializeCharts()
})
</script>
