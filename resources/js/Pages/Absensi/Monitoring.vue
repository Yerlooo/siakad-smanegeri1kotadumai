<template>
  <AppLayout title="Monitoring Absensi Real-Time">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        📊 Monitoring Absensi Real-Time
      </h2>
    </template>

    <div class="py-6 sm:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header dengan Auto Refresh -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
          <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div>
              <h3 class="text-lg font-medium text-gray-900">Monitoring Absensi</h3>
              <p class="text-sm text-gray-600 mt-1">
                Data absensi diperbarui secara otomatis setiap 30 detik
              </p>
            </div>
            <div class="flex items-center space-x-4">
              <!-- Last Updated -->
              <div class="text-sm text-gray-500">
                <div class="flex items-center space-x-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span>Update terakhir: {{ lastUpdated || 'Memuat...' }}</span>
                </div>
              </div>
              <!-- Auto Refresh Toggle -->
              <button 
                @click="toggleAutoRefresh"
                :class="autoRefresh ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
              >
                <svg class="w-4 h-4 mr-2" :class="{ 'animate-spin': autoRefresh }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ autoRefresh ? 'Auto Refresh ON' : 'Auto Refresh OFF' }}
              </button>
              <!-- Manual Refresh -->
              <button 
                @click="refreshData"
                :disabled="loading"
                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors"
              >
                <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Refresh</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
          <h3 class="text-base font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filter Monitoring
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
              <input 
                type="date" 
                v-model="filters.tanggal"
                @change="applyFilters"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
              <select 
                v-model="filters.kelas_id"
                @change="applyFilters"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Semua Kelas</option>
                <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
                  {{ kelas.nama_kelas }}
                </option>
              </select>
            </div>
            <div class="flex items-end">
              <button 
                @click="applyFilters"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
              </button>
            </div>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Mata Pelajaran</p>
                <p class="text-2xl font-bold text-gray-900">{{ summary.totalMataPelajaran }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Sudah Input Absensi</p>
                <p class="text-2xl font-bold text-green-600">{{ summary.sudahInput }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Belum Input</p>
                <p class="text-2xl font-bold text-yellow-600">{{ summary.belumInput }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Rata-rata Kehadiran</p>
                <p class="text-2xl font-bold text-purple-600">{{ summary.rataRataKehadiran }}%</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Monitoring Data -->
        <div v-if="monitoringData.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Data</h3>
          <p class="text-gray-600">Tidak ada jadwal mata pelajaran untuk tanggal yang dipilih</p>
        </div>

        <div v-else class="space-y-6">
          <div v-for="data in monitoringData" :key="data.jadwal_id" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <h4 class="font-semibold text-lg text-gray-900 mb-1">{{ data.mata_pelajaran }}</h4>
                  <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                      <span class="font-medium">{{ data.kelas }}</span>
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                      <span>{{ data.guru }}</span>
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <span>{{ data.jam }}</span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm text-gray-500">Kehadiran</div>
                  <div class="text-2xl font-bold text-blue-600">{{ data.persentase_kehadiran }}%</div>
                  <div v-if="data.last_input" class="text-xs text-gray-500 mt-1">
                    Input terakhir: {{ data.last_input }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Statistics -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
              <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center">
                  <div class="text-xl font-bold text-gray-900">{{ data.statistik.total_siswa }}</div>
                  <div class="text-xs text-gray-500">Total Siswa</div>
                </div>
                <div class="text-center">
                  <div class="text-xl font-bold text-green-600">{{ data.statistik.hadir }}</div>
                  <div class="text-xs text-gray-500">Hadir</div>
                </div>
                <div class="text-center">
                  <div class="text-xl font-bold text-yellow-600">{{ data.statistik.sakit }}</div>
                  <div class="text-xs text-gray-500">Sakit</div>
                </div>
                <div class="text-center">
                  <div class="text-xl font-bold text-blue-600">{{ data.statistik.izin }}</div>
                  <div class="text-xs text-gray-500">Izin</div>
                </div>
                <div class="text-center">
                  <div class="text-xl font-bold text-red-600">{{ data.statistik.alpha }}</div>
                  <div class="text-xs text-gray-500">Alpha</div>
                </div>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="px-6 py-3 bg-white border-b border-gray-200">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progress Input Absensi</span>
                <span class="text-sm text-gray-500">
                  {{ data.statistik.total_siswa - data.statistik.belum_absen }}/{{ data.statistik.total_siswa }}
                </span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: getProgressPercentage(data.statistik) + '%' }"
                ></div>
              </div>
            </div>

            <!-- Siswa Belum Absen -->
            <div v-if="data.statistik.belum_absen > 0" class="px-6 py-4">
              <h5 class="font-medium text-gray-900 mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Siswa Belum Absen ({{ data.statistik.belum_absen }})
              </h5>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <div v-for="siswa in data.siswa_belum_absen" :key="siswa.id" 
                     class="flex items-center p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-yellow-600 font-medium text-sm">{{ siswa.nama_lengkap.charAt(0) }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ siswa.nama_lengkap }}</p>
                    <p class="text-xs text-gray-500">NIS: {{ siswa.nis }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Completed State -->
            <div v-else class="px-6 py-4 bg-green-50 border-l-4 border-green-400">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-green-800">
                  Absensi sudah lengkap untuk mata pelajaran ini
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  monitoringData: Array,
  kelasList: Array,
  filters: Object
})

const loading = ref(false)
const autoRefresh = ref(true)
const lastUpdated = ref('')
const refreshInterval = ref(null)
const currentData = ref(props.monitoringData || [])

const filters = reactive({
  tanggal: props.filters?.tanggal || new Date().toISOString().split('T')[0],
  kelas_id: props.filters?.kelas_id || ''
})

// Computed properties
const monitoringData = computed(() => currentData.value)

const summary = computed(() => {
  const total = monitoringData.value.length
  const sudahInput = monitoringData.value.filter(data => data.statistik.belum_absen === 0).length
  const belumInput = total - sudahInput
  
  let totalKehadiran = 0
  let totalSiswa = 0
  
  monitoringData.value.forEach(data => {
    totalKehadiran += data.statistik.hadir
    totalSiswa += data.statistik.total_siswa
  })
  
  const rataRataKehadiran = totalSiswa > 0 ? Math.round((totalKehadiran / totalSiswa) * 100) : 0
  
  return {
    totalMataPelajaran: total,
    sudahInput,
    belumInput,
    rataRataKehadiran
  }
})

// Methods
const refreshData = async () => {
  loading.value = true
  
  try {
    const response = await fetch(route('absensi.monitoring.api'), {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    })
    
    if (response.ok) {
      const result = await response.json()
      currentData.value = result.data
      lastUpdated.value = result.last_updated
    }
  } catch (error) {
    console.error('Error refreshing data:', error)
  } finally {
    loading.value = false
  }
}

const toggleAutoRefresh = () => {
  autoRefresh.value = !autoRefresh.value
  
  if (autoRefresh.value) {
    startAutoRefresh()
  } else {
    stopAutoRefresh()
  }
}

const startAutoRefresh = () => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
  }
  
  refreshInterval.value = setInterval(() => {
    if (autoRefresh.value) {
      refreshData()
    }
  }, 30000) // 30 seconds
}

const stopAutoRefresh = () => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
    refreshInterval.value = null
  }
}

const applyFilters = () => {
  router.get(route('absensi.monitoring'), filters, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      currentData.value = page.props.monitoringData
    }
  })
}

const getProgressPercentage = (statistik) => {
  if (statistik.total_siswa === 0) return 0
  return Math.round(((statistik.total_siswa - statistik.belum_absen) / statistik.total_siswa) * 100)
}

// Lifecycle
onMounted(() => {
  // Set initial last updated time
  lastUpdated.value = new Date().toLocaleTimeString('id-ID')
  
  // Start auto refresh if enabled
  if (autoRefresh.value) {
    startAutoRefresh()
  }
})

onBeforeUnmount(() => {
  stopAutoRefresh()
})
</script>
