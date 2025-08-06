<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

// Check session status
echo "=== SESSION DEBUG INFO ===\n";
echo "Session ID: " . Session::getId() . "\n";
echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Lifetime: " . config('session.lifetime') . " minutes\n";
echo "App Key Present: " . (config('app.key') ? 'YES' : 'NO') . "\n";

// Check if user authenticated
if (Auth::check()) {
    echo "User Authenticated: YES\n";
    echo "User ID: " . Auth::id() . "\n";
    echo "User Name: " . Auth::user()->name . "\n";
} else {
    echo "User Authenticated: NO\n";
}

// Check sessions table
try {
    $sessionCount = DB::table('sessions')->count();
    echo "Sessions in DB: " . $sessionCount . "\n";
    
    $recentSessions = DB::table('sessions')
        ->orderBy('last_activity', 'desc')
        ->limit(3)
        ->get(['id', 'user_id', 'last_activity']);
    
    echo "Recent Sessions:\n";
    foreach ($recentSessions as $session) {
        echo "- ID: " . substr($session->id, 0, 8) . "..., User: " . ($session->user_id ?: 'Guest') . ", Last: " . date('Y-m-d H:i:s', $session->last_activity) . "\n";
    }
} catch (Exception $e) {
    echo "Error checking sessions: " . $e->getMessage() . "\n";
}

echo "=== END DEBUG INFO ===\n";
