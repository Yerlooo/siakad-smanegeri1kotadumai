<template>
    <AppLayout title="Edit Pengajuan Absensi">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Pengajuan Absensi
                </h2>
                <Link :href="route('absensi-guru.index')" 
                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">
                            Edit Pengajuan Ketidakhadiran
                        </h3>

                        <form @submit.prevent="submit">
                            <!-- Tanggal -->
                            <div class="mb-6">
                                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    id="tanggal"
                                    v-model="form.tanggal"
                                    type="date" 
                                    required
                                    :min="today"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.tanggal }"
                                />
                                <div v-if="errors.tanggal" class="mt-1 text-sm text-red-600">
                                    {{ errors.tanggal }}
                                </div>
                            </div>

                            <!-- Jenis Ketidakhadiran -->
                            <div class="mb-6">
                                <label for="jenis_ketidakhadiran" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Ketidakhadiran <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="jenis_ketidakhadiran"
                                    v-model="form.jenis_ketidakhadiran"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.jenis_ketidakhadiran }"
                                >
                                    <option value="">Pilih Jenis Ketidakhadiran</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="izin">Izin</option>
                                    <option value="dinas">Dinas Luar Kota</option>
                                    <option value="cuti">Cuti</option>
                                </select>
                                <div v-if="errors.jenis_ketidakhadiran" class="mt-1 text-sm text-red-600">
                                    {{ errors.jenis_ketidakhadiran }}
                                </div>
                            </div>

                            <!-- Alasan -->
                            <div class="mb-6">
                                <label for="alasan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alasan <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="alasan"
                                    v-model="form.alasan"
                                    rows="4"
                                    required
                                    placeholder="Jelaskan alasan ketidakhadiran Anda..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.alasan }"
                                ></textarea>
                                <div v-if="errors.alasan" class="mt-1 text-sm text-red-600">
                                    {{ errors.alasan }}
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    Maksimal 500 karakter
                                </div>
                            </div>

                            <!-- Keterangan Tambahan -->
                            <div class="mb-6">
                                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan Tambahan
                                </label>
                                <textarea 
                                    id="keterangan"
                                    v-model="form.keterangan"
                                    rows="3"
                                    placeholder="Keterangan tambahan (opsional)..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.keterangan }"
                                ></textarea>
                                <div v-if="errors.keterangan" class="mt-1 text-sm text-red-600">
                                    {{ errors.keterangan }}
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    Maksimal 1000 karakter
                                </div>
                            </div>

                            <!-- Info Edit -->
                            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">
                                            Informasi Edit
                                        </h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <ul class="list-disc pl-5 space-y-1">
                                                <li>Pengajuan hanya dapat diubah selama status masih "Menunggu Persetujuan"</li>
                                                <li>Setelah disetujui atau ditolak, pengajuan tidak dapat diubah</li>
                                                <li>Pastikan data yang dimasukkan sudah benar sebelum menyimpan</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="flex justify-end space-x-3">
                                <Link :href="route('absensi-guru.index')" 
                                      class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Batal
                                </Link>
                                <button 
                                    type="submit" 
                                    :disabled="processing"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                >
                                    <span v-if="processing">Menyimpan...</span>
                                    <span v-else>Simpan Perubahan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    absensiGuru: Object
})

const form = useForm({
    tanggal: props.absensiGuru.tanggal,
    jenis_ketidakhadiran: props.absensiGuru.jenis_ketidakhadiran,
    alasan: props.absensiGuru.alasan,
    keterangan: props.absensiGuru.keterangan || ''
})

// Get today's date in YYYY-MM-DD format for min attribute
const today = new Date().toISOString().split('T')[0]

const { errors, processing } = form

const submit = () => {
    form.put(route('absensi-guru.update', props.absensiGuru.id))
}
</script>
