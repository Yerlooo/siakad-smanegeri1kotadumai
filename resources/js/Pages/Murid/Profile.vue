<template>
    <AppLayout title="Profile Saya">
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        👤 Profile Saya
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Kelola data pribadi dan biodata lengkap
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Profile Header Card -->
                <div class="bg-white shadow rounded-lg mb-8">
                    <div class="px-6 py-8 text-center">
                        <div class="relative inline-block">
                            <!-- Profile Photo -->
                            <div class="w-32 h-32 mx-auto mb-4 relative">
                                <img v-if="siswa.foto" 
                                     :src="`/storage/${siswa.foto}`" 
                                     :alt="siswa.nama_lengkap"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-100">
                                <div v-else 
                                     class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center border-4 border-blue-100">
                                    <span class="text-4xl text-gray-600">
                                        {{ siswa.nama_lengkap.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                
                                <!-- Photo Upload Button -->
                                <button @click="$refs.photoInput.click()"
                                        class="absolute bottom-0 right-0 bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 transition-colors shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>
                                
                                <!-- Delete Photo Button -->
                                <button v-if="siswa.foto" 
                                        @click="deletePhoto"
                                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 transition-colors shadow-lg">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Hidden File Input -->
                            <input ref="photoInput" 
                                   type="file" 
                                   accept="image/*" 
                                   @change="handlePhotoUpload" 
                                   class="hidden">
                        </div>
                        
                        <h1 class="text-2xl font-bold text-gray-900">{{ siswa.nama_lengkap }}</h1>
                        <p class="text-gray-600">{{ siswa.nis }} • {{ siswa.kelas?.nama_kelas || 'Belum ada kelas' }}</p>
                        <div class="flex justify-center items-center mt-2">
                            <span :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                siswa.status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                            ]">
                                {{ siswa.status.charAt(0).toUpperCase() + siswa.status.slice(1) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Biodata Lengkap</h3>
                        <p class="text-sm text-gray-600">Perbarui informasi pribadi dan data keluarga</p>
                    </div>

                    <form @submit.prevent="updateProfile" class="p-6 space-y-6">
                        <!-- Data Pribadi -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-4">📋 Data Pribadi</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nama Lengkap (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <input :value="siswa.nama_lengkap"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- NIS (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">NIS</label>
                                    <input :value="siswa.nis"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- NISN (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">NISN</label>
                                    <input :value="siswa.nisn || '-'"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- Jenis Kelamin (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                                    <input :value="siswa.jenis_kelamin"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- Tempat Lahir (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                                    <input :value="siswa.tempat_lahir"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- Tanggal Lahir (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                    <input :value="siswa.tanggal_lahir_formatted"
                                           type="date"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- Agama (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
                                    <input :value="siswa.agama || '-'"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- Email (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input :value="siswa.email || '-'"
                                           type="email"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- No Telepon -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                                    <input v-model="form.no_telepon"
                                           type="tel"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <div v-if="form.errors.no_telepon" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.no_telepon }}
                                    </div>
                                </div>
                            </div>

                            <!-- Alamat (readonly) -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                                <textarea :value="siswa.alamat || '-'"
                                          readonly
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed"></textarea>
                            </div>
                        </div>

                        <!-- Data Keluarga -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">👨‍👩‍👧‍👦 Data Keluarga</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nama Ayah (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                                    <input :value="siswa.nama_ayah || '-'"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- Nama Ibu (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                                    <input :value="siswa.nama_ibu || '-'"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- Pekerjaan Ayah (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ayah</label>
                                    <input :value="siswa.pekerjaan_ayah || '-'"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>

                                <!-- Pekerjaan Ibu (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ibu</label>
                                    <input :value="siswa.pekerjaan_ibu || '-'"
                                           type="text"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                                </div>
                            </div>
                    </div>

                    <!-- Data Akademik (readonly) -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">🎓 Data Akademik</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kelas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                                <input :value="siswa.kelas?.nama_kelas || 'Belum ada kelas'"
                                       type="text"
                                       readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                            </div>

                            <!-- Tahun Masuk -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Masuk</label>
                                <input :value="siswa.tahun_masuk"
                                       type="text"
                                       readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <input :value="siswa.status"
                                       type="text"
                                       readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button untuk field yang bisa diedit -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <Link :href="route('dashboard')" 
                              class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Kembali
                        </Link>
                        <button type="submit" 
                                :disabled="form.processing"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 transition-colors">
                            <span v-if="form.processing">⏳ Menyimpan...</span>
                            <span v-else>💾 Update </span>
                        </button>
                    </div>
                </form>
                </div>

                <!-- Ubah Password Card -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">🔒 Ubah Password</h3>
                        <p class="text-sm text-gray-600">Perbarui password untuk keamanan akun Anda</p>
                    </div>

                    <form @submit.prevent="updatePassword" class="p-6 space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password Saat Ini *
                            </label>
                            <input v-model="passwordForm.current_password"
                                   id="current_password"
                                   type="password"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div v-if="passwordForm.errors.current_password" class="text-red-600 text-sm mt-1">
                                {{ passwordForm.errors.current_password }}
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password Baru *
                            </label>
                            <input v-model="passwordForm.password"
                                   id="password"
                                   type="password"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div v-if="passwordForm.errors.password" class="text-red-600 text-sm mt-1">
                                {{ passwordForm.errors.password }}
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password Baru *
                            </label>
                            <input v-model="passwordForm.password_confirmation"
                                   id="password_confirmation"
                                   type="password"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div v-if="passwordForm.errors.password_confirmation" class="text-red-600 text-sm mt-1">
                                {{ passwordForm.errors.password_confirmation }}
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    :disabled="passwordForm.processing"
                                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 transition-colors">
                                <span v-if="passwordForm.processing">⏳ Mengubah...</span>
                                <span v-else>🔒 Ubah Password</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    siswa: Object,
    kelasList: Array,
    user: Object
})

// Form data - hanya field yang bisa diedit (tanpa foto)
const form = useForm({
    no_telepon: props.siswa.no_telepon || ''
})

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
})

// Form refs
const photoInput = ref(null)

// Methods
const updateProfile = () => {
    form.post(route('murid.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Refresh halaman setelah berhasil update profile
            setTimeout(() => {
                window.location.reload()
            }, 1500) // Delay 1.5 detik untuk melihat pesan sukses
        }
    })
}

const handlePhotoUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        // Buat form khusus untuk upload foto
        const photoForm = useForm({
            foto: file
        })
        
        // Auto submit the form to upload photo immediately
        photoForm.post(route('murid.profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                // Reload the page to show new photo
                window.location.reload()
            }
        })
    }
}

const deletePhoto = () => {
    if (confirm('Yakin ingin menghapus foto profile?')) {
        fetch(route('murid.profile.delete-photo'), {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload()
            }
        })
        .catch(error => {
            console.error('Error:', error)
            alert('Terjadi kesalahan saat menghapus foto')
        })
    }
}

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset()
            // Refresh halaman setelah berhasil update password
            setTimeout(() => {
                window.location.reload()
            }, 1500) // Delay 1.5 detik untuk melihat pesan sukses
        },
        onError: () => {
            passwordForm.reset('password', 'password_confirmation')
        }
    })
}
</script>
