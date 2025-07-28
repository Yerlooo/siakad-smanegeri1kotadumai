<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'siakad-smanegeri1kotadumai',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Mark notifications migration as done
try {
    $capsule::table('migrations')->insert([
        'migration' => '2025_07_27_220532_create_notifications_table',
        'batch' => 3
    ]);
    echo "Notifications migration marked as done\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
