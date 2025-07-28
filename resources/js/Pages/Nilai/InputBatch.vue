<template>
    <AppLayout title="Input Nilai Batch">
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        📝 Input Nilai: {{ jenisNilai.nama }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ mataPelajaran.nama_mapel }} - {{ kelas.nama_kelas }} - {{ jenisNilai.nama }} - {{ semester }} {{ tahunAjaran }}
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
                    <div class="text-center sm:text-right">
                        <div class="text-sm text-gray-600">KKM</div>
                        <div class="text-lg font-bold text-blue-600">{{ kkm?.kkm || 75 }}</div>
                    </div>
                    <div class="text-center sm:text-right">
                        <div class="text-sm text-gray-600">Bobot</div>
                        <div class="text-lg font-bold text-purple-600">{{ jenisNilai.bobot }}%</div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Info Panel -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Petunjuk Input Nilai</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p class="mb-2"><strong>Jenis Nilai:</strong> {{ jenisNilai.nama }} (Bobot: {{ jenisNilai.bobot }}%)</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Masukkan nilai dengan rentang 0-100</li>
                                    <li>Nilai di bawah KKM ({{ kkm?.kkm || 75 }}) akan ditandai merah</li>
                                    <li>Simpan sebagai <strong>Draft</strong> untuk sementara atau <strong>Final</strong> untuk menyelesaikan</li>
                                    <li>Nilai yang sudah <strong>Final</strong> tidak dapat diubah tanpa persetujuan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Input Nilai -->
                <form @submit.prevent="submitNilai" class="bg-white shadow rounded-lg">
                    <!-- Header Form -->
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Daftar Siswa - {{ jenisNilai.nama }}</h3>
                                <p class="text-sm text-gray-600">{{ kelas.siswa?.length || 0 }} siswa • Bobot: {{ jenisNilai.bobot }}%</p>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-3 sm:space-y-0">
                                <!-- Bulk Actions -->
                                <div class="flex items-center space-x-2">
                                    <input type="number" 
                                           v-model="bulkValue" 
                                           placeholder="Nilai sama"
                                           min="0" max="100"
                                           class="w-24 px-3 py-1 border border-gray-300 rounded-md text-sm">
                                    <button type="button" 
                                            @click="applyBulkValue"
                                            class="px-3 py-1 bg-gray-500 text-white text-sm rounded-md hover:bg-gray-600 whitespace-nowrap">
                                        Apply ke Semua
                                    </button>
                                </div>
                                
                                <!-- Search -->
                                <div class="relative">
                                    <input type="text" 
                                           v-model="searchSiswa"
                                           placeholder="Cari siswa..."
                                           class="w-full sm:w-48 pl-8 pr-3 py-1 border border-gray-300 rounded-md text-sm">
                                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Input Nilai - Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NIS
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Siswa
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Predikat
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Keterangan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(siswa, index) in filteredSiswa" :key="siswa.id"
                                    :class="{ 'bg-red-50': (form.nilai_siswa[siswa.id]?.nilai || 0) < (kkm?.kkm || 75) }">
                                    <!-- Nomor -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ index + 1 }}
                                    </td>
                                    
                                    <!-- NIS -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ siswa.nis }}
                                    </td>
                                    
                                    <!-- Nama Siswa -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ siswa.nama_lengkap.charAt(0) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ siswa.nama_lengkap }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ siswa.jenis_kelamin }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                    <!-- Input Nilai -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <input type="number" 
                                   v-model="form.nilai_siswa[siswa.id].nilai"
                                   min="0" max="100" step="0.01"
                                   :disabled="isNilaiFinal(siswa.id)"
                                   :class="[
                                       'w-20 px-3 py-2 border rounded-md text-sm text-center font-medium',
                                       isNilaiFinal(siswa.id) 
                                           ? 'bg-gray-100 border-gray-200 text-gray-500 cursor-not-allowed'
                                           : (form.nilai_siswa[siswa.id]?.nilai || 0) < (kkm?.kkm || 75) 
                                               ? 'border-red-300 bg-red-50 text-red-900' 
                                               : 'border-gray-300 bg-white text-gray-900'
                                   ]"
                                   @input="updatePredikat(siswa.id)"
                                   :placeholder="nilaiExisting[siswa.id]?.nilai || '0'">
                            
                            <!-- Button untuk request approval jika nilai final -->
                            <button v-if="isNilaiFinal(siswa.id)" 
                                    type="button"
                                    @click="openApprovalRequestModal(siswa)"
                                    class="ml-2 px-2 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600"
                                    title="Ajukan perubahan nilai">
                                ✏️
                            </button>
                            
                            <span v-if="form.errors[`nilai_siswa.${siswa.id}.nilai`]" 
                                  class="text-red-500 text-xs">
                                {{ form.errors[`nilai_siswa.${siswa.id}.nilai`] }}
                            </span>
                        </div>
                        
                        <!-- Status Final Indicator -->
                        <div v-if="isNilaiFinal(siswa.id)" class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                🔒 Final
                            </span>
                        </div>
                    </td>                                    <!-- Predikat -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <span :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                getPredikatClass(form.nilai_siswa[siswa.id]?.nilai || 0)
                                            ]">
                                                {{ getPredikat(form.nilai_siswa[siswa.id]?.nilai || 0).huruf }}
                                            </span>
                                            <span class="text-xs text-gray-600">
                                                {{ getPredikat(form.nilai_siswa[siswa.id]?.nilai || 0).predikat }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Status Tuntas -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            (form.nilai_siswa[siswa.id]?.nilai || 0) >= (kkm?.kkm || 75)
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'
                                        ]">
                                            {{ (form.nilai_siswa[siswa.id]?.nilai || 0) >= (kkm?.kkm || 75) ? 'Tuntas' : 'Belum Tuntas' }}
                                        </span>
                                    </td>
                                    
                                    <!-- Keterangan -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" 
                                               v-model="form.nilai_siswa[siswa.id].keterangan"
                                               placeholder="Catatan..."
                                               class="w-32 px-2 py-1 border border-gray-300 rounded-md text-xs">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-4 p-4">
                        <div v-for="(siswa, index) in filteredSiswa" :key="siswa.id"
                             :class="[
                                 'bg-white border rounded-lg p-4 shadow-sm',
                                 (form.nilai_siswa[siswa.id]?.nilai || 0) < (kkm?.kkm || 75) ? 'border-red-200 bg-red-50' : 'border-gray-200'
                             ]">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ siswa.nama_lengkap.charAt(0) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ siswa.nama_lengkap }}</h4>
                                        <p class="text-sm text-gray-500">{{ siswa.nis }} • {{ siswa.jenis_kelamin }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-500">#{{ index + 1 }}</span>
                            </div>
                            
                            <!-- Input Nilai -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="number" 
                                               v-model="form.nilai_siswa[siswa.id].nilai"
                                               min="0" max="100" step="0.01"
                                               :disabled="isNilaiFinal(siswa.id)"
                                               :class="[
                                                   'w-full px-3 py-2 border rounded-md text-sm text-center font-medium',
                                                   isNilaiFinal(siswa.id) 
                                                       ? 'bg-gray-100 border-gray-200 text-gray-500 cursor-not-allowed'
                                                       : (form.nilai_siswa[siswa.id]?.nilai || 0) < (kkm?.kkm || 75) 
                                                           ? 'border-red-300 bg-red-50 text-red-900' 
                                                           : 'border-gray-300 bg-white text-gray-900'
                                               ]"
                                               @input="updatePredikat(siswa.id)"
                                               :placeholder="nilaiExisting[siswa.id]?.nilai || '0'">
                                        
                                        <!-- Button untuk request approval jika nilai final -->
                                        <button v-if="isNilaiFinal(siswa.id)" 
                                                type="button"
                                                @click="openApprovalRequestModal(siswa)"
                                                class="ml-2 px-2 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600 flex-shrink-0"
                                                title="Ajukan perubahan nilai">
                                            ✏️
                                        </button>
                                    </div>
                                    
                                    <!-- Status Final Indicator -->
                                    <div v-if="isNilaiFinal(siswa.id)" class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            🔒 Final
                                        </span>
                                    </div>
                                    
                                    <span v-if="form.errors[`nilai_siswa.${siswa.id}.nilai`]" 
                                          class="text-red-500 text-xs mt-1 block">
                                        {{ form.errors[`nilai_siswa.${siswa.id}.nilai`] }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <input type="text" 
                                           v-model="form.nilai_siswa[siswa.id].keterangan"
                                           placeholder="Catatan..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                </div>
                            </div>
                            
                            <!-- Status & Predikat -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        getPredikatClass(form.nilai_siswa[siswa.id]?.nilai || 0)
                                    ]">
                                        {{ getPredikat(form.nilai_siswa[siswa.id]?.nilai || 0).huruf }}
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        {{ getPredikat(form.nilai_siswa[siswa.id]?.nilai || 0).predikat }}
                                    </span>
                                </div>
                                <span :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    (form.nilai_siswa[siswa.id]?.nilai || 0) >= (kkm?.kkm || 75)
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                ]">
                                    {{ (form.nilai_siswa[siswa.id]?.nilai || 0) >= (kkm?.kkm || 75) ? 'Tuntas' : 'Belum Tuntas' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="px-4 sm:px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                            <div class="bg-white rounded-lg p-3">
                                <div class="text-xl sm:text-2xl font-bold text-blue-600">{{ summary.total }}</div>
                                <div class="text-xs sm:text-sm text-gray-600">Total Siswa</div>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <div class="text-xl sm:text-2xl font-bold text-green-600">{{ summary.tuntas }}</div>
                                <div class="text-xs sm:text-sm text-gray-600">Tuntas</div>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <div class="text-xl sm:text-2xl font-bold text-red-600">{{ summary.belumTuntas }}</div>
                                <div class="text-xs sm:text-sm text-gray-600">Belum Tuntas</div>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <div class="text-xl sm:text-2xl font-bold text-purple-600">{{ summary.rataRata.toFixed(1) }}</div>
                                <div class="text-xs sm:text-sm text-gray-600">Rata-rata</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="px-4 sm:px-6 py-4 bg-white border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
                                <Link :href="route('nilai-siswa.index')" 
                                      class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    ← Kembali
                                </Link>
                                
                                <button type="button" 
                                        @click="resetForm"
                                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    🔄 Reset
                                </button>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
                                <button type="button" 
                                        @click="submitAsDraft"
                                        :disabled="form.processing"
                                        class="inline-flex items-center justify-center px-6 py-2 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 disabled:opacity-50">
                                    💾 Simpan Draft
                                </button>
                                
                                <button type="submit" 
                                        :disabled="form.processing || !isFormValid"
                                        class="inline-flex items-center justify-center px-6 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                                    <span v-if="form.processing">⏳ Menyimpan...</span>
                                    <span v-else>✅ Simpan Final</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Approval Request Modal -->
        <Modal :show="showApprovalModal" @close="closeApprovalModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Ajukan Perubahan Nilai Final
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Nilai sudah berstatus final dan tidak dapat diubah. Ajukan permintaan perubahan kepada Tata Usaha untuk mendapat persetujuan.
                </p>
                
                <div v-if="selectedSiswaForApproval" class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h4 class="font-medium text-gray-900">Detail Perubahan</h4>
                    <div class="mt-2 text-sm space-y-1">
                        <p><strong>Siswa:</strong> {{ selectedSiswaForApproval.nama_lengkap }} ({{ selectedSiswaForApproval.nis }})</p>
                        <p><strong>Mata Pelajaran:</strong> {{ mataPelajaran.nama_mapel }}</p>
                        <p><strong>Jenis Nilai:</strong> {{ jenisNilai.nama }}</p>
                        <p><strong>Nilai Lama:</strong> {{ nilaiExisting[selectedSiswaForApproval.id]?.nilai || 0 }}</p>
                        <p><strong>Nilai Baru:</strong> 
                            <input type="number" 
                                   v-model="approvalForm.new_value"
                                   min="0" max="100" step="0.01"
                                   class="w-20 px-2 py-1 border border-gray-300 rounded text-center ml-2">
                        </p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Perubahan *
                    </label>
                    <textarea 
                        v-model="approvalForm.reason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        rows="3"
                        required
                        placeholder="Jelaskan alasan mengapa nilai perlu diubah..."></textarea>
                    <div v-if="approvalForm.errors.reason" class="text-red-600 text-sm mt-1">
                        {{ approvalForm.errors.reason }}
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="closeApprovalModal"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded transition-colors">
                        Batal
                    </button>
                    <button 
                        @click="submitApprovalRequest"
                        :disabled="approvalForm.processing || !approvalForm.new_value || !approvalForm.reason"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors disabled:opacity-50">
                        {{ approvalForm.processing ? 'Mengirim...' : 'Ajukan Permintaan' }}
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    mataPelajaran: Object,
    kelas: Object,
    jenisNilai: Object,
    kkm: Object,
    nilaiExisting: Object,
    semester: String,
    tahunAjaran: String
})

