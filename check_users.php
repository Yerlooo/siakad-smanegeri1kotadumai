<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "=== Available Roles ===\n";
foreach (Role::all() as $role) {
    echo "ID: {$role->id} - Name: {$role->name} - Display: {$role->display_name}\n";
}

echo "\n=== Users and their Roles ===\n";
foreach (User::with('role')->get() as $user) {
    $roleName = $user->role ? $user->role->name : 'No role';
    echo "ID: {$user->id} - Name: {$user->name} - Email: {$user->email} - Role: {$roleName}\n";
}

echo "\n=== Users with role_id = 4 (murid) ===\n";
foreach (User::where('role_id', 4)->get() as $user) {
    echo "ID: {$user->id} - Name: {$user->name} - Email: {$user->email}\n";
}
