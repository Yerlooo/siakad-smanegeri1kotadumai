<template>
    <AppLayout title="Laporkan Ketidakhadiran">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Laporkan Ketidakhadiran
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
                            Form Laporan Ketidakhadiran
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

                            <!-- Info Pengajuan -->
                            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">
                                            Informasi Pengajuan
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc pl-5 space-y-1">
                                                <li>Laporan akan tercatat dan dapat dilihat oleh kepala sekolah</li>
                                                <li>Status laporan dapat dilihat di halaman riwayat</li>
                                                <li>Laporan hanya dapat diubah selama status masih "Dilaporkan"</li>
                                                <li>Satu tanggal hanya dapat dilaporkan satu kali</li>
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
                                    <span v-else>Laporkan Ketidakhadiran</span>
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

// Get today's date in YYYY-MM-DD format for min attribute and default value
const today = new Date().toISOString().split('T')[0]

const form = useForm({
    tanggal: today,
    jenis_ketidakhadiran: '',
    alasan: '',
    keterangan: ''
})

const { errors, processing } = form

const submit = () => {
    form.post(route('absensi-guru.store'))
}
</script>
