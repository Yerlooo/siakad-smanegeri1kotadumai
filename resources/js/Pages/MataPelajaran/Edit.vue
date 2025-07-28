<template>
    <AppLayout page-title="Edit Mata Pelajaran">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Edit Mata Pelajaran</h3>
                        <div class="flex space-x-2">
                            <Link :href="route('mata-pelajaran.show', mataPelajaran.id)" 
                                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </Link>
                            <Link :href="route('mata-pelajaran.index')" 
                                  class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali
                            </Link>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Mata Pelajaran -->
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Mata Pelajaran <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="nama"
                                    v-model="form.nama"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.nama }"
                                    placeholder="Contoh: Matematika"
                                    required
                                />
                                <div v-if="form.errors.nama" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.nama }}
                                </div>
                            </div>

                            <!-- Kode Mata Pelajaran -->
                            <div>
                                <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Mata Pelajaran <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="kode"
                                    v-model="form.kode"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.kode }"
                                    placeholder="Contoh: MTK"
                                    maxlength="10"
                                    required
                                />
                                <div v-if="form.errors.kode" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.kode }}
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Maksimal 10 karakter</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kategori -->
                            <div>
                                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="kategori"
                                    v-model="form.kategori"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.kategori }"
                                    required
                                >
                                    <option value="">Pilih Kategori</option>
                                    <option value="wajib">Mata Pelajaran Wajib</option>
                                    <option value="peminatan">Mata Pelajaran Peminatan</option>
                                    <option value="lintas_minat">Lintas Minat</option>
                                    <option value="pendalaman_minat">Pendalaman Minat</option>
                                    <option value="muatan_lokal">Muatan Lokal</option>
                                </select>
                                <div v-if="form.errors.kategori" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.kategori }}
                                </div>
                            </div>

                            <!-- Jenis -->
                            <div>
                                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="jenis"
                                    v-model="form.jenis"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.jenis }"
                                    required
                                >
                                    <option value="">Pilih Jenis</option>
                                    <option value="teori">Teori</option>
                                    <option value="praktik">Praktik</option>
                                    <option value="teori_praktik">Teori dan Praktik</option>
                                </select>
                                <div v-if="form.errors.jenis" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.jenis }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Jam Tatap Muka -->
                            <div>
                                <label for="jam_tatap_muka" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Tatap Muka/Minggu <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="jam_tatap_muka"
                                    v-model="form.jam_tatap_muka"
                                    type="number"
                                    min="1"
                                    max="20"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.jam_tatap_muka }"
                                    placeholder="2"
                                    required
                                />
                                <div v-if="form.errors.jam_tatap_muka" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.jam_tatap_muka }}
                                </div>
                            </div>

                            <!-- SKS -->
                            <div>
                                <label for="sks" class="block text-sm font-medium text-gray-700 mb-2">
                                    SKS
                                </label>
                                <input
                                    id="sks"
                                    v-model="form.sks"
                                    type="number"
                                    min="1"
                                    max="10"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.sks }"
                                    placeholder="2"
                                />
                                <div v-if="form.errors.sks" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.sks }}
                                </div>
                            </div>

                            <!-- KKM -->
                            <div>
                                <label for="kkm" class="block text-sm font-medium text-gray-700 mb-2">
                                    KKM (Kriteria Ketuntasan Minimal)
                                </label>
                                <input
                                    id="kkm"
                                    v-model="form.kkm"
                                    type="number"
                                    min="50"
                                    max="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.kkm }"
                                    placeholder="75"
                                />
                                <div v-if="form.errors.kkm" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.kkm }}
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi
                            </label>
                            <textarea
                                id="deskripsi"
                                v-model="form.deskripsi"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.deskripsi }"
                                placeholder="Deskripsi mata pelajaran..."
                            ></textarea>
                            <div v-if="form.errors.deskripsi" class="mt-2 text-sm text-red-600">
                                {{ form.errors.deskripsi }}
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

                        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                            <Link :href="route('mata-pelajaran.index')" 
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
                                {{ form.processing ? 'Menyimpan...' : 'Update' }}
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
    mataPelajaran: Object
})

const form = useForm({
    nama: props.mataPelajaran.nama,
    kode: props.mataPelajaran.kode,
    kategori: props.mataPelajaran.kategori,
    jenis: props.mataPelajaran.jenis,
    jam_tatap_muka: props.mataPelajaran.jam_tatap_muka,
    sks: props.mataPelajaran.sks,
    kkm: props.mataPelajaran.kkm,
    deskripsi: props.mataPelajaran.deskripsi,
    status: props.mataPelajaran.status
})

const submit = () => {
    form.put(route('mata-pelajaran.update', props.mataPelajaran.id))
}
</script>
