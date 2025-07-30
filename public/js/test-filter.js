/**
 * Script untuk test dan perbaikan filter kelas di halaman siswa
 */

// Test filter kelas di browser console
function testKelasFilter() {
    console.log('=== TEST FILTER KELAS ===');
    
    // Check if we're on the siswa index page
    if (!window.location.pathname.includes('/siswa')) {
        console.log('❌ Bukan halaman siswa index');
        return;
    }
    
    // Check Vue app data
    const vueApp = document.querySelector('#app').__vueParentComponent;
    if (!vueApp) {
        console.log('❌ Vue app tidak ditemukan');
        return;
    }
    
    console.log('✅ Halaman siswa index detected');
    
    // Test filter dropdown
    const kelasSelect = document.querySelector('select[v-model="kelasFilter"]');
    if (kelasSelect) {
        console.log('✅ Dropdown kelas ditemukan');
        console.log('Options available:', Array.from(kelasSelect.options).map(opt => ({
            value: opt.value,
            text: opt.text
        })));
    } else {
        console.log('❌ Dropdown kelas tidak ditemukan');
    }
    
    // Test data props
    console.log('Props siswa:', window.siswaData?.data?.length || 'tidak ada');
    console.log('Props allKelas:', window.allKelasData?.length || 'tidak ada');
}

// Function to manually trigger filter
function triggerKelasFilter(kelasId) {
    const kelasSelect = document.querySelector('select');
    if (kelasSelect) {
        kelasSelect.value = kelasId;
        kelasSelect.dispatchEvent(new Event('change', { bubbles: true }));
        console.log('Filter triggered for kelas ID:', kelasId);
    }
}

// Run test when page loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', testKelasFilter);
} else {
    setTimeout(testKelasFilter, 1000);
}

console.log('Filter test script loaded. Run testKelasFilter() manually if needed.');
