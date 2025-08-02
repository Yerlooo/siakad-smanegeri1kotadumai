<template>
    <teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Mobile-first responsive positioning -->
            <div class="flex items-center justify-center min-h-screen px-4 py-6 sm:p-0">
                <!-- Background overlay -->
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="show" @click="$emit('close')" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-all"></div>
                </transition>

                <!-- Modal panel -->
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4"
                >
                    <div v-if="show" class="relative bg-white rounded-xl shadow-2xl transform transition-all w-full max-w-md mx-4 sm:max-w-lg">
                        <!-- Close button -->
                        <button @click="$emit('close')" 
                                class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors z-10"
                                title="Tutup">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <!-- Modal content -->
                        <div class="p-6 sm:p-8">
                            <div class="flex flex-col items-center text-center sm:flex-row sm:items-start sm:text-left">
                                <!-- Warning icon -->
                                <div class="flex-shrink-0 mx-auto mb-4 sm:mx-0 sm:mb-0 sm:mr-4">
                                    <div class="flex items-center justify-center h-16 w-16 sm:h-12 sm:w-12 rounded-full bg-red-100">
                                        <svg class="h-8 w-8 sm:h-6 sm:w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 w-full">
                                    <h3 class="text-xl sm:text-lg font-semibold text-gray-900 mb-3">
                                        <slot name="title">{{ title }}</slot>
                                    </h3>
                                    <div class="text-base sm:text-sm text-gray-600 leading-relaxed">
                                        <slot name="content">{{ message }}</slot>
                                    </div>
                                </div>
                            </div>

                            <!-- Action buttons -->
                            <div class="mt-6 flex flex-col-reverse space-y-3 space-y-reverse sm:flex-row sm:space-y-0 sm:space-x-3 sm:justify-end">
                                <!-- Cancel button -->
                                <button 
                                    @click="$emit('close')"
                                    type="button" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:px-4 sm:py-2 border border-gray-300 rounded-lg text-base sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                >
                                    <svg class="w-5 h-5 mr-2 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    {{ cancelText }}
                                </button>
                                
                                <!-- Confirm button -->
                                <button 
                                    @click="$emit('confirm')"
                                    type="button" 
                                    :class="[
                                        'w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:px-4 sm:py-2 border border-transparent rounded-lg text-base sm:text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors',
                                        confirmClass
                                    ]"
                                >
                                    <svg class="w-5 h-5 mr-2 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    {{ confirmText }}
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </teleport>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false
    },
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
        default: 'Hapus'
    },
    cancelText: {
        type: String,
        default: 'Batal'
    },
    confirmClass: {
        type: String,
        default: 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
    }
})

defineEmits(['close', 'confirm'])
</script>
