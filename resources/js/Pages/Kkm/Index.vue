<template>
  <AppLayout title="Manajemen KKM">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Manajemen KKM (Kriteria Ketuntasan Minimal)
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h3 class="text-lg font-medium text-gray-900">Data KKM</h3>
                <p class="text-sm text-gray-600">
                  Semester {{ semester.charAt(0).toUpperCase() + semester.slice(1) }} - {{ tahunAjaran }}
                </p>
              </div>
              
              <div class="flex flex-col sm:flex-row gap-2">
                <button
                  @click="showBulkModal = true"
                  v-if="userRole !== 'guru'"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  Bulk Input
                </button>
                
                <button
                  @click="showFormModal = true"
                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  Tambah KKM
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Filter Mata Pelajaran
                </label>
                <select
                  v-model="filters.mata_pelajaran_id"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">Semua Mata Pelajaran</option>
                  <option v-for="mapel in mataPelajaranList" :key="mapel.id" :value="mapel.id">
                    {{ mapel.nama_mapel }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Filter Kelas
                </label>
                <select
                  v-model="filters.kelas_id"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                  class="w-full px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  Filter
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Bulk Delete Actions -->
          <div v-if="selectedItems.length > 0" class="bg-blue-50 px-6 py-3 border-b border-blue-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="text-sm text-blue-800">
                  {{ selectedItems.length }} item dipilih
                </span>
              </div>
              <button
                @click="showBulkDeleteModal = true"
                class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus Terpilih
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3">
                    <input
                      type="checkbox"
                      v-model="selectAll"
                      @change="toggleSelectAll"
                      class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    />
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    No
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Mata Pelajaran
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Kelas
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nilai KKM
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="kkmList.data.length === 0">
                  <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    Belum ada data KKM
                  </td>
                </tr>
                <tr v-for="(kkm, index) in kkmList.data" :key="kkm.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input
                      type="checkbox"
                      v-model="selectedItems"
                      :value="kkm.id"
                      @change="updateSelectAll"
                      class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    />
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ (kkmList.current_page - 1) * kkmList.per_page + index + 1 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ kkm.mata_pelajaran.nama_mapel }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ kkm.mata_pelajaran.kode_mapel }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ kkm.kelas?.nama_kelas || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getKkmStatusClass(kkm.kkm)">
                      {{ kkm.kkm }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="kkm.kkm >= 75 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                      {{ kkm.kkm >= 75 ? 'Tinggi' : 'Rendah' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <button
                      @click="editKkm(kkm)"
                      class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button
                      @click="deleteKkm(kkm)"
                      class="text-red-600 hover:text-red-900 transition-colors duration-150"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="kkmList.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link
                  v-if="kkmList.prev_page_url"
                  :href="kkmList.prev_page_url"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Previous
                </Link>
                <Link
                  v-if="kkmList.next_page_url"
                  :href="kkmList.next_page_url"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ kkmList.from }}</span>
                    to
                    <span class="font-medium">{{ kkmList.to }}</span>
                    of
                    <span class="font-medium">{{ kkmList.total }}</span>
                    results
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <Link
                      v-if="kkmList.prev_page_url"
                      :href="kkmList.prev_page_url"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                      <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                    </Link>
                    <Link
                      v-if="kkmList.next_page_url"
                      :href="kkmList.next_page_url"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                      <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                      </svg>
                    </Link>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Modal -->
    <div v-if="showFormModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeFormModal"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="submitForm">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                    {{ isEditing ? 'Edit KKM' : 'Tambah KKM' }}
                  </h3>
                  
                  <div class="space-y-4">
                    <div>
                      <label for="mata_pelajaran_id" class="block text-sm font-medium text-gray-700">
                        Mata Pelajaran <span class="text-red-500">*</span>
                      </label>
                      <select
                        id="mata_pelajaran_id"
                        v-model="form.mata_pelajaran_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                      >
                        <option value="">Pilih Mata Pelajaran</option>
                        <option v-for="mapel in mataPelajaranList" :key="mapel.id" :value="mapel.id">
                          {{ mapel.nama_mapel }}
                        </option>
                      </select>
                      <span v-if="errors.mata_pelajaran_id" class="text-red-500 text-sm">{{ errors.mata_pelajaran_id }}</span>
                    </div>

                    <div>
                      <label for="kelas_id" class="block text-sm font-medium text-gray-700">
                        Kelas <span class="text-red-500">*</span>
                      </label>
                      <select
                        id="kelas_id"
                        v-model="form.kelas_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                      >
                        <option value="">Pilih Kelas</option>
                        <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
                          {{ kelas.nama_kelas }}
                        </option>
                      </select>
                      <span v-if="errors.kelas_id" class="text-red-500 text-sm">{{ errors.kelas_id }}</span>
                    </div>

                    <div>
                      <label for="kkm" class="block text-sm font-medium text-gray-700">
                        Nilai KKM <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="number"
                        id="kkm"
                        v-model="form.kkm"
                        min="0"
                        max="100"
                        step="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Masukkan nilai KKM (0-100)"
                        required
                      />
                      <span v-if="errors.kkm" class="text-red-500 text-sm">{{ errors.kkm }}</span>
                      <p class="mt-1 text-sm text-gray-500">
                        Nilai KKM harus antara 0 sampai 100 untuk kelas yang dipilih
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="processing"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                <span v-if="processing" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                {{ isEditing ? 'Update' : 'Simpan' }}
              </button>
              <button
                type="button"
                @click="closeFormModal"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Bulk Input Modal -->
    <div v-if="showBulkModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="bulk-modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeBulkModal"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <form @submit.prevent="submitBulkForm">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="bulk-modal-title">
                    Bulk Input KKM
                  </h3>
                  
                  <div class="mb-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                      <button
                        type="button"
                        @click="generateBulkTemplate"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                      >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Generate Template
                      </button>
                      
                      <div v-if="bulkForm.kkm_data.length > 0" class="flex items-center gap-2">
                        <input
                          type="number"
                          v-model="bulkApplyValue"
                          min="0"
                          max="100"
                          step="0.01"
                          placeholder="Nilai KKM"
                          class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        />
                        <button
                          type="button"
                          @click="applyToAllSubjects"
                          :disabled="!bulkApplyValue"
                          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                          </svg>
                          Apply ke Semua
                        </button>
                      </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                      <span v-if="bulkForm.kkm_data.length === 0">
                        Klik "Generate Template" untuk membuat template bulk input KKM.
                      </span>
                      <span v-else>
                        Template berhasil dibuat. Masukkan nilai KKM dan klik "Apply ke Semua" untuk mengatur nilai yang sama untuk semua mata pelajaran.
                      </span>
                    </p>
                  </div>

                  <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mata Pelajaran
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kelas
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nilai KKM
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="bulkForm.kkm_data.length === 0">
                          <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Klik "Generate Template" untuk membuat template bulk input
                          </td>
                        </tr>
                        <tr v-for="(item, index) in bulkForm.kkm_data" :key="index" class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap">
                            <select
                              v-model="item.mata_pelajaran_id"
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              required
                            >
                              <option value="">Pilih Mata Pelajaran</option>
                              <option v-for="mapel in mataPelajaranList" :key="mapel.id" :value="mapel.id">
                                {{ mapel.nama_mapel }}
                              </option>
                            </select>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <select
                              v-model="item.kelas_id"
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              required
                            >
                              <option value="">Pilih Kelas</option>
                              <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
                                {{ kelas.nama_kelas }}
                              </option>
                            </select>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <input
                              type="number"
                              v-model="item.kkm"
                              min="0"
                              max="100"
                              step="0.01"
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="KKM"
                              required
                            />
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <button
                              type="button"
                              @click="removeBulkItem(index)"
                              class="text-red-600 hover:text-red-900"
                            >
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="processing || bulkForm.kkm_data.length === 0"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                <span v-if="processing" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                Simpan Semua
              </button>
              <button
                type="button"
                @click="closeBulkModal"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Notification Container -->
    <NotificationContainer />

    <!-- Confirm Modal -->
    <ConfirmModal
      :show="showConfirmModal"
      :title="confirmModalData.title"
      :message="confirmModalData.message"
      :confirm-text="confirmModalData.confirmText"
      :confirm-color="confirmModalData.confirmColor"
      @confirm="confirmModalData.onConfirm"
      @cancel="showConfirmModal = false"
    />

    <!-- Bulk Delete Modal -->
    <div v-if="showBulkDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="bulk-delete-modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="bulk-delete-modal-title">
                  Hapus Data KKM
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Anda yakin ingin menghapus {{ selectedItems.length }} data KKM yang dipilih? 
                    Tindakan ini tidak dapat dibatalkan.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="confirmBulkDelete"
              :disabled="processing"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              <span v-if="processing" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
              Hapus
            </button>
            <button
              type="button"
              @click="showBulkDeleteModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
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
import { ref, reactive, computed, onMounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import NotificationContainer from '@/Components/NotificationContainer.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import { useNotification } from '@/composables/useNotification'

// Props
const props = defineProps({
  kkmList: Object,
  mataPelajaranList: Array,
  kelasList: Array,
  semester: String,
  tahunAjaran: String,
  userRole: String,
  errors: {
    type: Object,
    default: () => ({})
  }
})

// Composables
const { showSuccess, showError, showWarning } = useNotification()

// Reactive data
const showFormModal = ref(false)
const showBulkModal = ref(false)
const showConfirmModal = ref(false)
const showBulkDeleteModal = ref(false)
const isEditing = ref(false)
const processing = ref(false)
const bulkApplyValue = ref(75)

// Bulk delete state
const selectedItems = ref([])
const selectAll = ref(false)

const form = reactive({
  mata_pelajaran_id: '',
  kelas_id: '',
  kkm: '',
  semester: props.semester,
  tahun_ajaran: props.tahunAjaran
})

const bulkForm = reactive({
  kkm_data: [],
  semester: props.semester,
  tahun_ajaran: props.tahunAjaran
})

const filters = reactive({
  mata_pelajaran_id: '',
  kelas_id: ''
})

const confirmModalData = reactive({
  title: '',
  message: '',
  confirmText: 'Ya',
  confirmColor: 'red',
  onConfirm: () => {}
})

// Methods
const resetForm = () => {
  form.mata_pelajaran_id = ''
  form.kelas_id = ''
  form.kkm = ''
  form.id = null
  isEditing.value = false
}

const openFormModal = () => {
  resetForm()
  showFormModal.value = true
}

const closeFormModal = () => {
  showFormModal.value = false
  resetForm()
}

const editKkm = (kkm) => {
  form.mata_pelajaran_id = kkm.mata_pelajaran_id
  form.kelas_id = kkm.kelas_id
  form.kkm = kkm.kkm
  form.id = kkm.id
  isEditing.value = true
  showFormModal.value = true
}

const submitForm = () => {
  processing.value = true
  
  const url = isEditing.value ? `/kkm/${form.id}` : '/kkm'
  const method = isEditing.value ? 'put' : 'post'
  
  router[method](url, form, {
    onSuccess: () => {
      showSuccess(`KKM berhasil ${isEditing.value ? 'diperbarui' : 'ditambahkan'}`)
      closeFormModal()
      // Refresh halaman untuk menampilkan data terbaru
      router.get(route('kkm.index'))
    },
    onError: (errors) => {
      if (typeof errors === 'string') {
        showError(errors)
      } else {
        showError('Terjadi kesalahan, silakan periksa input Anda')
      }
    },
    onFinish: () => {
      processing.value = false
    }
  })
}

const deleteKkm = (kkm) => {
  confirmModalData.title = 'Konfirmasi Hapus'
  confirmModalData.message = `Apakah Anda yakin ingin menghapus KKM untuk mata pelajaran "${kkm.mata_pelajaran.nama_mapel}" kelas "${kkm.kelas?.nama_kelas || 'N/A'}"?`
  confirmModalData.confirmText = 'Hapus'
  confirmModalData.confirmColor = 'red'
  confirmModalData.onConfirm = () => {
    router.delete(`/kkm/${kkm.id}`, {
      onSuccess: () => {
        showSuccess('KKM berhasil dihapus')
        showConfirmModal.value = false
        // Refresh halaman untuk menampilkan data terbaru
        router.get(route('kkm.index'))
      },
      onError: (error) => {
        showError(error || 'Gagal menghapus KKM')
      }
    })
  }
  showConfirmModal.value = true
}

const generateBulkTemplate = () => {
  bulkForm.kkm_data = []
  
  // Generate kombinasi mata pelajaran dan kelas
  props.mataPelajaranList.forEach(mapel => {
    props.kelasList.forEach(kelas => {
      bulkForm.kkm_data.push({
        mata_pelajaran_id: mapel.id,
        kelas_id: kelas.id,
        kkm: 75
      })
    })
  })
}

const applyToAllSubjects = () => {
  if (!bulkApplyValue.value || bulkForm.kkm_data.length === 0) {
    showWarning('Masukkan nilai KKM dan pastikan template sudah di-generate')
    return
  }
  
  if (bulkApplyValue.value < 0 || bulkApplyValue.value > 100) {
    showError('Nilai KKM harus antara 0 sampai 100')
    return
  }
  
  // Apply nilai ke semua item di bulk form
  bulkForm.kkm_data.forEach(item => {
    item.kkm = bulkApplyValue.value
  })
  
  showSuccess(`Nilai KKM ${bulkApplyValue.value} berhasil diterapkan ke semua mata pelajaran`)
}

const removeBulkItem = (index) => {
  bulkForm.kkm_data.splice(index, 1)
}

const closeBulkModal = () => {
  showBulkModal.value = false
  bulkForm.kkm_data = []
  bulkApplyValue.value = 75
}

const submitBulkForm = () => {
  processing.value = true
  
  // Validasi data sebelum submit
  const validData = bulkForm.kkm_data.filter(item => {
    return item.mata_pelajaran_id && item.kelas_id && item.kkm && 
           item.kkm >= 0 && item.kkm <= 100
  })
  
  if (validData.length === 0) {
    showError('Tidak ada data valid untuk diproses')
    processing.value = false
    return
  }
  
  router.post('/kkm/bulk', { kkm_data: validData }, {
    onSuccess: () => {
      showSuccess('Bulk input KKM berhasil diproses')
      closeBulkModal()
      // Refresh halaman untuk menampilkan data terbaru
      router.get(route('kkm.index'))
    },
    onError: (error) => {
      showError(error || 'Gagal memproses bulk input KKM')
    },
    onFinish: () => {
      processing.value = false
    }
  })
}

const applyFilters = () => {
  const params = new URLSearchParams()
  
  if (filters.mata_pelajaran_id) {
    params.append('mata_pelajaran_id', filters.mata_pelajaran_id)
  }
  
  if (filters.kelas_id) {
    params.append('kelas_id', filters.kelas_id)
  }
  
  if (filters.search) {
    params.append('search', filters.search)
  }
  
  const queryString = params.toString()
  router.get(route('kkm.index') + (queryString ? '?' + queryString : ''))
}

// Select All functionality
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedItems.value = props.kkmList.data.map(item => item.id)
  } else {
    selectedItems.value = []
  }
}

