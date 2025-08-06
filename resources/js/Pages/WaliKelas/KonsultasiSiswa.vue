<template>
    <AppLayout page-title="Konsultasi Siswa - Wali Kelas">
        <!-- Loading Overlay -->
        <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
                <p class="text-gray-700 font-medium">{{ loadingMessage }}</p>
            </div>
        </div>

        <!-- Modern Header with Gradient -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Konsultasi Siswa</h1>
                    <p class="text-purple-100">Kelola konsultasi dan bimbingan siswa dengan efektif</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ consultationStats.total }}</div>
                    <div class="text-sm text-purple-100">Total Konsultasi</div>
                </div>
            </div>
        </div>

        <!-- Filter and Action Bar -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <!-- Class Filter -->
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"></path>
                        </svg>
                        <select v-model="selectedKelas" @change="filterByKelas" 
                                class="border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Semua Kelas</option>
                            <option v-for="kelas in kelasAsWali" :key="kelas.id" :value="kelas.id">
                                {{ kelas.nama_kelas }}
                            </option>
                        </select>
                    </div>
                    
                    <!-- Search -->
                    <div class="relative">
                        <input v-model="searchQuery" 
                               type="text" 
                               placeholder="Cari siswa..."
                               class="pl-10 pr-4 py-2 border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <button @click="openNewConsultation" 
                        :disabled="isLoading"
                        class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg text-sm transition-all duration-200 flex items-center space-x-2 shadow-lg">
                    <svg v-if="isLoading && loadingType === 'new'" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Konsultasi Baru</span>
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold">{{ consultationStats.total }}</div>
                        <div class="text-blue-100 text-sm">Total Konsultasi</div>
                    </div>
                    <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold">{{ consultationStats.akademik }}</div>
                        <div class="text-green-100 text-sm">Konsultasi Akademik</div>
                    </div>
                    <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold">{{ consultationStats.perilaku }}</div>
                        <div class="text-yellow-100 text-sm">Konsultasi Perilaku</div>
                    </div>
                    <svg class="w-8 h-8 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold">{{ consultationStats.thisMonth }}</div>
                        <div class="text-purple-100 text-sm">Bulan Ini</div>
                    </div>
                    <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Students Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="siswa in filteredSiswa" :key="siswa.id" 
                 class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-all duration-200 border border-gray-100">
                <div class="p-6">
                    <!-- Student Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-blue-500 rounded-full flex items-center justify-center shadow-sm">
                                <span class="text-white font-bold text-sm">
                                    {{ siswa.nama_lengkap.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ siswa.nama_lengkap }}</h3>
                                <p class="text-sm text-gray-500">{{ siswa.kelas.nama_kelas }} • {{ siswa.nisn }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full" 
                              :class="getConsultationStatusBadge(siswa.id).class">
                            {{ getConsultationStatusBadge(siswa.id).text }}
                        </span>
                    </div>

                    <!-- Consultation Summary -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                </svg>
                                Riwayat Konsultasi
                            </span>
                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">
                                {{ getConsultationCount(siswa.id) }} sesi
                            </span>
                        </div>
                        <div class="text-xs text-gray-600 mb-2">
                            <strong>Terakhir:</strong> {{ getLastConsultation(siswa.id) }}
                        </div>
                        <div class="text-xs text-gray-600">
                            <strong>Jenis Terbanyak:</strong> {{ getMostCommonType(siswa.id) }}
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-lg font-bold text-blue-600">{{ getAcademicConsultations(siswa.id) }}</div>
                            <div class="text-xs text-blue-600">Akademik</div>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-lg">
                            <div class="text-lg font-bold text-yellow-600">{{ getBehaviorConsultations(siswa.id) }}</div>
                            <div class="text-xs text-yellow-600">Perilaku</div>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-600">{{ getAttendanceConsultations(siswa.id) }}</div>
                            <div class="text-xs text-green-600">Kehadiran</div>
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <button @click="startConsultation(siswa)" 
                                :disabled="isLoading && loadingStudentId === siswa.id"
                                class="flex-1 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm py-2 px-3 rounded-lg transition-all duration-200 flex items-center justify-center space-x-1">
                            <svg v-if="isLoading && loadingStudentId === siswa.id" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                            <span>Konsultasi</span>
                        </button>
                        <button @click="viewHistory(siswa)" 
                                :disabled="isLoading && loadingStudentId === siswa.id"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm py-2 px-3 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-1">
                            <svg v-if="isLoading && loadingStudentId === siswa.id" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Riwayat</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredSiswa.length === 0" class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-12 text-center">
                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada siswa yang sesuai</h3>
                <p class="text-gray-500 mb-4">Ubah filter atau kriteria pencarian untuk melihat data siswa.</p>
                <button @click="resetFilters" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>

        <!-- Consultation Modal -->
        <div v-if="showConsultationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
             @click="closeConsultationModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" 
                 @click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Konsultasi dengan {{ selectedSiswaForConsultation?.nama_lengkap }}
                        </h3>
                        <button @click="closeConsultationModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form @submit.prevent="saveConsultation" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Konsultasi</label>
                            <input type="date" 
                                   v-model="consultationForm.tanggal"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Konsultasi</label>
                            <select v-model="consultationForm.jenis" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <option value="">Pilih Jenis Konsultasi</option>
                                <option value="akademik">Akademik</option>
                                <option value="perilaku">Perilaku</option>
                                <option value="kehadiran">Kehadiran</option>
                                <option value="keluarga">Masalah Keluarga</option>
                                <option value="pribadi">Masalah Pribadi</option>
                                <option value="karir">Bimbingan Karir</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Permasalahan / Topik</label>
                            <textarea v-model="consultationForm.topik" 
                                      rows="3"
                                      placeholder="Jelaskan permasalahan atau topik yang dibahas..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      required>
                            </textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Solusi / Tindak Lanjut</label>
                            <textarea v-model="consultationForm.solusi" 
                                      rows="3"
                                      placeholder="Jelaskan solusi yang diberikan atau tindak lanjut yang akan dilakukan..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      required>
                            </textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hasil / Kesimpulan</label>
                            <textarea v-model="consultationForm.hasil" 
                                      rows="2"
                                      placeholder="Ringkasan hasil konsultasi..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </textarea>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   v-model="consultationForm.perlu_followup"
                                   id="followup"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <label for="followup" class="ml-2 text-sm text-gray-700">
                                Perlu tindak lanjut konsultasi
                            </label>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" 
                                    @click="closeConsultationModal"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                    :disabled="consultationProcessing"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                                <span v-if="consultationProcessing">Menyimpan...</span>
                                <span v-else>Simpan Konsultasi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Modal -->
        <div v-if="showHistoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
             @click="closeHistoryModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white" 
                 @click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Riwayat Konsultasi - {{ selectedSiswaForHistory?.nama_lengkap }}
                        </h3>
                        <button @click="closeHistoryModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        <div v-for="consultation in consultationHistory" :key="consultation.id" 
                             class="border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-900">{{ consultation.jenis }}</span>
                                <span class="text-sm text-gray-500">{{ formatDate(consultation.tanggal) }}</span>
                            </div>
                            <div class="text-sm text-gray-700 mb-2">
                                <strong>Topik:</strong> {{ consultation.topik }}
                            </div>
                            <div class="text-sm text-gray-700 mb-2">
                                <strong>Solusi:</strong> {{ consultation.solusi }}
                            </div>
                            <div v-if="consultation.hasil" class="text-sm text-gray-700 mb-2">
                                <strong>Hasil:</strong> {{ consultation.hasil }}
                            </div>
                            <div v-if="consultation.perlu_followup" class="text-xs text-orange-600">
                                ⚠️ Perlu tindak lanjut
                            </div>
                        </div>
                        
                        <div v-if="consultationHistory.length === 0" class="text-center py-8 text-gray-500">
                            Belum ada riwayat konsultasi
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    siswaList: Array,
    kelasAsWali: Array
});

