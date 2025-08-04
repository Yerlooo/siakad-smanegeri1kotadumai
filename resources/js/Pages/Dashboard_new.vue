<template>
    <Head title="SIAKAD SMANSA" />

    <AppLayout page-title="Dashboard">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-[#43a047] via-[#aee571] to-[#fbc02d] rounded-lg shadow-lg p-6 text-white mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">
                        Selamat Datang, {{ $page.props.auth.user.name }}!
                    </h1>
                    <p class="text-yellow-100">
                        {{ $page.props.auth.user.role?.display_name }} - Sistem Informasi Akademik SMA Negeri 1 Kota Dumai
                    </p>
                </div>
                <div class="text-6xl opacity-80">
                    {{ getRoleIcon($page.props.auth.user.role?.name) }}
                </div>
            </div>
        </div>

        <!-- Statistics Cards Based on Role -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Admin/Kepala Sekolah Stats -->
            <template v-if="isAdmin()">
                <StatCard 
                    title="Total Guru" 
                    :value="stats.total_guru" 
                    icon="👨‍🏫" 
                    color="bg-blue-600" 
                />
                <StatCard 
                    title="Total Siswa" 
                    :value="stats.total_siswa" 
                    icon="👨‍🎓" 
                    color="bg-green-600" 
                />
                <StatCard 
                    title="Total Kelas" 
                    :value="stats.total_kelas" 
                    icon="🏫" 
                    color="bg-purple-600" 
                />
                <StatCard 
                    title="Mata Pelajaran" 
                    :value="stats.total_mata_pelajaran" 
                    icon="📚" 
                    color="bg-orange-600" 
                />
                <StatCard 
                    title="Siswa Laki-laki" 
                    :value="stats.siswa_laki" 
                    icon="👦" 
                    color="bg-cyan-600" 
                />
                <StatCard 
                    title="Siswa Perempuan" 
                    :value="stats.siswa_perempuan" 
                    icon="👧" 
                    color="bg-pink-600" 
                />
                <StatCard 
                    title="Kelas Aktif" 
                    :value="stats.kelas_aktif" 
                    icon="✅" 
                    color="bg-emerald-600" 
                />
                <StatCard 
                    title="Rata-rata Nilai" 
                    :value="stats.rata_rata_nilai" 
                    icon="📊" 
                    color="bg-indigo-600" 
                />
            </template>

            <!-- Guru Stats -->
            <template v-else-if="isGuru()">
                <StatCard 
                    title="Kelas Diampu" 
                    :value="stats.kelas_diampu" 
                    icon="🏫" 
                    color="bg-blue-600" 
                />
                <StatCard 
                    title="Total Siswa" 
                    :value="stats.total_siswa_diampu" 
                    icon="👨‍🎓" 
                    color="bg-green-600" 
                />
                <StatCard 
                    title="Jadwal Minggu Ini" 
                    :value="stats.jadwal_minggu_ini" 
                    icon="📅" 
                    color="bg-purple-600" 
                />
                <StatCard 
                    title="Tugas Belum Dinilai" 
                    :value="stats.tugas_belum_dinilai" 
                    icon="📝" 
                    color="bg-red-600" 
                />
                <StatCard 
                    title="Kehadiran Hari Ini" 
                    :value="stats.kehadiran_hari_ini" 
                    icon="✅" 
                    color="bg-emerald-600" 
                />
                <StatCard 
                    title="Rata-rata Kelas" 
                    :value="stats.rata_rata_kelas?.toFixed(2) || '0'" 
                    icon="📊" 
                    color="bg-indigo-600" 
                />
            </template>

            <!-- Siswa Stats -->
            <template v-else-if="isSiswa()">
                <StatCard 
                    title="Kelas" 
                    :value="stats.kelas" 
                    icon="🏫" 
                    color="bg-blue-600"
                />
                <StatCard 
                    title="Wali Kelas" 
                    :value="stats.wali_kelas" 
                    icon="👨‍🏫" 
                    color="bg-green-600"
                />
                <StatCard 
                    title="Mata Pelajaran" 
                    :value="stats.mata_pelajaran" 
                    icon="📚" 
                    color="bg-purple-600" 
                />
                <StatCard 
                    title="Nilai Tertinggi" 
                    :value="stats.nilai_tertinggi" 
                    icon="🏆" 
                    color="bg-yellow-600" 
                />
                <StatCard 
                    title="Nilai Terendah" 
                    :value="stats.nilai_terendah" 
                    icon="📉" 
                    color="bg-red-600" 
                />
                <StatCard 
                    title="Rata-rata Nilai" 
                    :value="stats.rata_rata_nilai" 
                    icon="📊" 
                    color="bg-indigo-600" 
                />
                <StatCard 
                    title="Total Absensi" 
                    :value="stats.total_absensi" 
                    icon="✅" 
                    color="bg-emerald-600" 
                />
                <StatCard 
                    title="Ranking Kelas" 
                    :value="stats.ranking_kelas || 'N/A'" 
                    icon="🏅" 
                    color="bg-orange-600" 
                />
            </template>

            <!-- Default Stats -->
            <template v-else>
                <StatCard 
                    title="Total Pengguna" 
                    :value="stats.total_pengguna" 
                    icon="👥" 
                    color="bg-gray-600" 
                />
                <StatCard 
                    title="Siswa Aktif" 
                    :value="stats.siswa_aktif" 
                    icon="👨‍🎓" 
                    color="bg-green-600" 
                />
                <StatCard 
                    title="Kelas Tersedia" 
                    :value="stats.kelas_tersedia" 
                    icon="🏫" 
                    color="bg-blue-600" 
                />
                <StatCard 
                    title="Mata Pelajaran" 
                    :value="stats.mata_pelajaran" 
                    icon="📚" 
                    color="bg-purple-600" 
                />
            </template>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ getActivityTitle() }}
                </h3>
            </div>
            <div class="p-6">
                <div v-if="recentActivities.length > 0" class="space-y-4">
                    <div v-for="activity in recentActivities" 
                         :key="activity.id || activity.title + activity.time" 
                         class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0">
                            <div :class="getActivityIconClass(activity.type)">
                                {{ getActivityIcon(activity.type) }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ activity.title || 'Aktivitas' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ activity.description || '-' }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-sm text-gray-400">
                            {{ formatActivityTime(activity.time) }}
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8">
                    <div class="text-6xl mb-4">📋</div>
                    <p class="text-gray-500">{{ getEmptyActivityMessage() }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions Based on Role -->
        <div v-if="roleSpecific?.quickActions" class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Menu Cepat</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <QuickActionCard 
                        v-for="action in roleSpecific.quickActions"
                        :key="action.route"
                        :title="action.title" 
                        :icon="action.icon" 
                        :href="route(action.route)" 
                    />
                </div>
            </div>
        </div>

        <!-- Charts Section (for Admin and Guru) -->
        <div v-if="shouldShowCharts()" class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 1 -->
            <div v-if="chartData?.siswaPerKelas || chartData?.siswaKelas" class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ isAdmin() ? 'Distribusi Siswa Per Kelas' : 'Siswa di Kelas Anda' }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div v-for="item in (chartData.siswaPerKelas || chartData.siswaKelas)" 
                             :key="item.name" 
                             class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ item.name }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         :style="{ width: getPercentage(item.value, getMaxValue(chartData.siswaPerKelas || chartData.siswaKelas)) + '%' }">
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600 min-w-8">{{ item.value }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart 2 for Admin -->
            <div v-if="isAdmin() && chartData?.nilaiRataRata" class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Rata-rata Nilai Per Mata Pelajaran</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div v-for="item in chartData.nilaiRataRata" 
                             :key="item.name" 
                             class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ item.name }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" 
                                         :style="{ width: (item.value / 100) * 100 + '%' }">
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600 min-w-8">{{ item.value }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart for Siswa -->
            <div v-if="isSiswa() && chartData?.nilaiSiswa" class="bg-white rounded-lg shadow-sm border border-gray-200 lg:col-span-2">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Nilai Anda Per Mata Pelajaran</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div v-for="item in chartData.nilaiSiswa" 
                             :key="item.name" 
                             class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ item.name }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-3">
                                    <div :class="getNilaiColor(item.value)" 
                                         class="h-3 rounded-full transition-all duration-300" 
                                         :style="{ width: (item.value / 100) * 100 + '%' }">
                                    </div>
                                </div>
                                <span class="text-sm font-semibold min-w-12" :class="getNilaiTextColor(item.value)">
                                    {{ item.value }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
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
    chartData: Object,
    roleSpecific: Object,
    user: Object
})

