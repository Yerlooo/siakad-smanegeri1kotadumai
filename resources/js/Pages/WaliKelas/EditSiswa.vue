<template>
    <AppLayout :page-title="`Edit Data Siswa - ${siswa.nama_lengkap}`">
        <!-- Action Button -->
        <div class="mb-6 flex justify-end">
            <Link :href="route('wali-kelas.monitoring-siswa')" 
                  class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                Kembali
            </Link>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Privilege Notice -->
                        <div v-if="isWaliKelasAccess" class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="flex-shrink-0 h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Akses Wali Kelas</h3>
                                    <p class="mt-1 text-sm text-blue-700">
                                        Anda dapat mengedit data siswa ini karena Anda adalah wali kelas dari {{ siswa.kelas.nama_kelas }}.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="updateSiswa">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Data Pribadi -->
                                <div class="col-span-2">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Data Pribadi Siswa</h3>
                                </div>
                                
                                <div>
                                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="nama_lengkap" 
                                           v-model="form.nama_lengkap"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                    <div v-if="errors.nama_lengkap" class="mt-1 text-sm text-red-600">
                                        {{ errors.nama_lengkap }}
                                    </div>
                                </div>

                                <div>
                                    <label for="no_telepon" class="block text-sm font-medium text-gray-700">
                                        No. Telepon
                                    </label>
                                    <input type="text" 
                                           id="no_telepon" 
                                           v-model="form.no_telepon"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <div v-if="errors.no_telepon" class="mt-1 text-sm text-red-600">
                                        {{ errors.no_telepon }}
                                    </div>
                                </div>

                                <div class="col-span-2">
                                    <label for="alamat" class="block text-sm font-medium text-gray-700">
                                        Alamat
                                    </label>
                                    <textarea id="alamat" 
                                              v-model="form.alamat"
                                              rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </textarea>
                                    <div v-if="errors.alamat" class="mt-1 text-sm text-red-600">
                                        {{ errors.alamat }}
                                    </div>
                                </div>

                                <!-- Data Orang Tua -->
                                <div class="col-span-2 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Data Orang Tua</h3>
                                </div>

                                <div>
                                    <label for="nama_ayah" class="block text-sm font-medium text-gray-700">
                                        Nama Ayah
                                    </label>
                                    <input type="text" 
                                           id="nama_ayah" 
                                           v-model="form.nama_ayah"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <div v-if="errors.nama_ayah" class="mt-1 text-sm text-red-600">
                                        {{ errors.nama_ayah }}
                                    </div>
                                </div>

                                <div>
                                    <label for="pekerjaan_ayah" class="block text-sm font-medium text-gray-700">
                                        Pekerjaan Ayah
                                    </label>
                                    <input type="text" 
                                           id="pekerjaan_ayah" 
                                           v-model="form.pekerjaan_ayah"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <div v-if="errors.pekerjaan_ayah" class="mt-1 text-sm text-red-600">
                                        {{ errors.pekerjaan_ayah }}
                                    </div>
                                </div>

                                <div>
                                    <label for="nama_ibu" class="block text-sm font-medium text-gray-700">
                                        Nama Ibu
                                    </label>
                                    <input type="text" 
                                           id="nama_ibu" 
                                           v-model="form.nama_ibu"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <div v-if="errors.nama_ibu" class="mt-1 text-sm text-red-600">
                                        {{ errors.nama_ibu }}
                                    </div>
                                </div>

                                <div>
                                    <label for="pekerjaan_ibu" class="block text-sm font-medium text-gray-700">
                                        Pekerjaan Ibu
                                    </label>
                                    <input type="text" 
                                           id="pekerjaan_ibu" 
                                           v-model="form.pekerjaan_ibu"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <div v-if="errors.pekerjaan_ibu" class="mt-1 text-sm text-red-600">
                                        {{ errors.pekerjaan_ibu }}
                                    </div>
                                </div>

                                <!-- Keterangan -->
                                <div class="col-span-2 mt-6">
                                    <label for="keterangan" class="block text-sm font-medium text-gray-700">
                                        Keterangan / Catatan Wali Kelas
                                    </label>
                                    <textarea id="keterangan" 
                                              v-model="form.keterangan"
                                              rows="3"
                                              placeholder="Catatan khusus tentang siswa ini..."
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </textarea>
                                    <div v-if="errors.keterangan" class="mt-1 text-sm text-red-600">
                                        {{ errors.keterangan }}
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-8 flex items-center justify-end space-x-4">
                                <Link :href="route('wali-kelas.monitoring-siswa')" 
                                      class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                                    Batal
                                </Link>
                                <button type="submit" 
                                        :disabled="processing"
                                        :class="[
                                            'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                                            processing 
                                                ? 'bg-gray-400 text-white cursor-not-allowed' 
                                                : 'bg-blue-600 hover:bg-blue-700 text-white'
                                        ]">
                                    <span v-if="processing" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Menyimpan...
                                    </span>
                                    <span v-else>Simpan Perubahan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Student Info Card -->
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Siswa</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NISN</label>
                                <p class="mt-1 text-sm text-gray-900">{{ siswa.nisn }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kelas</label>
                                <p class="mt-1 text-sm text-gray-900">{{ siswa.kelas.nama_kelas }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                <p class="mt-1 text-sm text-gray-900">{{ siswa.jenis_kelamin }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDate(siswa.tanggal_lahir) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                                <p class="mt-1 text-sm text-gray-900">{{ siswa.tempat_lahir || '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    siswa: Object,
    kelasList: Array,
    isWaliKelasAccess: Boolean,
    errors: Object
});

const form = useForm({
    nama_lengkap: props.siswa.nama_lengkap || '',
    no_telepon: props.siswa.no_telepon || '',
    alamat: props.siswa.alamat || '',
    nama_ayah: props.siswa.nama_ayah || '',
    nama_ibu: props.siswa.nama_ibu || '',
    pekerjaan_ayah: props.siswa.pekerjaan_ayah || '',
    pekerjaan_ibu: props.siswa.pekerjaan_ibu || '',
    keterangan: props.siswa.keterangan || ''
});

const updateSiswa = () => {
    form.put(route('wali-kelas.update-siswa', props.siswa.id), {
        onSuccess: () => {
            // Handle success
        },
        onError: (errors) => {
            // Handle errors
            console.error('Validation errors:', errors);
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const processing = form.processing;
</script>