const selectedKelas = ref('');
const searchQuery = ref('');
const showConsultationModal = ref(false);
const showHistoryModal = ref(false);
const selectedSiswaForConsultation = ref(null);
const selectedSiswaForHistory = ref(null);
const consultationProcessing = ref(false);

// Loading states
const isLoading = ref(false);
const loadingMessage = ref('');
const loadingType = ref(null);
const loadingStudentId = ref(null);

// Mock data - In real app, this would come from backend
const consultations = ref([]);
const consultationHistory = ref([]);

const consultationForm = ref({
    tanggal: new Date().toISOString().split('T')[0],
    jenis: '',
    topik: '',
    solusi: '',
    hasil: '',
    perlu_followup: false
});

const filterByKelas = () => {
    // Filter logic is handled by computed property
};

const openNewConsultation = async () => {
    try {
        isLoading.value = true;
        loadingType.value = 'new';
        loadingMessage.value = 'Menyiapkan form konsultasi...';
        
        // Small delay to show loading state
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // Quick access to create new consultation
        showConsultationModal.value = true;
        selectedSiswaForConsultation.value = null;
        resetConsultationForm();
    } catch (error) {
        console.error('Error opening new consultation:', error);
    } finally {
        isLoading.value = false;
        loadingType.value = null;  
        loadingMessage.value = '';
    }
};

