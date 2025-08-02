<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get current timestamp
$now = time();
$expiredTime = $now - (1 * 60); // 1 minute ago

echo "Current time: " . date('Y-m-d H:i:s', $now) . "\n";
echo "Expired time: " . date('Y-m-d H:i:s', $expiredTime) . "\n";

// Get sessions
$sessions = DB::table('sessions')->get();
echo "Total sessions: " . $sessions->count() . "\n";

foreach ($sessions as $session) {
    $lastActivity = date('Y-m-d H:i:s', $session->last_activity);
    $isExpired = $session->last_activity < $expiredTime ? 'EXPIRED' : 'ACTIVE';
    echo "Session: " . substr($session->id, 0, 10) . "..., Last: $lastActivity, Status: $isExpired\n";
}

// Delete expired sessions
$deleted = DB::table('sessions')->where('last_activity', '<', $expiredTime)->delete();
echo "Deleted $deleted expired sessions\n";

echo "Remaining sessions: " . DB::table('sessions')->count() . "\n";
