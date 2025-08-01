<template>
    <Link 
        :href="href"
        :class="[
            'flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200',
            active 
                ? 'bg-green-100 text-green-700 border-r-4 border-green-500' 
                : 'text-gray-600 hover:bg-green-50 hover:text-green-700'
        ]"
    >
        <span class="mr-3 text-lg">{{ icon }}</span>
        <slot />
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: {
        type: String,
        required: true
    },
    active: {
        type: Boolean,
        default: false
    },
    icon: {
        type: String,
        default: '📄'
    }
})
</script>
