<template>
    <div class="fixed top-4 right-4 z-50 space-y-2">
        <FlashMessage
            v-for="message in messages"
            :key="message.id"
            :type="message.type"
            :title="message.title"
            :message="message.message"
            :persistent="message.persistent"
            :duration="message.duration"
            @close="removeMessage(message.id)"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import FlashMessage from './FlashMessage.vue'

const page = usePage()
const messages = ref([])
let nextId = 1

const addMessage = (type, title, message = '', options = {}) => {
    const newMessage = {
        id: nextId++,
        type,
        title,
        message,
        persistent: options.persistent || false,
        duration: options.duration || 5000
    }
    messages.value.push(newMessage)
}

const removeMessage = (id) => {
    const index = messages.value.findIndex(msg => msg.id === id)
    if (index > -1) {
        messages.value.splice(index, 1)
    }
}

const checkFlashMessages = () => {
    // Clear existing messages first
    messages.value = []
    
    // Success messages
    if (page.props.flash?.success) {
        addMessage('success', 'Berhasil!', page.props.flash.success)
    }
    
    // Error messages
    if (page.props.flash?.error) {
        addMessage('error', 'Error!', page.props.flash.error)
    }
    
    // Warning messages
    if (page.props.flash?.warning) {
        addMessage('warning', 'Peringatan!', page.props.flash.warning)
    }
    
    // Info messages
    if (page.props.flash?.info) {
        addMessage('info', 'Informasi', page.props.flash.info)
    }
}

// Check for flash messages on mount
onMounted(() => {
    checkFlashMessages()
})

// Watch for changes in flash messages
watch(() => page.props.flash, () => {
    checkFlashMessages()
}, { deep: true })

// Expose methods for programmatic use
defineExpose({
    addMessage,
    removeMessage
})
</script>
