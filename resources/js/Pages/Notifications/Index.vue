<template>
    <AppLayout title="Notifikasi">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-medium text-gray-900">
                                Notifikasi
                            </h1>
                            <p class="mt-2 text-gray-600">
                                Kelola notifikasi dan pesan penting sistem.
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button 
                                @click="markAllAsRead"
                                :disabled="unreadCount === 0"
                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Tandai Semua Dibaca
                            </button>
                            <button 
                                @click="deleteAllRead"
                                :disabled="totalCount === 0"
                                class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Hapus Semua Notifikasi
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Statistik -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h16z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Notifikasi</p>
                                    <p class="text-2xl font-semibold text-blue-900">{{ totalCount }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">Belum Dibaca</p>
                                    <p class="text-2xl font-semibold text-yellow-900">{{ unreadCount }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Sudah Dibaca</p>
                                    <p class="text-2xl font-semibold text-green-900">{{ readCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                        <div class="flex space-x-2 mb-4 sm:mb-0">
                            <button 
                                @click="selectedFilter = 'all'"
                                :class="{'bg-blue-600 text-white': selectedFilter === 'all', 'bg-gray-200 text-gray-700': selectedFilter !== 'all'}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Semua
                            </button>
                            <button 
                                @click="selectedFilter = 'unread'"
                                :class="{'bg-yellow-600 text-white': selectedFilter === 'unread', 'bg-gray-200 text-gray-700': selectedFilter !== 'unread'}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Belum Dibaca
                            </button>
                            <button 
                                @click="selectedFilter = 'read'"
                                :class="{'bg-green-600 text-white': selectedFilter === 'read', 'bg-gray-200 text-gray-700': selectedFilter !== 'read'}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Sudah Dibaca
                            </button>
                        </div>
                    </div>

                    <!-- Daftar Notifikasi -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        <ul class="divide-y divide-gray-200">
                            <li v-for="notification in filteredNotifications" :key="notification.id" 
                                :class="{'bg-blue-50': !notification.read_at, 'bg-white': notification.read_at}"
                                class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div 
                                                    :class="getNotificationColor(notification.type)"
                                                    class="flex-shrink-0 w-3 h-3 rounded-full mr-3">
                                                </div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ getNotificationTitle(notification) }}
                                                </p>
                                                <div v-if="!notification.read_at" class="ml-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Baru
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-500">
                                                {{ formatDate(notification.created_at) }}
                                            </p>
                                        </div>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-600">
                                                {{ getNotificationMessage(notification) }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="ml-4 flex space-x-2">
                                        <button 
                                            v-if="!notification.read_at"
                                            @click="markAsRead(notification.id)"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Tandai Dibaca
                                        </button>
                                        <button 
                                            v-if="hasActionLink(notification)"
                                            @click="navigateToAction(notification)"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                            Lihat
                                        </button>
                                        <button 
                                            @click="deleteNotification(notification.id)"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        
                        <div v-if="filteredNotifications.length === 0" class="text-center py-8">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405M9 21h6m-3-18a6 6 0 00-6 6v3.159c0 .538-.214 1.055-.595 1.436L4 17h16M15 9l6 6m0-6l-6 6" />
                                </svg>
                                <p class="text-gray-500">
                                    {{ selectedFilter === 'all' ? 'Tidak ada notifikasi.' : `Tidak ada notifikasi ${getFilterText()}.` }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    notifications: Array
})

// State
const selectedFilter = ref('all')

// Computed
const filteredNotifications = computed(() => {
    if (selectedFilter.value === 'all') {
        return props.notifications
    } else if (selectedFilter.value === 'unread') {
        return props.notifications.filter(notification => !notification.read_at)
    } else if (selectedFilter.value === 'read') {
        return props.notifications.filter(notification => notification.read_at)
    }
    return props.notifications
})

const totalCount = computed(() => {
    return props.notifications.length
})

const unreadCount = computed(() => {
    return props.notifications.filter(n => !n.read_at).length
})

const readCount = computed(() => {
    return props.notifications.filter(n => n.read_at).length
})

// Methods
const getNotificationColor = (type) => {
    const colorMap = {
        'App\\Notifications\\ApprovalRequestReceived': 'bg-yellow-400',
        'App\\Notifications\\ApprovalRequestProcessed': 'bg-green-400',
        default: 'bg-blue-400'
    }
    return colorMap[type] || colorMap.default
}

const getNotificationTitle = (notification) => {
    const data = notification.data
    const typeMap = {
        'App\\Notifications\\ApprovalRequestReceived': 'Permintaan Persetujuan Baru',
        'App\\Notifications\\ApprovalRequestProcessed': `Permintaan ${data.status === 'approved' ? 'Disetujui' : 'Ditolak'}`,
    }
    return typeMap[notification.type] || 'Notifikasi'
}

const getNotificationMessage = (notification) => {
    const data = notification.data
    if (notification.type === 'App\\Notifications\\ApprovalRequestReceived') {
        return `${data.requester_name} mengajukan permintaan perubahan nilai ${data.mata_pelajaran} untuk siswa ${data.siswa_name}.`
    } else if (notification.type === 'App\\Notifications\\ApprovalRequestProcessed') {
        return `Permintaan perubahan nilai ${data.mata_pelajaran} untuk siswa ${data.siswa_name} telah ${data.status === 'approved' ? 'disetujui' : 'ditolak'}.`
    }
    return data.message || 'Tidak ada pesan'
}

const hasActionLink = (notification) => {
    return notification.data.action_url !== undefined
}

const getFilterText = () => {
    const filterMap = {
        'unread': 'yang belum dibaca',
        'read': 'yang sudah dibaca'
    }
    return filterMap[selectedFilter.value] || ''
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    
    if (diffDays === 1) {
        return 'Hari ini'
    } else if (diffDays === 2) {
        return 'Kemarin'
    } else if (diffDays <= 7) {
        return `${diffDays - 1} hari yang lalu`
    } else {
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
    }
}

const markAsRead = (notificationId) => {
    router.patch(route('notifications.mark-read', notificationId), {}, {
        preserveScroll: true,
        preserveState: true
    })
}

const markAllAsRead = () => {
    router.patch(route('notifications.mark-all-read'), {}, {
        preserveScroll: true,
        preserveState: true
    })
}

const deleteNotification = (notificationId) => {
    if (confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
        router.delete(route('notifications.destroy', notificationId), {
            preserveScroll: true,
            preserveState: true
        })
    }
}

const deleteAllRead = () => {
    console.log('Mengirim request hapus semua notifikasi...');
    if (confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')) {
        router.delete(route('notifications.delete-read'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                router.reload()
            }
        })
    }
}

const navigateToAction = (notification) => {
    if (notification.data.action_url) {
        // Mark as read first, then navigate
        if (!notification.read_at) {
            markAsRead(notification.id)
        }
        router.visit(notification.data.action_url)
    }
}
</script>