const startConsultation = async (siswa) => {
    try {
        loadingStudentId.value = siswa.id;
        isLoading.value = true;
        loadingMessage.value = 'Memulai konsultasi untuk ' + siswa.nama_lengkap + '...';
        
        // Small delay to show loading state
        await new Promise(resolve => setTimeout(resolve, 400));
        
        selectedSiswaForConsultation.value = siswa;
        showConsultationModal.value = true;
        resetConsultationForm();
    } catch (error) {
        console.error('Error starting consultation:', error);
    } finally {
        isLoading.value = false;
        loadingStudentId.value = null;
        loadingMessage.value = '';
    }
};

const closeConsultationModal = () => {
    showConsultationModal.value = false;
    selectedSiswaForConsultation.value = null;
    resetConsultationForm();
};

const resetConsultationForm = () => {
    consultationForm.value = {
        tanggal: new Date().toISOString().split('T')[0],
        jenis: '',
        topik: '',
        solusi: '',
        hasil: '',
        perlu_followup: false
    };
};

const saveConsultation = async () => {
    try {
        consultationProcessing.value = true;
        isLoading.value = true;
        loadingMessage.value = 'Menyimpan konsultasi...';
        
        // Mock save - In real app, this would be an API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        const newConsultation = {
            id: Date.now(),
            siswa_id: selectedSiswaForConsultation.value.id,
            ...consultationForm.value
        };
        
        consultations.value.push(newConsultation);
        closeConsultationModal();
        
        // Show success message
        alert('Konsultasi berhasil disimpan!');
    } catch (error) {
        console.error('Error saving consultation:', error);
    } finally {
        consultationProcessing.value = false;
        isLoading.value = false;
        loadingMessage.value = '';
    }
};

const viewHistory = async (siswa) => {
    try {
        loadingStudentId.value = siswa.id;
        isLoading.value = true;
        loadingMessage.value = 'Memuat riwayat konsultasi...';
        
        selectedSiswaForHistory.value = siswa;
        
        // Mock delay for loading state
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Mock history data - In real app, this would be fetched from backend
        consultationHistory.value = consultations.value.filter(c => c.siswa_id === siswa.id);
        
        showHistoryModal.value = true;
    } catch (error) {
        console.error('Error loading consultation history:', error);
    } finally {
        isLoading.value = false;
        loadingStudentId.value = null;
        loadingMessage.value = '';
    }
};

