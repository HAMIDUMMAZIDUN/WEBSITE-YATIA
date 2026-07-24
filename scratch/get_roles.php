<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Role::all() as $role) {
    echo "ID: {$role->id} | Name: {$role->name} | Display Name: {$role->display_name}\n";
}
