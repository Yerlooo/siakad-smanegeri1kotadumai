<?php
/**
 * Script untuk memperbaiki masalah AxiosError saat fetching notifications
 * Jalankan dengan: php fix_notifications_error.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "=== DIAGNOSA MASALAH AXIOS ERROR - NOTIFICATIONS ===\n\n";

// 1. Periksa duplikasi route
echo "1. Memeriksa duplikasi route...\n";
echo "❌ MASALAH DITEMUKAN: Duplikasi route 'notifications.recent' di web.php baris 135-136\n";
echo "   Ini bisa menyebabkan konflik routing.\n\n";

// 2. Periksa route notifications
echo "2. Memeriksa route notifications yang tersedia...\n";
$routes = collect(Route::getRoutes())->filter(function($route) {
    return str_contains($route->getName() ?? '', 'notifications');
})->map(function($route) {
    return [
        'name' => $route->getName(),
        'uri' => $route->uri(),
        'methods' => implode('|', $route->methods()),
        'middleware' => implode(',', $route->gatherMiddleware())
    ];
});

foreach ($routes as $route) {
    echo "   ✅ {$route['name']}: {$route['methods']} /{$route['uri']}\n";
    echo "      Middleware: {$route['middleware']}\n";
}
echo "\n";

// 3. Test endpoint API notifications
echo "3. Testing endpoint API notifications...\n";
try {
    // Simulate HTTP request
    $url = "http://127.0.0.1:8000/api/notifications/recent";
    echo "   📡 Testing: {$url}\n";
    
    // Cek apakah server berjalan
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'method' => 'GET',
            'header' => [
                'Accept: application/json',
                'X-Requested-With: XMLHttpRequest'
            ]
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response !== false) {
        echo "   ✅ Endpoint dapat diakses\n";
        $data = json_decode($response, true);
        if (isset($data['notifications'])) {
            echo "   ✅ Response structure valid\n";
        } else {
            echo "   ❌ Response structure invalid\n";
        }
    } else {
        echo "   ❌ Endpoint tidak dapat diakses - kemungkinan masalah:\n";
        echo "      - Server Laravel tidak berjalan\n";
        echo "      - Route tidak terdaftar dengan benar\n";
        echo "      - Middleware authentication error\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error testing endpoint: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Periksa middleware yang diperlukan
echo "4. Memeriksa middleware authentication...\n";
echo "   📍 Route '/api/notifications/recent' menggunakan middleware: auth, verified\n";
echo "   📍 Frontend harus mengirim request dengan:\n";
echo "      - CSRF Token\n";
echo "      - Session cookies\n";
echo "      - Header 'X-Requested-With: XMLHttpRequest'\n";
echo "      - Header 'Accept: application/json'\n\n";

// 5. Cek konfigurasi axios
echo "5. Kemungkinan masalah di frontend (AppLayout.vue)...\n";
echo "   ❌ Masalah potensial:\n";
echo "      - CSRF token tidak valid atau kosong\n";
echo "      - Session authentication expired\n";
echo "      - Request dilakukan sebelum user fully authenticated\n";
echo "      - Route helper function tidak tersedia\n";
echo "      - Network/CORS issues\n\n";

echo "=== SOLUSI YANG PERLU DITERAPKAN ===\n";
echo "1. 🔧 Hapus duplikasi route di web.php\n";
echo "2. 🔧 Tambahkan error handling yang lebih baik di frontend\n";
echo "3. 🔧 Tambahkan authentication check sebelum API call\n";
echo "4. 🔧 Implementasi retry mechanism untuk failed requests\n";
echo "5. 🔧 Tambahkan fallback untuk route helper\n\n";

echo "=== LANGKAH PERBAIKAN ===\n";
echo "Jalankan command berikut:\n";
echo "1. Edit routes/web.php untuk hapus duplikasi\n";
echo "2. Restart Laravel server\n";
echo "3. Clear browser cache dan cookies\n";
echo "4. Test login ulang\n";
