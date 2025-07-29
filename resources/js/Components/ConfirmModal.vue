<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <!-- Warning Icon -->
                    <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ title }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            {{ message }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button 
                    type="button"
                    @click="$emit('close')"
                    class="bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    {{ cancelText }}
                </button>
                <button 
                    @click="$emit('confirm')"
                    :class="[
                        'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                        type === 'danger' 
                            ? 'bg-red-600 hover:bg-red-700 text-white' 
                            : 'bg-blue-600 hover:bg-blue-700 text-white'
                    ]">
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from './Modal.vue'

defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Konfirmasi'
    },
    message: {
        type: String,
        default: 'Apakah Anda yakin?'
    },
    confirmText: {
        type: String,
        default: 'Ya'
    },
    cancelText: {
        type: String,
        default: 'Batal'
    },
    type: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'danger'].includes(value)
    }
})

defineEmits(['close', 'confirm'])
</script>
