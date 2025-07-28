<template>
    <Head title="Data Guru" />

    <AppLayout page-title="Data Guru">
        <div class="space-y-6">
            <!-- Flash Messages -->
            <div v-if="$page.props.flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $page.props.flash.success }}</span>
            </div>
            
            <div v-if="$page.props.flash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $page.props.flash.error }}</span>
            </div>
        
            <!-- Header dengan tombol tambah -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Data Guru</h1>
                    <p class="text-gray-600">
                        {{ permissions && permissions.canCreate ? 'Kelola data guru dan staff sekolah' : 'Lihat data guru dan staff sekolah' }}
                    </p>
                </div>
                <Link v-if="permissions && permissions.canCreate"
                      :href="route('guru.create')" 
                      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Guru</span>
                </Link>
                <!-- Info role untuk guru -->
                <div v-if="permissions && !permissions.canCreate" class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Mode Baca Saja
                    </span>
                </div>
            </div>

            <!-- Search dan Filter -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-4 lg:space-y-0 lg:space-x-4">
                    <div class="flex-1">
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Cari nama guru, NIP, atau email..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    <select 
                        v-model="roleFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Semua Role</option>
                        <option value="kepala_tatausaha">Kepala Tata Usaha</option>
                        <option value="tata_usaha">Tata Usaha</option>
                        <option value="guru">Guru</option>
                    </select>
                </div>
            </div>

            <!-- Tabel Data Guru -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Guru
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NIP
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kontak
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in filteredGuru" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img v-if="item.foto" 
                                                 :src="item.foto" 
                                                 :alt="item.name"
                                                 class="h-10 w-10 rounded-full object-cover">
                                            <div v-else 
                                                 class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600">
                                                    {{ item.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                                            <div class="text-sm text-gray-500">{{ item.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ item.nip || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          :class="getRoleBadgeClass(item.role?.name)">
                                        {{ item.role?.display_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ item.no_telepon || '-' }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-32">{{ item.alamat || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Lihat Detail - Semua role bisa akses -->
                                        <Link :href="route('guru.show', item.id)"
                                              class="text-blue-600 hover:text-blue-900 p-1 rounded"
                                              title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </Link>
                                        
                                        <!-- Edit - Hanya untuk kepala tata usaha dan tata usaha -->
                                        <Link v-if="permissions && permissions.canEdit"
                                              :href="route('guru.edit', item.id)"
                                              class="text-yellow-600 hover:text-yellow-900 p-1 rounded"
                                              title="Edit Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </Link>
                                        
                                        <!-- Hapus - Hanya untuk kepala tata usaha dan tata usaha -->
                                        <button v-if="permissions && permissions.canDelete"
                                                @click="confirmDelete(item)"
                                                class="text-red-600 hover:text-red-900 p-1 rounded"
                                                title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- Info untuk guru yang tidak bisa edit/hapus -->
                                        <div v-if="permissions && !permissions.canEdit && !permissions.canDelete" 
                                             class="text-gray-400 text-xs italic px-2">
                                            Baca Saja
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="guru.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <Pagination :links="guru.links" />
                </div>

                <!-- Empty State -->
                <div v-if="guru.data.length === 0" class="text-center py-12">
                    <div class="text-6xl mb-4">👨‍🏫</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data guru</h3>
                    <p class="text-gray-500 mb-4">
                        {{ permissions && permissions.canCreate ? 'Mulai dengan menambahkan data guru pertama' : 'Belum ada data guru yang tersedia' }}
                    </p>
                    <Link v-if="permissions && permissions.canCreate"
                          :href="route('guru.create')" 
                          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Tambah Guru</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal 
            :show="showDeleteModal" 
            @close="showDeleteModal = false"
            @confirm="deleteGuru"
            title="Hapus Data Guru"
            :message="`Apakah Anda yakin ingin menghapus data guru ${guruToDelete?.name}? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Hapus"
            confirm-class="bg-red-600 hover:bg-red-700"
        />
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    guru: Object,
    userRole: String,
    permissions: Object
})

const search = ref('')
const roleFilter = ref('')
const showDeleteModal = ref(false)
const guruToDelete = ref(null)

const filteredGuru = computed(() => {
    return props.guru.data.filter(item => {
        const matchesSearch = search.value === '' || 
            item.name.toLowerCase().includes(search.value.toLowerCase()) ||
            item.email.toLowerCase().includes(search.value.toLowerCase()) ||
            item.nip?.toLowerCase().includes(search.value.toLowerCase())
        
        const matchesRole = roleFilter.value === '' || item.role?.name === roleFilter.value
        
        return matchesSearch && matchesRole
    })
})

const getRoleBadgeClass = (roleName) => {
    const classes = {
        'kepala_tatausaha': 'bg-purple-100 text-purple-800',
        'tata_usaha': 'bg-blue-100 text-blue-800',
        'guru': 'bg-green-100 text-green-800'
    }
    return classes[roleName] || 'bg-gray-100 text-gray-800'
}

const confirmDelete = (guru) => {
    guruToDelete.value = guru
    showDeleteModal.value = true
}

const deleteGuru = () => {
    router.delete(route('guru.destroy', guruToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
            guruToDelete.value = null
        }
    })
}
</script>
