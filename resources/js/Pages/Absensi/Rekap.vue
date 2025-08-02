<template>
  <AppLayout title="Rekap Absensi">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Rekap Absensi
      </h2>
    </template>

    <div class="py-0">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Tombol Kembali -->
            <div class="mb-6">
              <button 
                @click="router.visit(route('absensi.monitoring'))"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition duration-150 ease-in-out"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Monitoring Absensi
              </button>
            </div>
            
            <!-- Kelas Selection -->
            <div class="mb-8">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Pilih Kelas</h3>
                <div v-if="selectedKelas" class="text-sm text-gray-500">
                  Kelas dipilih: <span class="font-medium text-blue-600">{{ selectedKelas.nama_kelas }}</span>
                </div>
              </div>
              
              <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <div 
                  v-for="kelas in kelasList" 
                  :key="kelas.id"
                  @click="selectKelas(kelas)"
                  :class="selectedKelas?.id === kelas.id 
                    ? 'border-blue-500 bg-blue-50 shadow-md' 
                    : 'border-gray-200 hover:border-blue-300 hover:shadow-sm'"
                  class="border-2 rounded-lg p-4 cursor-pointer transition-all duration-200 bg-white text-center"
                >
                  <div :class="selectedKelas?.id === kelas.id ? 'text-blue-600' : 'text-gray-400'" 
                       class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-100 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h4M7 7h10M7 11h6"/>
                    </svg>
                  </div>
                  <h4 :class="selectedKelas?.id === kelas.id ? 'text-blue-900' : 'text-gray-900'" 
                      class="font-medium text-sm mb-1">{{ kelas.nama_kelas }}</h4>
                  <p :class="selectedKelas?.id === kelas.id ? 'text-blue-600' : 'text-gray-500'" 
                     class="text-xs">{{ kelas.siswa_count || 0 }} siswa</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Mata Pelajaran -->
    <div 
      v-if="showMataPelajaranModal" 
      class="fixed inset-0 z-50 overflow-y-auto"
      @click="showMataPelajaranModal = false"
    >
      <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Background Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <!-- Modal Panel -->
        <div 
          class="relative bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-hidden"
          @click.stop
        >
          <!-- Modal Header -->
          <div class="px-6 pt-6 pb-4">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-lg font-medium text-gray-900">
                  Pilih Mata Pelajaran
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                  Kelas: {{ selectedKelas?.nama_kelas }}
                </p>
              </div>
              <button 
                @click="showMataPelajaranModal = false"
                class="text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Mata Pelajaran List -->
            <div class="max-h-80 overflow-y-auto">
              <div class="space-y-2">
                <div 
                  v-for="mapel in filteredMataPelajaran" 
                  :key="mapel.id"
                  @click="selectMataPelajaran(mapel)"
                  class="border border-gray-200 hover:border-blue-300 hover:bg-blue-50 rounded-lg p-3 cursor-pointer transition-all duration-200"
                >
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h4 class="font-medium text-sm text-gray-900 mb-1">{{ mapel.nama_mapel }}</h4>
                      <p class="text-xs text-gray-500">{{ mapel.guru_name || 'Belum ada guru' }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
            <button 
              @click="lihatSemuaMataPelajaran"
              class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
            >
              Lihat Semua Mata Pelajaran
            </button>
            <button 
              @click="showMataPelajaranModal = false"
              class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  kelasList: Array,
  mataPelajaranList: Array,
  guruList: Array,
  canViewAll: Boolean
})

const selectedKelas = ref(null)
const selectedMapel = ref(null)
const showMataPelajaranModal = ref(false)

// Computed properties
const filteredMataPelajaran = computed(() => {
  if (!selectedKelas.value) return []
  
  // Filter mata pelajaran berdasarkan kelas yang dipilih
  // Untuk sementara, tampilkan semua mata pelajaran
  return props.mataPelajaranList.filter(mapel => {
    // Jika ada relasi mata pelajaran dengan kelas, bisa difilter di sini
    return true
  })
})

// New methods for kelas and mata pelajaran selection
const selectKelas = (kelas) => {
  selectedKelas.value = kelas
  selectedMapel.value = null // Reset mata pelajaran selection
  
  // Langsung buka modal mata pelajaran
  showMataPelajaranModal.value = true
}

const selectMataPelajaran = (mapel) => {
  selectedMapel.value = mapel
  showMataPelajaranModal.value = false // Tutup modal
  
  // Navigate to detail page with kelas and mata pelajaran filters
  const params = {
    kelas_id: selectedKelas.value.id,
    mata_pelajaran_id: mapel.id
  }
  
  router.visit(route('absensi.rekap.detail', params))
}

const lihatSemuaMataPelajaran = () => {
  showMataPelajaranModal.value = false // Tutup modal
  
  // Navigate to detail page with kelas filter only (all mata pelajaran)
  const params = {
    kelas_id: selectedKelas.value.id
  }
  
  router.visit(route('absensi.rekap.detail', params))
}

const clearSelection = () => {
  selectedKelas.value = null
  selectedMapel.value = null
  showMataPelajaranModal.value = false // Tutup modal
}
</script>

<style scoped>
/* Simple transitions only */
.transition-all {
  transition: all 0.2s ease-in-out;
}

.hover\:shadow-sm:hover {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.hover\:shadow-md:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>
