<template>
    <nav v-if="links && links.length > 0" class="flex items-center justify-between">
        <!-- Mobile pagination dengan layout responsif -->
        <div class="w-full sm:hidden">
            <!-- Info pagination mobile -->
            <div class="flex flex-col space-y-3">
                <!-- Info halaman dan total -->
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">
                        Halaman {{ getCurrentPage() }} dari {{ getLastPage() }}
                    </span>
                    <span class="text-gray-500">
                        {{ from || 0 }}-{{ to || 0 }} dari {{ total || 0 }}
                    </span>
                </div>
                
                <!-- Navigation buttons stack -->
                <div class="flex flex-col space-y-2">
                    <!-- Row 1: Previous and Next buttons -->
                    <div class="flex items-center space-x-2">
                        <Link v-if="getPrevUrl()" 
                              :href="getPrevUrl()" 
                              class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span class="truncate">Sebelumnya</span>
                        </Link>
                        <div v-else class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-400 bg-gray-50">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span class="truncate">Sebelumnya</span>
                        </div>
                        
                        <Link v-if="getNextUrl()" 
                              :href="getNextUrl()" 
                              class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <span class="truncate">Selanjutnya</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                        <div v-else class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-400 bg-gray-50">
                            <span class="truncate">Selanjutnya</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Row 2: Jump to page (jika diperlukan) -->
                    <div v-if="getLastPage() > 1" class="flex items-center justify-center space-x-2">
                        <span class="text-xs text-gray-500 whitespace-nowrap">Lompat ke:</span>
                        <select @change="jumpToPage" :value="getCurrentPage()" class="text-sm border border-gray-300 rounded px-2 py-1 min-w-0 flex-shrink">
                            <option v-for="page in getPageNumbers()" :key="page" :value="page">
                                Hal {{ page }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ from }}</span>
                    to
                    <span class="font-medium">{{ to }}</span>
                    of
                    <span class="font-medium">{{ total }}</span>
                    results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <template v-for="(link, index) in links" :key="index">
                        <Link v-if="link.url && index === 0"
                              :href="link.url"
                              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                        
                        <span v-else-if="!link.url && index === 0"
                              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        
                        <Link v-else-if="link.url && index === links.length - 1"
                              :href="link.url"
                              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                        
                        <span v-else-if="!link.url && index === links.length - 1"
                              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        
                        <Link v-else-if="link.url && index !== 0 && index !== links.length - 1"
                              :href="link.url"
                              :class="[
                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                link.active 
                                  ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                              ]">
                            <span v-html="link.label"></span>
                        </Link>
                        
                        <span v-else-if="!link.url && index !== 0 && index !== links.length - 1"
                              class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                              v-html="link.label">
                        </span>
                    </template>
                </nav>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    links: {
        type: Array,
        default: () => []
    },
    from: {
        type: Number,
        default: 0
    },
    to: {
        type: Number,
        default: 0
    },
    total: {
        type: Number,
        default: 0
    }
})

// Helper functions untuk mendapatkan informasi pagination
const getCurrentPage = () => {
    if (!props.links || props.links.length === 0) return 1
    const activePage = props.links.find(link => link.active)
    return activePage ? parseInt(activePage.label) || 1 : 1
}

const getLastPage = () => {
    if (!props.links || props.links.length < 3) return 1
    // Cari link dengan label numerik tertinggi
    const pageLinks = props.links.filter(link => link.label && !isNaN(parseInt(link.label)))
    if (pageLinks.length === 0) return 1
    const lastPageLink = pageLinks[pageLinks.length - 1]
    return lastPageLink ? parseInt(lastPageLink.label) || 1 : 1
}

const getPrevUrl = () => {
    if (!props.links || props.links.length === 0) return null
    return props.links[0]?.url || null
}

const getNextUrl = () => {
    if (!props.links || props.links.length === 0) return null
    return props.links[props.links.length - 1]?.url || null
}

const getPageNumbers = () => {
    const lastPage = getLastPage()
    if (lastPage <= 1) return [1]
    return Array.from({ length: lastPage }, (_, i) => i + 1)
}

const jumpToPage = (event) => {
    try {
        const page = parseInt(event.target.value)
        if (!page || page < 1) return
        
        // Jika browser tidak support window object (SSR), skip
        if (typeof window === 'undefined') return
        
        // Dapatkan URL halaman yang dipilih dari links yang ada
        const targetPageLink = props.links?.find(link => {
            const pageNumber = parseInt(link.label)
            return pageNumber === page
        })
        
        if (targetPageLink && targetPageLink.url) {
            router.get(targetPageLink.url)
        } else {
            // Fallback: buat URL dengan parameter page
            const currentParams = new URLSearchParams(window.location.search)
            currentParams.set('page', page.toString())
            const newUrl = window.location.pathname + '?' + currentParams.toString()
            router.get(newUrl)
        }
    } catch (error) {
        console.error('Error in jumpToPage:', error)
    }
}
</script>
