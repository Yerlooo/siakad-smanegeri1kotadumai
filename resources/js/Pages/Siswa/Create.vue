<template>
    <Head title="SIAKAD SMANSA" />

    <AppLayout page-title="Tambah Data Siswa">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center space-x-4">
                    <Link :href="route('siswa.index')" 
                          class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tambah Data Siswa</h1>
                        <p class="text-gray-600">Masukkan data siswa baru</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Data Pribadi -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Data Pribadi</h3>
                        
                        <!-- Info Box -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium mb-1">Pembuatan Akun Otomatis</p>
                                    <p>Jika email diisi, sistem akan otomatis membuat akun login dengan role "murid" untuk siswa ini. Password default akan menggunakan tanggal lahir (format: YYYYMMDD).</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    NIS <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.nis"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.nis }"
                                    placeholder="Masukkan NIS"
                                >
                                <div v-if="errors.nis" class="mt-1 text-sm text-red-600">{{ errors.nis }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.nama_lengkap"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.nama_lengkap }"
                                    placeholder="Masukkan nama lengkap"
                                >
                                <div v-if="errors.nama_lengkap" class="mt-1 text-sm text-red-600">{{ errors.nama_lengkap }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Kelamin <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    v-model="form.jenis_kelamin"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.jenis_kelamin }"
                                >
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <div v-if="errors.jenis_kelamin" class="mt-1 text-sm text-red-600">{{ errors.jenis_kelamin }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tempat Lahir <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.tempat_lahir"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.tempat_lahir }"
                                    placeholder="Masukkan tempat lahir"
                                >
                                <div v-if="errors.tempat_lahir" class="mt-1 text-sm text-red-600">{{ errors.tempat_lahir }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Lahir <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.tanggal_lahir"
                                    type="date" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.tanggal_lahir }"
                                >
                                <div v-if="errors.tanggal_lahir" class="mt-1 text-sm text-red-600">{{ errors.tanggal_lahir }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    No. Telepon
                                </label>
                                <input 
                                    v-model="form.no_telepon"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.no_telepon }"
                                    placeholder="Masukkan no. telepon"
                                >
                                <div v-if="errors.no_telepon" class="mt-1 text-sm text-red-600">{{ errors.no_telepon }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input 
                                    v-model="form.email"
                                    type="email" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.email }"
                                    placeholder="Masukkan email"
                                >
                                <div v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</div>
                                <div class="mt-1 text-xs text-blue-600">
                                    💡 Jika diisi, akun login siswa akan otomatis dibuat dengan role "murid" (password: tanggal lahir)
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kelas
                                </label>
                                <select 
                                    v-model="form.kelas_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.kelas_id }"
                                >
                                    <option value="">Pilih Kelas</option>
                                    <option 
                                        v-for="kelasItem in kelas" 
                                        :key="kelasItem.id" 
                                        :value="kelasItem.id"
                                        :disabled="kelasItem.siswa_count >= kelasItem.kapasitas"
                                        :class="kelasItem.siswa_count >= kelasItem.kapasitas ? 'text-gray-400' : ''"
                                    >
                                        {{ kelasItem.nama_kelas }} 
                                        ({{ kelasItem.siswa_count || 0 }}/{{ kelasItem.kapasitas }})
                                        <span v-if="kelasItem.siswa_count >= kelasItem.kapasitas"> - PENUH</span>
                                    </option>
                                </select>
                                <div v-if="selectedKelas && selectedKelas.siswa_count >= selectedKelas.kapasitas" 
                                     class="mt-1 text-sm text-orange-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Kelas ini sudah mencapai kapasitas maksimal
                                </div>
                                <div v-if="errors.kelas_id" class="mt-1 text-sm text-red-600">{{ errors.kelas_id }}</div>
                                <div v-if="selectedKelas && selectedKelas.siswa_count < selectedKelas.kapasitas" 
                                     class="mt-1 text-sm text-green-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Tersisa {{ selectedKelas.kapasitas - selectedKelas.siswa_count }} tempat
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    v-model="form.alamat"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.alamat }"
                                    placeholder="Masukkan alamat lengkap"
                                ></textarea>
                                <div v-if="errors.alamat" class="mt-1 text-sm text-red-600">{{ errors.alamat }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Data Orang Tua</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Ayah
                                </label>
                                <input 
                                    v-model="form.nama_ayah"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.nama_ayah }"
                                    placeholder="Masukkan nama ayah"
                                >
                                <div v-if="errors.nama_ayah" class="mt-1 text-sm text-red-600">{{ errors.nama_ayah }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Ibu
                                </label>
                                <input 
                                    v-model="form.nama_ibu"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.nama_ibu }"
                                    placeholder="Masukkan nama ibu"
                                >
                                <div v-if="errors.nama_ibu" class="mt-1 text-sm text-red-600">{{ errors.nama_ibu }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pekerjaan Ayah
                                </label>
                                <input 
                                    v-model="form.pekerjaan_ayah"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.pekerjaan_ayah }"
                                    placeholder="Masukkan pekerjaan ayah"
                                >
                                <div v-if="errors.pekerjaan_ayah" class="mt-1 text-sm text-red-600">{{ errors.pekerjaan_ayah }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pekerjaan Ibu
                                </label>
                                <input 
                                    v-model="form.pekerjaan_ibu"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.pekerjaan_ibu }"
                                    placeholder="Masukkan pekerjaan ibu"
                                >
                                <div v-if="errors.pekerjaan_ibu" class="mt-1 text-sm text-red-600">{{ errors.pekerjaan_ibu }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Akademik -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Data Akademik</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tahun Masuk <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.tahun_masuk"
                                    type="number" 
                                    :min="2000"
                                    :max="new Date().getFullYear() + 1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.tahun_masuk }"
                                    placeholder="Masukkan tahun masuk"
                                >
                                <div v-if="errors.tahun_masuk" class="mt-1 text-sm text-red-600">{{ errors.tahun_masuk }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    v-model="form.status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.status }"
                                >
                                    <option value="">Pilih Status</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="lulus">Lulus</option>
                                    <option value="pindah">Pindah</option>
                                    <option value="keluar">Keluar</option>
                                </select>
                                <div v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-200 pt-6 flex justify-end space-x-4">
                        <Link :href="route('siswa.index')" 
                              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </Link>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    kelas: Array,
    errors: Object
})

const form = useForm({
    nis: '',
    nama_lengkap: '',
    jenis_kelamin: '',
    tanggal_lahir: '',
    tempat_lahir: '',
    alamat: '',
    no_telepon: '',
    email: '',
    nama_ayah: '',
    nama_ibu: '',
    pekerjaan_ayah: '',
    pekerjaan_ibu: '',
    kelas_id: '',
    tahun_masuk: new Date().getFullYear(),
    status: 'aktif'
})

const selectedKelas = computed(() => {
    if (!form.kelas_id) return null
    return props.kelas.find(k => k.id == form.kelas_id)
})

const submit = () => {
    form.post(route('siswa.store'), {
        onSuccess: () => {
            // Success akan dihandle oleh flash message di layout
        }
    })
}
</script>