const searchSiswa = ref('')
const bulkValue = ref('')

// Approval Modal State
const showApprovalModal = ref(false)
const selectedSiswaForApproval = ref(null)

// Approval Form
const approvalForm = useForm({
    siswa_id: null,
    mata_pelajaran_id: props.mataPelajaran.id,
    jenis_nilai_id: props.jenisNilai.id,
    old_value: null,
    new_value: '',
    reason: ''
})

// Initialize form with pre-populated nilai_siswa
const initializeNilaiSiswa = () => {
    const nilaiSiswa = {}
    
    // Debug: log data yang tersedia
    console.log('Kelas data:', props.kelas)
    console.log('Siswa data:', props.kelas?.siswa)
    
    if (props.kelas?.siswa && Array.isArray(props.kelas.siswa)) {
        props.kelas.siswa.forEach(siswa => {
            nilaiSiswa[siswa.id] = {
                siswa_id: siswa.id,
                nilai: props.nilaiExisting?.[siswa.id]?.nilai || '',
                keterangan: props.nilaiExisting?.[siswa.id]?.keterangan || ''
            }
        })
    } else {
        console.warn('Data siswa tidak tersedia atau bukan array:', props.kelas?.siswa)
    }
    
    console.log('Initialized nilai siswa:', nilaiSiswa)
    return nilaiSiswa
}

