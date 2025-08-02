/**
 * Script untuk test dan perbaikan filter kelas di halaman siswa
 */

// Test filter kelas di browser console
function testKelasFilter() {
    // Check if we're on the siswa index page
    if (!window.location.pathname.includes('/siswa')) {
        return;
    }
    
    // Check Vue app data
    const vueApp = document.querySelector('#app').__vueParentComponent;
    if (!vueApp) {
        return;
    }
    
    // Test filter dropdown
    const kelasSelect = document.querySelector('select[v-model="kelasFilter"]');
}

// Function to manually trigger filter
function triggerKelasFilter(kelasId) {
    const kelasSelect = document.querySelector('select');
    if (kelasSelect) {
        kelasSelect.value = kelasId;
        kelasSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

// Run test when page loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', testKelasFilter);
} else {
    setTimeout(testKelasFilter, 1000);
}


