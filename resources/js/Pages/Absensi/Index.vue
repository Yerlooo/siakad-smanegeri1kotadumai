<template>
  <AppLayout title="Absensi Siswa">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Absensi Siswa
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            
            <!-- Filter Section -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Data</h3>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                  <input 
                    type="date" 
                    v-model="filters.tanggal"
                    @change="applyFilters"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  >
                </div>
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

            <!-- Success/Error Modal Pop-up -->
            <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
              <div class="relative mx-auto p-6 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                  <!-- Success Icon -->
                  <div v-if="modalType === 'success'" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                  
                  <!-- Error Icon -->
                  <div v-if="modalType === 'error'" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </div>
                  
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                    {{ modalType === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan!' }}
                  </h3>
                  <p class="text-sm text-gray-500 mb-4">
                    {{ modalMessage }}
                  </p>
                  <div class="items-center px-4 py-3">
                    <button 
                      @click="closeModal"
                      :class="modalType === 'success' ? 'bg-green-500 hover:bg-green-700' : 'bg-red-500 hover:bg-red-700'"
                      class="px-4 py-2 text-white text-base font-medium rounded-md w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 transition duration-200"
                    >
                      OK
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Navigation -->
            <div class="mb-6 flex space-x-4">
              <Link 
                :href="route('absensi.rekap-siswa')"
                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200"
              >
                📊 Rekap Siswa
              </Link>
              <Link 
                v-if="isKepalaSekolah"
                :href="route('absensi.laporan')"
                class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition duration-200"
              >
                📈 Laporan Absensi
              </Link>
            </div>

            <!-- Absensi Data -->
            <div v-if="!formData || formData.length === 0" class="text-center py-8">
              <div class="text-gray-500 text-lg">
                Tidak ada data untuk tanggal {{ formatTanggal(filters.tanggal) }}
              </div>
            </div>

            <div v-else class="space-y-6">
              <div v-for="(data, index) in formData" :key="index" class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-100 px-4 py-3 border-b">
                  <h4 class="font-semibold text-lg text-gray-800">
                    {{ data.jadwal?.mata_pelajaran?.nama_mapel || 'Mata Pelajaran' }} - {{ data.jadwal?.kelas?.nama_kelas || 'Kelas' }}
                  </h4>
                  <p class="text-sm text-gray-600">
                    Guru: {{ data.jadwal.guru?.name || data.jadwal.guru?.nama_guru || 'N/A' }} | 
                    Jadwal: {{ data.jadwal.hari || 'N/A' }} {{ data.jadwal.jam_mulai || 'N/A' }} - {{ data.jadwal.jam_selesai || 'N/A' }}
                  </p>
                </div>

                <form @submit.prevent="submitAbsensi(data)">
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
                            NISN
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jam Masuk
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jam Keluar
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Keterangan
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(siswaData, siswaIndex) in data.siswa" :key="`${siswaData.siswa.id}-${siswaData.statusForm}`" 
                            :class="getRowClass(siswaData.statusForm)">
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ siswaIndex + 1 }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                              {{ siswaData.siswa.nama_lengkap }}
                            </div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ siswaData.siswa.nisn }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <select 
                              v-model="siswaData.statusForm"
                              @change="handleStatusChange(siswaData)"
                              :key="`status-${siswaData.siswa.id}-${siswaData.statusForm}`"
                              class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                              <option v-for="(label, value) in statusOptions" :key="value" :value="value">
                                {{ label }}
                              </option>
                            </select>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <input 
                              type="time" 
                              v-model="siswaData.jamMasukForm"
                              :disabled="siswaData.statusForm !== 'hadir'"
                              :key="`jam-masuk-${siswaData.siswa.id}`"
                              class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                            >
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <input 
                              type="time" 
                              v-model="siswaData.jamKeluarForm"
                              :disabled="siswaData.statusForm !== 'hadir'"
                              :key="`jam-keluar-${siswaData.siswa.id}`"
                              class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                            >
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <input 
                              type="text" 
                              v-model="siswaData.keteranganForm"
                              placeholder="Keterangan..."
                              class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full"
                            >
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div class="bg-gray-50 px-4 py-3 text-right">
                    <div class="flex items-center justify-between">
                      <div class="text-sm text-gray-600">
                        Total Siswa: {{ data.siswa.length }}
                      </div>
                      <button 
                        type="submit" 
                        :disabled="processing"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                      >
                        <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ processing ? 'Menyimpan...' : 'Simpan Absensi' }}
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  absensiData: Array,
  tanggal: String,
  kelasList: Array,
  mataPelajaranList: Array,
  selectedKelas: String,
  selectedMataPelajaran: String,
  statusOptions: Object,
  isKepalaSekolah: Boolean
})

