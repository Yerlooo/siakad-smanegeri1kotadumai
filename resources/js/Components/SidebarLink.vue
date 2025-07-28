<template>
    <Link 
        :href="href"
        :class="[
            'flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200',
            active 
                ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' 
                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'
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
