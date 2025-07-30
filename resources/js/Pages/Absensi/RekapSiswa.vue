<template>
  <AppLayout title="Rekap Absensi Siswa">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Rekap Absensi Siswa
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            
            <!-- Navigation -->
            <div class="mb-6 flex justify-between items-center">
              <div class="flex space-x-4">
                <Link 
                  :href="route('absensi.index')"
                  class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition duration-200"
                >
                  ← Kembali ke Absensi
                </Link>
                <Link 
                  v-if="isKepalaSekolah"
                  :href="route('absensi.laporan')"
                  class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition duration-200"
                >
                  📈 Laporan Absensi
                </Link>
              </div>
              
              <!-- Export Buttons -->
              <div class="flex space-x-2">
                <button 
                  @click="exportExcel"
                  :disabled="exporting"
                  class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200 flex items-center disabled:opacity-50"
                >
                  <svg v-if="exporting === 'excel'" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  {{ exporting === 'excel' ? 'Mengexport...' : 'Export Excel' }}
                </button>
                
                <button 
                  @click="exportPdf"
                  :disabled="exporting"
                  class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200 flex items-center disabled:opacity-50"
                >
                  <svg v-if="exporting === 'pdf'" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                  {{ exporting === 'pdf' ? 'Mengexport...' : 'Export PDF' }}
                </button>
              </div>
            </div>

            <!-- Filter Section -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Data</h3>
              <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                  <select 
                    v-model="filters.kelas_id"
                    @change="applyFilters"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  >
                    <option value="">Semua Kelas</option>
                    <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
                      {{ kelas.nama_kelas }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                  <select 
                    v-model="filters.mata_pelajaran_id"
                    @change="applyFilters"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  >
                    <option value="">Semua Mata Pelajaran</option>
                    <option v-for="mapel in mataPelajaranList" :key="mapel.id" :value="mapel.id">
                      {{ mapel.nama_mapel }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                  <select 
                    v-model="filters.bulan"
                    @change="applyFilters"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  >
                    <option v-for="(nama, value) in bulanOptions" :key="value" :value="value">
                      {{ nama }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                  <select 
                    v-model="filters.tahun"
                    @change="applyFilters"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  >
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                  </select>
                </div>
                <div class="flex items-end">
                  <button 
                    @click="applyFilters"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200"
                  >
                    Filter
                  </button>
                </div>
              </div>
            </div>

            <!-- Summary Cards -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="bg-green-100 p-4 rounded-lg">
                <div class="text-green-800 text-sm font-medium">Total Siswa</div>
                <div class="text-green-900 text-2xl font-bold">{{ rekapData.length }}</div>
              </div>
              <div class="bg-blue-100 p-4 rounded-lg">
                <div class="text-blue-800 text-sm font-medium">Rata-rata Kehadiran</div>
                <div class="text-blue-900 text-2xl font-bold">{{ averageAttendance }}%</div>
              </div>
              <div class="bg-yellow-100 p-4 rounded-lg">
                <div class="text-yellow-800 text-sm font-medium">Periode</div>
                <div class="text-yellow-900 text-lg font-bold">{{ namaBulan }} {{ tahun }}</div>
              </div>
              <div class="bg-purple-100 p-4 rounded-lg">
                <div class="text-purple-800 text-sm font-medium">Total Absensi</div>
                <div class="text-purple-900 text-2xl font-bold">{{ totalAbsensi }}</div>
              </div>
            </div>

            <!-- Rekap Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      No
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nama Siswa
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Kelas
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      NISN
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Hadir
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Sakit
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Izin
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Alpha
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      % Hadir
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(data, index) in rekapData" :key="data.siswa.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ index + 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ data.siswa.nama_lengkap }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ data.siswa.kelas?.nama_kelas || 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ data.siswa.nisn }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ data.statistik.hadir }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ data.statistik.sakit }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ data.statistik.izin }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ data.statistik.alpha }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                      {{ data.statistik.total }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                      <span 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getPercentageClass(data.statistik.persentase_hadir)"
                      >
                        {{ data.statistik.persentase_hadir }}%
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                      <button 
                        @click="showDetail(data)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        Detail
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Detail Modal -->
            <div v-if="showDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
              <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                  <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                      Detail Absensi - {{ selectedSiswa?.nama_lengkap }}
                    </h3>
                    <button @click="closeDetail" class="text-gray-400 hover:text-gray-600">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  
                  <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Pelajaran</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="absensi in selectedAbsensi" :key="absensi.id">
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ formatTanggal(absensi.tanggal) }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ absensi.mata_pelajaran?.nama_mapel || 'N/A' }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span 
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="getStatusClass(absensi.status)"
                            >
                              {{ formatStatus(absensi.status) }}
                            </span>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatWaktu(absensi.jam_masuk) }} - {{ formatWaktu(absensi.jam_keluar) }}
                          </td>
                          <td class="px-6 py-4 text-sm text-gray-500">
                            {{ absensi.keterangan || '-' }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button 
                              @click="confirmDelete(absensi)"
                              class="text-red-600 hover:text-red-900 transition duration-200"
                              title="Hapus data absensi"
                            >
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                              </svg>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Confirm Delete Modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
              <div class="relative mx-auto p-6 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                  <!-- Warning Icon -->
                  <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                  </div>
                  
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                    Konfirmasi Hapus
                  </h3>
                  <p class="text-sm text-gray-500 mb-4">
                    Apakah Anda yakin ingin menghapus data absensi ini? Tindakan ini tidak dapat dibatalkan.
                  </p>
                  <div class="flex space-x-4 justify-center">
                    <button 
                      @click="cancelDelete"
                      class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition duration-200"
                    >
                      Batal
                    </button>
                    <button 
                      @click="deleteAbsensi"
                      :disabled="deleting"
                      class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 transition duration-200 flex items-center"
                    >
                      <svg v-if="deleting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ deleting ? 'Menghapus...' : 'Hapus' }}
                    </button>
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
import { ref, reactive, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  rekapData: Array,
  kelasList: Array, // Sudah terfilter dari backend untuk guru
  mataPelajaranList: Array, // Sudah terfilter dari backend untuk guru
  selectedKelas: String,
  selectedMataPelajaran: String,
  bulan: Number,
  tahun: Number,
  namaBulan: String,
  isKepalaSekolah: Boolean
})

