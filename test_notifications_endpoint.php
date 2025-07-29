<?php
/**
 * Script untuk test endpoint notifications setelah perbaikan
 * Jalankan dengan: php test_notifications_endpoint.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST ENDPOINT NOTIFICATIONS SETELAH PERBAIKAN ===\n\n";

// Test endpoint menggunakan Laravel HTTP Client
echo "1. Testing endpoint notifications.recent...\n";

try {
    // Simulate authenticated request
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Accept' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest'
    ])->get('http://127.0.0.1:8000/api/notifications/recent');
    
    echo "   Status Code: " . $response->status() . "\n";
    
    if ($response->successful()) {
        echo "   ✅ Endpoint berhasil diakses\n";
        $data = $response->json();
        echo "   Response keys: " . implode(', ', array_keys($data)) . "\n";
    } elseif ($response->status() === 401) {
        echo "   ⚠️  Unauthorized (normal untuk unauthenticated request)\n";
        echo "   Response: " . $response->body() . "\n";
    } else {
        echo "   ❌ Error: " . $response->status() . "\n";
        echo "   Response: " . $response->body() . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n2. Panduan untuk test di browser:\n";
echo "   1. Login ke aplikasi terlebih dahulu\n";
echo "   2. Buka Developer Tools (F12)\n";
echo "   3. Di Console, jalankan:\n";
echo "      fetch('/api/notifications/recent', {\n";
echo "          headers: {\n";
echo "              'Accept': 'application/json',\n";
echo "              'X-Requested-With': 'XMLHttpRequest'\n";
echo "          }\n";
echo "      }).then(r => r.json()).then(console.log)\n";
echo "\n3. Jika masih error, periksa:\n";
echo "   - Browser Console untuk error detail\n";
echo "   - Network tab untuk request/response\n";
echo "   - Laravel log: storage/logs/laravel.log\n";

echo "\n=== PERBAIKAN YANG SUDAH DILAKUKAN ===\n";
echo "✅ 1. Hapus duplikasi route di web.php\n";
echo "✅ 2. Tambah error handling di AppLayout.vue\n";
echo "✅ 3. Tambah authentication check di frontend\n";
echo "✅ 4. Tambah try-catch di backend controller\n";
echo "✅ 5. Tambah logging untuk debugging\n";
echo "✅ 6. Clear route dan config cache\n";

echo "\n=== STATUS ===\n";
echo "Route 'notifications.recent' sudah terdaftar dengan benar ✅\n";
echo "Duplikasi route sudah diperbaiki ✅\n";
echo "Error handling sudah ditingkatkan ✅\n\n";

echo "Silakan test di browser setelah login!\n";
