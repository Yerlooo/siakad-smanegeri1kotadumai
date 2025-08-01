<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out flex flex-col" 
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-green-600 text-white flex-shrink-0">
                <img src="/storage/logo-smansa.png" alt="Logo SMAN 1" class="h-10 w-10 mr-3 rounded-full bg-white object-contain border border-white shadow" />
                <h1 class="text-lg font-bold">SIAKAD SMAN 1</h1>
            </div>
            
            <!-- Navigation - Scrollable -->
            <nav class="flex-1 overflow-y-auto mt-4 pb-4 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                <div class="px-4 space-y-2">
                    <!-- Dashboard -->
                    <SidebarLink 
                        :href="route('dashboard')" 
                        :active="route().current('dashboard')"
                        icon="🏠"
                        active-class="bg-green-600 text-white">
                        Dashboard
                    </SidebarLink>
                    
                    <!-- Data Guru -->
                    <SidebarLink 
                        v-if="canAccess(['kepala_tatausaha', 'tata_usaha', 'guru'])"
                        :href="route('guru.index')" 
                        :active="route().current('guru.*')"
                        icon="👨‍🏫">
                        Data Guru
                    </SidebarLink>
                    
                    <!-- Wali Kelas (Kepala Tata Usaha & Tata Usaha) -->
                    <SidebarLink 
                        v-if="canAccess(['kepala_tatausaha', 'tata_usaha'])"
                        :href="route('wali-kelas.index')" 
                        :active="route().current('wali-kelas.*')"
                        icon="👩‍🏫">
                        Wali Kelas
                    </SidebarLink>
                    
                    <!-- Data Siswa -->
                    <SidebarLink 
                        v-if="canAccess(['kepala_tatausaha', 'tata_usaha', 'guru'])"
                        :href="route('siswa.index')" 
                        :active="route().current('siswa.*')"
                        icon="👨‍🎓">
                        Data Siswa
                    </SidebarLink>
                    
                    <!-- Kelas -->
                    <SidebarLink 
                        :href="route('kelas.index')" 
                        :active="route().current('kelas.*')"
                        icon="🏫">
                        Kelas
                    </SidebarLink>
                    
                    <!-- Mata Pelajaran -->
                    <SidebarLink 
                        :href="route('mata-pelajaran.index')" 
                        :active="route().current('mata-pelajaran.*')"
                        icon="📚">
                        Mata Pelajaran
                    </SidebarLink>
                    
                    <!-- Jadwal Pelajaran -->
                    <SidebarLink 
                        :href="route('jadwal-pelajaran.index')" 
                        :active="route().current('jadwal-pelajaran.*')"
                        icon="📅">
                        Jadwal Pelajaran
                    </SidebarLink>
                    
                    <!-- Input Nilai (Guru & Kepala Tata Usaha) -->
                    <SidebarLink 
                        v-if="canAccess(['guru', 'kepala_tatausaha'])"
                        :href="route('nilai-siswa.index')" 
                        :active="route().current('nilai-siswa.*')"
                        icon="📝">
                        Input Nilai
                    </SidebarLink>
                    
                    <!-- Absensi (Guru & Kepala Tata Usaha) -->
                    <SidebarLink 
                        v-if="canAccess(['guru', 'kepala_tatausaha'])"
                        :href="route('absensi.index')" 
                        :active="route().current('absensi.*')"
                        icon="📋">
                        Absensi Siswa
                    </SidebarLink>
                    
                    <!-- KKM Management (Guru, Kepala Tata Usaha & Tata Usaha) -->
                    <SidebarLink 
                        v-if="canAccess(['guru', 'kepala_tatausaha', 'tata_usaha'])"
                        :href="route('kkm.index')" 
                        :active="route().current('kkm.*')"
                        icon="🎯">
                        Manajemen KKM
                    </SidebarLink>
                    
                    <!-- Nilai Saya (Murid) -->
                    <SidebarLink 
                        v-if="canAccess(['murid'])"
                        :href="route('nilai-saya.index')" 
                        :active="route().current('nilai-saya.*')"
                        icon="📊">
                        Nilai Saya
                    </SidebarLink>
                    
                    <!-- Approval Requests (Kepala Tata Usaha & Tata Usaha) -->
                    <SidebarLink 
                        v-if="canAccess(['kepala_tatausaha', 'tata_usaha'])"
                        :href="route('approval-requests.index')" 
                        :active="route().current('approval-requests.*')"
                        icon="✅">
                        Persetujuan
                    </SidebarLink>
                    
                    <!-- My Approval Requests (All users) -->
                    <SidebarLink 
                        :href="route('my-approval-requests.index')" 
                        :active="route().current('my-approval-requests.*')"
                        icon="📄">
                        Status Permintaan
                    </SidebarLink>
                    
                    <!-- Settings (Kepala Tata Usaha only) -->
                    <SidebarLink 
                        v-if="canAccess(['kepala_tatausaha'])"
                        :href="route('settings.index')" 
                        :active="route().current('settings.*')"
                        icon="⚙️">
                        Pengaturan
                    </SidebarLink>
                    
                    <!-- Spacer untuk mendorong user info ke bawah -->
                    <div class="py-4"></div>
                </div>
                
                <!-- User Info & Logout (Sticky di bawah) -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 mt-auto">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 text-sm font-semibold">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $page.props.auth.user.name }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $page.props.auth.user.role?.display_name || 'User' }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Logout Button -->
                    <Link :href="route('logout')" 
                          method="post"
                          class="w-full flex items-center justify-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </Link>
                </div>
            </nav>
        </div>
        
        <!-- Overlay for mobile -->
        <div v-if="sidebarOpen" 
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"></div>
        
        <!-- Main Content -->
        <div class="lg:ml-64">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-4 py-3 sm:py-4">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="lg:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <h1 class="ml-2 lg:ml-3 text-lg sm:text-xl font-semibold text-gray-800 truncate">{{ pageTitle }}</h1>
                    </div>
                    
                    <!-- User Profile Dropdown -->
                    <div class="relative flex items-center space-x-3">
                        <!-- Notifications Dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="96">
                                <template #trigger>
                                    <button @click="onNotificationButtonClick" class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h16z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0" />
                                        </svg>
                                        <!-- Notification Badge -->
                                        <span v-if="unreadNotificationsCount > 0" 
                                              class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[1.25rem] h-5">
                                            {{ unreadNotificationsCount > 99 ? '99+' : unreadNotificationsCount }}
                                        </span>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="w-96">
                                        <!-- Header -->
                                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-sm font-medium text-gray-900">Notifikasi</h3>
                                                <div class="flex space-x-2">
                                                    <Link :href="route('notifications.index')" 
                                                          class="text-xs text-blue-600 hover:text-blue-800">
                                                        Lihat semua
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Notifications List -->
                                        <div class="max-h-96 overflow-y-auto">
                                            <div v-if="recentNotifications.length === 0" class="px-4 py-6 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h16z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0" />
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-500">Tidak ada notifikasi</p>
                                            </div>
                                            
                                            <div v-for="notification in recentNotifications" 
                                                 :key="notification.id" 
                                                 @click="handleNotificationClick(notification)"
                                                 class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                                                <div class="flex items-start space-x-3">
                                                    <div :class="getNotificationIconColor(notification.type)" 
                                                         class="flex-shrink-0 w-2 h-2 rounded-full mt-2"></div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">
                                                            {{ getNotificationTitle(notification) }}
                                                        </p>
                                                        <p class="text-sm text-gray-600 line-clamp-2">
                                                            {{ getNotificationMessage(notification) }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            {{ getRelativeTime(notification.created_at) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                        
                        <!-- User Profile Dropdown -->
                        <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center px-2 sm:px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150">
                                    <div class="flex items-center">
                                        <!-- Untuk role murid, gunakan foto siswa; untuk role lain gunakan foto user -->
                                        <img v-if="getUserPhoto()" 
                                             :src="getUserPhoto()" 
                                             class="w-6 h-6 sm:w-8 sm:h-8 rounded-full mr-1 sm:mr-2 object-cover border border-gray-200" 
                                             :alt="$page.props.auth.user.name">
                                        <div v-else 
                                             class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-gray-300 mr-1 sm:mr-2 flex items-center justify-center">
                                            <span class="text-xs sm:text-sm font-medium text-gray-600">
                                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="text-left hidden sm:block">
                                            <div class="font-medium text-gray-800 text-sm">{{ $page.props.auth.user.name }}</div>
                                            <div class="text-xs text-gray-500">{{ $page.props.auth.user.role?.display_name }}</div>
                                        </div>
                                    </div>
                                    <svg class="ml-1 sm:ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="canAccess(['murid']) ? route('murid.profile.edit') : route('profile.edit')">
                                    Lihat Profile
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Logout
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
            </header>
            
            <!-- Page Content -->
            <main class="p-3 sm:p-6">
                <slot />
            </main>
        </div>
        
        <!-- Flash Messages -->
        <FlashMessages />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import SidebarLink from '@/Components/SidebarLink.vue'
import FlashMessages from '@/Components/FlashMessages.vue'

const props = defineProps({
    pageTitle: {
        type: String,
        default: 'Dashboard'
    }
})

const page = usePage()
const sidebarOpen = ref(false)
const notifications = ref([])

const canAccess = (roles) => {
    const userRole = page.props.auth.user.role?.name
    return roles.includes(userRole)
}

// Method untuk mendapatkan foto user
const getUserPhoto = () => {
    const userRole = page.props.auth.user.role?.name
    
    // Jika role murid, gunakan foto siswa
    if (userRole === 'murid' && page.props.auth.user.siswa?.foto) {
        return `/storage/${page.props.auth.user.siswa.foto}`
    }
    
    // Untuk role lain, gunakan foto user
    if (page.props.auth.user.foto) {
        return page.props.auth.user.foto
    }
    
    return null
}

// Notification computed properties
const unreadNotifications = computed(() => {
    return notifications.value.filter(n => !n.read_at)
})

const recentNotifications = computed(() => {
    // Tampilkan hanya notifikasi yang belum dibaca, maksimal 5
    return unreadNotifications.value.slice(0, 5)
})

const unreadNotificationsCount = computed(() => {
    return unreadNotifications.value.length
})

// Notification methods
const getNotificationIconColor = (type) => {
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

const getRelativeTime = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffMinutes = Math.ceil(diffTime / (1000 * 60))
    const diffHours = Math.ceil(diffTime / (1000 * 60 * 60))
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    
    if (diffMinutes < 60) {
        return `${diffMinutes} menit yang lalu`
    } else if (diffHours < 24) {
        return `${diffHours} jam yang lalu`
    } else if (diffDays === 1) {
        return 'Kemarin'
    } else if (diffDays <= 7) {
        return `${diffDays} hari yang lalu`
    } else {
        return date.toLocaleDateString('id-ID')
    }
}

const markAllNotificationsAsRead = async () => {
    try {
        // Menggunakan Inertia untuk request yang lebih aman
        router.patch(route('notifications.mark-all-read'), {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                fetchNotifications()
                // Tidak menampilkan pesan apapun ke user
            },
            onError: (errors) => {
                // Hanya log error di console, tidak tampilkan ke user
                console.error('Error marking all notifications as read:', errors)
            }
        })
    } catch (error) {
        // Hanya log error di console, tidak tampilkan ke user
        console.error('Error marking all notifications as read:', error)
    }
}

// Fungsi untuk menangani klik pada logo notifikasi
const onNotificationButtonClick = async () => {
    // Tidak melakukan apapun, hanya membuka dropdown notifikasi
    // Badge akan tetap tampil sampai notifikasi benar-benar ditandai sebagai dibaca di halaman notifikasi
    // ...existing code...
}

const handleNotificationClick = async (notification) => {
    // Navigate to action if available
    if (notification.data.action_url) {
        router.visit(notification.data.action_url)
    }
}

const fetchNotifications = async () => {
    try {
        // Pastikan user sudah terauthentikasi sebelum fetch
        if (!page.props.auth.user) {
            console.log('User not authenticated, skipping notifications fetch');
            return;
        }
        
        // Pastikan route helper tersedia
        if (typeof route !== 'function') {
            console.error('Route helper not available');
            return;
        }
        
        // Coba menggunakan axios jika tersedia, jika tidak gunakan fetch
        let response
        if (window.axios) {
            try {
                response = await window.axios.get(route('notifications.recent'), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                notifications.value = response.data.notifications || []
            } catch (axiosError) {
                console.error('Axios error fetching notifications:', axiosError);
                
                // Jika error 401/403, mungkin session expired
                if (axiosError.response?.status === 401 || axiosError.response?.status === 403) {
                    console.log('Authentication error, redirecting to login');
                    window.location.href = route('login');
                    return;
                }
                
                // Fallback ke fetch API
                throw axiosError;
            }
        } else {
            response = await fetch(route('notifications.recent'), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin' // Penting untuk session
            })
            
            if (response.status === 401 || response.status === 403) {
                console.log('Authentication error, redirecting to login');
                window.location.href = route('login');
                return;
            }
            
            if (response.ok) {
                const data = await response.json()
                notifications.value = data.notifications || []
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
    } catch (error) {
        console.error('Error fetching notifications:', error)
        
        // Jangan reset notifications jika ini adalah network error sementara
        if (error.name === 'TypeError' && error.message.includes('fetch')) {
            console.log('Network error, keeping existing notifications');
        } else {
            // Reset notifications on other errors
            notifications.value = []
        }
    }
}

// Load notifications on mount
onMounted(() => {
    // Delay initial fetch untuk menghindari konflik dengan login redirect
    setTimeout(() => {
        fetchNotifications()
        
        // Poll for new notifications every 30 seconds
        setInterval(fetchNotifications, 30000)
    }, 1000) // Delay 1 detik
})
</script>

<style scoped>
/* Custom scrollbar styles */
.scrollbar-thin {
    scrollbar-width: thin;
    scrollbar-color: #d1d5db #f3f4f6;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
    transition: background-color 0.2s ease;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Smooth scrolling */
nav {
    scroll-behavior: smooth;
}

/* Fade effect for top and bottom to indicate more content */
nav::before {
    content: '';
    position: sticky;
    top: 0;
    height: 8px;
    background: linear-gradient(to bottom, white, transparent);
    z-index: 1;
    display: block;
}

nav::after {
    content: '';
    position: sticky;
    bottom: 0;
    height: 8px;
    background: linear-gradient(to top, white, transparent);
    z-index: 1;
    display: block;
}

/* Mobile improvements */
@media (max-width: 640px) {
    .sidebar-user-info {
        padding: 12px 16px;
    }
    
    .sidebar-user-info .text-sm {
        font-size: 12px;
    }
    
    .sidebar-user-info .text-xs {
        font-size: 10px;
    }
}

/* Prevent horizontal scroll on small screens */
@media (max-width: 1024px) {
    .lg\:ml-64 {
        margin-left: 0;
    }
}
</style>