const processing = ref(false)
const showModal = ref(false)
const modalType = ref('') // 'success' or 'error'
const modalMessage = ref('')
const formData = ref([]) // Local reactive copy of absensiData

const filters = reactive({
  tanggal: props.tanggal,
  kelas_id: props.selectedKelas || '',
  mata_pelajaran_id: props.selectedMataPelajaran || ''
})

// Initialize form data
onMounted(() => {
  console.log('Component mounted, absensiData:', props.absensiData)
  initializeFormData()
  
  // Add keyboard listener for modal
  document.addEventListener('keydown', handleKeydown)
})

// Cleanup event listener
onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeydown)
})

const handleKeydown = (e) => {
  if (e.key === 'Escape' && showModal.value) {
    closeModal()
  }
}

const initializeFormData = () => {
  if (!props.absensiData || props.absensiData.length === 0) {
    formData.value = []
    return
  }
  
  // Create a deep reactive copy of absensiData
  formData.value = JSON.parse(JSON.stringify(props.absensiData)).map(data => {
    if (data.siswa && Array.isArray(data.siswa)) {
      data.siswa = data.siswa.map(siswaData => {
        // Pastikan siswaData.siswa exists
        if (!siswaData.siswa) {
          console.warn('Missing siswa data:', siswaData)
          return siswaData
        }

        // Set default values atau dari data yang sudah ada
        if (siswaData.absensi) {
          siswaData.statusForm = siswaData.absensi.status || 'alpha'
          
          // Handle jam masuk dengan lebih aman
          if (siswaData.absensi.jam_masuk) {
            try {
              const jamMasuk = new Date(siswaData.absensi.jam_masuk)
              siswaData.jamMasukForm = jamMasuk.toTimeString().slice(0, 5)
            } catch (e) {
              siswaData.jamMasukForm = ''
            }
          } else {
            siswaData.jamMasukForm = ''
          }
          
          // Handle jam keluar dengan lebih aman
          if (siswaData.absensi.jam_keluar) {
            try {
              const jamKeluar = new Date(siswaData.absensi.jam_keluar)
              siswaData.jamKeluarForm = jamKeluar.toTimeString().slice(0, 5)
            } catch (e) {
              siswaData.jamKeluarForm = ''
            }
          } else {
            siswaData.jamKeluarForm = ''
          }
          
          siswaData.keteranganForm = siswaData.absensi.keterangan || ''
        } else {
          // Set default values jika belum ada data absensi
          siswaData.statusForm = 'alpha'
          siswaData.jamMasukForm = ''
          siswaData.jamKeluarForm = ''
          siswaData.keteranganForm = ''
        }
        
        return siswaData
      })
    }
    return data
  })
  
  console.log('Form data initialized:', formData.value)
}

watch(() => props.absensiData, (newData) => {
  console.log('AbsensiData changed:', newData)
  initializeFormData()
}, { deep: true, immediate: true })

const applyFilters = () => {
  // Pastikan value bertipe string sesuai kebutuhan backend
  filters.kelas_id = filters.kelas_id ? String(filters.kelas_id) : ''
  filters.mata_pelajaran_id = filters.mata_pelajaran_id ? String(filters.mata_pelajaran_id) : ''
  router.get(route('absensi.index'), filters, {
    preserveState: true,
    preserveScroll: true
  })
}

const handleStatusChange = async (siswaData) => {
  console.log('Status changed:', siswaData.statusForm, 'for student:', siswaData.siswa.nama_lengkap)
  
  // Clear jam masuk dan keluar jika status bukan hadir
  if (siswaData.statusForm !== 'hadir') {
    siswaData.jamMasukForm = ''
    siswaData.jamKeluarForm = ''
  } else {
    // Set default time jika hadir dan belum ada
    if (!siswaData.jamMasukForm) {
      const now = new Date()
      siswaData.jamMasukForm = now.toTimeString().slice(0, 5)
    }
    if (!siswaData.jamKeluarForm) {
      const now = new Date()
      // Set jam keluar 1 jam setelah jam masuk sebagai default
      const defaultEnd = new Date(now.getTime() + 60 * 60 * 1000)
      siswaData.jamKeluarForm = defaultEnd.toTimeString().slice(0, 5)
    }
  }
  
  // Force reactivity update
  await nextTick()
}

