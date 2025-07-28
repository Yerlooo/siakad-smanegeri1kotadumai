<template>
    <Head title="Tambah Data Guru" />

    <AppLayout page-title="Tambah Data Guru">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center space-x-4">
                    <Link :href="route('guru.index')" 
                          class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tambah Data Guru</h1>
                        <p class="text-gray-600">Masukkan data guru baru</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Data Pribadi -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Data Pribadi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.name"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.name }"
                                    placeholder="Masukkan nama lengkap"
                                >
                                <div v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    NIP <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.nip"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.nip }"
                                    placeholder="Masukkan NIP"
                                >
                                <div v-if="errors.nip" class="mt-1 text-sm text-red-600">{{ errors.nip }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.email"
                                    type="email" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.email }"
                                    placeholder="Masukkan email"
                                >
                                <div v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    v-model="form.role_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.role_id }"
                                >
                                    <option value="">Pilih Role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.display_name }}
                                    </option>
                                </select>
                                <div v-if="errors.role_id" class="mt-1 text-sm text-red-600">{{ errors.role_id }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Kelamin <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    v-model="form.jenis_kelamin"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.jenis_kelamin }"
                                >
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <div v-if="errors.jenis_kelamin" class="mt-1 text-sm text-red-600">{{ errors.jenis_kelamin }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Lahir
                                </label>
                                <input 
                                    v-model="form.tanggal_lahir"
                                    type="date" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.tanggal_lahir }"
                                >
                                <div v-if="errors.tanggal_lahir" class="mt-1 text-sm text-red-600">{{ errors.tanggal_lahir }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    No. Telepon
                                </label>
                                <input 
                                    v-model="form.no_telepon"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.no_telepon }"
                                    placeholder="Masukkan no. telepon"
                                >
                                <div v-if="errors.no_telepon" class="mt-1 text-sm text-red-600">{{ errors.no_telepon }}</div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat
                                </label>
                                <textarea 
                                    v-model="form.alamat"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.alamat }"
                                    placeholder="Masukkan alamat lengkap"
                                ></textarea>
                                <div v-if="errors.alamat" class="mt-1 text-sm text-red-600">{{ errors.alamat }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Akun -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Data Akun</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.password"
                                    type="password" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.password }"
                                    placeholder="Masukkan password"
                                >
                                <div v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.password_confirmation"
                                    type="password" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.password_confirmation }"
                                    placeholder="Konfirmasi password"
                                >
                                <div v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-200 pt-6 flex justify-end space-x-4">
                        <Link :href="route('guru.index')" 
                              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </Link>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    roles: Array,
    errors: Object
})

const form = useForm({
    name: '',
    email: '',
    nip: '',
    role_id: '',
    jenis_kelamin: '',
    tanggal_lahir: '',
    no_telepon: '',
    alamat: '',
    password: '',
    password_confirmation: ''
})

const submit = () => {
    form.post(route('guru.store'), {
        onSuccess: () => {
            // Success akan dihandle oleh flash message di layout
        }
    })
}
</script>
