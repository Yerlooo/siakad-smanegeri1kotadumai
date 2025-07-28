<template>
    <Head title="Edit Siswa" />

    <AppLayout page-title="Edit Siswa">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Siswa</h1>
                    <p class="text-gray-600">Ubah data siswa</p>
                </div>
                <Link :href="route('siswa.index')" 
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
                    <h2 class="text-lg font-semibold text-gray-900">Form Edit Siswa</h2>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Data Personal -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Data Personal
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="nama_lengkap" value="Nama Lengkap" />
                                <TextInput
                                    id="nama_lengkap"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.nama_lengkap"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.nama_lengkap" />
                            </div>

                            <div>
                                <InputLabel for="nis" value="NIS" />
                                <TextInput
                                    id="nis"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.nis"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.nis" />
                            </div>

                            <div>
                                <InputLabel for="nisn" value="NISN" />
                                <TextInput
                                    id="nisn"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.nisn"
                                />
                                <InputError class="mt-2" :message="form.errors.nisn" />
                            </div>

                            <div>
                                <InputLabel for="jenis_kelamin" value="Jenis Kelamin" />
                                <select
                                    id="jenis_kelamin"
                                    v-model="form.jenis_kelamin"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.jenis_kelamin" />
                            </div>

                            <div>
                                <InputLabel for="tempat_lahir" value="Tempat Lahir" />
                                <TextInput
                                    id="tempat_lahir"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.tempat_lahir"
                                />
                                <InputError class="mt-2" :message="form.errors.tempat_lahir" />
                            </div>

                            <div>
                                <InputLabel for="tanggal_lahir" value="Tanggal Lahir" />
                                <TextInput
                                    id="tanggal_lahir"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.tanggal_lahir"
                                />
                                <InputError class="mt-2" :message="form.errors.tanggal_lahir" />
                            </div>

                            <div>
                                <InputLabel for="agama" value="Agama" />
                                <select
                                    id="agama"
                                    v-model="form.agama"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">Pilih Agama</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.agama" />
                            </div>

                            <div>
                                <InputLabel for="kelas_id" value="Kelas" />
                                <select
                                    id="kelas_id"
                                    v-model="form.kelas_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">Pilih Kelas</option>
                                    <option v-for="kelasItem in kelas" :key="kelasItem.id" :value="kelasItem.id">
                                        {{ kelasItem.nama_kelas }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.kelas_id" />
                            </div>

                            <div>
                                <InputLabel for="tahun_masuk" value="Tahun Masuk" />
                                <TextInput
                                    id="tahun_masuk"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.tahun_masuk"
                                    :min="2000"
                                    :max="new Date().getFullYear() + 1"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.tahun_masuk" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="alamat" value="Alamat" />
                            <textarea
                                id="alamat"
                                v-model="form.alamat"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.alamat" />
                        </div>
                    </div>

                    <!-- Data Kontak -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Data Kontak
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="no_telepon" value="No. Telepon" />
                                <TextInput
                                    id="no_telepon"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.no_telepon"
                                />
                                <InputError class="mt-2" :message="form.errors.no_telepon" />
                            </div>
                        </div>
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Data Orang Tua/Wali
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-700">Data Ayah</h4>
                                <div>
                                    <InputLabel for="nama_ayah" value="Nama Ayah" />
                                    <TextInput
                                        id="nama_ayah"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.nama_ayah"
                                    />
                                    <InputError class="mt-2" :message="form.errors.nama_ayah" />
                                </div>
                                <div>
                                    <InputLabel for="pekerjaan_ayah" value="Pekerjaan Ayah" />
                                    <TextInput
                                        id="pekerjaan_ayah"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.pekerjaan_ayah"
                                    />
                                    <InputError class="mt-2" :message="form.errors.pekerjaan_ayah" />
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-700">Data Ibu</h4>
                                <div>
                                    <InputLabel for="nama_ibu" value="Nama Ibu" />
                                    <TextInput
                                        id="nama_ibu"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.nama_ibu"
                                    />
                                    <InputError class="mt-2" :message="form.errors.nama_ibu" />
                                </div>
                                <div>
                                    <InputLabel for="pekerjaan_ibu" value="Pekerjaan Ibu" />
                                    <TextInput
                                        id="pekerjaan_ibu"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.pekerjaan_ibu"
                                    />
                                    <InputError class="mt-2" :message="form.errors.pekerjaan_ibu" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                            Status
                        </h3>
                        
                        <div>
                            <InputLabel for="status" value="Status Siswa" />
                            <select
                                id="status"
                                v-model="form.status"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="pindah">Pindah</option>
                                <option value="keluar">Keluar</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6">
                        <Link :href="route('siswa.index')" 
                              class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                            Batal
                        </Link>
                        <button type="submit" 
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors disabled:opacity-50">
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
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
    siswa: Object,
    kelas: Array
})

// Normalize jenis_kelamin data for backward compatibility
const normalizeJenisKelamin = (value) => {
    if (value === 'L') return 'Laki-laki'
    if (value === 'P') return 'Perempuan'
    return value
}

const form = useForm({
    nama_lengkap: props.siswa.nama_lengkap || '',
    nis: props.siswa.nis || '',
    nisn: props.siswa.nisn || '',
    jenis_kelamin: normalizeJenisKelamin(props.siswa.jenis_kelamin) || '',
    tempat_lahir: props.siswa.tempat_lahir || '',
    tanggal_lahir: props.siswa.tanggal_lahir || '',
    agama: props.siswa.agama || '',
    alamat: props.siswa.alamat || '',
    email: props.siswa.email || '',
    no_telepon: props.siswa.no_telepon || '',
    nama_ayah: props.siswa.nama_ayah || '',
    pekerjaan_ayah: props.siswa.pekerjaan_ayah || '',
    nama_ibu: props.siswa.nama_ibu || '',
    pekerjaan_ibu: props.siswa.pekerjaan_ibu || '',
    kelas_id: props.siswa.kelas_id || '',
    tahun_masuk: props.siswa.tahun_masuk || new Date().getFullYear(),
    status: props.siswa.status || ''
})

const submit = () => {
    form.put(route('siswa.update', props.siswa.id))
}
</script>