const showDetailModal = ref(false)
const selectedSiswa = ref(null)
const selectedAbsensi = ref([])

// State untuk delete modal
const showDeleteModal = ref(false)
const selectedAbsensiToDelete = ref(null)
const deleting = ref(false)

// State untuk export
const exporting = ref(false)

const filters = reactive({
  kelas_id: props.selectedKelas || '',
  mata_pelajaran_id: props.selectedMataPelajaran || '',
  bulan: Number(props.bulan),
  tahun: Number(props.tahun)
})

const bulanOptions = {
  1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April',
  5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus',
  9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
}

const averageAttendance = computed(() => {
  if (props.rekapData.length === 0) return 0
  const total = props.rekapData.reduce((sum, data) => sum + data.statistik.persentase_hadir, 0)
  return Math.round(total / props.rekapData.length)
})

const totalAbsensi = computed(() => {
  return props.rekapData.reduce((sum, data) => sum + data.statistik.total, 0)
})

const applyFilters = () => {
  // Pastikan bulan dan tahun bertipe number sebelum dikirim
  filters.bulan = Number(filters.bulan)
  filters.tahun = Number(filters.tahun)
  router.get(route('absensi.rekap-siswa'), filters, {
    preserveState: true,
    preserveScroll: true
  })
}

const getPercentageClass = (percentage) => {
  if (percentage >= 90) return 'bg-green-100 text-green-800'
  if (percentage >= 75) return 'bg-yellow-100 text-yellow-800'
  if (percentage >= 60) return 'bg-orange-100 text-orange-800'
  return 'bg-red-100 text-red-800'
}