// Role helper functions
function isAdmin() {
    return ['kepala_tatausaha', 'tata_usaha'].includes($page.props.auth.user.role?.name)
}

function isGuru() {
    return $page.props.auth.user.role?.name === 'guru'
}

function isSiswa() {
    return ['siswa', 'murid'].includes($page.props.auth.user.role?.name)
}

function getRoleIcon(roleName) {
    const icons = {
        'kepala_tatausaha': '👔',
        'tata_usaha': '📋',
        'guru': '👨‍🏫',
        'siswa': '🎓',
        'murid': '🎓'
    }
    return icons[roleName] || '👤'
}

function getActivityTitle() {
    if (isAdmin()) return 'Aktivitas Terbaru Sistem'
    if (isGuru()) return 'Jadwal & Tugas Anda'
    if (isSiswa()) return 'Jadwal & Nilai Terbaru'
    return 'Aktivitas Terbaru'
}

function getEmptyActivityMessage() {
    if (isAdmin()) return 'Tidak ada aktivitas sistem terbaru'
    if (isGuru()) return 'Tidak ada jadwal atau tugas hari ini'
    if (isSiswa()) return 'Tidak ada jadwal atau nilai terbaru'
    return 'Tidak ada aktivitas terbaru'
}

function getActivityIcon(type) {
    const icons = {
        'schedule': '📅',
        'nilai': '📝',
        'user': '👤',
        'siswa': '🎓',
        'default': '📋'
    }
    return icons[type] || icons.default
}

