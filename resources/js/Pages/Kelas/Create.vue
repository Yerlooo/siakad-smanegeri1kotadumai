<template>
    <Head title="SIAKAD SMANSA" />

    <AppLayout page-title="Tambah Kelas">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Kelas</h1>
                    <p class="text-gray-600">Buat data kelas baru</p>
                </div>
                <Link :href="route('kelas.index')" 
                      class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali</span>
                </Link>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Form Tambah Kelas</h2>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Informasi Kelas -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Informasi Kelas
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="nama_kelas" value="Nama Kelas" />
                                <TextInput
                                    id="nama_kelas"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.nama_kelas"
                                    required
                                    autofocus
                                    placeholder="Contoh: X IPA 1, XI IPS 2"
                                />
                                <InputError class="mt-2" :message="form.errors.nama_kelas" />
                            </div>

                            <div>
                                <InputLabel for="tingkat" value="Tingkat" />
                                <select
                                    id="tingkat"
                                    v-model="form.tingkat"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Pilih Tingkat</option>
                                    <option value="10">X (Sepuluh)</option>
                                    <option value="11">XI (Sebelas)</option>
                                    <option value="12">XII (Duabelas)</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.tingkat" />
                            </div>

                            <div>
                                <InputLabel for="jurusan" value="Jurusan" />
                                <select
                                    id="jurusan"
                                    v-model="form.jurusan"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Pilih Jurusan</option>
                                    <option value="IPA">IPA (Ilmu Pengetahuan Alam)</option>
                                    <option value="IPS">IPS (Ilmu Pengetahuan Sosial)</option>
                                    <option value="BAHASA">Bahasa</option>
                                    <option value="UMUM">Umum</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.jurusan" />
                            </div>

                            <div>
                                <InputLabel for="kapasitas" value="Kapasitas Siswa" />
                                <TextInput
                                    id="kapasitas"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.kapasitas"
                                    min="1"
                                    max="50"
                                    placeholder="Contoh: 36"
                                />
                                <InputError class="mt-2" :message="form.errors.kapasitas" />
                            </div>

                            <div>
                                <InputLabel for="wali_kelas_id" value="Wali Kelas" />
                                <select
                                    id="wali_kelas_id"
                                    v-model="form.wali_kelas_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">Pilih Wali Kelas</option>
                                    <option v-for="guruItem in guru" :key="guruItem.id" :value="guruItem.id">
                                        {{ guruItem.name }} - {{ guruItem.nip }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.wali_kelas_id" />
                            </div>

                            <div>
                                <InputLabel for="ruang_kelas" value="Ruang Kelas" />
                                <TextInput
                                    id="ruang_kelas"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.ruang_kelas"
                                    placeholder="Contoh: R101, Lab Komputer"
                                />
                                <InputError class="mt-2" :message="form.errors.ruang_kelas" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="keterangan" value="Keterangan" />
                            <textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                                placeholder="Keterangan tambahan tentang kelas (opsional)"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.keterangan" />
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Status
                        </h3>
                        
                        <div>
                            <InputLabel for="status" value="Status Kelas" />
                            <select
                                id="status"
                                v-model="form.status"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6">
                        <Link :href="route('kelas.index')" 
                              class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                            Batal
                        </Link>
                        <button type="submit" 
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors disabled:opacity-50">
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Kelas</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Informasi Bantuan -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Petunjuk Pengisian</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Nama kelas harus unik dan jelas mencerminkan tingkat dan jurusan</li>
                                <li>Kapasitas siswa disesuaikan dengan standar ruang kelas (maksimal 50 siswa)</li>
                                <li>Setiap kelas sebaiknya memiliki wali kelas untuk pembinaan siswa</li>
                                <li>Status aktif untuk kelas yang sedang berjalan, non-aktif untuk kelas yang sudah selesai</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
    guru: Array
})

const form = useForm({
    nama_kelas: '',
    tingkat: '',
    jurusan: '',
    kapasitas: '',
    wali_kelas_id: '',
    ruang_kelas: '',
    keterangan: '',
    status: 'aktif'
})

const submit = () => {
    form.post(route('kelas.store'))
}
</script>
