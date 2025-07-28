<template>
    <AppLayout page-title="Tambah Jadwal Pelajaran">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Tambah Jadwal Pelajaran Baru</h3>
                        <Link :href="route('jadwal-pelajaran.index')" 
                              class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </Link>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Mata Pelajaran -->
                            <div>
                                <label for="mata_pelajaran_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mata Pelajaran <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="mata_pelajaran_id"
                                    v-model="form.mata_pelajaran_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.mata_pelajaran_id }"
                                    required
                                >
                                    <option value="">Pilih Mata Pelajaran</option>
                                    <option v-for="mapel in mataPelajaran" :key="mapel.id" :value="mapel.id">
                                        {{ mapel.nama_mapel }} ({{ mapel.kode_mapel }})
                                    </option>
                                </select>
                                <div v-if="form.errors.mata_pelajaran_id" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.mata_pelajaran_id }}
                                </div>
                            </div>

                            <!-- Kelas -->
                            <div>
                                <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kelas <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="kelas_id"
                                    v-model="form.kelas_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.kelas_id }"
                                    required
                                >
                                    <option value="">Pilih Kelas</option>
                                    <option v-for="kelas in kelasOptions" :key="kelas.id" :value="kelas.id">
                                        {{ kelas.nama_kelas }} - {{ kelas.tingkat }}
                                    </option>
                                </select>
                                <div v-if="form.errors.kelas_id" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.kelas_id }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Guru -->
                            <div>
                                <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Guru Pengampu <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="guru_id"
                                    v-model="form.guru_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.guru_id }"
                                    required
                                >
                                    <option value="">Pilih Guru</option>
                                    <option v-for="guruItem in guru" :key="guruItem.id" :value="guruItem.id">
                                        {{ guruItem.name }} ({{ guruItem.nip }})
                                    </option>
                                </select>
                                <div v-if="form.errors.guru_id" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.guru_id }}
                                </div>
                            </div>

                            <!-- Hari -->
                            <div>
                                <label for="hari" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hari <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="hari"
                                    v-model="form.hari"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.hari }"
                                    required
                                >
                                    <option value="">Pilih Hari</option>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                                <div v-if="form.errors.hari" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.hari }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Jam Mulai -->
                            <div>
                                <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Mulai <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="jam_mulai"
                                    v-model="form.jam_mulai"
                                    type="time"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.jam_mulai }"
                                    required
                                />
                                <div v-if="form.errors.jam_mulai" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.jam_mulai }}
                                </div>
                            </div>

                            <!-- Jam Selesai -->
                            <div>
                                <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Selesai <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="jam_selesai"
                                    v-model="form.jam_selesai"
                                    type="time"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.jam_selesai }"
                                    required
                                />
                                <div v-if="form.errors.jam_selesai" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.jam_selesai }}
                                </div>
                            </div>

                            <!-- Ruangan -->
                            <div>
                                <label for="ruangan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ruangan
                                </label>
                                <input
                                    id="ruangan"
                                    v-model="form.ruangan"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.ruangan }"
                                    placeholder="Contoh: R101"
                                />
                                <div v-if="form.errors.ruangan" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.ruangan }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Semester -->
                            <div>
                                <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                                    Semester <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="semester"
                                    v-model="form.semester"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.semester }"
                                    required
                                >
                                    <option value="">Pilih Semester</option>
                                    <option value="ganjil">Semester Ganjil</option>
                                    <option value="genap">Semester Genap</option>
                                </select>
                                <div v-if="form.errors.semester" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.semester }}
                                </div>
                            </div>

                            <!-- Tahun Ajaran -->
                            <div>
                                <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tahun Ajaran <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="tahun_ajaran"
                                    v-model="form.tahun_ajaran"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.tahun_ajaran }"
                                    required
                                >
                                    <option value="">Pilih Tahun Ajaran</option>
                                    <option value="2024/2025">2024/2025</option>
                                    <option value="2025/2026">2025/2026</option>
                                    <option value="2026/2027">2026/2027</option>
                                </select>
                                <div v-if="form.errors.tahun_ajaran" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.tahun_ajaran }}
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <div class="flex items-center">
                                <input
                                    id="status"
                                    v-model="form.status"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="status" class="ml-2 block text-sm text-gray-900">
                                    Aktif
                                </label>
                            </div>
                            <div v-if="form.errors.status" class="mt-2 text-sm text-red-600">
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div v-if="form.errors.error" class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        Terjadi kesalahan
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        {{ form.errors.error }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                            <Link :href="route('jadwal-pelajaran.index')" 
                                  class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    mataPelajaran: Array,
    kelas: Array,
    guru: Array
})

const kelasOptions = props.kelas

const form = useForm({
    mata_pelajaran_id: '',
    kelas_id: '',
    guru_id: '',
    hari: '',
    jam_mulai: '',
    jam_selesai: '',
    ruangan: '',
    semester: '',
    tahun_ajaran: '2024/2025',
    status: true
})

const submit = () => {
    form.post(route('jadwal-pelajaran.store'))
}
</script>