const updateSelectAll = () => {
  const totalItems = props.kkmList.data.length
  const selectedCount = selectedItems.value.length
  
  if (selectedCount === 0) {
    selectAll.value = false
  } else if (selectedCount === totalItems) {
    selectAll.value = true
  } else {
    selectAll.value = false
  }
}

// Bulk delete functionality
const confirmBulkDelete = () => {
  if (selectedItems.value.length === 0) {
    showWarning('Tidak ada data yang dipilih')
    return
  }
  
  processing.value = true
  
  // Tutup modal konfirmasi terlebih dahulu
  showBulkDeleteModal.value = false
  
  router.post('/kkm/bulk-delete', {
    ids: selectedItems.value
  }, {
    onSuccess: () => {
      showSuccess(`Berhasil menghapus ${selectedItems.value.length} data KKM`)
      selectedItems.value = []
      selectAll.value = false
      // Refresh halaman untuk menampilkan data terbaru
      router.get(route('kkm.index'))
    },
    onError: (error) => {
      showError(error.message || 'Gagal menghapus data KKM')
      // Jika error, buka kembali modal untuk user bisa mencoba lagi
      showBulkDeleteModal.value = true
    },
    onFinish: () => {
      processing.value = false
    }
  })
}

const getKkmStatusClass = (nilai) => {
  if (nilai >= 85) return 'bg-green-100 text-green-800'
  if (nilai >= 75) return 'bg-blue-100 text-blue-800'
  if (nilai >= 65) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

// Watch for flash messages
onMounted(() => {
  const flashMessage = document.querySelector('meta[name="flash-message"]')
  const flashType = document.querySelector('meta[name="flash-type"]')
  
  if (flashMessage && flashType) {
    const type = flashType.content
    const message = flashMessage.content
    
    if (type === 'success') {
      showSuccess(message)
    } else if (type === 'error') {
      showError(message)
    } else if (type === 'warning') {
      showWarning(message)
    }
  }
})
</script>