function getActivityIconClass(type) {
    const classes = {
        'schedule': 'w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center',
        'nilai': 'w-8 h-8 bg-green-100 rounded-full flex items-center justify-center',
        'user': 'w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center',
        'siswa': 'w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center',
        'default': 'w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center'
    }
    return classes[type] || classes.default
}

function shouldShowCharts() {
    return isAdmin() || isGuru() || isSiswa()
}

function getMaxValue(data) {
    if (!data || data.length === 0) return 1
    return Math.max(...data.map(item => item.value))
}

function getPercentage(value, max) {
    if (max === 0) return 0
    return (value / max) * 100
}

function getNilaiColor(nilai) {
    if (nilai >= 85) return 'bg-green-600'
    if (nilai >= 75) return 'bg-blue-600'
    if (nilai >= 65) return 'bg-yellow-600'
    return 'bg-red-600'
}

function getNilaiTextColor(nilai) {
    if (nilai >= 85) return 'text-green-600'
    if (nilai >= 75) return 'text-blue-600'
    if (nilai >= 65) return 'text-yellow-600'
    return 'text-red-600'
}

function formatActivityTime(time) {
    if (!time) return ''
    // Coba parse ISO string
    const date = new Date(time)
    if (isNaN(date.getTime())) return time // fallback
    // Format: HH:mm atau dd MMM yyyy HH:mm
    const options = { hour: '2-digit', minute: '2-digit' }
    // Jika tanggal hari ini, tampilkan jam saja
    const now = new Date()
    if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString('id-ID', options)
    } else {
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) + ' ' + date.toLocaleTimeString('id-ID', options)
    }
}
</script>
