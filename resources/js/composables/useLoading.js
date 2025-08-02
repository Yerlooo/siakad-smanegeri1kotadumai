import { ref } from 'vue'

export function useLoading() {
    const isLoading = ref(false)
    const loadingText = ref('Memuat...')

    const startLoading = (text = 'Memuat...') => {
        isLoading.value = true
        loadingText.value = text
    }

    const stopLoading = () => {
        isLoading.value = false
        loadingText.value = 'Memuat...'
    }

    return {
        isLoading,
        loadingText,
        startLoading,
        stopLoading
    }
}
