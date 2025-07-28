<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">{{ title }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ formattedValue }}</p>
            </div>
            <div :class="[color, 'w-12 h-12 rounded-lg flex items-center justify-center text-white text-2xl']">
                {{ icon }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    color: {
        type: String,
        default: 'bg-blue-500'
    }
})

const formattedValue = computed(() => {
    if (typeof props.value === 'number') {
        return props.value.toLocaleString('id-ID')
    }
    return props.value
})
</script>
