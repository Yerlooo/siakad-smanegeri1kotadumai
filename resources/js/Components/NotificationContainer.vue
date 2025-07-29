<template>
    <div class="fixed top-4 right-4 z-50 space-y-2">
        <Toast
            v-for="notification in notifications"
            :key="notification.id"
            :show="notification.show"
            :type="notification.type"
            :title="notification.title"
            :message="notification.message"
            @close="closeNotification(notification.id)"
        />
    </div>
</template>

<script setup>
import Toast from './Toast.vue'
import { useNotification } from '@/composables/useNotification'

const { notifications, closeNotification } = useNotification()
</script>
