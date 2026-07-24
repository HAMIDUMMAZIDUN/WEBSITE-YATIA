<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$adminRole = App\Models\Role::where('name', 'admin')->first();
if (!$adminRole) {
    echo "Admin role not found!\n";
    exit(1);
}

$user = App\Models\User::firstOrCreate(
    ['email' => 'admin@gmail.com'],
    [
        'name' => 'Admin Madrasah',
        'nik' => '3204461404040007',
        'password' => Illuminate\Support\Facades\Hash::make('hamidun123'),
        'role_id' => $adminRole->id,
        'is_active' => true,
    ]
);

echo "Admin user created: ID {$user->id} | Email: {$user->email}\n";
