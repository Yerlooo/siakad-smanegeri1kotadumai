<template>
    <AppLayout title="Detail Rekap Absensi">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Rekap Absensi
            </h2>
        </template>

        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header & Back Button -->
                        <div class="mb-6">
                            <Link :href="route('absensi.rekap')" 
                                  class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-4">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali ke Rekap Absensi
                            </Link>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                    Detail Absensi {{ selectedKelas.nama_kelas }}
                                </h1>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span v-if="selectedMapel" class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                        {{ selectedMapel.nama_mapel }}
                                    </span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                                        16 Pertemuan (Seminggu Sekali)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Semester Info -->
                        <div class="bg-blue-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-medium text-blue-900 mb-2">Informasi Semester</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-blue-800">Total Pertemuan:</span>
                                    <span class="ml-2 text-blue-700">16 Pertemuan</span>
                                </div>
                                <div>
                                    <span class="font-medium text-blue-800">Periode:</span>
                                    <span class="ml-2 text-blue-700">
                                        <template v-if="actualPeriod.start || actualPeriod.end">
                                            {{ actualPeriod.start ? formatDate(actualPeriod.start) : '-' }} s/d {{ actualPeriod.end ? formatDate(actualPeriod.end) : '-' }}
                                        </template>
                                        <template v-else>
                                            -
                                        </template>
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-blue-800">Frekuensi:</span>
                                    <span class="ml-2 text-blue-700">Seminggu Sekali</span>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <!-- Total Siswa Card -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Total Siswa</p>
                                        <p class="text-2xl font-bold text-blue-600">{{ studentAttendanceData.length }}</p>
                                    </div>
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pertemuan Terlaksana Card -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Pertemuan Terlaksana</p>
                                        <p class="text-2xl font-bold text-orange-600">{{ completedMeetings }}</p>
                                    </div>
                                    <div class="p-2 bg-orange-100 rounded-lg">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Rata-rata Kehadiran Card -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Rata-rata Kehadiran</p>
                                        <p class="text-2xl font-bold text-green-600">{{ averageAttendance }}%</p>
                                    </div>
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Total Pertemuan Card -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Total Pertemuan</p>
                                        <p class="text-2xl font-bold text-gray-900">16</p>
                                    </div>
                                    <div class="p-2 bg-gray-100 rounded-lg">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table - Student Attendance Matrix -->
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Rekap Kehadiran Siswa {{ selectedKelas.nama_kelas }}</h3>
                                        <span class="text-sm text-gray-500">{{ selectedMapel ? selectedMapel.nama_mapel : 'Semua Mata Pelajaran' }}</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <!-- Export Buttons -->
                                        <button @click="exportToPDF" 
                                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150"
                                                :disabled="isExporting">
                                            <svg v-if="!isExporting" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ isExporting ? 'Exporting...' : 'Export PDF' }}
                                        </button>
                                        
                                        <button @click="exportToExcel" 
                                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
                                                :disabled="isExporting">
                                            <svg v-if="!isExporting" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ isExporting ? 'Exporting...' : 'Export Excel' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 border-r">
                                                Siswa
                                            </th>
                                            <th v-for="meeting in allMeetingSlots" :key="meeting.pertemuan" 
                                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">
                                                <div>Pertemuan {{ meeting.pertemuan }}</div>
                                                <div class="text-[10px] font-normal text-gray-400 mt-1">
                                                    {{ meeting.tanggal ? formatDateShort(meeting.tanggal) : '-' }}
                                                </div>
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-blue-50">
                                                Total Kehadiran
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-green-50">
                                                Presentasi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="siswa in studentAttendanceData" :key="siswa.id" class="hover:bg-gray-50">
                                            <td class="px-4 py-4 text-sm sticky left-0 bg-white z-10 border-r">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium mr-3">
                                                        {{ getInitials(siswa.nama_lengkap) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ siswa.nama_lengkap }}</div>
                                                        <div class="text-xs text-gray-500">{{ siswa.nis }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td v-for="meeting in allMeetingSlots" :key="`${siswa.id}-${meeting.pertemuan}`"
                                                class="px-3 py-4 text-center whitespace-nowrap">
                                                <span v-if="siswa.attendance[meeting.pertemuan]" 
                                                      :class="getAttendanceClass(siswa.attendance[meeting.pertemuan])"
                                                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                                    {{ getAttendanceSymbol(siswa.attendance[meeting.pertemuan]) }}
                                                </span>
                                                <span v-else class="text-gray-300 text-xs">-</span>
                                            </td>
                                            <td class="px-4 py-4 text-center bg-blue-50">
                                                <span class="text-sm font-medium text-blue-800">
                                                    {{ siswa.totalHadir }}/{{ siswa.totalPertemuan }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-center bg-green-50">
                                                <div class="flex items-center justify-center">
                                                    <div class="w-12 bg-gray-200 rounded-full h-2 mr-2">
                                                        <div class="bg-green-600 h-2 rounded-full" :style="`width: ${siswa.percentage}%`"></div>
                                                    </div>
                                                    <span class="text-sm font-medium" :class="getPercentageClass(siswa.percentage)">
                                                        {{ siswa.percentage }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary Row -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                    <div class="text-center">
                                        <span class="font-medium text-gray-600">Total Siswa:</span>
                                        <span class="ml-2 text-gray-900 font-bold">{{ studentAttendanceData.length }}</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="font-medium text-gray-600">Pertemuan Terlaksana:</span>
                                        <span class="ml-2 text-blue-700 font-bold">{{ completedMeetingsCount }}</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="font-medium text-gray-600">Rata-rata Kehadiran Kelas:</span>
                                        <span class="ml-2 text-green-700 font-bold">{{ classAverageAttendance }}%</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="font-medium text-gray-600">Total Pertemuan:</span>
                                        <span class="ml-2 text-orange-700 font-bold">16</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty state -->
                            <div v-if="!studentAttendanceData.length" class="text-center py-12">
                                <div class="max-w-sm mx-auto">
                                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Siswa</h3>
                                    <p class="text-gray-500 mb-4">
                                        Belum ada data kehadiran siswa untuk kelas ini.
                                    </p>
                                    <div class="text-sm text-gray-400">
                                        <p>Kelas: {{ selectedKelas?.nama_kelas || '-' }}</p>
                                        <p v-if="selectedMapel">Mata Pelajaran: {{ selectedMapel.nama_mapel }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    absensiData: Object,
    selectedKelas: Object,
    selectedMapel: Object,
    filters: Object,
    statistics: Object,
    canViewAll: Boolean,
    mataPelajaranList: {
        type: Array,
        default: () => []
    }
})

// Debug log untuk melihat data yang diterima
console.log('RekapDetail Props:', {
    absensiData: props.absensiData,
    selectedKelas: props.selectedKelas,
    selectedMapel: props.selectedMapel,
    filters: props.filters
})

// Additional debug untuk structure data
if (props.absensiData && props.absensiData.data) {
    console.log('Absensi Raw Data Sample:', props.absensiData.data.slice(0, 5))
    console.log('Total Absensi Records:', props.absensiData.data.length)
    
    // Group by date to see structure
    const groupedByDate = {}
    props.absensiData.data.forEach(item => {
        if (!groupedByDate[item.tanggal]) {
            groupedByDate[item.tanggal] = []
        }
        groupedByDate[item.tanggal].push({
            id: item.id,
            siswa: item.siswa.nama_lengkap,
            status: item.status
        })
    })
    console.log('Grouped by Date:', groupedByDate)
}

// Setiap sesi input absensi adalah satu pertemuan, berdasarkan created_at atau session input
const meetingDates = computed(() => {
    if (!props.absensiData || !props.absensiData.data || props.absensiData.data.length === 0) {
        return []
    }
    
    console.log('Raw absensi data for meetings:', props.absensiData.data)
    
    // Untuk menentukan pertemuan berdasarkan session input guru
    // Kita kelompokkan berdasarkan kombinasi tanggal, created_at, dan batch input
    const sessionMap = new Map()
    
    // Sort berdasarkan created_at untuk mendapatkan urutan input yang benar
    const sorted = [...props.absensiData.data].sort((a, b) => {
        return new Date(a.created_at) - new Date(b.created_at)
    })
    
    sorted.forEach(absensi => {
        // Gunakan created_at yang dibulatkan ke menit untuk mengelompokkan session
        const sessionTime = new Date(absensi.created_at)
        sessionTime.setSeconds(0, 0) // Bulatkan ke menit
        const sessionKey = sessionTime.toISOString()
        
        if (!sessionMap.has(sessionKey)) {
            sessionMap.set(sessionKey, {
                tanggal: absensi.tanggal,
                created_at: absensi.created_at,
                sessionTime: sessionKey
            })
        }
    })
    
    // Convert ke array dan sort berdasarkan created_at
    const meetings = Array.from(sessionMap.values()).sort((a, b) => {
        return new Date(a.created_at) - new Date(b.created_at)
    })
    
    console.log('Meeting sessions found:', meetings)
    return meetings
})

// Generate student attendance matrix from real database data
const studentAttendanceData = computed(() => {
    if (!props.absensiData || !props.absensiData.data || !props.absensiData.data.length) return []
    
    console.log('Processing absensi data:', props.absensiData.data.length, 'records')
    
    // Ambil daftar siswa unik dari data absensi
    const studentMap = new Map()
    
    // Dapatkan meeting sessions yang sudah ditentukan
    const meetingSessions = meetingDates.value
    console.log('Meeting sessions for student data:', meetingSessions)
    
    // Initialize students
    props.absensiData.data.forEach(absensi => {
        if (!studentMap.has(absensi.siswa.id)) {
            studentMap.set(absensi.siswa.id, {
                id: absensi.siswa.id,
                nis: absensi.siswa.nis,
                nama_lengkap: absensi.siswa.nama_lengkap,
                attendance: {},
                totalHadir: 0,
                totalPertemuan: 0,
                percentage: 0,
                recordsBySession: new Map()
            })
        }
        
        // Group records by session time untuk setiap siswa
        const student = studentMap.get(absensi.siswa.id)
        const sessionTime = new Date(absensi.created_at)
        sessionTime.setSeconds(0, 0) // Bulatkan ke menit
        const sessionKey = sessionTime.toISOString()
        
        if (!student.recordsBySession.has(sessionKey)) {
            student.recordsBySession.set(sessionKey, [])
        }
        student.recordsBySession.get(sessionKey).push({
            id: absensi.id,
            tanggal: absensi.tanggal,
            status: absensi.status,
            created_at: absensi.created_at,
            sessionKey: sessionKey
        })
    })
    
    // Process each student's attendance berdasarkan meeting sessions
    studentMap.forEach(student => {
        console.log(`Processing student ${student.nama_lengkap}:`)
        
        // Assign attendance berdasarkan urutan meeting sessions
        meetingSessions.forEach((session, index) => {
            const pertemuanNumber = index + 1
            const recordsForSession = student.recordsBySession.get(session.sessionTime)
            
            if (recordsForSession && recordsForSession.length > 0) {
                // Ambil record terakhir jika ada multiple records di session yang sama
                const latestRecord = recordsForSession[recordsForSession.length - 1]
                student.attendance[pertemuanNumber] = latestRecord.status
                student.totalPertemuan++
                
                if (latestRecord.status === 'hadir') {
                    student.totalHadir++
                }
                
                console.log(`  Pertemuan ${pertemuanNumber} (${session.tanggal}): ${latestRecord.status}`)
            } else {
                console.log(`  Pertemuan ${pertemuanNumber} (${session.tanggal}): No record`)
            }
        })
        
        // Calculate percentage
        student.percentage = student.totalPertemuan > 0 
            ? Math.round((student.totalHadir / student.totalPertemuan) * 100) 
            : 0
        
        console.log(`Final data for ${student.nama_lengkap}:`, {
            totalHadir: student.totalHadir,
            totalPertemuan: student.totalPertemuan,
            percentage: student.percentage,
            attendance: student.attendance
        })
        
        // Clean up
        delete student.recordsBySession
    })
    
    return Array.from(studentMap.values()).sort((a, b) => a.nama_lengkap.localeCompare(b.nama_lengkap))
})

// Generate meeting dates with empty slots up to 16 meetings
const allMeetingSlots = computed(() => {
    const realMeetings = meetingDates.value
    const allSlots = []
    
    // Tambahkan pertemuan yang sudah ada dengan tanggal yang benar
    for (let i = 1; i <= 16; i++) {
        const existingMeeting = realMeetings[i - 1] // Index 0-based
        allSlots.push({
            pertemuan: i,
            tanggal: existingMeeting ? existingMeeting.tanggal : null
        })
    }
    
    return allSlots
})
const completedMeetingsCount = computed(() => {
    // Hitung berdasarkan jumlah meeting sessions yang benar-benar ada
    return meetingDates.value.length
})

// Computed untuk periode aktual berdasarkan data absensi
const actualPeriod = computed(() => {
    const meetings = meetingDates.value
    const allSlots = allMeetingSlots.value
    
    // Cari tanggal pertemuan 1 dan pertemuan 16
    const pertemuan1 = allSlots.find(slot => slot.pertemuan === 1)
    const pertemuan16 = allSlots.find(slot => slot.pertemuan === 16)
    
    // Ambil tanggal pertemuan 1 dan 16 (bisa null jika belum ada data)
    const startDate = pertemuan1?.tanggal || null
    const endDate = pertemuan16?.tanggal || null
    
    return {
        start: startDate,
        end: endDate
    }
})

const classAverageAttendance = computed(() => {
    if (studentAttendanceData.value.length === 0) return 0
    const totalPercentage = studentAttendanceData.value.reduce((sum, student) => sum + student.percentage, 0)
    return Math.round(totalPercentage / studentAttendanceData.value.length)
})

const averageAttendance = computed(() => {
    return classAverageAttendance.value
})

const completedMeetings = computed(() => {
    // Menghitung pertemuan yang benar-benar terlaksana (ada data absensinya)
    return completedMeetingsCount.value
})

// Form untuk filter (simplified, no mata pelajaran filter)
const filterForm = reactive({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
    kelas_id: props.filters.kelas_id,
    guru_id: props.filters.guru_id || ''
})

// State untuk export
const isExporting = ref(false)

// Fungsi untuk export PDF
const exportToPDF = async () => {
    if (isExporting.value) return
    
    // Check if there's any meeting data to export
    if (completedMeetingsCount.value === 0) {
        alert('Tidak ada data pertemuan untuk di-export.')
        return
    }
    
    isExporting.value = true
    
    try {
        const params = new URLSearchParams({
            kelas_id: props.selectedKelas.id,
            mata_pelajaran_id: props.selectedMapel?.id || '',
            start_date: actualPeriod.value.start || '',
            end_date: actualPeriod.value.end || '',
            format: 'pdf'
        })
        
        // Create a temporary link to download the file
        const response = await fetch(route('absensi.rekap.export') + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/pdf',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        
        if (response.ok) {
            const blob = await response.blob()
            const url = window.URL.createObjectURL(blob)
            const link = document.createElement('a')
            link.href = url
            
            // Generate filename
            const className = props.selectedKelas.nama_kelas.replace(/\s+/g, '_')
            const mapelName = props.selectedMapel ? props.selectedMapel.nama_mapel.replace(/\s+/g, '_') : 'Semua_Mapel'
            const currentDate = new Date().toISOString().split('T')[0]
            link.download = `Rekap_Absensi_${className}_${mapelName}_${currentDate}.pdf`
            
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
            window.URL.revokeObjectURL(url)
        } else {
            throw new Error('Export failed')
        }
    } catch (error) {
        console.error('Export PDF error:', error)
        alert('Gagal mengexport PDF. Silakan coba lagi.')
    } finally {
        isExporting.value = false
    }
}

// Fungsi untuk export Excel
const exportToExcel = async () => {
    if (isExporting.value) return
    
    // Check if there's any meeting data to export
    if (completedMeetingsCount.value === 0) {
        alert('Tidak ada data pertemuan untuk di-export.')
        return
    }
    
    isExporting.value = true
    
    try {
        const params = new URLSearchParams({
            kelas_id: props.selectedKelas.id,
            mata_pelajaran_id: props.selectedMapel?.id || '',
            start_date: actualPeriod.value.start || '',
            end_date: actualPeriod.value.end || '',
            format: 'excel'
        })
        
        // Create a temporary link to download the file
        const response = await fetch(route('absensi.rekap.export') + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        
        if (response.ok) {
            const blob = await response.blob()
            const url = window.URL.createObjectURL(blob)
            const link = document.createElement('a')
            link.href = url
            
            // Generate filename
            const className = props.selectedKelas.nama_kelas.replace(/\s+/g, '_')
            const mapelName = props.selectedMapel ? props.selectedMapel.nama_mapel.replace(/\s+/g, '_') : 'Semua_Mapel'
            const currentDate = new Date().toISOString().split('T')[0]
            link.download = `Rekap_Absensi_${className}_${mapelName}_${currentDate}.xlsx`
            
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
            window.URL.revokeObjectURL(url)
        } else {
            throw new Error('Export failed')
        }
    } catch (error) {
        console.error('Export Excel error:', error)
        alert('Gagal mengexport Excel. Silakan coba lagi.')
    } finally {
        isExporting.value = false
    }
}

// Fungsi untuk apply filter
const applyFilters = () => {
    router.get(route('absensi.rekap.detail'), filterForm, {
        preserveState: true,
        preserveScroll: true
    })
}

// Fungsi utility
const formatDate = (dateString) => {
    if (!dateString) return '-'
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

const formatDateShort = (dateString) => {
    if (!dateString) return '-'
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit'
    })
}

const getInitials = (name) => {
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
}

const getAttendanceSymbol = (status) => {
    const symbols = {
        'hadir': 'H',
        'sakit': 'S',
        'izin': 'I',
        'alpha': 'A'
    }
    return symbols[status] || '-'
}

const getAttendanceClass = (status) => {
    const classes = {
        'hadir': 'bg-green-100 text-green-800',
        'sakit': 'bg-yellow-100 text-yellow-800',
        'izin': 'bg-orange-100 text-orange-800',
        'alpha': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status) => {
    const statusMap = {
        'hadir': 'Hadir',
        'sakit': 'Sakit', 
        'izin': 'Izin',
        'alpha': 'Alpha'
    }
    return statusMap[status] || status
}

const getStatusBadgeClass = (status) => {
    const classMap = {
        'hadir': 'bg-green-100 text-green-800',
        'sakit': 'bg-yellow-100 text-yellow-800',
        'izin': 'bg-orange-100 text-orange-800', 
        'alpha': 'bg-red-100 text-red-800'
    }
    return classMap[status] || 'bg-gray-100 text-gray-800'
}

const getPercentageClass = (percentage) => {
    if (percentage >= 80) return 'text-green-600'
    if (percentage >= 70) return 'text-yellow-600'
    return 'text-red-600'
}
</script>
