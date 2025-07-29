<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

// Cek semua role
echo "=== All Roles ===\n";
foreach (Role::all() as $role) {
    echo "ID: {$role->id}, Name: {$role->name}, Display: {$role->display_name}\n";
}

// Cek semua user
echo "\n=== All Users ===\n";
foreach (User::all() as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Role ID: {$user->role_id}, Role: " . ($user->role ? $user->role->name : 'No role') . "\n";
}

// Cek user dengan role_id = 4
echo "\n=== Users with role_id = 4 ===\n";
$users = User::where('role_id', 4)->get();
echo "Found: " . $users->count() . " users\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
}

// Cek role murid
echo "\n=== Role Murid ===\n";
$muridRole = Role::where('name', 'murid')->first();
if ($muridRole) {
    echo "Role ID: {$muridRole->id}, Name: {$muridRole->name}\n";
    $muridUsers = User::where('role_id', $muridRole->id)->get();
    echo "Users with this role: " . $muridUsers->count() . "\n";
} else {
    echo "Role 'murid' not found!\n";
}