const form = useForm({
    mata_pelajaran_id: props.mataPelajaran.id,
    kelas_id: props.kelas.id,
    jenis_nilai_id: props.jenisNilai.id,
    semester: props.semester,
    tahun_ajaran: props.tahunAjaran,
    status: 'final',
    nilai_siswa: initializeNilaiSiswa()
})

// If initial data is empty, reinitialize on mount
onMounted(() => {
    console.log('Mounted - checking nilai siswa')
    console.log('Props kelas:', props.kelas)
    console.log('Props kelas siswa:', props.kelas?.siswa)
    console.log('Current form nilai_siswa:', form.nilai_siswa)
    
    // If nilai_siswa is empty or doesn't match current students, reinitialize
    const currentStudentIds = props.kelas?.siswa?.map(s => s.id) || []
    const formStudentIds = Object.keys(form.nilai_siswa).map(id => parseInt(id))
    
    if (Object.keys(form.nilai_siswa).length === 0 || !currentStudentIds.every(id => formStudentIds.includes(id))) {
        console.log('Reinitializing nilai siswa data...')
        const newNilaiSiswa = initializeNilaiSiswa()
        form.nilai_siswa = newNilaiSiswa
        console.log('Form nilai_siswa after reinitialization:', form.nilai_siswa)
    }
})

