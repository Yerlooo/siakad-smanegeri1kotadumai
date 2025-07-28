<template>
    <Head title="Dashboard" />

    <AppLayout page-title="Dashboard">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-6 text-white mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">
                        Selamat Datang, {{ $page.props.auth.user.name }}!
                    </h1>
                    <p class="text-blue-100">
                        {{ $page.props.auth.user.role?.display_name }} - Sistem Informasi Akademik SMA Negeri 1 Kota Dumai
                    </p>
                </div>
                <div class="text-6xl opacity-80">
                    🎓
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <StatCard 
                title="Total Guru" 
                :value="stats.total_guru" 
                icon="👨‍🏫" 
                color="bg-blue-500" 
            />
            <StatCard 
                title="Total Siswa" 
                :value="stats.total_siswa" 
                icon="👨‍🎓" 
                color="bg-green-500" 
            />
            <StatCard 
                title="Total Kelas" 
                :value="stats.total_kelas" 
                icon="🏫" 
                color="bg-yellow-500" 
            />
            <StatCard 
                title="Mata Pelajaran" 
                :value="stats.total_mata_pelajaran" 
                icon="📚" 
                color="bg-purple-500" 
            />
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
            </div>
            <div class="p-6">
                <div v-if="recentActivities.length > 0" class="space-y-4">
                    <div v-for="activity in recentActivities" 
                         :key="activity.title" 
                         class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                📅
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ activity.title }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ activity.description }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-sm text-gray-400">
                            {{ activity.time }}
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8">
                    <div class="text-6xl mb-4">📋</div>
                    <p class="text-gray-500">Tidak ada aktivitas terbaru</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions (for mobile) -->
        <div class="mt-6 lg:hidden">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Menu Cepat</h3>
            <div class="grid grid-cols-2 gap-4">
                <QuickActionCard 
                    title="Data Siswa" 
                    icon="👨‍🎓" 
                    :href="route('siswa.index')" 
                />
                <QuickActionCard 
                    title="Jadwal" 
                    icon="📅" 
                    :href="route('jadwal-pelajaran.index')" 
                />
                <QuickActionCard 
                    title="Kelas" 
                    icon="🏫" 
                    :href="route('kelas.index')" 
                />
                <QuickActionCard 
                    title="Mata Pelajaran" 
                    icon="📚" 
                    :href="route('mata-pelajaran.index')" 
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import QuickActionCard from '@/Components/QuickActionCard.vue'

defineProps({
    stats: Object,
    recentActivities: Array,
    user: Object
})
</script>