const submitAbsensi = (data) => {
  // Validasi form sebelum submit
  if (!filters.tanggal) {
    showModal.value = true
    modalType.value = 'error'
    modalMessage.value = 'Silakan pilih tanggal terlebih dahulu!'
    return
  }

  // Validasi mata pelajaran dan guru ID
  const mataPelajaranId = data.jadwal?.mata_pelajaran_id || data.jadwal?.mata_pelajaran?.id
  const guruId = data.jadwal?.guru_id || data.jadwal?.guru?.id

  if (!mataPelajaranId) {
    showModal.value = true
    modalType.value = 'error'
    modalMessage.value = 'Data mata pelajaran tidak valid!'
    return
  }

  processing.value = true
  
  // Validasi dan format data absensi
  const validAbsensi = data.siswa.map(siswaData => {
    if (!siswaData.siswa || !siswaData.siswa.id) {
      console.error('Invalid siswa data:', siswaData)
      return null
    }

    const absensiData = {
      siswa_id: siswaData.siswa.id,
      status: siswaData.statusForm || 'alpha',
      jam_masuk: (siswaData.statusForm === 'hadir' && siswaData.jamMasukForm) ? siswaData.jamMasukForm : null,
      jam_keluar: (siswaData.statusForm === 'hadir' && siswaData.jamKeluarForm) ? siswaData.jamKeluarForm : null,
      keterangan: siswaData.keteranganForm || null
    }
    
    console.log(`Preparing absensi for ${siswaData.siswa.nama_lengkap}:`, absensiData)
    return absensiData
  }).filter(Boolean) // Remove null entries

  if (validAbsensi.length === 0) {
    processing.value = false
    showModal.value = true
    modalType.value = 'error'
    modalMessage.value = 'Tidak ada data siswa yang valid untuk disimpan!'
    return
  }

  const formSubmitData = {
    tanggal: filters.tanggal,
    mata_pelajaran_id: mataPelajaranId,
    guru_id: guruId,
    absensi: validAbsensi
  }

  console.log('Submitting absensi data:', formSubmitData)

  router.post(route('absensi.store'), formSubmitData, {
    onFinish: () => {
      processing.value = false
    },
    onError: (errors) => {
      console.error('Submission errors:', errors)
      processing.value = false
      
      let errorMsg = 'Terjadi kesalahan saat menyimpan absensi.'
      
      if (errors.message) {
        errorMsg = errors.message
      } else if (errors.tanggal) {
        errorMsg = 'Format tanggal tidak valid.'
      } else if (errors.mata_pelajaran_id) {
        errorMsg = 'Mata pelajaran tidak valid.'
      } else if (errors.absensi) {
        errorMsg = 'Data absensi tidak valid. Periksa kembali input Anda.'
      } else if (Object.keys(errors).length > 0) {
        errorMsg = 'Periksa kembali data yang diinput.'
      }
      
      showModal.value = true
      modalType.value = 'error'
      modalMessage.value = errorMsg
    },
    onSuccess: (page) => {
      console.log('Absensi berhasil disimpan!')
      
      // Set flag bahwa data absensi telah berubah
      localStorage.setItem('absensi_updated', Date.now().toString())
      
      // Reload data after successful submission
      initializeFormData()
      
      // Safe navigation untuk pesan sukses
      const mataPelajaranNama = data.jadwal?.mata_pelajaran?.nama_mapel || 
                               'Mata Pelajaran'
      const kelasNama = data.jadwal?.kelas?.nama_kelas || 
                       'Kelas'
      
      showModal.value = true
      modalType.value = 'success'
      modalMessage.value = `Absensi untuk ${mataPelajaranNama} - ${kelasNama} berhasil disimpan!`
    },
    preserveScroll: true
  })
}

const closeModal = () => {
  showModal.value = false
  modalType.value = ''
  modalMessage.value = ''
}

const getRowClass = (status) => {
  const classes = {
    'hadir': 'bg-green-50',
    'sakit': 'bg-yellow-50', 
    'izin': 'bg-blue-50',
    'alpha': 'bg-red-50'
  }
  return classes[status] || ''
}

const formatTanggal = (tanggal) => {
  if (!tanggal) return 'N/A'
  try {
    return new Date(tanggal).toLocaleDateString('id-ID', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (e) {
    return tanggal
  }
}
</script>