const closeHistoryModal = () => {
    showHistoryModal.value = false;
    selectedSiswaForHistory.value = null;
    consultationHistory.value = [];
};

const getConsultationCount = (siswaId) => {
    return consultations.value.filter(c => c.siswa_id === siswaId).length;
};

const getLastConsultation = (siswaId) => {
    const siswaConsultations = consultations.value.filter(c => c.siswa_id === siswaId);
    if (siswaConsultations.length === 0) return 'Belum pernah';
    
    const latest = siswaConsultations.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal))[0];
    return formatDate(latest.tanggal);
};

const getMostCommonType = (siswaId) => {
    const siswaConsultations = consultations.value.filter(c => c.siswa_id === siswaId);
    if (siswaConsultations.length === 0) return 'Belum ada';
    
    const types = siswaConsultations.map(c => c.jenis);
    const counts = {};
    types.forEach(type => counts[type] = (counts[type] || 0) + 1);
    
    const mostCommon = Object.keys(counts).reduce((a, b) => counts[a] > counts[b] ? a : b);
    return mostCommon.charAt(0).toUpperCase() + mostCommon.slice(1);
};

const getAcademicConsultations = (siswaId) => {
    return consultations.value.filter(c => c.siswa_id === siswaId && c.jenis === 'akademik').length;
};

const getBehaviorConsultations = (siswaId) => {
    return consultations.value.filter(c => c.siswa_id === siswaId && c.jenis === 'perilaku').length;
};

const getAttendanceConsultations = (siswaId) => {
    return consultations.value.filter(c => c.siswa_id === siswaId && c.jenis === 'kehadiran').length;
};

const getConsultationStatusBadge = (siswaId) => {
    const count = getConsultationCount(siswaId);
    if (count === 0) {
        return { text: 'Belum ada konsultasi', class: 'bg-gray-100 text-gray-800' };
    } else if (count <= 2) {
        return { text: 'Konsultasi terbatas', class: 'bg-yellow-100 text-yellow-800' };
    } else if (count <= 5) {
        return { text: 'Konsultasi aktif', class: 'bg-blue-100 text-blue-800' };
    } else {
        return { text: 'Konsultasi intensif', class: 'bg-red-100 text-red-800' };
    }
};

// Computed property for consultation statistics
const consultationStats = computed(() => {
    const total = consultations.value.length;
    const akademik = consultations.value.filter(c => c.jenis === 'akademik').length;
    const perilaku = consultations.value.filter(c => c.jenis === 'perilaku').length;
    
    // This month consultations
    const currentMonth = new Date().getMonth();
    const currentYear = new Date().getFullYear();
    const thisMonth = consultations.value.filter(c => {
        const consultationDate = new Date(c.tanggal);
        return consultationDate.getMonth() === currentMonth && consultationDate.getFullYear() === currentYear;
    }).length;
    
    return {
        total,
        akademik,
        perilaku,
        thisMonth
    };
});

// Filtered siswa based on search and class filter
const filteredSiswa = computed(() => {
    let filtered = props.siswaList || [];
    
    // Search filter
    if (searchQuery.value) {
        filtered = filtered.filter(siswa => 
            siswa.nama_lengkap.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            siswa.nisn.includes(searchQuery.value)
        );
    }
    
    // Class filter
    if (selectedKelas.value) {
        filtered = filtered.filter(siswa => siswa.kelas.id == selectedKelas.value);
    }
    
    return filtered;
});

const resetFilters = () => {
    searchQuery.value = '';
    selectedKelas.value = '';
};

const getAcademicStatus = (siswaId) => {
    // Mock academic status - In real app, this would be calculated from actual data
    const statuses = ['Baik', 'Cukup', 'Perlu Perhatian'];
    return statuses[siswaId % 3];
};

const getAttendanceStatus = (siswaId) => {
    // Mock attendance status - In real app, this would be calculated from actual data
    const statuses = ['95%', '87%', '78%'];
    return statuses[siswaId % 3];
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>