const filteredSiswa = computed(() => {
    if (!props.kelas?.siswa || !Array.isArray(props.kelas.siswa)) {
        console.warn('Data siswa tidak tersedia untuk filtering')
        return []
    }
    
    // Ensure all students have entries in form.nilai_siswa
    props.kelas.siswa.forEach(siswa => {
        if (!form.nilai_siswa[siswa.id]) {
            form.nilai_siswa[siswa.id] = {
                siswa_id: siswa.id,
                nilai: props.nilaiExisting?.[siswa.id]?.nilai || '',
                keterangan: props.nilaiExisting?.[siswa.id]?.keterangan || ''
            }
        }
    })
    
    if (!searchSiswa.value) return props.kelas.siswa
    
    return props.kelas.siswa.filter(siswa => 
        siswa.nama_lengkap?.toLowerCase().includes(searchSiswa.value.toLowerCase()) ||
        siswa.nis?.includes(searchSiswa.value)
    )
})

const summary = computed(() => {
    const nilaiList = Object.values(form.nilai_siswa)
        .map(item => parseFloat(item?.nilai) || 0)
        .filter(nilai => nilai > 0)
    
    const total = props.kelas?.siswa?.length || 0
    const tuntas = nilaiList.filter(nilai => nilai >= (props.kkm?.kkm || 75)).length
    const belumTuntas = nilaiList.filter(nilai => nilai > 0 && nilai < (props.kkm?.kkm || 75)).length
    const rataRata = nilaiList.length > 0 ? nilaiList.reduce((a, b) => a + b, 0) / nilaiList.length : 0

    return { total, tuntas, belumTuntas, rataRata }
})

const isFormValid = computed(() => {
    return Object.values(form.nilai_siswa).some(item => item.nilai !== '' && item.nilai > 0)
})

const applyBulkValue = () => {
    if (bulkValue.value >= 0 && bulkValue.value <= 100) {
        Object.keys(form.nilai_siswa).forEach(siswaId => {
            form.nilai_siswa[siswaId].nilai = bulkValue.value
        })
    }
}

