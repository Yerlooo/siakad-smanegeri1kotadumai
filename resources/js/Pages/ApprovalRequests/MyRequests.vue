<template>
    <AppLayout title="Status Permintaan Saya">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Status Permintaan Saya
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Lihat status permintaan persetujuan yang telah Anda ajukan.
                    </p>
                </div>

                <div class="p-6">
                    <!-- Statistik -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">Menunggu</p>
                                    <p class="text-2xl font-semibold text-yellow-900">{{ pendingCount }}</p>
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
                                    <p class="text-sm font-medium text-green-600">Disetujui</p>
                                    <p class="text-2xl font-semibold text-green-900">{{ approvedCount }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-red-600">Ditolak</p>
                                    <p class="text-2xl font-semibold text-red-900">{{ rejectedCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                        <div class="flex space-x-2 mb-4 sm:mb-0">
                            <button 
                                @click="selectedStatus = 'all'"
                                :class="{'bg-blue-600 text-white': selectedStatus === 'all', 'bg-gray-200 text-gray-700': selectedStatus !== 'all'}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Semua
                            </button>
                            <button 
                                @click="selectedStatus = 'pending'"
                                :class="{'bg-yellow-600 text-white': selectedStatus === 'pending', 'bg-gray-200 text-gray-700': selectedStatus !== 'pending'}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Menunggu
                            </button>
                            <button 
                                @click="selectedStatus = 'approved'"
                                :class="{'bg-green-600 text-white': selectedStatus === 'approved', 'bg-gray-200 text-gray-700': selectedStatus !== 'approved'}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Disetujui
                            </button>
                            <button 
                                @click="selectedStatus = 'rejected'"
                                :class="{'bg-red-600 text-white': selectedStatus === 'rejected', 'bg-gray-200 text-gray-700': selectedStatus !== 'rejected'}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Ditolak
                            </button>
                        </div>
                    </div>

                    <!-- Daftar Permintaan -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        <ul class="divide-y divide-gray-200">
                            <li v-for="request in filteredRequests" :key="request.id" class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-blue-600">
                                                {{ request.siswa.nama }} ({{ request.siswa.nis }})
                                            </p>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <span 
                                                    :class="{
                                                        'bg-yellow-100 text-yellow-800': request.status === 'pending',
                                                        'bg-green-100 text-green-800': request.status === 'approved',
                                                        'bg-red-100 text-red-800': request.status === 'rejected'
                                                    }"
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                    {{ getStatusText(request.status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-600">
                                                <strong>Mata Pelajaran:</strong> {{ request.mata_pelajaran.nama }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <strong>Jenis Nilai:</strong> {{ request.jenis_nilai.nama }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <strong>Nilai Lama:</strong> {{ request.old_value }} → <strong>Nilai Baru:</strong> {{ request.new_value }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <strong>Alasan:</strong> {{ request.reason }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Diajukan pada: {{ formatDate(request.created_at) }}
                                            </p>
                                            
                                            <!-- Status Details -->
                                            <div v-if="request.status !== 'pending'" class="mt-2 p-3 rounded-lg"
                                                 :class="{
                                                    'bg-green-50 border border-green-200': request.status === 'approved',
                                                    'bg-red-50 border border-red-200': request.status === 'rejected'
                                                 }">
                                                <p class="text-sm font-medium"
                                                   :class="{
                                                      'text-green-800': request.status === 'approved',
                                                      'text-red-800': request.status === 'rejected'
                                                   }">
                                                    {{ request.status === 'approved' ? 'Permintaan Disetujui' : 'Permintaan Ditolak' }}
                                                </p>
                                                <p class="text-xs" 
                                                   :class="{
                                                      'text-green-600': request.status === 'approved',
                                                      'text-red-600': request.status === 'rejected'
                                                   }">
                                                    Diproses oleh: {{ request.processed_by_user?.name || 'Tata Usaha' }}
                                                </p>
                                                <p class="text-xs"
                                                   :class="{
                                                      'text-green-600': request.status === 'approved',
                                                      'text-red-600': request.status === 'rejected'
                                                   }">
                                                    Pada: {{ formatDate(request.processed_at) }}
                                                </p>
                                                <div v-if="request.rejection_reason" class="mt-2">
                                                    <p class="text-xs text-red-600">
                                                        <strong>Alasan Penolakan:</strong> {{ request.rejection_reason }}
                                                    </p>
                                                </div>
                                                <div v-if="request.status === 'approved'" class="mt-2">
                                                    <p class="text-xs text-green-600">
                                                        <strong>Nilai telah berhasil diubah!</strong>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        
                        <div v-if="filteredRequests.length === 0" class="text-center py-8">
                            <p class="text-gray-500">
                                {{ requests.length === 0 ? 'Anda belum pernah mengajukan permintaan.' : `Tidak ada permintaan ${selectedStatus === 'all' ? '' : getStatusText(selectedStatus).toLowerCase()}.` }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    requests: Array
})

// State
const selectedStatus = ref('all')

// Computed
const filteredRequests = computed(() => {
    if (selectedStatus.value === 'all') {
        return props.requests
    }
    return props.requests.filter(request => request.status === selectedStatus.value)
})

const pendingCount = computed(() => {
    return props.requests.filter(r => r.status === 'pending').length
})

const approvedCount = computed(() => {
    return props.requests.filter(r => r.status === 'approved').length
})

const rejectedCount = computed(() => {
    return props.requests.filter(r => r.status === 'rejected').length
})

// Methods
const getStatusText = (status) => {
    const statusMap = {
        'pending': 'Menunggu',
        'approved': 'Disetujui', 
        'rejected': 'Ditolak'
    }
    return statusMap[status] || status
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>
