<script setup>
import { ref, onMounted } from 'vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const isLoading = ref(false);
const currentSlide = ref(0);

const slides = [
    {
        title: "Selamat Datang di SIAKAD",
        subtitle: "SMA Negeri 1 Kota Dumai",
        description: "Sistem Informasi Akademik yang memudahkan pengelolaan data siswa, guru, dan jadwal pelajaran.",
        icon: "🎓"
    },
    {
        title: "Kelola Data Siswa",
        subtitle: "Dengan Mudah dan Efisien",
        description: "Manajemen data siswa, nilai, dan perkembangan akademik dalam satu platform terintegrasi.",
        icon: "👨‍🎓"
    },
    {
        title: "Pantau Jadwal Pelajaran",
        subtitle: "Real-time dan Akurat",
        description: "Akses jadwal pelajaran, informasi guru, dan ruang kelas secara real-time.",
        icon: "📅"
    }
];

const submit = () => {
    isLoading.value = true;
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            isLoading.value = false;
        },
    });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

onMounted(() => {
    // Auto slide carousel
    setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % slides.length;
    }, 4000);
});
</script>

<template>
    <div class="min-h-screen flex" style="background: linear-gradient(135deg, #43a047 0%, #fbc02d 80%, #fbc02d 100%);">
        <Head title="SIAKAD - SMA Negeri 1 Kota Dumai" />
        
        <!-- Left Side - Login Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-white">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo and Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center shadow-lg overflow-hidden" style="background: linear-gradient(135deg, #43a047 0%, #fbc02d 100%);">
                            <img src="/storage/logo-smansa.png" alt="Logo SMA Negeri 1 Kota Dumai" class="w-16 h-16 object-contain" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h1>
                    <p class="text-gray-600">SIAKAD SMA Negeri 1 Kota Dumai</p>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-green-800">{{ status }}</span>
                    </div>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
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
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-150 ease-in-out"
                                :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500': form.errors.email }"
                                placeholder="Masukkan email Anda"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-150 ease-in-out"
                                :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500': form.errors.password }"
                                placeholder="Masukkan password Anda"
                            />
                            <button
                                type="button"
                                @click="togglePassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg v-if="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414L8.464 8.464m5.657 5.657l1.415 1.414M14.828 14.828L14.828 14.828"/>
                                </svg>
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember"
                                type="checkbox"
                                v-model="form.remember"
                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                            />
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Ingat saya
                            </label>
                        </div>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm font-medium"
                            style="color: #43a047; transition: color 0.2s;"
                            @mouseover="event.target.style.color = '#fbc02d'"
                            @mouseout="event.target.style.color = '#43a047'"
                        >
                            Lupa password?
                        </Link>
                    </div>

                    <!-- Login Button -->
                    <button
                        type="submit"
                        :disabled="form.processing || isLoading"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white"
                        style="background: linear-gradient(90deg, #43a047 0%, #fbc02d 100%);"
                        :class="['focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out transform hover:scale-105']"
                    >
                        <span v-if="!isLoading" class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Masuk ke SIAKAD
                        </span>
                        <span v-else class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500">
                        © 2025 SMA Negeri 1 Kota Dumai. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Info Carousel -->
        <div class="hidden lg:block relative w-0 flex-1" style="background: linear-gradient(135deg, #43a047 0%, #fbc02d 80%, #fbc02d 100%);">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <svg class="h-full w-full" fill="currentColor" viewBox="0 0 100 100">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)" />
                </svg>
            </div>

            <!-- Carousel Content -->
            <div class="relative h-full flex items-center justify-center p-12">
                <div class="text-center text-white max-w-lg">
                    <div class="transition-all duration-500 ease-in-out">
                        <div class="text-6xl mb-6 animate-bounce">
                            {{ slides[currentSlide].icon }}
                        </div>
                        <h2 class="text-4xl font-bold mb-4 animate-fade-in">
                            {{ slides[currentSlide].title }}
                        </h2>
                        <h3 class="text-xl text-blue-100 mb-6 animate-fade-in" style="animation-delay: 0.2s">
                            {{ slides[currentSlide].subtitle }}
                        </h3>
                        <p class="text-lg text-blue-100 leading-relaxed animate-fade-in" style="animation-delay: 0.4s">
                            {{ slides[currentSlide].description }}
                        </p>
                    </div>

                    <!-- Slide Indicators -->
                    <div class="flex justify-center space-x-2 mt-8">
                        <button
                            v-for="(slide, index) in slides"
                            :key="index"
                            @click="currentSlide = index"
                            class="w-3 h-3 rounded-full transition-all duration-300"
                            :class="index === currentSlide ? 'bg-white' : 'bg-white bg-opacity-40 hover:bg-opacity-60'"
                        ></button>
                    </div>
                </div>

                <!-- Floating Elements -->
                <div class="absolute top-10 left-10 animate-float">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                
                <div class="absolute bottom-10 right-10 animate-float" style="animation-delay: 2s">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>

                <div class="absolute top-1/2 right-20 animate-float" style="animation-delay: 4s">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full"></div>
                </div>
            </div>
        </div>
    </div>
</template>