const getPredikat = (nilai) => {
    const kkm = props.kkm?.kkm || 75
    if (nilai >= 90) return { huruf: 'A', predikat: 'Sangat Baik' }
    if (nilai >= 80) return { huruf: 'B+', predikat: 'Baik' }
    if (nilai >= kkm) return { huruf: 'B', predikat: 'Baik' }
    if (nilai >= kkm - 10) return { huruf: 'C+', predikat: 'Cukup' }
    if (nilai >= kkm - 20) return { huruf: 'C', predikat: 'Cukup' }
    return { huruf: 'D', predikat: 'Kurang' }
}

const getPredikatClass = (nilai) => {
    const kkm = props.kkm?.kkm || 75
    if (nilai >= 90) return 'bg-blue-100 text-blue-800'
    if (nilai >= 80) return 'bg-green-100 text-green-800'
    if (nilai >= kkm) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}

const updatePredikat = (siswaId) => {
    // Trigger reactivity for predikat computation
}

const resetForm = () => {
    if (confirm('Yakin ingin mereset semua nilai?')) {
        Object.keys(form.nilai_siswa).forEach(siswaId => {
            // Only reset if not final
            if (!isNilaiFinal(siswaId)) {
                form.nilai_siswa[siswaId].nilai = ''
                form.nilai_siswa[siswaId].keterangan = ''
            }
        })
    }
}

// Check if nilai is final (cannot be edited)
const isNilaiFinal = (siswaId) => {
    return props.nilaiExisting[siswaId]?.status === 'final'
}

// Open approval request modal
const openApprovalRequestModal = (siswa) => {
    selectedSiswaForApproval.value = siswa
    approvalForm.siswa_id = siswa.id
    approvalForm.old_value = props.nilaiExisting[siswa.id]?.nilai || 0
    approvalForm.new_value = ''
    approvalForm.reason = ''
    showApprovalModal.value = true
}

// Close approval request modal
const closeApprovalModal = () => {
    showApprovalModal.value = false
    selectedSiswaForApproval.value = null
    approvalForm.reset()
}

// Submit approval request
const submitApprovalRequest = () => {
    approvalForm.post(route('approval-requests.store'), {
        onSuccess: () => {
            closeApprovalModal()
            // Show success message
            alert('Permintaan perubahan nilai berhasil diajukan! Tunggu persetujuan dari Tata Usaha.')
        },
        onError: (errors) => {
            console.error('Error submitting approval request:', errors)
        }
    })
}

const submitAsDraft = () => {
    // Only allow draft if no final values are being changed
    const hasChangedFinalValues = Object.keys(form.nilai_siswa).some(siswaId => {
        return isNilaiFinal(siswaId) && 
               form.nilai_siswa[siswaId].nilai !== '' && 
               form.nilai_siswa[siswaId].nilai != props.nilaiExisting[siswaId]?.nilai
    })
    
    if (hasChangedFinalValues) {
        alert('Tidak dapat menyimpan: terdapat nilai final yang diubah. Gunakan fitur permintaan persetujuan.')
        return
    }
    
    form.status = 'draft'
    submitNilai()
}

const submitNilai = () => {
    // Check for changed final values
    const hasChangedFinalValues = Object.keys(form.nilai_siswa).some(siswaId => {
        return isNilaiFinal(siswaId) && 
               form.nilai_siswa[siswaId].nilai !== '' && 
               form.nilai_siswa[siswaId].nilai != props.nilaiExisting[siswaId]?.nilai
    })
    
    if (hasChangedFinalValues) {
        alert('Tidak dapat menyimpan: terdapat nilai final yang diubah. Gunakan fitur permintaan persetujuan.')
        return
    }
    
    // Filter hanya siswa yang memiliki nilai dan bukan nilai final yang diubah
    const nilaiSiswaValid = Object.values(form.nilai_siswa)
        .filter(item => {
            const isValidValue = item.nilai !== '' && item.nilai >= 0
            const isNotChangedFinal = !isNilaiFinal(item.siswa_id) || 
                                    item.nilai == props.nilaiExisting[item.siswa_id]?.nilai
            return isValidValue && isNotChangedFinal
        })

    if (nilaiSiswaValid.length === 0) {
        alert('Minimal input 1 nilai siswa yang dapat diubah!')
        return
    }

    form.nilai_siswa = nilaiSiswaValid.reduce((acc, item) => {
        acc[item.siswa_id] = item
        return acc
    }, {})

    form.post(route('nilai-siswa.store'))
}
</script>
