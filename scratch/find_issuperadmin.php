<?php
$file = 'd:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/app/Http/Controllers/DashboardController.php';
$lines = file($file);
foreach ($lines as $i => $line) {
    if (strpos($line, 'isSuperAdmin') !== false) {
        echo ($i + 1) . ': ' . trim($line) . "\n";
    }
}
