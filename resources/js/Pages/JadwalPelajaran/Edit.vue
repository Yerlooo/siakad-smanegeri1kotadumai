<template>
    <Head title="SIAKAD SMANSA" />

    <AppLayout page-title="Edit Jadwal Pelajaran">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Jadwal Pelajaran</h1>
                    <p class="text-gray-600">Ubah jadwal pelajaran</p>
                </div>
                <Link :href="route('jadwal-pelajaran.index')" 
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
                    <h2 class="text-lg font-semibold text-gray-900">Form Edit Jadwal Pelajaran</h2>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Informasi Dasar -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Informasi Dasar
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="mata_pelajaran_id" value="Mata Pelajaran" />
                                <select
                                    id="mata_pelajaran_id"
                                    v-model="form.mata_pelajaran_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Pilih Mata Pelajaran</option>
                                    <option v-for="mapel in mataPelajaranList" :key="mapel.id" :value="mapel.id">
                                        {{ mapel.kode_mapel }} - {{ mapel.nama_mapel }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.mata_pelajaran_id" />
                            </div>

                            <div>
                                <InputLabel for="kelas_id" value="Kelas" />
                                <select
                                    id="kelas_id"
                                    v-model="form.kelas_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Pilih Kelas</option>
                                    <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
                                        {{ kelas.nama_kelas }} ({{ kelas.tingkat }} {{ kelas.jurusan }})
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.kelas_id" />
                            </div>

                            <div>
                                <InputLabel for="guru_id" value="Guru Pengampu" />
                                <select
                                    id="guru_id"
                                    v-model="form.guru_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Pilih Guru</option>
                                    <option v-for="guru in guruList" :key="guru.id" :value="guru.id">
                                        {{ guru.name }} - {{ guru.nip || 'No NIP' }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.guru_id" />
                            </div>

                            <div>
                                <InputLabel for="hari" value="Hari" />
                                <select
                                    id="hari"
                                    v-model="form.hari"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
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
                                <InputError class="mt-2" :message="form.errors.hari" />
                            </div>
                        </div>
                    </div>

                    <!-- Waktu dan Ruangan -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Waktu dan Ruangan
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <InputLabel for="jam_mulai" value="Jam Mulai" />
                                <select
                                    id="jam_mulai"
                                    v-model="form.jam_mulai"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                    @change="updateJamSelesai"
                                >
                                    <option value="">Pilih Jam Mulai</option>
                                    <option value="07:00">07:00 (Jam ke-1)</option>
                                    <option value="07:45">07:45 (Jam ke-2)</option>
                                    <option value="08:30">08:30 (Jam ke-3)</option>
                                    <option value="09:15">09:15 (Jam ke-4)</option>
                                    <option value="10:15">10:15 (Jam ke-5)</option>
                                    <option value="11:00">11:00 (Jam ke-6)</option>
                                    <option value="11:45">11:45 (Jam ke-7)</option>
                                    <option value="13:00">13:00 (Jam ke-8)</option>
                                    <option value="13:45">13:45 (Jam ke-9)</option>
                                    <option value="14:30">14:30 (Jam ke-10)</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.jam_mulai" />
                            </div>

                            <div>
                                <InputLabel for="jam_selesai" value="Jam Selesai" />
                                <select
                                    id="jam_selesai"
                                    v-model="form.jam_selesai"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Pilih Jam Selesai</option>
                                    <option v-for="jamSelesai in availableJamSelesai" :key="jamSelesai.value" :value="jamSelesai.value">
                                        {{ jamSelesai.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.jam_selesai" />
                            </div>

                            <div>
                                <InputLabel for="ruangan" value="Ruangan" />
                                <TextInput
                                    id="ruangan"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.ruangan"
                                    placeholder="Contoh: R101, Lab Komputer"
                                />
                                <InputError class="mt-2" :message="form.errors.ruangan" />
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-start space-x-3">
                                <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-blue-800 font-medium">Informasi Waktu Pelajaran</p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        Setiap jam pelajaran berdurasi 45 menit. Jam selesai akan otomatis disesuaikan berdasarkan jam mulai dan durasi mata pelajaran.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Akademik -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Informasi Akademik
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="semester" value="Semester" />
                                <select
                                    id="semester"
                                    v-model="form.semester"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Pilih Semester</option>
                                    <option value="ganjil">Semester Ganjil</option>
                                    <option value="genap">Semester Genap</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.semester" />
                            </div>

                            <div>
                                <InputLabel for="tahun_ajaran" value="Tahun Ajaran" />
                                <select
                                    id="tahun_ajaran"
                                    v-model="form.tahun_ajaran"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Pilih Tahun Ajaran</option>
                                    <option value="2024/2025">2024/2025</option>
                                    <option value="2025/2026">2025/2026</option>
                                    <option value="2026/2027">2026/2027</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.tahun_ajaran" />
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center">
                                <input
                                    id="status"
                                    v-model="form.status"
                                    type="checkbox"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="status" class="ml-2 block text-sm text-gray-900">
                                    Jadwal Aktif
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Keterangan
                        </h3>
                        
                        <div>
                            <InputLabel for="keterangan" value="Keterangan (Opsional)" />
                            <textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                                placeholder="Keterangan tambahan untuk jadwal ini..."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.keterangan" />
                        </div>
                    </div>

                    <!-- Konflik Jadwal -->
                    <div v-if="hasConflict" class="space-y-4">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Peringatan Konflik Jadwal</h3>
                                    <p class="text-sm text-red-700 mt-1">
                                        Terdapat konflik jadwal dengan pengaturan yang dipilih. Silakan periksa kembali.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6">
                        <Link :href="route('jadwal-pelajaran.index')" 
                              class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                            Batal
                        </Link>
                        <button type="submit" 
                                :disabled="form.processing || hasConflict"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors disabled:opacity-50">
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Informasi Bantuan -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Peringatan</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Perubahan jadwal akan mempengaruhi semua siswa di kelas terkait</li>
                                <li>Pastikan tidak ada bentrok jadwal dengan guru atau ruangan yang sama</li>
                                <li>Konfirmasi perubahan dengan guru pengampu sebelum menyimpan</li>
                                <li>Jadwal yang sudah dimulai sebaiknya tidak diubah secara drastis</li>
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
import { computed, ref, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
    jadwal: Object,
    mataPelajaranList: Array,
    kelasList: Array,
    guruList: Array
})

const form = useForm({
    mata_pelajaran_id: props.jadwal.mata_pelajaran_id,
    kelas_id: props.jadwal.kelas_id,
    guru_id: props.jadwal.guru_id,
    hari: props.jadwal.hari,
    jam_mulai: props.jadwal.jam_mulai,
    jam_selesai: props.jadwal.jam_selesai,
    ruangan: props.jadwal.ruangan || '',
    semester: props.jadwal.semester || 'ganjil',
    tahun_ajaran: props.jadwal.tahun_ajaran || '2024/2025',
    status: props.jadwal.status ?? true,
    keterangan: props.jadwal.keterangan || ''
})

const hasConflict = ref(false)

const jamSelesaiOptions = {
    '07:00': [
        { value: '07:45', label: '07:45 (1 jam pelajaran)' },
        { value: '08:30', label: '08:30 (2 jam pelajaran)' },
        { value: '09:15', label: '09:15 (3 jam pelajaran)' }
    ],
    '07:45': [
        { value: '08:30', label: '08:30 (1 jam pelajaran)' },
        { value: '09:15', label: '09:15 (2 jam pelajaran)' },
        { value: '10:00', label: '10:00 (3 jam pelajaran)' }
    ],
    '08:30': [
        { value: '09:15', label: '09:15 (1 jam pelajaran)' },
        { value: '10:15', label: '10:15 (2 jam pelajaran)' },
        { value: '11:00', label: '11:00 (3 jam pelajaran)' }
    ],
    '09:15': [
        { value: '10:15', label: '10:15 (1 jam pelajaran)' },
        { value: '11:00', label: '11:00 (2 jam pelajaran)' },
        { value: '11:45', label: '11:45 (3 jam pelajaran)' }
    ],
    '10:15': [
        { value: '11:00', label: '11:00 (1 jam pelajaran)' },
        { value: '11:45', label: '11:45 (2 jam pelajaran)' },
        { value: '12:30', label: '12:30 (3 jam pelajaran)' }
    ],
    '11:00': [
        { value: '11:45', label: '11:45 (1 jam pelajaran)' },
        { value: '12:30', label: '12:30 (2 jam pelajaran)' },
        { value: '13:00', label: '13:00 (3 jam pelajaran)' }
    ],
    '11:45': [
        { value: '13:00', label: '13:00 (1 jam pelajaran)' },
        { value: '13:45', label: '13:45 (2 jam pelajaran)' },
        { value: '14:30', label: '14:30 (3 jam pelajaran)' }
    ],
    '13:00': [
        { value: '13:45', label: '13:45 (1 jam pelajaran)' },
        { value: '14:30', label: '14:30 (2 jam pelajaran)' },
        { value: '15:15', label: '15:15 (3 jam pelajaran)' }
    ],
    '13:45': [
        { value: '14:30', label: '14:30 (1 jam pelajaran)' },
        { value: '15:15', label: '15:15 (2 jam pelajaran)' },
        { value: '16:00', label: '16:00 (3 jam pelajaran)' }
    ],
    '14:30': [
        { value: '15:15', label: '15:15 (1 jam pelajaran)' },
        { value: '16:00', label: '16:00 (2 jam pelajaran)' },
        { value: '16:45', label: '16:45 (3 jam pelajaran)' }
    ]
}

const availableJamSelesai = computed(() => {
    return jamSelesaiOptions[form.jam_mulai] || []
})

const updateJamSelesai = () => {
    // Reset jam selesai ketika jam mulai berubah
    form.jam_selesai = ''
    
    // Auto-set jam selesai ke 1 jam pelajaran jika tersedia
    const options = availableJamSelesai.value
    if (options.length > 0) {
        form.jam_selesai = options[0].value
    }
}

// Watch untuk deteksi konflik
watch([() => form.guru_id, () => form.hari, () => form.jam_mulai, () => form.jam_selesai, () => form.ruangan], () => {
    // Simulasi pengecekan konflik (dalam implementasi nyata, ini harus memanggil API)
    hasConflict.value = false
}, { deep: true })

const submit = () => {
    form.put(route('jadwal-pelajaran.update', props.jadwal.id))
}
</script>