const getStatusClass = (status) => {
  const classes = {
    'hadir': 'bg-green-100 text-green-800',
    'sakit': 'bg-yellow-100 text-yellow-800',
    'izin': 'bg-blue-100 text-blue-800',
    'alpha': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status) => {
  const statusMap = {
    'hadir': 'Hadir',
    'sakit': 'Sakit',
    'izin': 'Izin',
    'alpha': 'Alpha'
  }
  return statusMap[status] || status
}

const showDetail = (data) => {
  selectedSiswa.value = data.siswa
  selectedAbsensi.value = data.absensi
  showDetailModal.value = true
}

const closeDetail = () => {
  showDetailModal.value = false
  selectedSiswa.value = null
  selectedAbsensi.value = []
}

const formatTanggal = (tanggal) => {
  return new Date(tanggal).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatWaktu = (waktu) => {
  if (!waktu) return '-'
  try {
    return new Date(waktu).toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    })
  } catch (e) {
    return '-'
  }
}

const confirmDelete = (absensi) => {
  selectedAbsensiToDelete.value = absensi
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  selectedAbsensiToDelete.value = null
}

const deleteAbsensi = () => {
  if (!selectedAbsensiToDelete.value) return
  
  deleting.value = true
  const absensiToDelete = selectedAbsensiToDelete.value
  
  router.delete(route('absensi.destroy', absensiToDelete.id), {
    onSuccess: () => {
      deleting.value = false
      showDeleteModal.value = false
      selectedAbsensiToDelete.value = null
      
      // Refresh data setelah hapus
      applyFilters()
      
      // Juga refresh detail modal jika masih terbuka
      if (showDetailModal.value && selectedSiswa.value) {
        // Filter out deleted record from selectedAbsensi
        selectedAbsensi.value = selectedAbsensi.value.filter(
          item => item.id !== absensiToDelete.id
        )
      }
    },
    onError: (errors) => {
      deleting.value = false
      console.error('Error deleting absensi:', errors)
      // Tampilkan error message (bisa ditambahkan modal error jika perlu)
      alert('Terjadi kesalahan saat menghapus data absensi.')
    }
  })
}

const exportExcel = () => {
  exporting.value = 'excel'
  
  // Buat URL dengan query parameters yang sama dengan filter current
  const queryParams = new URLSearchParams({
    ...filters,
    format: 'excel'
  }).toString()
  
  // Buat temporary link untuk download
  const downloadUrl = `${route('absensi.rekap-siswa')}?${queryParams}`
  
  // Download file
  window.open(downloadUrl, '_blank')
  
  // Reset state setelah delay
  setTimeout(() => {
    exporting.value = false
  }, 2000)
}

const exportPdf = () => {
  exporting.value = 'pdf'
  
  // Buat URL dengan query parameters yang sama dengan filter current
  const queryParams = new URLSearchParams({
    ...filters,
    format: 'pdf'
  }).toString()
  
  // Buat temporary link untuk download
  const downloadUrl = `${route('absensi.rekap-siswa')}?${queryParams}`
  
  // Download file
  window.open(downloadUrl, '_blank')
  
  // Reset state setelah delay
  setTimeout(() => {
    exporting.value = false
  }, 2000)
}

// Check for absensi updates dan refresh data jika perlu
onMounted(() => {
  const lastUpdate = localStorage.getItem('absensi_updated')
  const pageLoadTime = sessionStorage.getItem('rekap_page_load_time')
  
  // Jika ada update absensi setelah halaman ini di-load, refresh data
  if (lastUpdate && (!pageLoadTime || parseInt(lastUpdate) > parseInt(pageLoadTime))) {
    console.log('Mendeteksi update absensi, refresh data...')
    applyFilters()
    // Clear flag setelah refresh
    localStorage.removeItem('absensi_updated')
  }
  
  // Set waktu load halaman ini
  sessionStorage.setItem('rekap_page_load_time', Date.now().toString())
})
</script>
