<?php
$files = [
    'app/Http/Controllers/DashboardController.php',
    'app/Http/Controllers/AttendanceDailyController.php',
    'app/Http/Controllers/BkCaseController.php',
    'app/Http/Controllers/MtsIntegrationController.php',
    'app/Http/Controllers/PublicationController.php',
    'app/Http/Controllers/SystemController.php',
    'app/Http/Controllers/WaliKelasController.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Check for UTF-8 BOM (EF BB BF)
        if (substr($content, 0, 3) === "\xef\xbb\xbf") {
            $content = substr($content, 3);
            file_put_contents($file, $content);
            echo "Removed BOM from: $file\n";
        } else {
            echo "No BOM found in: $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
