<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Import models
use App\Models\MataPelajaran;

echo "=== Testing mataPelajaranList Data Type ===\n";

$mataPelajaranList = MataPelajaran::orderBy('nama_mapel')->get();

echo "Original Collection:\n";
echo "- Type: " . gettype($mataPelajaranList) . "\n";
echo "- Count: " . $mataPelajaranList->count() . "\n";
echo "- Is Array: " . (is_array($mataPelajaranList) ? 'Yes' : 'No') . "\n";

$asArray = $mataPelajaranList->values()->toArray();

echo "\nAs Array:\n";
echo "- Type: " . gettype($asArray) . "\n";
echo "- Count: " . count($asArray) . "\n";
echo "- Is Array: " . (is_array($asArray) ? 'Yes' : 'No') . "\n";

if (count($asArray) > 0) {
    echo "\nFirst item structure:\n";
    echo "- Type: " . gettype($asArray[0]) . "\n";
    if (is_array($asArray[0])) {
        echo "- Keys: " . implode(', ', array_keys($asArray[0])) . "\n";
    }
}

echo "\nData structure should now be valid for Vue props!\n";
