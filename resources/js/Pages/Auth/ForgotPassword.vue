<script setup>
import { ref, onMounted } from 'vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const isLoading = ref(false);
const currentTip = ref(0);

const securityTips = [
    {
        icon: "🔐",
        title: "Password yang Kuat",
        description: "Gunakan kombinasi huruf besar, kecil, angka, dan simbol untuk keamanan maksimal."
    },
    {
        icon: "🛡️",
        title: "Jaga Kerahasiaan",
        description: "Jangan bagikan password Anda kepada siapa pun, termasuk teman atau keluarga."
    },
    {
        icon: "🔄",
        title: "Update Berkala",
        description: "Ganti password secara berkala untuk menjaga keamanan akun Anda."
    },
    {
        icon: "📱",
        title: "Verifikasi Identitas",
        description: "Pastikan Anda menggunakan email yang terdaftar di sistem sekolah."
    }
];

const submit = () => {
    isLoading.value = true;
    form.post(route('password.email'), {
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

onMounted(() => {
    // Auto rotate security tips
    setInterval(() => {
        currentTip.value = (currentTip.value + 1) % securityTips.length;
    }, 3500);
});
</script>

<template>
    <div class="min-h-screen flex">
        <Head title="Reset Password - SIAKAD SMA Negeri 1 Kota Dumai" />
        
        <!-- Left Side - Reset Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-white">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo and Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m0 0V3a1 1 0 10-2 0v2H9V3a1 1 0 10-2 0v2a2 2 0 00-2 2v2a2 2 0 002 2h8a2 2 0 002-2V9z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h1>
                    <p class="text-gray-600">SIAKAD SMA Negeri 1 Kota Dumai</p>
                </div>

                <!-- Info Message -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-blue-800 mb-1">Lupa Password?</h3>
                            <p class="text-sm text-blue-700 mb-2">
                                Tidak masalah! Masukkan alamat email Anda dan kami akan mengirimkan link reset password yang memungkinkan Anda membuat password baru.
                            </p>
                            <div class="text-xs text-blue-600 bg-blue-100 p-2 rounded mt-2">
                                <strong>Catatan:</strong> Link reset password akan berlaku selama 60 menit setelah dikirim.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg animate-fade-in">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-green-800 mb-1">✅ Email Terkirim!</h3>
                            <p class="text-sm text-green-700 mb-3">{{ status }}</p>
                            
                            <div class="bg-green-100 p-3 rounded text-xs text-green-800">
                                <p class="font-medium mb-2">Langkah selanjutnya:</p>
                                <ol class="list-decimal list-inside space-y-1">
                                    <li>Cek email Anda (termasuk folder spam/junk)</li>
                                    <li>Klik link "Reset Password" di email</li>
                                    <li>Buat password baru yang kuat</li>
                                    <li>Login dengan password baru</li>
                                </ol>
                                <p class="mt-2 text-green-600">
                                    <strong>⏰ Penting:</strong> Link akan kedaluwarsa dalam 60 menit
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reset Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-150 ease-in-out"
                                :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500': form.errors.email }"
                                placeholder="Masukkan email yang terdaftar"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                        <p class="mt-2 text-xs text-gray-500">
                            Pastikan email yang Anda masukkan sama dengan yang terdaftar di sistem sekolah
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-4">
                        <button
                            type="submit"
                            :disabled="form.processing || isLoading"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out transform hover:scale-105"
                        >
                            <span v-if="!isLoading" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Kirim Link Reset Password
                            </span>
                            <span v-else class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengirim Email...
                            </span>
                        </button>

                        <Link
                            :href="route('login')"
                            class="group relative w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-150 ease-in-out"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Login
                        </Link>
                    </div>
                </form>

                <!-- Additional Help -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">🆘 Butuh Bantuan?</h4>
                    <p class="text-xs text-gray-600 mb-3">
                        Jika Anda mengalami masalah dengan reset password atau tidak menerima email:
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1 mb-4">
                        <li>• 📧 Periksa folder spam/junk email Anda</li>
                        <li>• ✅ Pastikan email yang dimasukkan benar dan terdaftar</li>
                        <li>• ⏰ Tunggu beberapa menit, email mungkin sedang dalam proses</li>
                        <li>• 🔄 Coba kirim ulang jika belum menerima setelah 10 menit</li>
                        <li>• 📞 Hubungi admin sekolah jika masalah berlanjut</li>
                    </ul>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-xs text-yellow-800 font-medium">Status Konfigurasi Email</p>
                                <p class="text-xs text-yellow-700 mt-1">
                                    Untuk admin: Pastikan konfigurasi MAIL di .env sudah benar. 
                                    Jalankan <code class="bg-yellow-200 px-1 rounded">php artisan email:test your@email.com</code> untuk test email.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500">
                        © 2025 SMA Negeri 1 Kota Dumai. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Security Tips -->
        <div class="hidden lg:block relative w-0 flex-1 bg-gradient-to-br from-orange-500 via-red-500 to-pink-600">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <svg class="h-full w-full" fill="currentColor" viewBox="0 0 100 100">
                    <defs>
                        <pattern id="security-grid" width="20" height="20" patternUnits="userSpaceOnUse">
                            <path d="M 20 0 L 0 0 0 20" fill="none" stroke="currentColor" stroke-width="0.5"/>
                            <circle cx="10" cy="10" r="1" fill="currentColor" opacity="0.3"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#security-grid)" />
                </svg>
            </div>

            <!-- Security Tips Content -->
            <div class="relative h-full flex items-center justify-center p-12">
                <div class="text-center text-white max-w-lg">
                    <div class="transition-all duration-500 ease-in-out">
                        <div class="text-6xl mb-6 animate-bounce">
                            {{ securityTips[currentTip].icon }}
                        </div>
                        <h2 class="text-3xl font-bold mb-4 animate-fade-in">
                            Tips Keamanan
                        </h2>
                        <h3 class="text-xl text-orange-100 mb-6 animate-fade-in" style="animation-delay: 0.2s">
                            {{ securityTips[currentTip].title }}
                        </h3>
                        <p class="text-lg text-orange-100 leading-relaxed animate-fade-in" style="animation-delay: 0.4s">
                            {{ securityTips[currentTip].description }}
                        </p>
                    </div>

                    <!-- Tip Indicators -->
                    <div class="flex justify-center space-x-2 mt-8">
                        <button
                            v-for="(tip, index) in securityTips"
                            :key="index"
                            @click="currentTip = index"
                            class="w-3 h-3 rounded-full transition-all duration-300"
                            :class="index === currentTip ? 'bg-white' : 'bg-white bg-opacity-40 hover:bg-opacity-60'"
                        ></button>
                    </div>

                    <!-- Security Features -->
                    <div class="mt-12 grid grid-cols-2 gap-4">
                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-2xl mb-2">🔒</div>
                            <h4 class="text-sm font-medium mb-1">Enkripsi Email</h4>
                            <p class="text-xs text-orange-100">Link reset aman dengan enkripsi</p>
                        </div>
                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-2xl mb-2">⏰</div>
                            <h4 class="text-sm font-medium mb-1">Berlaku Terbatas</h4>
                            <p class="text-xs text-orange-100">Link akan kedaluwarsa dalam 60 menit</p>
                        </div>
                    </div>
                </div>

                <!-- Floating Security Elements -->
                <div class="absolute top-10 left-10 animate-float">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                
                <div class="absolute bottom-10 right-10 animate-float" style="animation-delay: 2s">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>

                <div class="absolute top-1/2 right-20 animate-float" style="animation-delay: 4s">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full"></div>
                </div>

                <div class="absolute top-1/4 left-1/4 animate-float" style="animation-delay: 6s">
                    <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full"></div>
                </div>
            </div>
        </div>
    </div>
</template>
