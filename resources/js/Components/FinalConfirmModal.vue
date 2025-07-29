<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <!-- Warning Icon untuk Final -->
                    <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">
                        Konfirmasi Simpan Final
                    </h3>
                    <div class="mt-1">
                        <p class="text-sm text-gray-500">
                            Pastikan semua nilai sudah benar sebelum menyimpan sebagai final
                        </p>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-medium text-blue-900 mb-3">📊 Ringkasan Nilai</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-blue-700">Total Siswa:</span>
                        <span class="font-medium text-blue-900">{{ summary.total }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-blue-700">Sudah Dinilai:</span>
                        <span class="font-medium text-blue-900">{{ summary.sudahDinilai }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-green-700">Tuntas:</span>
                        <span class="font-medium text-green-900">{{ summary.tuntas }} siswa</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-red-700">Belum Tuntas:</span>
                        <span class="font-medium text-red-900">{{ summary.belumTuntas }} siswa</span>
                    </div>
                    <div class="flex justify-between col-span-2">
                        <span class="text-purple-700">Rata-rata:</span>
                        <span class="font-medium text-purple-900">{{ summary.rataRata.toFixed(1) }}</span>
                    </div>
                </div>
            </div>

            <!-- Warning -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            ⚠️ PERHATIAN PENTING
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Setelah disimpan sebagai <strong>FINAL</strong>, nilai tidak dapat diubah lagi</li>
                                <li>Perubahan nilai final hanya dapat dilakukan dengan persetujuan admin</li>
                                <li>Pastikan semua nilai sudah benar dan sesuai</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button 
                    type="button"
                    @click="$emit('close')"
                    class="bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Batal
                </button>
                <button 
                    @click="$emit('confirm')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors">
                    Ya, Simpan Final
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from './Modal.vue'

defineProps({
    show: Boolean,
    summary: {
        type: Object,
        required: true
    }
})

defineEmits(['close', 'confirm'])
</script>
